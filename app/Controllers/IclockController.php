<?php

namespace App\Controllers;

use App\Models\AttendanceLogModel;
use App\Models\DeviceModel;
use App\Models\DeviceUserMapModel;
use App\Libraries\AttendanceService;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use Throwable;

class IclockController extends BaseController
{
    public function registry()
    {
        $this->touchDevice();

        return $this->plainOk();
    }

    public function getrequest()
    {
        // No pending commands in MVP.
        $this->touchDevice();

        return $this->plainOk();
    }

    public function devicecmd()
    {
        // Device command acknowledgement endpoint (MVP: accept & ignore)
        $this->touchDevice();

        return $this->plainOk();
    }

    public function cdata()
    {
        $device = $this->touchDevice();

        $sn    = (string) ($device['sn'] ?? '');
        $table = (string) ($this->request->getGet('table') ?? $this->request->getPost('table') ?? '');
        $opts  = (string) ($this->request->getGet('options') ?? '');

        // Device requesting config/options.
        if ($this->request->getMethod() === 'get' && ($opts === 'all' || $table === '')) {
            $body = "GET OPTION FROM: {$sn}\n"
                . "STAMP=9999\n"
                . "ATTLOGStamp=0\n"
                . "OPERLOGStamp=0\n"
                . "ATTPHOTOStamp=0\n"
                . "ErrorDelay=60\n"
                . "Delay=30\n"
                . "TransInterval=1\n"
                . "TransFlag=1111111111\n"
                . "TimeZone=7\n"
                . "Realtime=1\n";

            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'text/plain')
                ->setBody($body);
        }

        if (strtoupper($table) === 'ATTLOG') {
            // In practice many devices POST plain text lines. Some send form-urlencoded.
            $rawBody = (string) $this->request->getBody();
            if (trim($rawBody) === '') {
                $rawBody = (string) ($this->request->getPost('data') ?? $this->request->getPost('DATA') ?? '');
            }

            $this->ingestAttlog((int) ($device['id'] ?? 0), $rawBody);

            // Most iClock implementations expect just 'OK'.
            return $this->plainOk();
        }

