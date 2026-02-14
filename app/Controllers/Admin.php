<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\DeviceModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\DeviceUserMapModel;
use App\Models\ClassModel;

class Admin extends BaseController
{
    protected $studentModel;
    protected $deviceModel;
    protected $attendanceSummaryModel;
    protected $attendanceLogModel;
    protected $deviceUserMapModel;
    protected $classModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->deviceModel = new DeviceModel();
        $this->attendanceSummaryModel = new AttendanceSummaryModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->deviceUserMapModel = new DeviceUserMapModel();
        $this->classModel = new ClassModel();
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

        $data = [
            'title' => 'Dashboard Admin',
            'pageTitle' => 'Dashboard Admin',
            'pageDescription' => 'Selamat datang di panel administrator',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
            'stats' => $stats,
            'unreadNotifications' => 0
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
            'pageTitle' => 'Mapping ID Mesin',
            'pageDescription' => 'Hubungkan ID mesin dengan data siswa',
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
        $shiftModel = model(\App\Models\ShiftModel::class);
        $shifts = $shiftModel->findAll();

        $data = [
            'title' => 'Pengaturan Shift',
            'pageTitle' => 'Pengaturan Shift',
            'pageDescription' => 'Kelola jam shift masuk dan pulang',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
            'shifts' => $shifts,
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
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/reports', $data);
    }

    public function attendanceLogs()
    {
        // Attendance logs page
        $data = [
            'title' => 'Log Absensi',
            'pageTitle' => 'Log Absensi',
            'pageDescription' => 'Monitor log absensi real-time dari mesin fingerprint',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Administrator'
            ],
        ];

        return view('admin/attendance_logs', $data);
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
            $date = $this->request->getGet('date');
            $deviceId = $this->request->getGet('device_id');
            $studentId = $this->request->getGet('student_id');
            $limit = $this->request->getGet('limit') ?? 100;

            $builder = $this->attendanceLogModel
                ->select('attendance_logs.*, students.nis, students.name as student_name, devices.name as device_name, devices.sn as device_sn')
                ->join('students', 'students.id = attendance_logs.student_id', 'left')
                ->join('devices', 'devices.id = attendance_logs.device_id', 'left')
                ->orderBy('attendance_logs.att_time', 'DESC')
                ->limit($limit);

            if ($date) {
                $builder->where('DATE(attendance_logs.att_time)', $date);
            }

            if ($deviceId) {
                $builder->where('attendance_logs.device_id', $deviceId);
            }

            if ($studentId) {
                $builder->where('attendance_logs.student_id', $studentId);
            }

            $logs = $builder->findAll();

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $logs,
                'total' => count($logs)
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil log absensi: ' . $e->getMessage()
            ]);
        }
    }
}
