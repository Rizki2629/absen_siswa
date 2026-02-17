<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\NotificationModel;
use App\Models\StudentHabitModel;
use App\Models\UserModel;

class Student extends BaseController
{
    protected $studentModel;
    protected $attendanceSummaryModel;
    protected $attendanceLogModel;
    protected $notificationModel;
    protected $habitModel;
    protected $userModel;

    protected $habitFields = [
        'bangun_pagi',
        'beribadah',
        'berolahraga',
        'makan_sehat',
        'gemar_belajar',
        'bermasyarakat',
        'tidur_cepat',
    ];

    protected $habitLabels = [
        'bangun_pagi' => 'Bangun Pagi',
        'beribadah' => 'Beribadah',
        'berolahraga' => 'Olahraga',
        'makan_sehat' => 'Makan Bergizi',
        'gemar_belajar' => 'Gemar Belajar',
        'bermasyarakat' => 'Bermasyarakat',
        'tidur_cepat' => 'Tidur Cepat',
    ];

    protected $habitTimeWindows = [
        'bangun_pagi' => ['04:00', '06:00'],
        'beribadah' => ['04:00', '21:30'],
        'berolahraga' => ['15:00', '18:00'],
        'makan_sehat' => ['06:00', '19:00'],
        'gemar_belajar' => ['18:30', '21:00'],
        'bermasyarakat' => ['07:00', '20:00'],
        'tidur_cepat' => ['20:00', '21:30'],
    ];

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->attendanceSummaryModel = new AttendanceSummaryModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->notificationModel = new NotificationModel();
        $this->habitModel = new StudentHabitModel();
        $this->userModel = new UserModel();
    }

    public function dashboard()
    {
        // Check if user is student or parent
        $role = session()->get('role');
        if (!in_array($role, ['student', 'siswa', 'parent', 'orang_tua'])) {
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
        $role = session()->get('role');
        if (!in_array($role, ['student', 'siswa', 'parent', 'orang_tua'])) {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || empty($user['student_id'])) {
            return redirect()->to('/')->with('error', 'Data siswa tidak ditemukan');
        }

        $student = $this->studentModel->find($user['student_id']);
        if (!$student) {
            return redirect()->to('/')->with('error', 'Profil siswa tidak ditemukan');
        }

        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        $attendanceList = $this->attendanceSummaryModel
            ->where('student_id', $student['id'])
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->orderBy('date', 'DESC')
            ->findAll();

        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $statsRaw = $this->attendanceSummaryModel->getStatistics((int) $student['id'], $monthStart, $monthEnd);

        $stats = [
            'total_days' => (int) ($statsRaw['total_days'] ?? 0),
            'present' => (int) (($statsRaw['hadir'] ?? 0) + ($statsRaw['terlambat'] ?? 0)),
            'late' => (int) ($statsRaw['terlambat'] ?? 0),
            'sick' => (int) ($statsRaw['sakit'] ?? 0),
            'izin' => (int) ($statsRaw['izin'] ?? 0),
            'absent' => (int) ($statsRaw['alpha'] ?? 0),
        ];

        $data = [
            'title' => 'Riwayat Kehadiran',
            'pageTitle' => 'Riwayat Kehadiran',
            'pageDescription' => 'Lihat detail kehadiran Anda',
            'activePage' => 'student/attendance',
            'user' => [
                'name' => session()->get('name'),
                'role' => in_array($role, ['student', 'siswa']) ? 'Siswa' : 'Orang Tua'
            ],
            'student' => [
                'name' => $student['name'] ?? '-',
                'nis' => $student['nis'] ?? '-',
            ],
            'attendanceList' => $attendanceList,
            'stats' => $stats,
            'unreadNotifications' => $this->notificationModel
                ->where('user_id', $userId)
                ->where('read_at IS NULL')
                ->countAllResults(),
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
        if (!in_array($role, ['student', 'siswa', 'parent', 'orang_tua'])) {
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
        $user = $this->userModel->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $targetDate = $this->resolveTargetHabitDate($this->request->getGet('date'));
        if ($targetDate === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal tidak valid atau belum waktunya'
            ]);
        }

        $habit = $this->getOrCreateTodayHabit((int) $user['student_id'], $targetDate);

        $habitAnswers = $this->decodeHabitAnswers($habit['habit_answers'] ?? null);
        $streak = $this->calculateStreak((int) $user['student_id']);
        $dailyStats = $this->calculateDailyStats($habit, $streak);
        $badges = $this->calculateBadges((int) $user['student_id']);
        $weeklySummary = $this->calculateWeeklySummary((int) $user['student_id']);
        $activeReminders = $targetDate === date('Y-m-d') ? $this->getActiveHabitReminders($habit) : [];
        if ($targetDate === date('Y-m-d')) {
            $this->storeReminderNotifications((int) $userId, (int) $user['student_id'], $activeReminders);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'habit' => $habit,
                'answers' => $habitAnswers,
                'stats' => $dailyStats,
                'badges' => $badges,
                'weekly_summary' => $weeklySummary,
                'reminders' => $activeReminders,
                'selected_date' => $targetDate,
            ]
        ]);
    }

    public function apiToggleHabit()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $payload = $this->request->getJSON(true) ?? [];
        $habitName = $payload['habit'] ?? null;
        $answers = $payload['answers'] ?? null;
        $updateOnly = !empty($payload['update_only']);
        $targetDate = $this->resolveTargetHabitDate($payload['date'] ?? null);
        if ($targetDate === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal tidak valid atau belum waktunya'
            ]);
        }

        $habit = $this->getOrCreateTodayHabit((int) $user['student_id'], $targetDate);

        // Toggle the habit
        $validHabits = $this->habitFields;

        if (!in_array($habitName, $validHabits)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid habit name'
            ]);
        }

        $updateData = [];

        if (is_array($answers)) {
            $habitAnswers = $this->decodeHabitAnswers($habit['habit_answers'] ?? null);
            $habitAnswers[$habitName] = $answers;
            $updateData['habit_answers'] = json_encode($habitAnswers, JSON_UNESCAPED_UNICODE);
        }

        if (!$updateOnly) {
            $updateData[$habitName] = !$habit[$habitName];
        }

        if (empty($updateData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No data to update'
            ]);
        }

        $this->habitModel->update($habit['id'], $updateData);

        // Get updated habit
        $habit = $this->habitModel->find($habit['id']);
        $habitAnswers = $this->decodeHabitAnswers($habit['habit_answers'] ?? null);
        $streak = $this->calculateStreak((int) $user['student_id']);
        $dailyStats = $this->calculateDailyStats($habit, $streak);
        $badges = $this->calculateBadges((int) $user['student_id']);
        $weeklySummary = $this->calculateWeeklySummary((int) $user['student_id']);
        $activeReminders = $targetDate === date('Y-m-d') ? $this->getActiveHabitReminders($habit) : [];
        if ($targetDate === date('Y-m-d')) {
            $this->storeReminderNotifications((int) $userId, (int) $user['student_id'], $activeReminders);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'habit' => $habit,
                'answers' => $habitAnswers,
                'completed' => $dailyStats['completed'],
                'xp' => $dailyStats['xp'],
                'stats' => $dailyStats,
                'badges' => $badges,
                'weekly_summary' => $weeklySummary,
                'reminders' => $activeReminders,
                'selected_date' => $targetDate,
            ]
        ]);
    }

    public function apiGetHabitsStats()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        // Get this month's stats
        $thisMonth = date('Y-m');
        $habits = $this->habitModel->where('student_id', $user['student_id'])
            ->where('date >=', $thisMonth . '-01')
            ->where('date <=', date('Y-m-t'))
            ->findAll();

        $totalDays = count($habits);
        $totalCompleted = 0;

        foreach ($habits as $habit) {
            $completed = 0;
            foreach ($this->habitFields as $field) {
                if (!empty($habit[$field])) {
                    $completed++;
                }
            }

            if ($completed === 7) $totalCompleted++;
        }

        $targetDate = $this->resolveTargetHabitDate($this->request->getGet('date'));
        if ($targetDate === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal tidak valid atau belum waktunya'
            ]);
        }

        $todayHabit = $this->getOrCreateTodayHabit((int) $user['student_id'], $targetDate);
        $streak = $this->calculateStreak((int) $user['student_id']);
        $todayStats = $this->calculateDailyStats($todayHabit, $streak);
        $badges = $this->calculateBadges((int) $user['student_id']);
        $weeklySummary = $this->calculateWeeklySummary((int) $user['student_id']);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'totalDays' => $totalDays,
                'perfectDays' => $totalCompleted,
                'streak' => $streak,
                'today' => $todayStats,
                'badges' => $badges,
                'weekly_summary' => $weeklySummary,
                'selected_date' => $targetDate,
            ]
        ]);
    }

    public function apiGetParentWeeklySummary()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user || !$user['student_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $student = $this->studentModel->find($user['student_id']);
        if (!$student) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Student data not found'
            ]);
        }

        $summary = $this->calculateWeeklySummary((int) $user['student_id']);
        $shareText = $this->buildParentSummaryText($student, $summary);
        $waLink = $this->buildWhatsAppShareLink((string) ($student['parent_phone'] ?? ''), $shareText);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student['id'],
                    'name' => $student['name'],
                    'nis' => $student['nis'] ?? '-',
                    'parent_phone' => $student['parent_phone'] ?? null,
                ],
                'summary' => $summary,
                'share_text' => $shareText,
                'wa_share_url' => $waLink,
            ]
        ]);
    }

    private function calculateStreak($studentId)
    {
        $habits = $this->habitModel->where('student_id', $studentId)
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
            $completed = true;
            foreach ($this->habitFields as $field) {
                if (empty($habit[$field])) {
                    $completed = false;
                    break;
                }
            }

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

    private function getOrCreateTodayHabit(int $studentId, ?string $date = null): array
    {
        $targetDate = $date ?: date('Y-m-d');
        $habit = $this->habitModel
            ->where('student_id', $studentId)
            ->where('date', $targetDate)
            ->first();

        if ($habit) {
            return $habit;
        }

        $newHabit = [
            'student_id' => $studentId,
            'date' => $targetDate,
            'habit_answers' => '{}',
        ];

        foreach ($this->habitFields as $field) {
            $newHabit[$field] = 0;
        }

        $this->habitModel->insert($newHabit);
        $newHabit['id'] = $this->habitModel->getInsertID();

        return $newHabit;
    }

    private function calculateDailyStats(array $habit, int $streak): array
    {
        $completed = 0;
        foreach ($this->habitFields as $field) {
            if (!empty($habit[$field])) {
                $completed++;
            }
        }

        $total = count($this->habitFields);
        $percentage = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return [
            'completed' => $completed,
            'total' => $total,
            'xp' => $completed * 20,
            'streak' => $streak,
            'percentage' => $percentage,
            'is_perfect_today' => $completed === $total,
            'today_status' => $completed . '/' . $total . ' selesai',
        ];
    }

    private function calculateWeeklySummary(int $studentId): array
    {
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));

        $habits = $this->habitModel
            ->where('student_id', $studentId)
            ->where('date >=', $weekStart)
            ->where('date <=', $weekEnd)
            ->orderBy('date', 'ASC')
            ->findAll();

        $daysWithData = count($habits);
        $totalCompleted = 0;
        $perfectDays = 0;
        $habitHitCount = array_fill_keys($this->habitFields, 0);

        foreach ($habits as $habit) {
            $dailyCompleted = 0;
            foreach ($this->habitFields as $field) {
                if (!empty($habit[$field])) {
                    $dailyCompleted++;
                    $habitHitCount[$field]++;
                }
            }
            $totalCompleted += $dailyCompleted;
            if ($dailyCompleted === count($this->habitFields)) {
                $perfectDays++;
            }
        }

        $avgCompleted = $daysWithData > 0 ? round($totalCompleted / $daysWithData, 2) : 0;

        $bestHabit = null;
        $leastHabit = null;
        if (!empty($habitHitCount)) {
            arsort($habitHitCount);
            $bestKey = (string) array_key_first($habitHitCount);
            $bestHabit = [
                'key' => $bestKey,
                'label' => $this->habitLabels[$bestKey] ?? $bestKey,
                'count' => $habitHitCount[$bestKey],
            ];

            asort($habitHitCount);
            $leastKey = (string) array_key_first($habitHitCount);
            $leastHabit = [
                'key' => $leastKey,
                'label' => $this->habitLabels[$leastKey] ?? $leastKey,
                'count' => $habitHitCount[$leastKey],
            ];
        }

        return [
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'days_with_data' => $daysWithData,
            'perfect_days' => $perfectDays,
            'avg_completed' => $avgCompleted,
            'completion_rate' => $daysWithData > 0 ? round(($totalCompleted / ($daysWithData * count($this->habitFields))) * 100, 1) : 0,
            'best_habit' => $bestHabit,
            'least_habit' => $leastHabit,
        ];
    }

    private function calculateBadges(int $studentId): array
    {
        $streak = $this->calculateStreak($studentId);
        $olahragaCount = $this->habitModel
            ->where('student_id', $studentId)
            ->where('berolahraga', 1)
            ->countAllResults();

        $perfectDaysCount = 0;
        $allHabits = $this->habitModel
            ->where('student_id', $studentId)
            ->orderBy('date', 'DESC')
            ->findAll(90);

        foreach ($allHabits as $habit) {
            $done = 0;
            foreach ($this->habitFields as $field) {
                $done += !empty($habit[$field]) ? 1 : 0;
            }
            if ($done === 7) {
                $perfectDaysCount++;
            }
        }

        $badges = [
            [
                'key' => 'streak_7_days',
                'title' => '7 Hari Tanpa Putus',
                'description' => 'Selesaikan 7/7 kebiasaan selama 7 hari berturut-turut',
                'earned' => $streak >= 7,
                'progress' => min($streak, 7),
                'target' => 7,
            ],
            [
                'key' => 'olahraga_5x',
                'title' => 'Olahraga 5x',
                'description' => 'Lakukan check-in olahraga minimal 5 kali',
                'earned' => $olahragaCount >= 5,
                'progress' => min($olahragaCount, 5),
                'target' => 5,
            ],
            [
                'key' => 'perfect_10_days',
                'title' => '10 Hari Sempurna',
                'description' => 'Capai 10 hari dengan 7/7 kebiasaan lengkap',
                'earned' => $perfectDaysCount >= 10,
                'progress' => min($perfectDaysCount, 10),
                'target' => 10,
            ],
        ];

        $earnedCount = 0;
        foreach ($badges as $badge) {
            if ($badge['earned']) {
                $earnedCount++;
            }
        }

        return [
            'earned_count' => $earnedCount,
            'total' => count($badges),
            'items' => $badges,
        ];
    }

    private function getActiveHabitReminders(array $todayHabit): array
    {
        $now = date('H:i');
        $active = [];

        foreach ($this->habitFields as $field) {
            $window = $this->habitTimeWindows[$field] ?? null;
            if (!$window || count($window) !== 2) {
                continue;
            }

            if (!empty($todayHabit[$field])) {
                continue;
            }

            if ($this->isNowInRange($now, $window[0], $window[1])) {
                $active[] = [
                    'habit_key' => $field,
                    'habit_label' => $this->habitLabels[$field] ?? $field,
                    'start_time' => $window[0],
                    'end_time' => $window[1],
                    'message' => 'Waktunya ' . ($this->habitLabels[$field] ?? $field) . '. Yuk check-in sekarang.',
                ];
            }
        }

        return $active;
    }

    private function isNowInRange(string $now, string $start, string $end): bool
    {
        return $now >= $start && $now <= $end;
    }

    private function storeReminderNotifications(int $userId, int $studentId, array $reminders): void
    {
        if (empty($reminders)) {
            return;
        }

        $todayStart = date('Y-m-d 00:00:00');

        foreach ($reminders as $reminder) {
            $title = 'Reminder Kebiasaan: ' . $reminder['habit_label'];
            $exists = $this->notificationModel
                ->where('user_id', $userId)
                ->where('type', 'habit_reminder')
                ->where('student_id', $studentId)
                ->where('title', $title)
                ->where('created_at >=', $todayStart)
                ->first();

            if ($exists) {
                continue;
            }

            $this->notificationModel->insert([
                'user_id' => $userId,
                'type' => 'habit_reminder',
                'title' => $title,
                'message' => $reminder['message'] . ' (' . $reminder['start_time'] . ' - ' . $reminder['end_time'] . ')',
                'student_id' => $studentId,
                'is_sent' => 1,
                'sent_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function buildParentSummaryText(array $student, array $summary): string
    {
        $bestHabit = $summary['best_habit']['label'] ?? '-';
        $leastHabit = $summary['least_habit']['label'] ?? '-';

        return trim(sprintf(
            "Ringkasan Mingguan 7 Kebiasaan\nNama: %s\nPeriode: %s s/d %s\nHari terisi: %d\nHari sempurna (7/7): %d\nRata-rata harian: %s/7\nKebiasaan terbaik: %s\nPerlu ditingkatkan: %s",
            $student['name'] ?? '-',
            $summary['week_start'] ?? '-',
            $summary['week_end'] ?? '-',
            (int) ($summary['days_with_data'] ?? 0),
            (int) ($summary['perfect_days'] ?? 0),
            (string) ($summary['avg_completed'] ?? '0'),
            $bestHabit,
            $leastHabit,
        ));
    }

    private function buildWhatsAppShareLink(string $parentPhone, string $message): ?string
    {
        $phone = preg_replace('/\D+/', '', $parentPhone);
        if (!$phone) {
            return null;
        }

        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }

    private function decodeHabitAnswers($rawAnswers): array
    {
        if (!$rawAnswers || !is_string($rawAnswers)) {
            return [];
        }

        $decoded = json_decode($rawAnswers, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function resolveTargetHabitDate(?string $requestedDate): ?string
    {
        $date = $requestedDate ?: date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return null;
        }

        [$year, $month, $day] = array_map('intval', explode('-', $date));
        if (!checkdate($month, $day, $year)) {
            return null;
        }

        if ($date > date('Y-m-d')) {
            return null;
        }

        return $date;
    }
}
