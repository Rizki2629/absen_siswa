<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\NotificationModel;
use App\Models\StudentModel;
use CodeIgniter\HTTP\ResponseInterface;

class StudentController extends BaseController
{
    protected $summaryModel;
    protected $logModel;
    protected $notificationModel;
    protected $studentModel;

    public function __construct()
    {
        $this->summaryModel = new AttendanceSummaryModel();
        $this->logModel = new AttendanceLogModel();
        $this->notificationModel = new NotificationModel();
        $this->studentModel = new StudentModel();
    }

    /**
     * Get student attendance logs
     */
    public function getAttendanceLogs()
    {
        $session = session();
        $userId = $session->get('user_id');
        $role = $session->get('role');

        // Get student_id based on role
        $studentId = $this->request->getGet('student_id');

        if ($role === 'siswa' || $role === 'orang_tua') {
            // For siswa and orang_tua, get their linked student_id
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
            $studentId = $user['student_id'] ?? null;
        }

        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $logs = $this->logModel
            ->where('student_id', $studentId)
            ->where('att_time >=', $startDate . ' 00:00:00')
            ->where('att_time <=', $endDate . ' 23:59:59')
            ->orderBy('att_time', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $logs,
        ]);
    }

    /**
     * Get student attendance summary
     */
    public function getAttendanceSummary()
    {
        $session = session();
        $userId = $session->get('user_id');
        $role = $session->get('role');

        // Get student_id based on role
        $studentId = $this->request->getGet('student_id');

        if ($role === 'siswa' || $role === 'orang_tua') {
            // For siswa and orang_tua, get their linked student_id
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
            $studentId = $user['student_id'] ?? null;
        }

        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $summary = $this->summaryModel->getStudentSummary($studentId, $startDate, $endDate);
        $statistics = $this->summaryModel->getStatistics($studentId, $startDate, $endDate);

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'summary'    => $summary,
                'statistics' => $statistics,
            ],
        ]);
    }

    /**
     * Get today's attendance
     */
    public function getTodayAttendance()
    {
        $session = session();
        $userId = $session->get('user_id');
        $role = $session->get('role');

        // Get student_id based on role
        $studentId = $this->request->getGet('student_id');

        if ($role === 'siswa' || $role === 'orang_tua') {
            // For siswa and orang_tua, get their linked student_id
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
            $studentId = $user['student_id'] ?? null;
        }

        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $date = date('Y-m-d');

        $summary = $this->summaryModel
            ->where('student_id', $studentId)
            ->where('date', $date)
            ->first();

        $logs = $this->logModel
            ->where('student_id', $studentId)
            ->where('DATE(att_time)', $date)
            ->orderBy('att_time', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'summary' => $summary,
                'logs'    => $logs,
            ],
        ]);
    }

    /**
     * Get notifications
     */
    public function getNotifications()
    {
        $session = session();
        $userId = $session->get('user_id');

        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;

        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);

        $unreadCount = $this->notificationModel
            ->where('user_id', $userId)
            ->where('read_at IS NULL')
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'notifications' => $notifications,
                'unread_count'  => $unreadCount,
                'pagination'    => $this->notificationModel->pager->getDetails(),
            ],
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $session = session();
        $userId = $session->get('user_id');

        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found',
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($this->notificationModel->markAsRead($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to mark notification as read',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $session = session();
        $userId = $session->get('user_id');

        if ($this->notificationModel->markAllAsRead($userId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to mark notifications as read',
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Get student profile
     */
    public function getProfile()
    {
        $session = session();
        $userId = $session->get('user_id');
        $role = $session->get('role');

        if ($role === 'siswa' || $role === 'orang_tua') {
            $db = \Config\Database::connect();
            $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
            $studentId = $user['student_id'] ?? null;

            if (!$studentId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student not found',
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            $student = $this->studentModel
                ->select('students.*, classes.name as class_name')
                ->join('classes', 'classes.id = students.class_id')
                ->find($studentId);

            if (!$student) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student not found',
                ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
            }

            return $this->response->setJSON([
                'success' => true,
                'data'    => $student,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Unauthorized',
        ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
    }
}
