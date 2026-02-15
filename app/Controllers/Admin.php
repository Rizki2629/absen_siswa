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
        // Users management page
        $userModel = model(\App\Models\UserModel::class);
        $users = $userModel->findAll();

        $data = [
            'title' => 'Manajemen User',
            'pageTitle' => 'Manajemen User',
            'pageDescription' => 'Kelola akun pengguna sistem',
            'activePage' => 'admin/users',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
            'users' => $users,
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
            $students = $this->studentModel
                ->select('students.*, classes.name as class_name')
                ->join('classes', 'classes.id = students.class_id', 'left')
                ->where('students.active', 1)
                ->findAll();

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

    /**
     * API: Get all classes
     */
    public function apiGetClasses()
    {
        try {
            $classes = $this->classModel
                ->select('classes.*, (SELECT COUNT(*) FROM students WHERE students.class_id = classes.id AND students.deleted_at IS NULL) as student_count')
                ->findAll();

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
                'name'            => $json['name'] ?? '',
                'check_in_start'  => $json['check_in_start'] ?? '',
                'check_in_end'    => $json['check_in_end'] ?? $json['check_in_start'] ?? '',
                'check_out_start' => $json['check_out_start'] ?? '',
                'check_out_end'   => $json['check_out_end'] ?? $json['check_out_start'] ?? '',
                'late_tolerance'  => (int)($json['late_tolerance'] ?? 15),
                'is_active'       => isset($json['is_active']) ? (int)$json['is_active'] : 1,
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
            if (isset($json['name']))            $data['name'] = $json['name'];
            if (isset($json['check_in_start']))   $data['check_in_start'] = $json['check_in_start'];
            if (isset($json['check_in_end']))     $data['check_in_end'] = $json['check_in_end'];
            if (isset($json['check_out_start']))  $data['check_out_start'] = $json['check_out_start'];
            if (isset($json['check_out_end']))    $data['check_out_end'] = $json['check_out_end'];
            if (isset($json['late_tolerance']))   $data['late_tolerance'] = (int)$json['late_tolerance'];
            if (isset($json['is_active']))        $data['is_active'] = (int)$json['is_active'];

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
}
