<?php

namespace App\Controllers;

use App\Models\ClassModel;
use App\Models\StudentModel;
use App\Models\AttendanceSummaryModel;
use App\Models\StudentHabitModel;
use App\Models\ShiftModel;

class Teacher extends BaseController
{
    protected $classModel;
    protected $studentModel;
    protected $attendanceModel;
    protected $habitModel;
    protected $shiftModel;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->studentModel = new StudentModel();
        $this->attendanceModel = new AttendanceSummaryModel();
        $this->habitModel = new StudentHabitModel();
        $this->shiftModel = new ShiftModel();
    }

    /**
     * Check if user is a teacher
     */
    private function checkTeacherAuth()
    {
        $role = session()->get('role');
        if ($role !== 'teacher') {
            return redirect()->to('/')->with('error', 'Akses tidak diizinkan');
        }
        return null;
    }

    /**
     * Get teacher's classes (wali kelas)
     */
    private function getTeacherClasses()
    {
        $userId = session()->get('user_id');
        return $this->classModel
            ->where('teacher_id', $userId)
            ->findAll();
    }

    /**
     * Dashboard page
     */
    public function dashboard()
    {
        $check = $this->checkTeacherAuth();
        if ($check) return $check;

        $classes = $this->getTeacherClasses();

        return view('teacher/dashboard', [
            'activePage' => 'teacher/dashboard',
            'classes' => $classes
        ]);
    }

    /**
     * Attendance page (Daftar Hadir)
     */
    public function attendance()
    {
        $check = $this->checkTeacherAuth();
        if ($check) return $check;

        $classes = $this->getTeacherClasses();

        return view('teacher/attendance', [
            'activePage' => 'teacher/attendance',
            'classes'    => $classes,
        ]);
    }

    /**
     * Students page (Daftar Siswa walikelas)
     */
    public function students()
    {
        $check = $this->checkTeacherAuth();
        if ($check) return $check;

        $classes = $this->getTeacherClasses();
        $singleClass = count($classes) === 1 ? $classes[0] : null;

        $students = [];
        if ($singleClass) {
            $students = $this->studentModel
                ->where('class_id', $singleClass['id'])
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();
        }

        return view('teacher/students', [
            'activePage' => 'teacher/students',
            'classes'    => $classes,
            'singleClass' => $singleClass,
            'students'   => $students,
        ]);
    }

    /**
     * Rekap page
     */
    public function rekap()
    {
        $check = $this->checkTeacherAuth();
        if ($check) return $check;

        return view('teacher/rekap', [
            'activePage' => 'teacher/rekap'
        ]);
    }

    /**
     * Habits Daily page
     */
    public function habitsDaily()
    {
        $check = $this->checkTeacherAuth();
        if ($check) return $check;

        return view('teacher/habits_daily', [
            'activePage' => 'teacher/habits-daily'
        ]);
    }

    /**
     * Habits Monthly page
     */
    public function habitsMonthly()
    {
        $check = $this->checkTeacherAuth();
        if ($check) return $check;

        return view('teacher/habits_monthly', [
            'activePage' => 'teacher/habits-monthly'
        ]);
    }

    // ===== API ENDPOINTS =====

    /**
     * API: Get teacher's classes
     */
    public function apiGetClasses()
    {
        try {
            $classes = $this->getTeacherClasses();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data kelas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get students from teacher's classes
     */
    public function apiGetStudents()
    {
        try {
            $classId = $this->request->getGet('class_id');

            // Verify this class belongs to the teacher
            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $classId)
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke kelas ini'
                ]);
            }

            $query = $this->studentModel
                ->select('students.*, classes.name as class_name')
                ->join('classes', 'classes.id = students.class_id', 'left')
                ->where('students.active', 1);

            if ($classId) {
                $query->where('students.class_id', $classId);
            }

            $query->orderBy('students.name', 'ASC');
            $students = $query->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data siswa: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get attendance data
     */
    public function apiGetAttendance()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $date = $this->request->getGet('date') ?? date('Y-m-d');

            // Verify class belongs to teacher
            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $classId)
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak'
                ]);
            }

            // Get students
            $students = $this->studentModel
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            // Get attendance records for the date
            $attendances = [];
            if (!empty($students)) {
                $studentIds = array_column($students, 'id');
                $records = $this->attendanceModel
                    ->whereIn('student_id', $studentIds)
                    ->where('date', $date)
                    ->findAll();

                foreach ($records as $record) {
                    $attendances[$record['student_id']] = $record;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'students' => $students,
                    'attendances' => $attendances,
                    'date' => $date
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Save attendance
     */
    public function apiSaveAttendance()
    {
        try {
            $json = $this->request->getJSON(true);
            $date = $json['date'] ?? date('Y-m-d');
            $records = $json['records'] ?? [];

            if (empty($records)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak valid'
                ]);
            }

            $saved = 0;
            foreach ($records as $record) {
                $studentId = $record['student_id'] ?? null;
                if (!$studentId) continue;

                // Verify student is in teacher's class
                $student = $this->studentModel->find($studentId);
                if (!$student) continue;

                $userId = session()->get('user_id');
                $class = $this->classModel
                    ->where('id', $student['class_id'])
                    ->where('teacher_id', $userId)
                    ->first();

                if (!$class) continue;

                $data = [
                    'student_id' => $studentId,
                    'date' => $date,
                    'status' => $record['status'] ?? 'hadir',
                    'notes' => $record['notes'] ?? null
                ];

                // Check if record exists
                $existing = $this->attendanceModel
                    ->where('student_id', $studentId)
                    ->where('date', $date)
                    ->first();

                if ($existing) {
                    $this->attendanceModel->update($existing['id'], $data);
                } else {
                    $this->attendanceModel->insert($data);
                }
                $saved++;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Data {$saved} siswa berhasil disimpan"
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get rekap attendance
     */
    public function apiGetRekap()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            // Verify class
            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $classId)
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak'
                ]);
            }

            // Get students
            $students = $this->studentModel
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            if (empty($students)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => ['class' => $class, 'students' => [], 'summary' => []]
                ]);
            }

            $studentIds = array_column($students, 'id');

            // Get attendance records for the month
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = date('Y-m-t', strtotime($startDate));

            $records = $this->attendanceModel
                ->whereIn('student_id', $studentIds)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            // Build summary
            $summary = [];
            foreach ($students as $student) {
                $summary[$student['id']] = [
                    'student' => $student,
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpa' => 0,
                    'total' => 0
                ];
            }

            foreach ($records as $record) {
                $sid = $record['student_id'];
                if (isset($summary[$sid])) {
                    $status = strtolower($record['status']);
                    if (isset($summary[$sid][$status])) {
                        $summary[$sid][$status]++;
                    }
                    $summary[$sid]['total']++;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'students' => $students,
                    'summary' => array_values($summary),
                    'month_name' => date('F Y', strtotime($startDate))
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get habits (reuse Admin's logic)
     */
    public function apiGetHabits()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $date = $this->request->getGet('date');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            // Verify class
            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $classId)
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak'
                ]);
            }

            $students = $this->studentModel
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            if (empty($students)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $date
                        ? ['students' => [], 'date' => $date, 'habit_labels' => StudentHabitModel::getHabitColumns()]
                        : ['students' => [], 'habits' => [], 'dates' => []]
                ]);
            }

            if ($date) {
                $studentIds = array_column($students, 'id');
                $dailyHabits = $this->habitModel
                    ->whereIn('student_id', $studentIds)
                    ->where('date', $date)
                    ->findAll();

                $dailyMap = [];
                foreach ($dailyHabits as $row) {
                    $dailyMap[$row['student_id']] = $row;
                }

                $rows = [];
                foreach ($students as $student) {
                    $habit = $dailyMap[$student['id']] ?? null;
                    $completed = 0;
                    $row = [
                        'student_id' => $student['id'],
                        'student_name' => $student['name'],
                    ];

                    foreach (array_keys(StudentHabitModel::getHabitColumns()) as $field) {
                        $value = $habit ? (int) ($habit[$field] ?? 0) : 0;
                        $row[$field] = $value;
                        $completed += $value;
                    }

                    $row['completed'] = $completed;
                    $row['total'] = 7;
                    $row['status'] = $completed >= 6 ? 'konsisten' : ($completed <= 3 ? 'sering bolong' : 'cukup');
                    $rows[] = $row;
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'class' => $class,
                        'date' => $date,
                        'students' => $rows,
                        'habit_labels' => StudentHabitModel::getHabitColumns(),
                    ]
                ]);
            }

            $studentIds = array_column($students, 'id');
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

            $dates = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $dayOfWeek = date('N', strtotime($dateStr));
                $dates[] = [
                    'date' => $dateStr,
                    'day_name' => $this->getIndonesianDayName($dayOfWeek),
                    'is_weekend' => $dayOfWeek >= 6,
                ];
            }

            $habits = $this->habitModel
                ->whereIn('student_id', $studentIds)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            $habitMap = [];
            foreach ($habits as $h) {
                $habitMap[$h['student_id']][$h['date']] = $h;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'students' => $students,
                    'habits' => $habitMap,
                    'dates' => $dates,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Rekap kelas kebiasaan + intervensi dini
     */
    public function apiGetHabitClassRecap()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $endDate = $this->request->getGet('date') ?? date('Y-m-d');

            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $classId)
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak'
                ]);
            }

            $students = $this->studentModel
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            if (empty($students)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'class' => $class,
                        'recap' => [],
                    ]
                ]);
            }

            $startDate = date('Y-m-d', strtotime($endDate . ' -13 days'));
            $studentIds = array_column($students, 'id');
            $habits = $this->habitModel
                ->whereIn('student_id', $studentIds)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->orderBy('date', 'ASC')
                ->findAll();

            $habitFields = array_keys(StudentHabitModel::getHabitColumns());
            $byStudent = [];
            foreach ($habits as $habit) {
                $done = 0;
                foreach ($habitFields as $field) {
                    $done += !empty($habit[$field]) ? 1 : 0;
                }
                $byStudent[$habit['student_id']][$habit['date']] = $done;
            }

            $recap = [];
            foreach ($students as $student) {
                $series = $byStudent[$student['id']] ?? [];
                $last7 = $this->extractLastDaysSeries($series, $endDate, 7);

                $avg = !empty($last7) ? round(array_sum($last7) / count($last7), 2) : 0;
                $perfectDays = 0;
                $lowDays = 0;
                foreach ($last7 as $count) {
                    if ($count === 7) {
                        $perfectDays++;
                    }
                    if ($count <= 3) {
                        $lowDays++;
                    }
                }

                $status = 'perlu bimbingan';
                if ($avg >= 5.5 && $perfectDays >= 2) {
                    $status = 'konsisten';
                } elseif ($lowDays >= 3 || $avg < 4) {
                    $status = 'sering bolong';
                }

                $intervention = $this->isThreeDayDeclining($series, $endDate);

                $recap[] = [
                    'student_id' => $student['id'],
                    'student_name' => $student['name'],
                    'avg_completed_7_days' => $avg,
                    'perfect_days_7_days' => $perfectDays,
                    'low_days_7_days' => $lowDays,
                    'status' => $status,
                    'need_intervention' => $intervention,
                    'intervention_reason' => $intervention ? '3 hari berturut-turut menurun' : null,
                ];
            }

            usort($recap, static function ($a, $b) {
                if ($a['need_intervention'] === $b['need_intervention']) {
                    return $a['avg_completed_7_days'] <=> $b['avg_completed_7_days'];
                }
                return $a['need_intervention'] ? -1 : 1;
            });

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'recap' => $recap,
                    'date' => $endDate,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get habit recap
     */
    public function apiGetHabitRecap()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            // Verify class
            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $classId)
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak'
                ]);
            }

            $students = $this->studentModel
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            if (empty($students)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => ['students' => [], 'recap' => []]
                ]);
            }

            $studentIds = array_column($students, 'id');
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

            $dates = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $dayOfWeek = date('N', strtotime($dateStr));
                $dates[] = [
                    'date' => $dateStr,
                    'day_name' => $this->getIndonesianDayName($dayOfWeek),
                    'day' => $d,
                    'is_weekend' => $dayOfWeek >= 6,
                ];
            }

            $habits = $this->habitModel
                ->whereIn('student_id', $studentIds)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            $habitColumns = array_keys(StudentHabitModel::getHabitColumns());
            $dateSummary = [];

            foreach ($dates as $dateInfo) {
                $dateStr = $dateInfo['date'];
                $summary = [
                    'date' => $dateStr,
                    'day_name' => $dateInfo['day_name'],
                    'day' => $dateInfo['day'],
                    'is_weekend' => $dateInfo['is_weekend'],
                ];

                $totalChecked = 0;
                $totalPossible = count($students) * 7;
                $hasData = false;

                foreach ($habitColumns as $col) {
                    $count = 0;
                    foreach ($habits as $h) {
                        if ($h['date'] === $dateStr && $h[$col]) {
                            $count++;
                            $hasData = true;
                        }
                    }
                    $summary[$col] = $count;
                    $totalChecked += $count;
                }

                $summary['total_students'] = count($students);
                $summary['percentage'] = $totalPossible > 0 ? round(($totalChecked / $totalPossible) * 100, 1) : 0;
                $summary['has_data'] = $hasData;

                $dateSummary[] = $summary;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'students' => $students,
                    'dates' => $dateSummary,
                    'habit_labels' => StudentHabitModel::getHabitColumns(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get student monthly habits
     */
    public function apiGetStudentMonthlyHabits()
    {
        try {
            $studentId = $this->request->getGet('student_id');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            $student = $this->studentModel->find($studentId);
            if (!$student) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Siswa tidak ditemukan'
                ]);
            }

            // Verify class belongs to teacher
            $userId = session()->get('user_id');
            $class = $this->classModel
                ->where('id', $student['class_id'])
                ->where('teacher_id', $userId)
                ->first();

            if (!$class) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Akses ditolak'
                ]);
            }

            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

            $habits = $this->habitModel
                ->where('student_id', $studentId)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            $habitsByDate = [];
            foreach ($habits as $h) {
                $habitsByDate[$h['date']] = $h;
            }

            $habitColumns = array_keys(StudentHabitModel::getHabitColumns());
            $dates = [];
            $totalChecked = 0;
            $totalPossible = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $dayOfWeek = date('N', strtotime($dateStr));
                $isWeekend = $dayOfWeek >= 6;

                $row = [
                    'date' => $dateStr,
                    'day' => $d,
                    'day_name' => $this->getIndonesianDayName($dayOfWeek),
                    'is_weekend' => $isWeekend,
                    'has_data' => isset($habitsByDate[$dateStr]),
                ];

                $dayChecked = 0;
                foreach ($habitColumns as $col) {
                    $val = isset($habitsByDate[$dateStr]) ? (int)$habitsByDate[$dateStr][$col] : 0;
                    $row[$col] = $val;
                    $dayChecked += $val;
                }

                $row['percentage'] = 7 > 0 ? round(($dayChecked / 7) * 100, 1) : 0;

                if (!$isWeekend) {
                    $totalChecked += $dayChecked;
                    $totalPossible += 7;
                }

                $dates[] = $row;
            }

            $overallPercentage = $totalPossible > 0 ? round(($totalChecked / $totalPossible) * 100, 1) : 0;

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'student' => $student,
                    'class' => $class,
                    'dates' => $dates,
                    'overall_percentage' => $overallPercentage,
                    'habit_labels' => StudentHabitModel::getHabitColumns(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper: Get Indonesian day name
     */
    private function getIndonesianDayName(int $dayOfWeek): string
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        return $days[$dayOfWeek] ?? '';
    }

    private function extractLastDaysSeries(array $seriesByDate, string $endDate, int $days): array
    {
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime($endDate . ' -' . $i . ' days'));
            $result[] = (int) ($seriesByDate[$date] ?? 0);
        }
        return $result;
    }

    private function isThreeDayDeclining(array $seriesByDate, string $endDate): bool
    {
        $d0 = date('Y-m-d', strtotime($endDate));
        $d1 = date('Y-m-d', strtotime($endDate . ' -1 day'));
        $d2 = date('Y-m-d', strtotime($endDate . ' -2 days'));

        if (!isset($seriesByDate[$d0], $seriesByDate[$d1], $seriesByDate[$d2])) {
            return false;
        }

        return $seriesByDate[$d2] > $seriesByDate[$d1] && $seriesByDate[$d1] > $seriesByDate[$d0];
    }
}
