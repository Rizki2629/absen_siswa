<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AttendanceExceptionModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\StudentModel;
use App\Models\ShiftModel;
use CodeIgniter\HTTP\ResponseInterface;

class GuruPiketController extends BaseController
{
    protected $exceptionModel;
    protected $summaryModel;
    protected $logModel;
    protected $studentModel;
    protected $shiftModel;

    public function __construct()
    {
        $this->exceptionModel = new AttendanceExceptionModel();
        $this->summaryModel = new AttendanceSummaryModel();
        $this->logModel = new AttendanceLogModel();
        $this->studentModel = new StudentModel();
        $this->shiftModel = new ShiftModel();
    }

    /**
     * Get daily attendance summary
     */
    public function getDailySummary()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $summary = $this->summaryModel->getDailySummary($date);
        $notCheckedIn = $this->summaryModel->getNotCheckedIn($date);

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'summary'        => $summary,
                'not_checked_in' => $notCheckedIn,
                'date'           => $date,
            ],
        ]);
    }

    /**
     * Get students who haven't checked in
     */
    public function getNotCheckedIn()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $students = $this->summaryModel->getNotCheckedIn($date);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $students,
        ]);
    }

    /**
     * Record exception (Sakit, Izin, Lupa Scan)
     */
    public function recordException()
    {
        $rules = [
            'student_id'     => 'required|integer',
            'date'           => 'required|valid_date',
            'exception_type' => 'required|in_list[sakit,izin,lupa_scan]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $this->validator->getErrors(),
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $session = session();
        
        $data = [
            'student_id'     => $this->request->getPost('student_id'),
            'date'           => $this->request->getPost('date'),
            'exception_type' => $this->request->getPost('exception_type'),
            'check_in_time'  => $this->request->getPost('check_in_time'),
            'check_out_time' => $this->request->getPost('check_out_time'),
            'notes'          => $this->request->getPost('notes'),
            'created_by'     => $session->get('user_id'),
        ];

        // Handle file upload for proof
        $file = $this->request->getFile('proof_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/proofs', $newName);
            $data['proof_file'] = $newName;
        }

        // Check if exception already exists
        $existing = $this->exceptionModel
            ->where('student_id', $data['student_id'])
            ->where('date', $data['date'])
            ->first();

        if ($existing) {
            // Update existing exception
            $this->exceptionModel->update($existing['id'], $data);
            $exceptionId = $existing['id'];
        } else {
            // Create new exception
            $exceptionId = $this->exceptionModel->insert($data);
        }

        // Update or create attendance summary
        $summary = $this->summaryModel
            ->where('student_id', $data['student_id'])
            ->where('date', $data['date'])
            ->first();

        $summaryData = [
            'student_id' => $data['student_id'],
            'date'       => $data['date'],
            'status'     => $data['exception_type'],
            'notes'      => $data['notes'],
        ];

        // For lupa_scan, mark as hadir
        if ($data['exception_type'] === 'lupa_scan') {
            $summaryData['status'] = 'hadir';
            $summaryData['check_in_time'] = $data['check_in_time'] ? 
                $data['date'] . ' ' . $data['check_in_time'] : null;
            $summaryData['check_out_time'] = $data['check_out_time'] ? 
                $data['date'] . ' ' . $data['check_out_time'] : null;
        }

        if ($summary) {
            $this->summaryModel->update($summary['id'], $summaryData);
        } else {
            $this->summaryModel->insert($summaryData);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Exception recorded successfully',
            'data'    => $this->exceptionModel->find($exceptionId),
        ]);
    }

    /**
     * Get exceptions for a date
     */
    public function getExceptions()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $exceptions = $this->exceptionModel->getExceptionsForDate($date);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $exceptions,
        ]);
    }

    /**
     * Delete exception
     */
    public function deleteException($id)
    {
        $exception = $this->exceptionModel->find($id);
        
        if (!$exception) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exception not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($this->exceptionModel->delete($id)) {
            // Recalculate attendance summary
            $this->recalculateAttendanceSummary(
                $exception['student_id'],
                $exception['date']
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exception deleted successfully',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete exception',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Get recent attendance logs (real-time monitoring)
     */
    public function getRecentLogs()
    {
        $limit = $this->request->getGet('limit') ?? 50;
        $date = date('Y-m-d');

        $logs = $this->logModel
            ->select('attendance_logs.*, students.name, students.nis, devices.name as device_name')
            ->join('students', 'students.id = attendance_logs.student_id', 'left')
            ->join('devices', 'devices.id = attendance_logs.device_id')
            ->where('DATE(attendance_logs.att_time)', $date)
            ->orderBy('attendance_logs.att_time', 'DESC')
            ->limit($limit)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $logs,
        ]);
    }

    /**
     * Recalculate attendance summary
     */
    protected function recalculateAttendanceSummary($studentId, $date)
    {
        // Get all logs for this student on this date
        $logs = $this->logModel
            ->where('student_id', $studentId)
            ->where('DATE(att_time)', $date)
            ->orderBy('att_time', 'ASC')
            ->findAll();

        if (empty($logs)) {
            // No logs, check for exception
            $exception = $this->exceptionModel
                ->where('student_id', $studentId)
                ->where('date', $date)
                ->first();

            if ($exception) {
                // Keep exception status
                return;
            }

            // Mark as alpha
            $summary = $this->summaryModel
                ->where('student_id', $studentId)
                ->where('date', $date)
                ->first();

            if ($summary) {
                $this->summaryModel->update($summary['id'], [
                    'status' => 'alpha',
                    'check_in_time' => null,
                    'check_out_time' => null,
                ]);
            } else {
                $this->summaryModel->insert([
                    'student_id' => $studentId,
                    'date' => $date,
                    'status' => 'alpha',
                ]);
            }

            return;
        }

        // Get first (check in) and last (check out) log
        $checkIn = $logs[0];
        $checkOut = end($logs);

        // Get active shift
        $shift = $this->shiftModel->getActiveShift();

        $isLate = false;
        $lateMinutes = 0;

        if ($shift) {
            $checkInTime = strtotime(date('H:i:s', strtotime($checkIn['att_time'])));
            $expectedTime = strtotime($shift['check_in_end']);
            
            if ($checkInTime > $expectedTime) {
                $isLate = true;
                $lateMinutes = floor(($checkInTime - $expectedTime) / 60);
            }
        }

        $summaryData = [
            'student_id' => $studentId,
            'date' => $date,
            'check_in_time' => $checkIn['att_time'],
            'check_out_time' => count($logs) > 1 ? $checkOut['att_time'] : null,
            'status' => $isLate ? 'terlambat' : 'hadir',
            'is_late' => $isLate ? 1 : 0,
            'late_minutes' => $lateMinutes,
        ];

        $summary = $this->summaryModel
            ->where('student_id', $studentId)
            ->where('date', $date)
            ->first();

        if ($summary) {
            $this->summaryModel->update($summary['id'], $summaryData);
        } else {
            $this->summaryModel->insert($summaryData);
        }
    }
}
