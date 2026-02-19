<?php

namespace App\Libraries;

/**
 * WhatsApp Notification Service via Fonnte API
 *
 * Requires environment variable:
 *   FONNTE_TOKEN = <your fonnte api token>
 *
 * Set on Heroku:
 *   heroku config:set FONNTE_TOKEN=xxxxxxxxxxxxxxxx
 */
class WhatsAppService
{
    protected string $apiUrl  = 'https://api.fonnte.com/send';
    protected string $token;

    public function __construct()
    {
        $this->token = (string) env('FONNTE_TOKEN', '');
    }

    /**
     * Returns true if the service is configured (token is set).
     */
    public function isConfigured(): bool
    {
        return $this->token !== '';
    }

    /**
     * Send a WhatsApp message to a phone number.
     *
     * @param string $phone   Target phone number (format: 08xx or 628xx)
     * @param string $message Message text
     * @return array{success: bool, message: string}
     */
    public function send(string $phone, string $message): array
    {
        if (! $this->isConfigured()) {
            log_message('warning', '[WhatsApp] FONNTE_TOKEN not set. Skipping WA notification.');
            return ['success' => false, 'message' => 'FONNTE_TOKEN not configured'];
        }

        $phone = $this->normalizePhone($phone);
        if ($phone === '') {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }

        try {
            $ch = curl_init($this->apiUrl);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: ' . $this->token,
                ],
                CURLOPT_POSTFIELDS     => http_build_query([
                    'target'      => $phone,
                    'message'     => $message,
                    'countryCode' => '62',
                ]),
                CURLOPT_TIMEOUT        => 15,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError !== '') {
                log_message('error', '[WhatsApp] cURL error: ' . $curlError);
                return ['success' => false, 'message' => 'cURL error: ' . $curlError];
            }

            $decoded = json_decode((string) $response, true);
            $status  = $decoded['status'] ?? false;

            if ($httpCode === 200 && $status) {
                log_message('info', '[WhatsApp] Sent to ' . $phone . ' | Resp: ' . $response);
                return ['success' => true, 'message' => 'Sent'];
            }

            log_message('warning', '[WhatsApp] Failed to send. HTTP ' . $httpCode . ' | Resp: ' . $response);
            return ['success' => false, 'message' => 'API error: ' . $response];
        } catch (\Throwable $e) {
            log_message('error', '[WhatsApp] Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Normalize phone number to Indonesian format starting with 08 or 628.
     * Fonnte handles country code conversion automatically with countryCode=62.
     */
    protected function normalizePhone(string $phone): string
    {
        // Strip non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        if ($phone === '' || strlen($phone) < 8) {
            return '';
        }

        // Remove leading 62 → keep as-is (Fonnte handles it)
        // Ensure it starts with 0 for local format or 62 for international
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        return $phone;
    }
}
