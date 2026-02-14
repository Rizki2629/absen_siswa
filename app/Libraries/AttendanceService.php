<?php

namespace App\Libraries;

use App\Models\AttendanceLogModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceExceptionModel;
use App\Models\ShiftModel;
use App\Models\NotificationModel;
use App\Models\DeviceUserMapModel;

class AttendanceService
{
    protected $logModel;
    protected $summaryModel;
    protected $exceptionModel;
    protected $shiftModel;
    protected $notificationModel;
    protected $deviceUserMapModel;

    public function __construct()
    {
        $this->logModel = new AttendanceLogModel();
        $this->summaryModel = new AttendanceSummaryModel();
        $this->exceptionModel = new AttendanceExceptionModel();
        $this->shiftModel = new ShiftModel();
        $this->notificationModel = new NotificationModel();
        $this->deviceUserMapModel = new DeviceUserMapModel();
    }

    /**
     * Process attendance log from device
     * Handles double scan filtering and attendance summary
     */
    public function processAttendanceLog($deviceId, $pin, $attTime, $status = 0, $workCode = 0, $raw = null)
    {
        // Get student ID from device user mapping
        $mapping = $this->deviceUserMapModel
            ->where('device_id', $deviceId)
            ->where('device_user_id', $pin)
            ->first();

        $studentId = $mapping ? $mapping['student_id'] : null;

        // Insert raw log
        $logData = [
            'device_id'  => $deviceId,
            'student_id' => $studentId,
            'pin'        => $pin,
            'att_time'   => $attTime,
            'status'     => $status,
            'work_code'  => $workCode,
            'raw'        => $raw,
        ];

        // Check if this exact log already exists (prevent duplicates)
        $existing = $this->logModel
            ->where('device_id', $deviceId)
            ->where('pin', $pin)
            ->where('att_time', $attTime)
            ->where('status', $status)
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Duplicate log',
                'log_id'  => $existing['id'],
            ];
        }

        $logId = $this->logModel->insert($logData);

        if (!$logId) {
            return [
                'success' => false,
                'message' => 'Failed to insert log',
            ];
        }

        // If student is mapped, process attendance summary
        if ($studentId) {
            $this->processAttendanceSummary($studentId, date('Y-m-d', strtotime($attTime)));
            
            // Create notification for parent
            $this->notificationModel->createCheckInNotification($studentId, $attTime);
        }

        return [
            'success'    => true,
            'message'    => 'Log processed successfully',
            'log_id'     => $logId,
            'student_id' => $studentId,
        ];
    }

    /**
     * Process attendance summary for a student on a specific date
     * Implements double scan filtering (first scan = check in, last scan = check out)
     */
    public function processAttendanceSummary($studentId, $date)
    {
        // Check if there's an exception for this date
        $exception = $this->exceptionModel
            ->where('student_id', $studentId)
            ->where('date', $date)
            ->first();

        if ($exception) {
            // Exception exists, update summary based on exception
            $summaryData = [
                'student_id' => $studentId,
                'date'       => $date,
                'status'     => $exception['exception_type'],
                'notes'      => $exception['notes'],
            ];

            if ($exception['exception_type'] === 'lupa_scan') {
                $summaryData['status'] = 'hadir';
                $summaryData['check_in_time'] = $exception['check_in_time'] ? 
                    $date . ' ' . $exception['check_in_time'] : null;
                $summaryData['check_out_time'] = $exception['check_out_time'] ? 
                    $date . ' ' . $exception['check_out_time'] : null;
            }

            $this->updateOrCreateSummary($studentId, $date, $summaryData);
            return;
        }

        // Get all logs for this student on this date
        $logs = $this->logModel
            ->where('student_id', $studentId)
            ->where('DATE(att_time)', $date)
            ->orderBy('att_time', 'ASC')
            ->findAll();

        if (empty($logs)) {
            // No logs, mark as alpha
            $this->updateOrCreateSummary($studentId, $date, [
                'student_id'     => $studentId,
                'date'           => $date,
                'status'         => 'alpha',
                'check_in_time'  => null,
                'check_out_time' => null,
            ]);
            return;
        }

        // Apply double scan filtering
        // First scan = check in, Last scan = check out
        $checkInLog = $logs[0];
        $checkOutLog = count($logs) > 1 ? end($logs) : null;

        // Get shift for this student's class (or fallback to active shift)
        $shift = null;
        $db = \Config\Database::connect();
        $studentClass = $db->table('students')
            ->select('classes.shift_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.id', $studentId)
            ->get()
            ->getRowArray();
        
        if ($studentClass && !empty($studentClass['shift_id'])) {
            $shift = $this->shiftModel->find($studentClass['shift_id']);
        }
        
        // Fallback to any active shift
        if (!$shift) {
            $shift = $this->shiftModel->getActiveShift();
        }

        $isLate = false;
        $lateMinutes = 0;

        if ($shift) {
            $checkInTime = strtotime(date('H:i:s', strtotime($checkInLog['att_time'])));
            $expectedTime = strtotime($shift['check_in_end']);
            $tolerance = $shift['late_tolerance'] * 60; // Convert minutes to seconds
            
            if ($checkInTime > ($expectedTime + $tolerance)) {
                $isLate = true;
                $lateMinutes = floor(($checkInTime - $expectedTime) / 60);
            }
        }

        $summaryData = [
            'student_id'     => $studentId,
            'date'           => $date,
            'check_in_time'  => $checkInLog['att_time'],
            'check_out_time' => $checkOutLog ? $checkOutLog['att_time'] : null,
            'status'         => $isLate ? 'terlambat' : 'hadir',
            'is_late'        => $isLate ? 1 : 0,
            'late_minutes'   => $lateMinutes,
        ];

        $this->updateOrCreateSummary($studentId, $date, $summaryData);
    }

    /**
     * Update or create attendance summary
     */
    protected function updateOrCreateSummary($studentId, $date, $data)
    {
        $summary = $this->summaryModel
            ->where('student_id', $studentId)
            ->where('date', $date)
            ->first();

        if ($summary) {
            $this->summaryModel->update($summary['id'], $data);
        } else {
            $this->summaryModel->insert($data);
        }
    }

    /**
     * Process batch attendance logs
     * Used when pulling data from device manually
     */
    public function processBatchLogs($logs)
    {
        $results = [
            'success' => 0,
            'failed'  => 0,
            'errors'  => [],
        ];

        foreach ($logs as $log) {
            try {
                $result = $this->processAttendanceLog(
                    $log['device_id'],
                    $log['pin'],
                    $log['att_time'],
                    $log['status'] ?? 0,
                    $log['work_code'] ?? 0,
                    $log['raw'] ?? null
                );

                if ($result['success']) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = $result['message'];
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Generate daily attendance summary for all students
     * Should be run at end of day or start of next day
     */
    public function generateDailySummary($date)
    {
        $db = \Config\Database::connect();
        
        // Get all active students
        $students = $db->table('students')
            ->where('active', 1)
            ->get()
            ->getResultArray();

        $processed = 0;

        foreach ($students as $student) {
            $this->processAttendanceSummary($student['id'], $date);
            $processed++;
        }

        return [
            'success'   => true,
            'date'      => $date,
            'processed' => $processed,
        ];
    }

    /**
     * Filter double scans from raw logs
     * Returns first scan (check in) and last scan (check out) for each student
     */
    public function filterDoubleScan($logs)
    {
        $filtered = [];
        
        // Group logs by student and date
        $grouped = [];
        foreach ($logs as $log) {
            $date = date('Y-m-d', strtotime($log['att_time']));
            $key = $log['student_id'] . '_' . $date;
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            
            $grouped[$key][] = $log;
        }

        // For each group, get first and last scan
        foreach ($grouped as $key => $group) {
            usort($group, function($a, $b) {
                return strtotime($a['att_time']) - strtotime($b['att_time']);
            });

            $filtered[] = [
                'check_in'  => $group[0],
                'check_out' => count($group) > 1 ? end($group) : null,
            ];
        }

        return $filtered;
    }
}
