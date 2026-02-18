<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\DeviceModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\DeviceUserMapModel;
use App\Models\ClassModel;
use App\Models\ShiftModel;
use App\Models\SchoolHolidayModel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Admin extends BaseController
{
    protected $studentModel;
    protected $deviceModel;
    protected $attendanceSummaryModel;
    protected $attendanceLogModel;
    protected $deviceUserMapModel;
    protected $classModel;
    protected $shiftModel;
    protected $schoolHolidayModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->deviceModel = new DeviceModel();
        $this->attendanceSummaryModel = new AttendanceSummaryModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->deviceUserMapModel = new DeviceUserMapModel();
        $this->classModel = new ClassModel();
        $this->shiftModel = new ShiftModel();
        $this->schoolHolidayModel = new SchoolHolidayModel();
    }

    public function dashboard()
    {
        // Check if user is admin
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        // Get statistics
        $today = date('Y-m-d');
        $stats = [
            'total_students' => $this->studentModel->countAll(),
            'present_today' => $this->attendanceSummaryModel
                ->where('date', $today)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->countAllResults(),
            'absent_today' => $this->attendanceSummaryModel
                ->where('date', $today)
                ->where('status', 'alpha')
                ->countAllResults(),
            'total_devices' => $this->deviceModel->countAll(),
            'active_devices' => $this->deviceModel->where('status', 'online')->countAllResults(),
        ];

        // Ambil 10 log absensi terbaru (real data)
        $logs = $this->attendanceLogModel
            ->select('attendance_logs.att_time, students.name as student_name, classes.name as class_name, devices.name as device_name, attendance_logs.status')
            ->join('students', 'students.id = attendance_logs.student_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('devices', 'devices.id = attendance_logs.device_id', 'left')
            ->orderBy('attendance_logs.att_time', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Dashboard Admin',
            'pageTitle' => 'Dashboard Admin',
            'pageDescription' => 'Selamat datang di panel administrator',
            'activePage' => 'admin/dashboard',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
            'stats' => $stats,
            'unreadNotifications' => 0,
            'logs' => $logs
        ];

        return view('dashboard/admin', $data);
    }

    public function devices()
    {
        // Devices management page
        $data = [
            'title' => 'Mesin Fingerprint',
            'pageTitle' => 'Mesin Fingerprint',
            'pageDescription' => 'Kelola mesin fingerprint',
            'activePage' => 'admin/devices',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/devices', $data);
    }

    public function deviceMapping()
    {
        // Device to student mapping page
        $data = [
            'title' => 'Mapping ID Mesin',
            'pageTitle' => '',
            'pageDescription' => '',
            'activePage' => 'admin/device-mapping',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/device_mapping', $data);
    }

    public function shifts()
    {
        // Shifts management page
        $data = [
            'title' => 'Pengaturan Shift',
            'pageTitle' => 'Pengaturan Shift',
            'pageDescription' => 'Kelola jam shift masuk dan pulang',
            'activePage' => 'admin/shifts',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/shifts', $data);
    }

    public function students()
    {
        // Students management page
        $data = [
            'title' => 'Data Siswa',
            'pageTitle' => 'Data Siswa',
            'pageDescription' => 'Kelola data siswa dan informasi absensi',
            'activePage' => 'admin/students',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/students', $data);
    }

    public function studentsImport()
    {
        // Students import page
        $data = [
            'title' => 'Import Data Siswa',
            'pageTitle' => 'Import Data Siswa',
            'pageDescription' => 'Upload data siswa dari file Excel atau CSV',
            'activePage' => 'admin/students-import',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/students_import', $data);
    }

    public function classes()
    {
        // Classes management page
        $data = [
            'title' => 'Data Kelas',
            'pageTitle' => 'Data Kelas',
            'pageDescription' => 'Kelola data kelas dan jumlah siswa',
            'activePage' => 'admin/classes',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/classes', $data);
    }

    public function users()
    {
        $data = [
            'title' => 'Manajemen User',
            'pageTitle' => 'Manajemen User',
            'pageDescription' => 'Kelola akun pengguna sistem',
            'activePage' => 'admin/users',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
            'users' => [],
        ];

        return view('admin/users', $data);
    }

    public function reports()
    {
        // Reports page
        $data = [
            'title' => 'Laporan',
            'pageTitle' => 'Laporan Absensi',
            'pageDescription' => 'Generate dan export laporan absensi siswa',
            'activePage' => 'admin/reports',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/reports', $data);
    }

    public function calendar()
    {
        $data = [
            'title' => 'Kalender Akademik',
            'pageTitle' => 'Kalender Akademik',
            'pageDescription' => 'Lihat dan atur hari libur sekolah',
            'activePage' => 'admin/calendar',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/calendar', $data);
    }

    public function rekap()
    {
        $data = [
            'title' => 'Rekap Daftar Hadir',
            'pageTitle' => 'Rekap Daftar Hadir',
            'pageDescription' => 'Rekap kehadiran siswa per bulan',
            'activePage' => 'admin/rekap',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/rekap', $data);
    }

    public function attendanceLogs()
    {
        // Attendance logs page
        $data = [
            'title' => 'Log Absensi',
            'pageTitle' => 'Log Absensi',
            'pageDescription' => 'Monitor log absensi real-time dari mesin fingerprint',
            'activePage' => 'admin/attendance-logs',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/attendance_logs', $data);
    }

    public function attendance()
    {
        // Daftar Hadir page
        $data = [
            'title' => 'Daftar Hadir',
            'pageTitle' => 'Daftar Hadir',
            'pageDescription' => 'Kelola kehadiran siswa per kelas dan tanggal',
            'activePage' => 'admin/attendance',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/attendance', $data);
    }

    public function teachers()
    {
        // Teachers management page
        $data = [
            'title' => 'Data Guru',
            'pageTitle' => 'Data Guru',
            'pageDescription' => 'Kelola data guru dan wali kelas',
            'activePage' => 'admin/teachers',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/teachers', $data);
    }

// ==================== API Methods ====================

    /**
     * API: Get all users
     */
    public function apiGetUsers()
    {
        try {
            $db = \Config\Database::connect();
            $role = mb_strtolower(trim((string) $this->request->getGet('role')));
            $search = trim((string) $this->request->getGet('search'));
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = (int) ($this->request->getGet('per_page') ?? 25);
            $perPage = max(1, min(100, $perPage));

            $builder = $db->table('users')
                ->select('id, username, email, role, full_name, is_active')
                ->where('deleted_at', null);

            if ($role !== '') {
                if ($role === 'teacher') {
                    $builder->groupStart()
                        ->where('role', 'teacher')
                        ->orWhere('role', 'guru_piket')
                        ->groupEnd();
                } elseif ($role === 'siswa' || $role === 'student') {
                    $builder->groupStart()
                        ->where('role', 'siswa')
                        ->orWhere('role', 'student')
                        ->groupEnd();
                } else {
                    $builder->where('role', $role);
                }
            }

            if ($search !== '') {
                $builder->groupStart()
                    ->like('full_name', $search)
                    ->orLike('username', $search)
                    ->orLike('email', $search)
                    ->groupEnd();
            }

            $countBuilder = clone $builder;
            $total = (int) $countBuilder->countAllResults();
            $totalPages = max(1, (int) ceil($total / $perPage));
            if ($page > $totalPages) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;
            $users = $builder
                ->orderBy('full_name', 'ASC')
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();

            $countRows = $db->table('users')
                ->select('role, COUNT(*) as total')
                ->where('deleted_at', null)
                ->groupBy('role')
                ->get()
                ->getResultArray();

            $counts = [
                'admin' => 0,
                'teacher' => 0,
                'siswa' => 0,
            ];

            foreach ($countRows as $row) {
                $rowRole = mb_strtolower((string) ($row['role'] ?? ''));
                $rowTotal = (int) ($row['total'] ?? 0);
                if ($rowRole === 'admin') {
                    $counts['admin'] += $rowTotal;
                } elseif ($rowRole === 'teacher' || $rowRole === 'guru_piket') {
                    $counts['teacher'] += $rowTotal;
                } elseif ($rowRole === 'siswa' || $rowRole === 'student') {
                    $counts['siswa'] += $rowTotal;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $users,
                'meta' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'counts' => $counts,
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data user: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get single user
     */
    public function apiGetUser($id)
    {
        $userModel = model(\App\Models\UserModel::class);
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * API: Create new user
     */
    public function apiCreateUser()
    {
        try {
            $json = $this->request->getJSON(true);
            $userModel = model(\App\Models\UserModel::class);
            $data = [
                'username' => $json['username'] ?? '',
                'email' => $json['email'] ?? null,
                'password' => $json['password'] ?? '',
                'role' => $json['role'] ?? 'teacher',
                'full_name' => $json['name'] ?? '',
                'phone' => $json['phone'] ?? null,
                'is_active' => isset($json['is_active']) ? (int)$json['is_active'] : 1,
            ];
            $id = $userModel->insert($data);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan',
                'data' => $userModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambah user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Update user
     */
    public function apiUpdateUser($id)
    {
        try {
            $json = $this->request->getJSON(true);
            $userModel = model(\App\Models\UserModel::class);
            $data = [];
            if (isset($json['username'])) $data['username'] = $json['username'];
            if (isset($json['email'])) $data['email'] = $json['email'];
            if (isset($json['password']) && $json['password']) $data['password'] = $json['password'];
            if (isset($json['role'])) $data['role'] = $json['role'];
            if (isset($json['name'])) $data['full_name'] = $json['name'];
            if (isset($json['phone'])) $data['phone'] = $json['phone'];
            if (isset($json['is_active'])) $data['is_active'] = (int)$json['is_active'];
            if (!empty($data)) {
                $userModel->update($id, $data);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil diperbarui',
                'data' => $userModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete user
     */
    public function apiDeleteUser($id)
    {
        try {
            $userModel = model(\App\Models\UserModel::class);
            $user = $userModel->find($id);
            if (!$user) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ]);
            }
            $userModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get all teachers
     */
    public function apiGetTeachers()
    {
        $userModel = model(\App\Models\UserModel::class);
        $teachers = $userModel->where('role', 'teacher')->findAll();
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $teachers
        ]);
    }

    /**
     * API: Get single teacher
     */
    public function apiGetTeacher($id)
    {
        $userModel = model(\App\Models\UserModel::class);
        $teacher = $userModel->where('role', 'teacher')->find($id);
        if (!$teacher) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Data guru tidak ditemukan'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $teacher
        ]);
    }

    /**
     * API: Create new teacher
     */
    public function apiCreateTeacher()
    {
        try {
            $json = $this->request->getJSON(true);
            $userModel = model(\App\Models\UserModel::class);

            // Check if username already exists (including soft deleted)
            $existingUser = $userModel->withDeleted()->where('username', $json['username'] ?? '')->first();

            if ($existingUser) {
                // If soft deleted, restore and update
                if ($existingUser['deleted_at'] !== null) {
                    $data = [
                        'email' => $json['email'] ?? null,
                        'role' => 'teacher',
                        'full_name' => $json['name'] ?? '',
                        'nip' => $json['nip'] ?? null,
                        'phone' => $json['phone'] ?? null,
                        'is_active' => isset($json['is_active']) ? (int)$json['is_active'] : 1,
                        'deleted_at' => null,
                    ];
                    if (isset($json['password']) && !empty($json['password'])) {
                        $data['password'] = $json['password'];
                    }
                    $userModel->update($existingUser['id'], $data);
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Data guru berhasil dipulihkan dan diperbarui',
                        'data' => $userModel->find($existingUser['id'])
                    ]);
                } else {
                    // Username already in use
                    return $this->response->setStatusCode(400)->setJSON([
                        'status' => 'error',
                        'message' => 'Username sudah digunakan. Silakan gunakan username lain.'
                    ]);
                }
            }

            $data = [
                'username' => $json['username'] ?? '',
                'email' => $json['email'] ?? null,
                'password' => $json['password'] ?? '',
                'role' => 'teacher',
                'full_name' => $json['name'] ?? '',
                'nip' => $json['nip'] ?? null,
                'phone' => $json['phone'] ?? null,
                'is_active' => isset($json['is_active']) ? (int)$json['is_active'] : 1,
            ];
            $id = $userModel->insert($data);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data guru berhasil ditambahkan',
                'data' => $userModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambah data guru: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Update teacher
     */
    public function apiUpdateTeacher($id)
    {
        try {
            $json = $this->request->getJSON(true);
            $userModel = model(\App\Models\UserModel::class);
            $data = [];
            if (isset($json['username'])) $data['username'] = $json['username'];
            if (isset($json['email'])) $data['email'] = $json['email'];
            if (isset($json['name'])) $data['full_name'] = $json['name'];
            if (isset($json['nip'])) $data['nip'] = $json['nip'];
            if (isset($json['phone'])) $data['phone'] = $json['phone'];
            if (isset($json['is_active'])) $data['is_active'] = (int)$json['is_active'];
            if (isset($json['password']) && !empty($json['password'])) {
                $data['password'] = $json['password'];
            }
            if (!empty($data)) {
                $userModel->update($id, $data);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data guru berhasil diperbarui',
                'data' => $userModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui data guru: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete teacher
     */
    public function apiDeleteTeacher($id)
    {
        try {
            $userModel = model(\App\Models\UserModel::class);
            $teacher = $userModel->where('role', 'teacher')->find($id);
            if (!$teacher) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Data guru tidak ditemukan'
                ]);
            }
            $userModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data guru berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus data guru: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== API Methods ====================

    /**
     * API: Get all devices
     */
    public function apiGetDevices()
    {
        try {
            $devices = $this->deviceModel->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $devices
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data mesin: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get single device
     */
    public function apiGetDevice($id)
    {
        try {
            $device = $this->deviceModel->find($id);

            if (!$device) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Mesin tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $device
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data mesin: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Create new device
     */
    public function apiCreateDevice()
    {
        try {
            $json = $this->request->getJSON(true);

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'sn' => 'required|is_unique[devices.sn]',
                'name' => 'required',
                'ip_address' => 'required|valid_ip',
                'location' => 'required',
            ]);

            if (!$validation->run($json)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors()
                ]);
            }

            $data = [
                'sn' => $json['sn'],
                'name' => $json['name'],
                'ip_address' => $json['ip_address'],
                'port' => $json['port'] ?? 4370,
                'comm_key' => $json['comm_key'] ?? null,
                'location' => $json['location'],
                'push_url' => $json['push_url'] ?? null,
                'status' => 'offline',
            ];

            $this->deviceModel->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mesin berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan mesin: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Update device
     */
    public function apiUpdateDevice($id)
    {
        try {
            $json = $this->request->getJSON(true);

            $device = $this->deviceModel->find($id);
            if (!$device) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Mesin tidak ditemukan'
                ]);
            }

            // Validation
            $validation = \Config\Services::validation();
            $rules = [
                'name' => 'required',
                'ip_address' => 'required|valid_ip',
                'location' => 'required',
            ];

            // Check if SN is being changed
            if (isset($json['sn']) && $json['sn'] !== $device['sn']) {
                $rules['sn'] = 'required|is_unique[devices.sn]';
            }

            $validation->setRules($rules);

            if (!$validation->run($json)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors()
                ]);
            }

            $data = [
                'name' => $json['name'],
                'ip_address' => $json['ip_address'],
                'port' => $json['port'] ?? 4370,
                'comm_key' => $json['comm_key'] ?? null,
                'location' => $json['location'],
                'push_url' => $json['push_url'] ?? null,
            ];

            if (isset($json['sn']) && $json['sn'] !== $device['sn']) {
                $data['sn'] = $json['sn'];
            }

            $this->deviceModel->update($id, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mesin berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui mesin: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete device
     */
    public function apiDeleteDevice($id)
    {
        try {
            $device = $this->deviceModel->find($id);
            if (!$device) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Mesin tidak ditemukan'
                ]);
            }

            $this->deviceModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mesin berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus mesin: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Test device connection
     */
    public function apiTestDevice($id)
    {
        try {
            $device = $this->deviceModel->find($id);
            if (!$device) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Mesin tidak ditemukan'
                ]);
            }

            // Try to connect to device
            $fp = @fsockopen($device['ip_address'], $device['port'], $errno, $errstr, 5);

            if ($fp) {
                fclose($fp);

                // Update last_seen_at
                $this->deviceModel->update($id, [
                    'status' => 'online',
                    'last_seen_at' => date('Y-m-d H:i:s')
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Koneksi berhasil! Mesin dapat diakses.'
                ]);
            } else {
                // Update status to offline
                $this->deviceModel->update($id, [
                    'status' => 'offline'
                ]);

                return $this->response->setStatusCode(503)->setJSON([
                    'status' => 'error',
                    'message' => "Koneksi gagal: $errstr ($errno)"
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menguji koneksi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get all device mappings
     */
    public function apiGetDeviceMappings()
    {
        try {
            $deviceId = $this->request->getGet('device_id');
            $classId = $this->request->getGet('class_id');
            $search = $this->request->getGet('search');

            $builder = $this->deviceUserMapModel
                ->select('device_user_maps.*, students.nis as student_nis, students.name as student_name, students.class_id, classes.name as class_name, devices.name as device_name, devices.sn as device_sn')
                ->join('students', 'students.id = device_user_maps.student_id')
                ->join('devices', 'devices.id = device_user_maps.device_id')
                ->join('classes', 'classes.id = students.class_id', 'left');

            if ($deviceId) {
                $builder->where('device_user_maps.device_id', $deviceId);
            }

            if ($classId) {
                $builder->where('students.class_id', $classId);
            }

            if ($search) {
                $builder->groupStart()
                    ->like('students.name', $search)
                    ->orLike('students.nis', $search)
                    ->groupEnd();
            }

            $mappings = $builder->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $mappings
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data mapping: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Create device mapping
     */
    public function apiCreateDeviceMapping()
    {
        try {
            $json = $this->request->getJSON(true);

            // Check if mapping already exists
            $existing = $this->deviceUserMapModel
                ->where('device_id', $json['device_id'])
                ->where('device_user_id', $json['device_user_id'])
                ->first();

            if ($existing) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'ID User di mesin ini sudah digunakan'
                ]);
            }

            $data = [
                'device_id' => $json['device_id'],
                'student_id' => $json['student_id'],
                'device_user_id' => $json['device_user_id'],
                'privilege_level' => $json['privilege_level'] ?? 0,
            ];

            $this->deviceUserMapModel->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mapping berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan mapping: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete device mapping
     */
    public function apiDeleteDeviceMapping($id)
    {
        try {
            $mapping = $this->deviceUserMapModel->find($id);
            if (!$mapping) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Mapping tidak ditemukan'
                ]);
            }

            $this->deviceUserMapModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Mapping berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus mapping: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get all students
     */
    public function apiGetStudents()
    {
        try {
            $search = trim((string) $this->request->getGet('search'));
            $classId = trim((string) $this->request->getGet('class_id'));
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = (int) ($this->request->getGet('per_page') ?? 25);
            $perPage = max(1, min(100, $perPage));
            $sortBy = trim((string) $this->request->getGet('sort_by'));
            $sortDir = mb_strtolower(trim((string) $this->request->getGet('sort_dir')));
            $sortDir = in_array($sortDir, ['asc', 'desc'], true) ? strtoupper($sortDir) : 'ASC';

            $query = $this->studentModel
                ->select('students.*, classes.name as class_name')
                ->join('classes', 'classes.id = students.class_id', 'left')
                ->where('students.active', 1);

            if ($classId) {
                $query->where('students.class_id', $classId);
            }

            if ($search !== '') {
                $query->groupStart()
                    ->like('students.name', $search)
                    ->orLike('students.nis', $search)
                    ->orLike('students.nisn', $search)
                    ->groupEnd();
            }

            // countAllResults(false) counts without resetting the query builder chain
            $total = (int) $query->countAllResults(false);
            $totalPages = max(1, (int) ceil($total / $perPage));
            if ($page > $totalPages) {
                $page = $totalPages;
            }

            $offset = ($page - 1) * $perPage;
            $allowedSorts = [
                'name'       => 'students.name',
                'nis'        => 'students.nis',
                'nisn'       => 'students.nisn',
                'birth_date' => 'students.birth_date',
                'gender'     => 'students.gender',
                'class'      => 'classes.name',
            ];
            if (isset($allowedSorts[$sortBy])) {
                $query->orderBy($allowedSorts[$sortBy], $sortDir);
                if ($sortBy === 'class') {
                    $query->orderBy('students.name', 'ASC');
                }
            } else {
                $query->orderBy('students.name', 'ASC');
            }
            $students = $query->findAll($perPage, $offset);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $students,
                'meta' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data siswa: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get single student
     */
    public function apiGetStudent($id)
    {
        try {
            $student = $this->studentModel
                ->select('students.*, classes.name as class_name')
                ->join('classes', 'classes.id = students.class_id', 'left')
                ->where('students.id', $id)
                ->first();

            if (!$student) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Siswa tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data siswa: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Create new student
     */
    public function apiCreateStudent()
    {
        try {
            $json = $this->request->getJSON(true);

            // Validation
            $validation = \Config\Services::validation();
            $validation->setRules([
                'nis' => 'required|is_unique[students.nis]|max_length[50]',
                'name' => 'required|max_length[255]',
                'class_id' => 'required|integer',
                'gender' => 'required|in_list[L,P]',
            ]);

            if (!$validation->run($json)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors()
                ]);
            }

            $data = [
                'nis' => $json['nis'],
                'name' => $json['name'],
                'class_id' => $json['class_id'],
                'gender' => $json['gender'],
                'parent_phone' => $json['parent_phone'] ?? null,
                'address' => $json['address'] ?? null,
                'active' => isset($json['is_active']) ? ($json['is_active'] ? 1 : 0) : 1,
            ];

            $this->studentModel->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Siswa berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan siswa: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Update student
     */
    public function apiUpdateStudent($id)
    {
        try {
            $json = $this->request->getJSON(true);

            $student = $this->studentModel->find($id);
            if (!$student) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Siswa tidak ditemukan'
                ]);
            }

            // Validation
            $validation = \Config\Services::validation();
            $rules = [
                'name' => 'required|max_length[255]',
                'class_id' => 'required|integer',
                'gender' => 'required|in_list[L,P]',
            ];

            // Check if NIS is being changed
            if (isset($json['nis']) && $json['nis'] !== $student['nis']) {
                $rules['nis'] = 'required|is_unique[students.nis]|max_length[50]';
            }

            $validation->setRules($rules);

            if (!$validation->run($json)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validation->getErrors()
                ]);
            }

            $data = [
                'name' => $json['name'],
                'class_id' => $json['class_id'],
                'gender' => $json['gender'],
                'parent_phone' => $json['parent_phone'] ?? null,
                'address' => $json['address'] ?? null,
                'active' => isset($json['is_active']) ? ($json['is_active'] ? 1 : 0) : 1,
            ];

            if (isset($json['nis']) && $json['nis'] !== $student['nis']) {
                $data['nis'] = $json['nis'];
            }

            $this->studentModel->update($id, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data siswa berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui data siswa: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete student
     */
    public function apiDeleteStudent($id)
    {
        try {
            $student = $this->studentModel->find($id);
            if (!$student) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Siswa tidak ditemukan'
                ]);
            }

            // Soft delete
            $this->studentModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Siswa berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus siswa: ' . $e->getMessage()
            ]);
        }
    }

    public function apiGenerateStudentAccounts()
    {
        try {
            $json = $this->request->getJSON(true) ?? [];
            $classId = !empty($json['class_id']) ? (int) $json['class_id'] : null;
            $defaultPassword = trim((string) ($json['default_password'] ?? 'siswa123'));

            if (strlen($defaultPassword) < 6) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Password default minimal 6 karakter'
                ]);
            }

            $query = $this->studentModel->where('active', 1);
            if ($classId) {
                $query->where('class_id', $classId);
            }

            $students = $query->findAll();
            if (empty($students)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Tidak ada siswa aktif untuk diproses',
                    'data' => [
                        'processed' => 0,
                        'created' => 0,
                        'restored' => 0,
                        'skipped' => 0,
                        'errors' => [],
                        'credentials' => [],
                    ],
                ]);
            }

            /** @var \App\Models\UserModel $userModel */
            $userModel = model(\App\Models\UserModel::class);

            $created = 0;
            $restored = 0;
            $skipped = 0;
            $errors = [];
            $credentials = [];

            foreach ($students as $student) {
                try {
                    $studentId = (int) ($student['id'] ?? 0);
                    $studentName = trim((string) ($student['name'] ?? ''));
                    $baseIdentity = trim((string) ($student['nisn'] ?? $student['nipd'] ?? $student['nis'] ?? ''));

                    if ($studentId <= 0 || $studentName === '') {
                        $skipped++;
                        continue;
                    }

                    if ($baseIdentity === '') {
                        $baseIdentity = 'siswa' . $studentId;
                    }

                    $existingStudentUser = $userModel->withDeleted()
                        ->where('student_id', $studentId)
                        ->groupStart()
                        ->where('role', 'siswa')
                        ->orWhere('role', 'student')
                        ->groupEnd()
                        ->first();

                    if ($existingStudentUser && empty($existingStudentUser['deleted_at'])) {
                        $skipped++;
                        continue;
                    }

                    $excludeId = $existingStudentUser ? (int) $existingStudentUser['id'] : null;
                    $username = $this->buildUniqueStudentUsername($userModel, $baseIdentity, $excludeId);
                    $email = $this->buildUniqueStudentEmail($userModel, $username, $excludeId);

                    $payload = [
                        'username' => $username,
                        'email' => $email,
                        'password' => $defaultPassword,
                        'role' => 'siswa',
                        'full_name' => $studentName,
                        'phone' => $student['phone'] ?? $student['parent_phone'] ?? null,
                        'student_id' => $studentId,
                        'is_active' => 1,
                    ];

                    if ($existingStudentUser) {
                        $payload['deleted_at'] = null;
                        $userModel->update((int) $existingStudentUser['id'], $payload);
                        $restored++;
                    } else {
                        $userModel->insert($payload);
                        $created++;
                    }

                    $credentials[] = [
                        'student_id' => $studentId,
                        'name' => $studentName,
                        'username' => $username,
                        'password' => $defaultPassword,
                    ];
                } catch (\Throwable $studentError) {
                    $errors[] = [
                        'student_id' => (int) ($student['id'] ?? 0),
                        'name' => (string) ($student['name'] ?? '-'),
                        'message' => $studentError->getMessage(),
                    ];
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Proses generate akun siswa selesai',
                'data' => [
                    'processed' => count($students),
                    'created' => $created,
                    'restored' => $restored,
                    'skipped' => $skipped,
                    'errors' => array_slice($errors, 0, 50),
                    'credentials' => array_slice($credentials, 0, 200),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal generate akun siswa: ' . $e->getMessage(),
            ]);
        }
    }

    private function buildUniqueStudentUsername($userModel, string $identity, ?int $excludeId = null): string
    {
        $base = preg_replace('/[^a-zA-Z0-9._-]/', '', $identity);
        $base = trim((string) $base);
        if ($base === '') {
            $base = 'siswa';
        }

        $candidate = $base;
        $counter = 1;

        while ($this->isUsernameTaken($userModel, $candidate, $excludeId)) {
            $counter++;
            $candidate = $base . $counter;
        }

        return $candidate;
    }

    private function buildUniqueStudentEmail($userModel, string $username, ?int $excludeId = null): string
    {
        $local = mb_strtolower($username);
        $candidate = $local . '@student.local';
        $counter = 1;

        while ($this->isEmailTaken($userModel, $candidate, $excludeId)) {
            $counter++;
            $candidate = $local . $counter . '@student.local';
        }

        return $candidate;
    }

    private function isUsernameTaken($userModel, string $username, ?int $excludeId = null): bool
    {
        $query = $userModel->withDeleted()->where('username', $username);
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }

        return $query->first() !== null;
    }

    private function isEmailTaken($userModel, string $email, ?int $excludeId = null): bool
    {
        $query = $userModel->withDeleted()->where('email', $email);
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }

        return $query->first() !== null;
    }

    public function apiDownloadStudentUploadTemplate()
    {
        $format = mb_strtolower((string) $this->request->getGet('format'));
        $format = in_array($format, ['csv', 'xlsx'], true) ? $format : 'xlsx';

        $headers = $this->getStudentUploadTemplateHeaders();
        $sample = $this->getStudentUploadTemplateSample();

        if ($format === 'csv') {
            $content = "\xEF\xBB\xBF" . implode(',', $headers) . "\n" . implode(',', array_map(static function ($value) {
                return '"' . str_replace('"', '""', (string) $value) . '"';
            }, $sample)) . "\n";

            return $this->response
                ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
                ->setHeader('Content-Disposition', 'attachment; filename="template-upload-siswa.csv"')
                ->setBody($content);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($sample, null, 'A2');

        $columnCount = count($headers);
        for ($index = 1; $index <= $columnCount; $index++) {
            $columnLetter = Coordinate::stringFromColumnIndex($index);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $content = (string) ob_get_clean();
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template-upload-siswa.xlsx"')
            ->setBody($content);
    }

    public function apiImportStudents()
    {
        try {
            $file = $this->request->getFile('file');

            if (!$file || !$file->isValid()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'File upload tidak valid'
                ]);
            }

            $extension = mb_strtolower((string) $file->getExtension());
            if (!in_array($extension, ['csv', 'xlsx', 'xls'], true)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Format file harus XLSX/XLS atau CSV'
                ]);
            }

            [$header, $rows] = $this->readStudentUploadRows($file->getTempName(), $extension);
            if (empty($header)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'File upload kosong atau tidak terbaca'
                ]);
            }

            $expectedHeaderMap = $this->getExpectedStudentUploadHeaderMap();

            $indexMap = [];
            foreach ($header as $index => $col) {
                $normalized = $this->normalizeUploadHeader($col);
                if (isset($expectedHeaderMap[$normalized])) {
                    $indexMap[$expectedHeaderMap[$normalized]] = $index;
                }
            }

            $required = ['name', 'nipd', 'gender', 'class_name'];
            $missing = [];
            foreach ($required as $requiredField) {
                if (!isset($indexMap[$requiredField])) {
                    $missing[] = $requiredField;
                }
            }

            if (!empty($missing)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Header file tidak sesuai. Wajib ada: Nama, NIPD, JK, Rombel Saat Ini',
                    'missing' => $missing,
                ]);
            }

            $classCache = [];
            $classes = $this->classModel->findAll();
            foreach ($classes as $class) {
                $classCache[mb_strtolower(trim((string) $class['name']))] = (int) $class['id'];
            }

            $inserted = 0;
            $updated = 0;
            $failed = 0;
            $errors = [];
            foreach ($rows as $rowIndex => $row) {
                $line = $rowIndex + 2;

                if ($this->isEmptyCsvRow($row)) {
                    continue;
                }

                try {
                    $name = $this->csvValue($row, $indexMap, 'name');
                    $nipd = $this->csvValue($row, $indexMap, 'nipd');
                    $className = $this->csvValue($row, $indexMap, 'class_name');
                    $gender = $this->normalizeGenderValue($this->csvValue($row, $indexMap, 'gender'));

                    if ($name === '' || $nipd === '' || $className === '' || $gender === null) {
                        throw new \RuntimeException('Kolom wajib (Nama, NIPD, JK, Rombel Saat Ini) tidak valid');
                    }

                    $classKey = mb_strtolower(trim($className));
                    if (!isset($classCache[$classKey])) {
                        $classId = $this->classModel->insert([
                            'name' => trim($className),
                        ]);
                        $classCache[$classKey] = (int) $classId;
                    }

                    $birthDate = $this->parseFlexibleDate($this->csvValue($row, $indexMap, 'birth_date'));
                    $phone = $this->csvValue($row, $indexMap, 'phone');

                    $studentData = [
                        'nis' => $nipd,
                        'nipd' => $nipd,
                        'name' => $name,
                        'class_id' => $classCache[$classKey],
                        'gender' => $gender,
                        'birth_place' => $this->csvValue($row, $indexMap, 'birth_place'),
                        'birth_date' => $birthDate,
                        'nisn' => $this->csvValue($row, $indexMap, 'nisn'),
                        'nik' => $this->csvValue($row, $indexMap, 'nik'),
                        'religion' => $this->csvValue($row, $indexMap, 'religion'),
                        'address' => $this->csvValue($row, $indexMap, 'address'),
                        'rt' => $this->csvValue($row, $indexMap, 'rt'),
                        'rw' => $this->csvValue($row, $indexMap, 'rw'),
                        'kelurahan' => $this->csvValue($row, $indexMap, 'kelurahan'),
                        'kecamatan' => $this->csvValue($row, $indexMap, 'kecamatan'),
                        'phone' => $phone,
                        'parent_phone' => $phone,
                        'father_name' => $this->csvValue($row, $indexMap, 'father_name'),
                        'mother_name' => $this->csvValue($row, $indexMap, 'mother_name'),
                        'active' => 1,
                    ];

                    $existing = $this->studentModel->withDeleted()->where('nis', $nipd)->first();

                    if ($existing) {
                        $this->studentModel->update((int) $existing['id'], $studentData);
                        if (!empty($existing['deleted_at'])) {
                            $this->studentModel->builder()
                                ->where('id', (int) $existing['id'])
                                ->update(['deleted_at' => null]);
                        }
                        $updated++;
                    } else {
                        $this->studentModel->insert($studentData);
                        $inserted++;
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = [
                        'line' => $line,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Upload data siswa selesai diproses',
                'data' => [
                    'inserted' => $inserted,
                    'updated' => $updated,
                    'failed' => $failed,
                    'errors' => array_slice($errors, 0, 50),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal import siswa: ' . $e->getMessage()
            ]);
        }
    }

    private function getStudentUploadTemplateHeaders(): array
    {
        return [
            'Nama',
            'NIPD',
            'JK',
            'NISN',
            'Tempat Lahir',
            'Tanggal Lahir',
            'NIK',
            'Agama',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'HP',
            'Nama Ayah',
            'Nama Ibu',
            'Rombel Saat Ini',
        ];
    }

    private function getStudentUploadTemplateSample(): array
    {
        return [
            'Budi Santoso',
            '4428',
            'L',
            '3186005758',
            'Jakarta',
            '15 Maret 2018',
            '3174051503180003',
            'Islam',
            'Jl. Kemandoan VIII',
            '7',
            '9',
            'Grogol Utara',
            'Kec. Kebayoran Lama',
            '081228258902',
            'Lukman Hakim',
            'Nida Ayu Kurniawati',
            'Kelas 1 A',
        ];
    }

    private function getExpectedStudentUploadHeaderMap(): array
    {
        return [
            'nama' => 'name',
            'nipd' => 'nipd',
            'jk' => 'gender',
            'nisn' => 'nisn',
            'tempat lahir' => 'birth_place',
            'tanggal lahir' => 'birth_date',
            'nik' => 'nik',
            'agama' => 'religion',
            'alamat' => 'address',
            'rt' => 'rt',
            'rw' => 'rw',
            'kelurahan' => 'kelurahan',
            'kecamatan' => 'kecamatan',
            'hp' => 'phone',
            'nama ayah' => 'father_name',
            'nama ibu' => 'mother_name',
            'rombel saat ini' => 'class_name',
        ];
    }

    private function readStudentUploadRows(string $path, string $extension): array
    {
        if ($extension === 'csv') {
            $handle = fopen($path, 'rb');
            if ($handle === false) {
                return [[], []];
            }

            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                return [[], []];
            }

            $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', (string) $firstLine);
            $header = str_getcsv($firstLine, $delimiter);

            $rows = [];
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $row;
            }

            fclose($handle);
            return [$header, $rows];
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $allRows = $sheet->toArray('', true, true, false);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if (empty($allRows)) {
            return [[], []];
        }

        $header = array_shift($allRows);
        if (!is_array($header)) {
            $header = [];
        }

        return [$header, $allRows];
    }

    private function normalizeUploadHeader(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', trim($value));
        $value = mb_strtolower((string) $value);
        return preg_replace('/\s+/', ' ', $value);
    }

    private function csvValue(array $row, array $indexMap, string $key): string
    {
        if (!isset($indexMap[$key])) {
            return '';
        }

        $index = $indexMap[$key];
        if (!array_key_exists($index, $row)) {
            return '';
        }

        return trim((string) $row[$index]);
    }

    private function isEmptyCsvRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeGenderValue(string $value): ?string
    {
        $normalized = mb_strtolower(trim($value));
        if ($normalized === '') {
            return null;
        }

        if (in_array($normalized, ['l', 'lk', 'laki-laki', 'laki laki'], true)) {
            return 'L';
        }

        if (in_array($normalized, ['p', 'pr', 'perempuan'], true)) {
            return 'P';
        }

        return null;
    }

    private function parseFlexibleDate($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $directFormats = ['Y-m-d', 'd-m-Y', 'd/m/Y'];
        foreach ($directFormats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        $indoMonths = [
            'januari' => 'January',
            'februari' => 'February',
            'maret' => 'March',
            'april' => 'April',
            'mei' => 'May',
            'juni' => 'June',
            'juli' => 'July',
            'agustus' => 'August',
            'september' => 'September',
            'oktober' => 'October',
            'november' => 'November',
            'desember' => 'December',
        ];

        $normalized = mb_strtolower($value);
        foreach ($indoMonths as $indo => $eng) {
            $normalized = str_replace($indo, $eng, $normalized);
        }

        $timestamp = strtotime($normalized);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }

    /**
     * API: Get all classes
     */
    public function apiGetClasses()
    {
        try {
            $classes = $this->classModel
                ->select('classes.*, users.full_name as teacher_name, (SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND students.deleted_at IS NULL) as student_count')
                ->join('users', 'users.id = classes.teacher_id', 'left')
                ->findAll();

            $classes = $this->sortClassesForDisplay($classes);

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

    private function sortClassesForDisplay(array $classes): array
    {
        usort($classes, function (array $left, array $right): int {
            $leftKey = $this->buildClassSortKey($left);
            $rightKey = $this->buildClassSortKey($right);

            for ($i = 0; $i < count($leftKey); $i++) {
                if ($leftKey[$i] < $rightKey[$i]) {
                    return -1;
                }

                if ($leftKey[$i] > $rightKey[$i]) {
                    return 1;
                }
            }

            return 0;
        });

        return $classes;
    }

    private function buildClassSortKey(array $class): array
    {
        $name = trim((string) ($class['name'] ?? ''));
        $normalized = mb_strtolower($name);

        if (preg_match('/kelas\s*(\d+)\s*([a-z])?/i', $normalized, $matches)) {
            $grade = isset($matches[1]) ? (int) $matches[1] : 999;
            $section = isset($matches[2]) && $matches[2] !== '' ? (ord(strtoupper($matches[2])) - 64) : 0;
            return [0, $grade, $section, $normalized];
        }

        return [1, 999, 999, $normalized];
    }

    /**
     * API: Get attendance logs
     */
    public function apiGetAttendanceLogs()
    {
        try {
            log_message('debug', 'apiGetAttendanceLogs called');

            $date = $this->request->getGet('date');
            $deviceId = $this->request->getGet('device_id');
            $studentId = $this->request->getGet('student_id');
            $limit = $this->request->getGet('limit') ?? 100;

            log_message('debug', 'Params: date=' . $date . ', deviceId=' . $deviceId . ', limit=' . $limit);

            $builder = $this->attendanceLogModel
                ->select('attendance_logs.*, students.nis, students.name as student_name, devices.name as device_name, devices.sn as device_sn')
                ->join('students', 'students.id = attendance_logs.student_id', 'left')
                ->join('devices', 'devices.id = attendance_logs.device_id', 'left')
                ->orderBy('attendance_logs.att_time', 'DESC')
                ->limit((int)$limit);

            if ($date) {
                $builder->where('DATE(attendance_logs.att_time)', $date);
            }

            if ($deviceId && $deviceId !== 'all') {
                $builder->where('attendance_logs.device_id', $deviceId);
            }

            if ($studentId) {
                $builder->where('attendance_logs.student_id', $studentId);
            }

            log_message('debug', 'Executing query...');
            $logs = $builder->findAll();
            log_message('debug', 'Query result: ' . count($logs) . ' records');

            // Debug: log the actual SQL query
            log_message('debug', 'SQL: ' . $this->attendanceLogModel->getLastQuery());

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $logs,
                'total' => count($logs),
                'debug' => [
                    'date_filter' => $date,
                    'device_filter' => $deviceId,
                    'limit' => $limit
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'apiGetAttendanceLogs error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil log absensi: ' . $e->getMessage(),
                'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
            ]);
        }
    }

    /**
     * API: Get attendance records for a class on a date
     */
    public function apiGetAttendance()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $date = $this->request->getGet('date') ?? date('Y-m-d');

            if (!$classId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'class_id diperlukan'
                ]);
            }

            // Get attendance summaries for students in this class on this date
            $db = \Config\Database::connect();
            $records = $db->table('attendance_summaries AS a')
                ->select('a.id, a.student_id, a.status, a.notes, a.date')
                ->join('students AS s', 's.id = a.student_id')
                ->where('s.class_id', $classId)
                ->where('a.date', $date)
                ->where('s.active', 1)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $records
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Save attendance records (bulk)
     */
    public function apiSaveAttendance()
    {
        try {
            $json = $this->request->getJSON(true);
            $date = $json['date'] ?? date('Y-m-d');
            $classId = $json['class_id'] ?? null;
            $records = $json['records'] ?? [];

            if (!$classId || empty($records)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'class_id dan records diperlukan'
                ]);
            }

            $saved = 0;
            $updated = 0;

            foreach ($records as $record) {
                $studentId = $record['student_id'];
                $status = $record['status'];
                $existingId = $record['id'] ?? null;

                $data = [
                    'student_id' => $studentId,
                    'date' => $date,
                    'status' => $status,
                    'notes' => $record['notes'] ?? null,
                ];

                if ($existingId) {
                    // Update existing record
                    $this->attendanceSummaryModel->update($existingId, $data);
                    $updated++;
                } else {
                    // Check if record already exists
                    $existing = $this->attendanceSummaryModel
                        ->where('student_id', $studentId)
                        ->where('date', $date)
                        ->first();

                    if ($existing) {
                        $this->attendanceSummaryModel->update($existing['id'], $data);
                        $updated++;
                    } else {
                        $this->attendanceSummaryModel->insert($data);
                        $saved++;
                    }
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menyimpan kehadiran ({$saved} baru, {$updated} diperbarui)",
                'saved' => $saved,
                'updated' => $updated
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'apiSaveAttendance error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan kehadiran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get rekap attendance for a class in a month
     */
    public function apiGetRekap()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $month = $this->request->getGet('month');
            $year = $this->request->getGet('year');

            if (!$classId || !$month || !$year) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'class_id, month, dan year diperlukan'
                ]);
            }

            $db = \Config\Database::connect();

            // Get class info
            $class = $this->classModel->find($classId);
            if (!$class) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Kelas tidak ditemukan'
                ]);
            }

            // Get students in this class
            $students = $db->table('students')
                ->select('id, nis, name')
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('nis', 'ASC')
                ->get()
                ->getResultArray();

            // Get all dates in the month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $dates = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dates[] = [
                    'date' => sprintf('%04d-%02d-%02d', $year, $month, $day)
                ];
            }

            // Get all attendance records for this class in this month
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

            $attendanceRecords = $db->table('attendance_summaries AS a')
                ->select('a.student_id, a.date, a.status')
                ->join('students AS s', 's.id = a.student_id')
                ->where('s.class_id', $classId)
                ->where('a.date >=', $startDate)
                ->where('a.date <=', $endDate)
                ->get()
                ->getResultArray();

            // Organize attendance by student and date
            $attendance = [];
            foreach ($attendanceRecords as $record) {
                $studentId = $record['student_id'];
                $date = $record['date'];
                $status = $record['status'];

                if (!isset($attendance[$studentId])) {
                    $attendance[$studentId] = [];
                }
                $attendance[$studentId][$date] = $status;
            }

            // Get homeroom teacher (if any)
            $homeroomTeacher = '-';
            if (!empty($class['homeroom_teacher_id'])) {
                $teacher = $db->table('users')
                    ->select('name')
                    ->where('id', $class['homeroom_teacher_id'])
                    ->get()
                    ->getRowArray();
                if ($teacher) {
                    $homeroomTeacher = $teacher['name'];
                }
            }

            // Academic year calculation
            $academicYear = $month >= 7 ? $year . '/' . ($year + 1) : ($year - 1) . '/' . $year;

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'class_name' => $class['name'],
                    'academic_year' => $academicYear,
                    'homeroom_teacher' => $homeroomTeacher,
                    'month' => (int)$month,
                    'year' => (int)$year,
                    'students' => $students,
                    'dates' => $dates,
                    'attendance' => $attendance
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'apiGetRekap error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data rekap: ' . $e->getMessage()
            ]);
        }
    }

            // ==================== Shift API Methods ====================

    /**
     * API: Get all shifts (with assigned classes)
     */
    public function apiGetShifts()
    {
        try {
            $shifts = $this->shiftModel->findAll();

            // Get classes for each shift
            $db = \Config\Database::connect();
            foreach ($shifts as &$shift) {
                $shift['classes'] = $db->table('classes')
                    ->select('id, name')
                    ->where('shift_id', $shift['id'])
                    ->where('deleted_at', null)
                    ->get()
                    ->getResultArray();
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $shifts
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data shift: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get single shift
     */
    public function apiGetShift($id)
    {
        try {
            $shift = $this->shiftModel->find($id);
            if (!$shift) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Shift tidak ditemukan'
                ]);
            }

            // Get assigned classes
            $db = \Config\Database::connect();
            $shift['classes'] = $db->table('classes')
                ->select('id, name')
                ->where('shift_id', $shift['id'])
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $shift
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data shift: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Create shift
     */
    public function apiCreateShift()
    {
        try {
            $json = $this->request->getJSON(true);

            $data = [
                'name' => $json['name'] ?? '',
                'check_in_start' => $json['check_in_start'] ?? '',
                'check_in_end' => $json['check_in_end'] ?? $json['check_in_start'] ?? '',
                'check_out_start' => $json['check_out_start'] ?? '',
                'check_out_end' => $json['check_out_end'] ?? $json['check_out_start'] ?? '',
                'late_tolerance' => (int)($json['late_tolerance'] ?? 15),
                'is_active' => isset($json['is_active']) ? (int)$json['is_active'] : 1,
            ];

            if (empty($data['name']) || empty($data['check_in_start']) || empty($data['check_out_start'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Nama, jam masuk, dan jam pulang wajib diisi'
                ]);
            }

            $id = $this->shiftModel->insert($data);

            // Assign classes if provided
            if (!empty($json['class_ids'])) {
                $this->assignClassesToShift($id, $json['class_ids']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift berhasil ditambahkan',
                'data' => $this->shiftModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menambah shift: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Update shift
     */
    public function apiUpdateShift($id)
    {
        try {
            $shift = $this->shiftModel->find($id);
            if (!$shift) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Shift tidak ditemukan'
                ]);
            }

            $json = $this->request->getJSON(true);

            $data = [];
            if (isset($json['name'])) $data['name'] = $json['name'];
            if (isset($json['check_in_start'])) $data['check_in_start'] = $json['check_in_start'];
            if (isset($json['check_in_end'])) $data['check_in_end'] = $json['check_in_end'];
            if (isset($json['check_out_start'])) $data['check_out_start'] = $json['check_out_start'];
            if (isset($json['check_out_end'])) $data['check_out_end'] = $json['check_out_end'];
            if (isset($json['late_tolerance'])) $data['late_tolerance'] = (int)$json['late_tolerance'];
            if (isset($json['is_active'])) $data['is_active'] = (int)$json['is_active'];

            if (!empty($data)) {
                $this->shiftModel->update($id, $data);
            }

            // Update class assignments if provided
            if (isset($json['class_ids'])) {
                $this->assignClassesToShift($id, $json['class_ids']);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift berhasil diperbarui',
                'data' => $this->shiftModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui shift: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete shift
     */
    public function apiDeleteShift($id)
    {
        try {
            $shift = $this->shiftModel->find($id);
            if (!$shift) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Shift tidak ditemukan'
                ]);
            }

            // Unassign classes from this shift
            $db = \Config\Database::connect();
            $db->table('classes')->where('shift_id', $id)->update(['shift_id' => null]);

            $this->shiftModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Shift berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus shift: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper: Assign classes to a shift
     */
    private function assignClassesToShift($shiftId, array $classIds)
    {
        $db = \Config\Database::connect();

        // Remove old assignments for this shift
        $db->table('classes')->where('shift_id', $shiftId)->update(['shift_id' => null]);

        // Assign new classes
        if (!empty($classIds)) {
            $db->table('classes')->whereIn('id', $classIds)->update(['shift_id' => $shiftId]);
        }
    }

    // ==========================================
    // School Holidays API
    // ==========================================

    public function apiGetSchoolHolidays()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');

        $holidays = $this->schoolHolidayModel->getHolidaysByMonth((int) $year, (int) $month);

        return $this->response->setJSON([
            'success' => true,
            'data' => $holidays
        ]);
    }

    public function apiSaveSchoolHoliday()
    {
        $json = $this->request->getJSON(true);
        $date = $json['date'] ?? null;
        $name = $json['name'] ?? 'Libur Sekolah';
        $isHoliday = $json['is_holiday'] ?? false;

        if (!$date) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tanggal wajib diisi']);
        }

        // Check if school holiday exists for this date
        $existing = $this->schoolHolidayModel->where('date', $date)->where('type', 'school')->first();

        if ($isHoliday) {
            if ($existing) {
                // Update
                $this->schoolHolidayModel->update($existing['id'], ['name' => $name]);
            } else {
                // Create
                $this->schoolHolidayModel->insert([
                    'date' => $date,
                    'name' => $name,
                    'type' => 'school',
                ]);
            }
        } else {
            // Remove school holiday
            if ($existing) {
                $this->schoolHolidayModel->delete($existing['id']);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Berhasil disimpan']);
    }

    /**
     * API: Create new class
     */
    public function apiCreateClass()
    {
        try {
            $json = $this->request->getJSON(true);
            $teacherId = !empty($json['teacher_id']) ? (int)$json['teacher_id'] : null;

            // If teacher is already assigned to another class, release previous assignment
            if ($teacherId) {
                $existingClass = $this->classModel->where('teacher_id', $teacherId)->first();
                if ($existingClass) {
                    $this->classModel->update((int) $existingClass['id'], [
                        'teacher_id' => null,
                        'homeroom_teacher' => '',
                    ]);
                }
            }

            // Get teacher name for homeroom_teacher field
            $homeroomTeacher = $json['homeroom_teacher'] ?? '';
            if ($teacherId) {
                $userModel = model(\App\Models\UserModel::class);
                $teacher = $userModel->find($teacherId);
                if ($teacher) {
                    $homeroomTeacher = $teacher['full_name'];
                }
            }

            $data = [
                'name' => $json['name'] ?? '',
                'grade' => $json['level'] ?? '',
                'homeroom_teacher' => $homeroomTeacher,
                'teacher_id' => $teacherId,
                'year' => $json['academic_year'] ?? '',
            ];
            $id = $this->classModel->insert($data);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kelas berhasil ditambahkan',
                'data' => $this->classModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambah kelas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Update class
     */
    public function apiUpdateClass($id)
    {
        try {
            $json = $this->request->getJSON(true);
            $data = [];
            if (isset($json['name'])) $data['name'] = $json['name'];
            if (isset($json['level'])) $data['grade'] = $json['level'];
            if (isset($json['academic_year'])) $data['year'] = $json['academic_year'];

            // Handle teacher selection
            if (isset($json['teacher_id'])) {
                $teacherId = !empty($json['teacher_id']) ? (int)$json['teacher_id'] : null;

                // If teacher is already assigned to another class, release previous assignment
                if ($teacherId) {
                    $existingClass = $this->classModel->where('teacher_id', $teacherId)->where('id !=', $id)->first();
                    if ($existingClass) {
                        $this->classModel->update((int) $existingClass['id'], [
                            'teacher_id' => null,
                            'homeroom_teacher' => '',
                        ]);
                    }
                }

                $data['teacher_id'] = $teacherId;

                if ($teacherId) {
                    $userModel = model(\App\Models\UserModel::class);
                    $teacher = $userModel->find($teacherId);
                    if ($teacher) {
                        $data['homeroom_teacher'] = $teacher['full_name'];
                    }
                } else {
                    $data['homeroom_teacher'] = '';
                }
            }

            if (!empty($data)) {
                $this->classModel->update($id, $data);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kelas berhasil diperbarui',
                'data' => $this->classModel->find($id)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui kelas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Delete class
     */
    public function apiDeleteClass($id)
    {
        try {
            $class = $this->classModel->find($id);
            if (!$class) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Kelas tidak ditemukan'
                ]);
            }

            // Check if class has students
            $studentCount = $this->studentModel->where('class_id', $id)->countAllResults();
            if ($studentCount > 0) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => "Kelas tidak dapat dihapus karena masih memiliki {$studentCount} siswa. Pindahkan siswa terlebih dahulu."
                ]);
            }

            $this->classModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kelas berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus kelas: ' . $e->getMessage()
            ]);
        }
    }

    // ==================== 7 Kebiasaan Anak Indonesia ====================

    /**
     * Page: 7 Kebiasaan Anak Indonesia
     */
    public function habitsDaily()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }
        return view('admin/habits_daily', ['activePage' => 'admin/habits-daily']);
    }

    public function habitsMonthly()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }
        return view('admin/habits_monthly', ['activePage' => 'admin/habits-monthly']);
    }

    /**
     * API: Get habit records for a class in a date range
     */
    public function apiGetHabits()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            if (!$classId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'class_id diperlukan'
                ]);
            }

            // Get students in class
            $students = $this->studentModel
                ->where('class_id', $classId)
                ->where('active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();

            if (empty($students)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'students' => [],
                        'habits' => [],
                        'dates' => [],
                    ]
                ]);
            }

            $studentIds = array_column($students, 'id');

            // Get date range for the month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

            // Build dates array
            $dates = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $dayOfWeek = date('N', strtotime($dateStr)); // 1=Mon, 7=Sun
                $dates[] = [
                    'date' => $dateStr,
                    'day_name' => $this->getIndonesianDayName($dayOfWeek),
                    'is_weekend' => $dayOfWeek >= 6,
                ];
            }

            // Get habit records
            $habitModel = model(\App\Models\StudentHabitModel::class);
            $habits = $habitModel
                ->whereIn('student_id', $studentIds)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            // Organize by student_id and date
            $habitMap = [];
            foreach ($habits as $h) {
                $habitMap[$h['student_id']][$h['date']] = $h;
            }

            // Get class info
            $class = $this->classModel->find($classId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'students' => $students,
                    'habits' => $habitMap,
                    'dates' => $dates,
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data kebiasaan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Save habit record for a student on a date
     */
    public function apiSaveHabit()
    {
        try {
            $json = $this->request->getJSON(true);
            $studentId = $json['student_id'] ?? null;
            $date = $json['date'] ?? null;

            if (!$studentId || !$date) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'student_id dan date diperlukan'
                ]);
            }

            $habitModel = model(\App\Models\StudentHabitModel::class);
            $habitColumns = array_keys(\App\Models\StudentHabitModel::getHabitColumns());

            $data = [
                'student_id' => $studentId,
                'date' => $date,
            ];

            foreach ($habitColumns as $col) {
                $data[$col] = isset($json[$col]) ? (int)$json[$col] : 0;
            }

            // Check if record exists
            $existing = $habitModel
                ->where('student_id', $studentId)
                ->where('date', $date)
                ->first();

            if ($existing) {
                $habitModel->update($existing['id'], $data);
            } else {
                $habitModel->insert($data);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data kebiasaan berhasil disimpan'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data kebiasaan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Bulk save habits for a class on a date
     */
    public function apiSaveHabitsBulk()
    {
        try {
            $json = $this->request->getJSON(true);
            $records = $json['records'] ?? [];
            $date = $json['date'] ?? null;

            if (!$date || empty($records)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'date dan records diperlukan'
                ]);
            }

            $habitModel = model(\App\Models\StudentHabitModel::class);
            $habitColumns = array_keys(\App\Models\StudentHabitModel::getHabitColumns());
            $saved = 0;

            foreach ($records as $record) {
                $studentId = $record['student_id'] ?? null;
                if (!$studentId) continue;

                $data = [
                    'student_id' => $studentId,
                    'date' => $date,
                ];

                foreach ($habitColumns as $col) {
                    $data[$col] = isset($record[$col]) ? (int)$record[$col] : 0;
                }

                $existing = $habitModel
                    ->where('student_id', $studentId)
                    ->where('date', $date)
                    ->first();

                if ($existing) {
                    $habitModel->update($existing['id'], $data);
                } else {
                    $habitModel->insert($data);
                }
                $saved++;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Data kebiasaan {$saved} siswa berhasil disimpan"
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data kebiasaan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get habit recap/summary for a class in a month
     */
    public function apiGetHabitRecap()
    {
        try {
            $classId = $this->request->getGet('class_id');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            if (!$classId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'class_id diperlukan'
                ]);
            }

            // Get students in class
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

            // Build dates
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

            // Get habits
            $habitModel = model(\App\Models\StudentHabitModel::class);
            $habits = $habitModel
                ->whereIn('student_id', $studentIds)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            // Per-date summary across all students
            $habitColumns = array_keys(\App\Models\StudentHabitModel::getHabitColumns());
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

            // Get class info
            $class = $this->classModel->find($classId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'class' => $class,
                    'students' => $students,
                    'dates' => $dateSummary,
                    'habit_labels' => \App\Models\StudentHabitModel::getHabitColumns(),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil rekap kebiasaan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get habit data for a specific student across a month
     */
    public function apiGetStudentMonthlyHabits()
    {
        try {
            $studentId = $this->request->getGet('student_id');
            $month = $this->request->getGet('month') ?? date('m');
            $year = $this->request->getGet('year') ?? date('Y');

            if (!$studentId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'student_id diperlukan'
                ]);
            }

            $student = $this->studentModel->find($studentId);
            if (!$student) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Siswa tidak ditemukan'
                ]);
            }

            // Get class info
            $class = $this->classModel->find($student['class_id']);

            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

            // Get habits for this student in the month
            $habitModel = model(\App\Models\StudentHabitModel::class);
            $habits = $habitModel
                ->where('student_id', $studentId)
                ->where('date >=', $startDate)
                ->where('date <=', $endDate)
                ->findAll();

            // Index by date
            $habitsByDate = [];
            foreach ($habits as $h) {
                $habitsByDate[$h['date']] = $h;
            }

            // Build dates with student's habit data
            $habitColumns = array_keys(\App\Models\StudentHabitModel::getHabitColumns());
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
                    'habit_labels' => \App\Models\StudentHabitModel::getHabitColumns(),
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil data kebiasaan siswa: ' . $e->getMessage()
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
}