        return $this->plainOk();
    }

    /**
     * @return array<string, mixed>
     */
    private function touchDevice(): array
    {
        $sn = (string) (
            $this->request->getGet('SN')
            ?? $this->request->getPost('SN')
            ?? $this->request->getGet('sn')
            ?? $this->request->getPost('sn')
            ?? ''
        );

        $deviceName = (string) (
            $this->request->getGet('DevName')
            ?? $this->request->getPost('DevName')
            ?? $this->request->getGet('devname')
            ?? $this->request->getPost('devname')
            ?? $this->request->getGet('Name')
            ?? $this->request->getPost('Name')
            ?? ''
        );

        $deviceLocation = (string) (
            $this->request->getGet('LOC')
            ?? $this->request->getPost('LOC')
            ?? $this->request->getGet('location')
            ?? $this->request->getPost('location')
            ?? ''
        );

        $deviceModel = model(DeviceModel::class);
        $device      = $sn !== '' ? $deviceModel->where('sn', $sn)->first() : null;

        $now = date('Y-m-d H:i:s');

        // Get real IP from X-Forwarded-For header (for Heroku/proxy) or fallback to request IP
        $realIp = $this->getRealIpAddress();

        if ($device === null) {
            $id = $deviceModel->insert([
                'sn'           => $sn !== '' ? $sn : ('UNKNOWN-' . bin2hex(random_bytes(4))),
                'name'         => $deviceName !== '' ? $deviceName : null,
                'ip_address'   => $realIp,
                'location'     => $deviceLocation !== '' ? $deviceLocation : null,
                'status'       => 'online',
                'last_seen_at' => $now,
            ], true);

            $device = $deviceModel->find($id);
        } else {
            $payload = [
                'status'       => 'online',
                'last_seen_at' => $now,
            ];

            // Only update IP if it changed and is not internal Heroku IP
            if (!str_starts_with($realIp, '10.') && $realIp !== $device['ip_address']) {
                $payload['ip_address'] = $realIp;
            }

            if ($deviceName !== '' && ($device['name'] === null || $device['name'] === '')) {
                $payload['name'] = $deviceName;
            }
            if ($deviceLocation !== '' && ($device['location'] === null || $device['location'] === '')) {
                $payload['location'] = $deviceLocation;
            }

            $deviceModel->update($device['id'], $payload);
            $device = $deviceModel->find($device['id']);
        }

        return $device ?? [];
    }

    private function plainOk(): ResponseInterface
    {
        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'text/plain')
            ->setBody('OK');
    }

    /**
     * Get real IP address from X-Forwarded-For header (for Heroku/proxy)
     * Falls back to request IP if header not present
     */
    private function getRealIpAddress(): string
    {
        // Check X-Forwarded-For header (set by Heroku/proxies)
        $forwardedFor = $this->request->getHeaderLine('X-Forwarded-For');
        if ($forwardedFor !== '') {
            // X-Forwarded-For can contain multiple IPs: client, proxy1, proxy2...
            // The first IP is the original client
            $ips = array_map('trim', explode(',', $forwardedFor));
            if (!empty($ips[0]) && filter_var($ips[0], FILTER_VALIDATE_IP)) {
                return $ips[0];
            }
        }

        // Fallback to request IP
        return $this->request->getIPAddress();
    }

    private function ingestAttlog(int $deviceId, string $rawBody): int
    {
        log_message('info', '[ICLOCK] ingestAttlog called. DeviceID=' . $deviceId);
        log_message('info', '[ICLOCK] Raw body length: ' . strlen($rawBody));

        if ($deviceId <= 0) {
            log_message('error', '[ICLOCK] Invalid device ID: ' . $deviceId);
            return 0;
        }

        $rawBody = str_replace("\r\n", "\n", $rawBody);
        $lines   = array_values(array_filter(array_map('trim', explode("\n", $rawBody)), static fn($l) => $l !== ''));

        log_message('info', '[ICLOCK] Number of lines: ' . count($lines));

        $attendanceService = new AttendanceService();
        $inserted = 0;

        foreach ($lines as $index => $line) {
            log_message('info', '[ICLOCK] Processing line ' . $index . ': ' . $line);

            $parsed = $this->parseAttlogLine($line);
            if ($parsed === null) {
                log_message('warning', '[ICLOCK] Failed to parse line: ' . $line);
                continue;
            }

            log_message('info', '[ICLOCK] Parsed: ' . json_encode($parsed));

            $pin     = $parsed['pin'];
            $attTime = $parsed['att_time'];
            $status  = $parsed['status'] ?? 0;
            $work    = $parsed['work_code'] ?? 0;

            try {
                // Use AttendanceService to process log
                $result = $attendanceService->processAttendanceLog(
                    $deviceId,
                    $pin,
                    $attTime,
                    $status,
                    $work,
                    $line
                );

                log_message('info', '[ICLOCK] Service result: ' . json_encode($result));

                if ($result['success']) {
                    $inserted++;
                }
            } catch (Throwable $e) {
                // Likely duplicate due to unique key; ignore.
                log_message('error', '[ICLOCK] Exception: ' . $e->getMessage());
                continue;
            }
        }

        log_message('info', '[ICLOCK] Total inserted: ' . $inserted);
        return $inserted;
    }

    /**
     * Attempt to parse one ATTLOG line in several common iClock formats.
     *
     * @return array{pin: string, att_time: string, status: int|null, work_code: int|null}|null
     */
    private function parseAttlogLine(string $line): ?array
    {
        $line = trim($line);
        if ($line === '') {
            return null;
        }

        // 1) Tab-separated: PIN\tYYYY-mm-dd HH:ii:ss\tStatus\tWorkCode
        $parts = preg_split('/\t+/', $line);
        if (is_array($parts) && count($parts) >= 2) {
            return $this->buildAttlogFromParts($parts);
        }

        // 2) Comma-separated
        $parts = array_map('trim', explode(',', $line));
        if (count($parts) >= 2) {
            return $this->buildAttlogFromParts($parts);
        }

        // 3) Space-separated (first token PIN, next two tokens datetime)
        $parts = preg_split('/\s+/', $line);
        if (is_array($parts) && count($parts) >= 3) {
            $pin  = (string) ($parts[0] ?? '');
            $dt   = trim((string) ($parts[1] ?? '') . ' ' . (string) ($parts[2] ?? ''));
            $rest = array_slice($parts, 3);
            $status = isset($rest[0]) && $rest[0] !== '' ? (int) $rest[0] : null;
            $work   = isset($rest[1]) && $rest[1] !== '' ? (int) $rest[1] : null;

            $attTime = $this->normalizeDatetime($dt);
            if ($pin !== '' && $attTime !== null) {
                return ['pin' => $pin, 'att_time' => $attTime, 'status' => $status, 'work_code' => $work];
            }
        }

        // 4) Key-value-ish (best-effort): e.g. PIN=123\tTime=2026-02-06 07:10:00
        if (str_contains($line, 'PIN=') || str_contains($line, 'Pin=')) {
            $pin = null;
            $dt  = null;
            foreach (preg_split('/[\t;]+/', $line) ?: [] as $chunk) {
                $chunk = trim($chunk);
                if ($chunk === '') {
                    continue;
                }
                if (stripos($chunk, 'PIN=') === 0) {
                    $pin = trim(substr($chunk, 4));
                }
                if (stripos($chunk, 'Time=') === 0) {
                    $dt = trim(substr($chunk, 5));
                }
            }

            $attTime = $dt !== null ? $this->normalizeDatetime($dt) : null;
            if ($pin !== null && $pin !== '' && $attTime !== null) {
                return ['pin' => $pin, 'att_time' => $attTime, 'status' => null, 'work_code' => null];
            }
        }

        return null;
    }

    /**
     * @param list<string|null> $parts
     * @return array{pin: string, att_time: string, status: int|null, work_code: int|null}|null
     */
    private function buildAttlogFromParts(array $parts): ?array
    {
        $pin     = trim((string) ($parts[0] ?? ''));
        $dateStr = trim((string) ($parts[1] ?? ''));
        $status  = isset($parts[2]) && $parts[2] !== '' ? (int) $parts[2] : null;
        $work    = isset($parts[3]) && $parts[3] !== '' ? (int) $parts[3] : null;

        if ($pin === '' || $dateStr === '') {
            return null;
        }

        $attTime = $this->normalizeDatetime($dateStr);
        if ($attTime === null) {
            return null;
        }

        return ['pin' => $pin, 'att_time' => $attTime, 'status' => $status, 'work_code' => $work];
    }

    private function normalizeDatetime(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // First try strict parse.
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if ($dt instanceof DateTime) {
            return $dt->format('Y-m-d H:i:s');
        }

        // Fallback to strtotime.
        $ts = strtotime($value);
        if ($ts === false) {
            return null;
        }

        // Reject nonsense epoch.
        if ($ts < 946684800) { // 2000-01-01
            return null;
        }

        return date('Y-m-d H:i:s', $ts);
    }
}
