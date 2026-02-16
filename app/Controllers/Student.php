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
        if (!in_array($role, ['student', 'siswa', 'parent'])) {
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
                'role' => in_array($role, ['student', 'siswa']) ? 'Siswa' : 'Orang Tua'
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
                ->where('read_at IS NULL')
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
                'role' => in_array(session()->get('role'), ['student', 'siswa']) ? 'Siswa' : 'Orang Tua'
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
                'role' => in_array(session()->get('role'), ['student', 'siswa']) ? 'Siswa' : 'Orang Tua'
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
                'role' => in_array(session()->get('role'), ['student', 'siswa']) ? 'Siswa' : 'Orang Tua'
            ],
        ];

        return view('student/profile', $data);
    }

    public function habits()
    {
        // 7 Kebiasaan page
        $role = session()->get('role');
        if (!in_array($role, ['student', 'siswa', 'parent'])) {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        $data = [
            'title' => '7 Kebiasaan Pelajar Hebat',
            'pageTitle' => '7 Kebiasaan',
            'pageDescription' => 'Lengkapi kebiasaan baik Anda hari ini',
            'activePage' => 'student/habits',
            'user' => [
                'name' => session()->get('name'),
                'role' => in_array($role, ['student', 'siswa']) ? 'Siswa' : 'Orang Tua'
            ],
        ];

        return view('student/habits', $data);
    }

    // API Methods
    public function apiGetTodayHabits()
    {
        $userId = session()->get('user_id');
        $user = model('UserModel')->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $today = date('Y-m-d');
        $habitModel = model('StudentHabitModel');

        $habit = $habitModel->where('student_id', $user['student_id'])
            ->where('date', $today)
            ->first();

        if (!$habit) {
            // Create today's record with all habits false
            $habit = [
                'student_id' => $user['student_id'],
                'date' => $today,
                'bangun_pagi' => 0,
                'beribadah' => 0,
                'berolahraga' => 0,
                'makan_sehat' => 0,
                'gemar_belajar' => 0,
                'bermasyarakat' => 0,
                'tidur_cepat' => 0,
            ];
            $habitModel->insert($habit);
            $habit['id'] = $habitModel->getInsertID();
        }

        // Calculate streak
        $streak = $this->calculateStreak($user['student_id']);

        // Calculate XP (20 XP per completed habit)
        $completed = 0;
        $habitFields = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];
        foreach ($habitFields as $field) {
            if ($habit[$field]) $completed++;
        }
        $xp = $completed * 20;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'habit' => $habit,
                'stats' => [
                    'completed' => $completed,
                    'total' => 7,
                    'xp' => $xp,
                    'streak' => $streak
                ]
            ]
        ]);
    }

    public function apiToggleHabit()
    {
        $userId = session()->get('user_id');
        $user = model('UserModel')->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $habitName = $this->request->getJSON()->habit;
        $today = date('Y-m-d');
        $habitModel = model('StudentHabitModel');

        $habit = $habitModel->where('student_id', $user['student_id'])
            ->where('date', $today)
            ->first();

        if (!$habit) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Habit record not found'
            ]);
        }

        // Toggle the habit
        $validHabits = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];

        if (!in_array($habitName, $validHabits)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid habit name'
            ]);
        }

        $newValue = !$habit[$habitName];
        $habitModel->update($habit['id'], [$habitName => $newValue]);

        // Get updated habit
        $habit = $habitModel->find($habit['id']);

        // Calculate completed
        $completed = 0;
        foreach ($validHabits as $field) {
            if ($habit[$field]) $completed++;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'habit' => $habit,
                'completed' => $completed,
                'xp' => $completed * 20
            ]
        ]);
    }

    public function apiGetHabitsStats()
    {
        $userId = session()->get('user_id');
        $user = model('UserModel')->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $habitModel = model('StudentHabitModel');

        // Get this month's stats
        $thisMonth = date('Y-m');
        $habits = $habitModel->where('student_id', $user['student_id'])
            ->where('date >=', $thisMonth . '-01')
            ->where('date <=', date('Y-m-t'))
            ->findAll();

        $totalDays = count($habits);
        $totalCompleted = 0;

        foreach ($habits as $habit) {
            $completed = 0;
            if ($habit['bangun_pagi']) $completed++;
            if ($habit['beribadah']) $completed++;
            if ($habit['berolahraga']) $completed++;
            if ($habit['makan_sehat']) $completed++;
            if ($habit['gemar_belajar']) $completed++;
            if ($habit['bermasyarakat']) $completed++;
            if ($habit['tidur_cepat']) $completed++;

            if ($completed === 7) $totalCompleted++;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'totalDays' => $totalDays,
                'perfectDays' => $totalCompleted,
                'streak' => $this->calculateStreak($user['student_id'])
            ]
        ]);
    }

    private function calculateStreak($studentId)
    {
        $habitModel = model('StudentHabitModel');
        $habits = $habitModel->where('student_id', $studentId)
            ->where('date <=', date('Y-m-d'))
            ->orderBy('date', 'DESC')
            ->findAll(30); // Check last 30 days

        $streak = 0;
        $expectedDate = date('Y-m-d');

        foreach ($habits as $habit) {
            if ($habit['date'] !== $expectedDate) {
                break;
            }

            // Check if all habits completed
            $completed = $habit['bangun_pagi'] && $habit['beribadah'] &&
                $habit['berolahraga'] && $habit['makan_sehat'] &&
                $habit['gemar_belajar'] && $habit['bermasyarakat'] &&
                $habit['tidur_cepat'];

            if ($completed) {
                $streak++;
                // Move to previous day
                $expectedDate = date('Y-m-d', strtotime($expectedDate . ' -1 day'));
            } else {
                break;
            }
        }

        return $streak;
    }
}
