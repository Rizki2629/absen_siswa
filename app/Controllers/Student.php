<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\NotificationModel;

class Student extends BaseController
{
    protected $studentModel;
    protected $attendanceSummaryModel;
    protected $attendanceLogModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->attendanceSummaryModel = new AttendanceSummaryModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->notificationModel = new NotificationModel();
    }

    public function dashboard()
    {
        // Check if user is student or parent
        $role = session()->get('role');
        if (!in_array($role, ['student', 'parent'])) {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        // Get student data
        $userId = session()->get('user_id');
        $user = model('UserModel')->find($userId);
        $student = $this->studentModel->find($user['student_id']);

        // Get attendance statistics for current month
        $currentMonth = date('Y-m');
        $stats = [
            'total_days' => 20, // Simplified
            'present' => $this->attendanceSummaryModel
                ->where('student_id', $student['id'])
                ->where('date >=', $currentMonth . '-01')
                ->whereIn('status', ['hadir', 'terlambat'])
                ->countAllResults(),
            'late' => $this->attendanceSummaryModel
                ->where('student_id', $student['id'])
                ->where('date >=', $currentMonth . '-01')
                ->where('status', 'terlambat')
                ->countAllResults(),
            'sick' => $this->attendanceSummaryModel
                ->where('student_id', $student['id'])
                ->where('date >=', $currentMonth . '-01')
                ->where('status', 'sakit')
                ->countAllResults(),
            'absent' => $this->attendanceSummaryModel
                ->where('student_id', $student['id'])
                ->where('date >=', $currentMonth . '-01')
                ->where('status', 'alpha')
                ->countAllResults(),
        ];

        // Get today's attendance
        $today = date('Y-m-d');
        $todayAttendance = $this->attendanceSummaryModel
            ->where('student_id', $student['id'])
            ->where('date', $today)
            ->first();

        $data = [
            'title' => 'Dashboard Siswa',
            'pageTitle' => 'Dashboard Siswa',
            'pageDescription' => 'Lihat riwayat kehadiran Anda',
            'activePage' => 'student/dashboard',
            'user' => [
                'name' => session()->get('name'),
                'role' => $role === 'student' ? 'Siswa' : 'Orang Tua'
            ],
            'student' => [
                'name' => $student['name'],
                'nis' => $student['nis'],
                'class' => $student['class_name'] ?? 'Kelas',
                'major' => $student['major'] ?? 'Jurusan',
            ],
            'stats' => $stats,
            'todayAttendance' => $todayAttendance ? [
                'status' => $todayAttendance['status'],
                'check_in' => $todayAttendance['check_in_time'],
                'check_out' => $todayAttendance['check_out_time'],
            ] : null,
            'unreadNotifications' => $this->notificationModel
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->countAllResults(),
        ];

        return view('dashboard/student', $data);
    }

    public function attendance()
    {
        // Attendance history page
        $data = [
            'title' => 'Riwayat Kehadiran',
            'pageTitle' => 'Riwayat Kehadiran',
            'pageDescription' => 'Lihat detail kehadiran Anda',
            'activePage' => 'student/attendance',
            'user' => [
                'name' => session()->get('name'),
                'role' => session()->get('role') === 'student' ? 'Siswa' : 'Orang Tua'
            ],
        ];

        return view('student/attendance', $data);
    }

    public function notifications()
    {
        // Notifications page
        $data = [
            'title' => 'Notifikasi',
            'pageTitle' => 'Notifikasi',
            'pageDescription' => 'Lihat semua notifikasi Anda',
            'activePage' => 'student/notifications',
            'user' => [
                'name' => session()->get('name'),
                'role' => session()->get('role') === 'student' ? 'Siswa' : 'Orang Tua'
            ],
        ];

        return view('student/notifications', $data);
    }

    public function profile()
    {
        // Profile page
        $data = [
            'title' => 'Profil',
            'pageTitle' => 'Profil Saya',
            'pageDescription' => 'Informasi profil Anda',
            'activePage' => 'student/profile',
            'user' => [
                'name' => session()->get('name'),
                'role' => session()->get('role') === 'student' ? 'Siswa' : 'Orang Tua'
            ],
        ];

        return view('student/profile', $data);
    }
}
