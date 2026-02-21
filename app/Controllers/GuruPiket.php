<?php

namespace App\Controllers;

use App\Models\StudentModel;
use App\Models\AttendanceSummaryModel;
use App\Models\AttendanceLogModel;
use App\Models\AttendanceExceptionModel;

class GuruPiket extends BaseController
{
    protected $studentModel;
    protected $attendanceSummaryModel;
    protected $attendanceLogModel;
    protected $exceptionModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->attendanceSummaryModel = new AttendanceSummaryModel();
        $this->attendanceLogModel = new AttendanceLogModel();
        $this->exceptionModel = new AttendanceExceptionModel();
    }

    public function dashboard()
    {
        // Check if user is guru_piket
        if (session()->get('role') !== 'guru_piket') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        // Get statistics for today
        $today = date('Y-m-d');
        $totalStudents = $this->studentModel->countAll();

        $stats = [
            'present' => $this->attendanceSummaryModel
                ->where('date', $today)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->countAllResults(),
            'sick' => $this->exceptionModel
                ->where('date', $today)
                ->where('exception_type', 'sakit')
                ->countAllResults(),
            'permission' => $this->exceptionModel
                ->where('date', $today)
                ->where('exception_type', 'izin')
                ->countAllResults(),
            'absent' => 0, // Will be calculated
            'not_scanned' => 0, // Will be calculated
        ];

        // Calculate absent and not scanned
        $recorded = $stats['present'] + $stats['sick'] + $stats['permission'];
        $stats['not_scanned'] = $totalStudents - $recorded;
        $stats['absent'] = $stats['not_scanned']; // For simplicity

        $data = [
            'title' => 'Dashboard Guru Piket',
            'pageTitle' => 'Dashboard Guru Piket',
            'pageDescription' => 'Monitoring kehadiran siswa real-time',
            'activePage' => 'guru-piket/dashboard',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Guru Piket'
            ],
            'stats' => $stats,
            'unreadNotifications' => 0
        ];

        return view('dashboard/guru_piket', $data);
    }

    public function monitoring()
    {
        if (session()->get('role') !== 'guru_piket') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        // Real-time monitoring page
        $data = [
            'title' => 'Monitoring Real-time',
            'pageTitle' => 'Monitoring Real-time',
            'pageDescription' => 'Pantau scan siswa secara langsung',
            'activePage' => 'guru-piket/monitoring',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Guru Piket'
            ],
        ];

        return view('guru_piket/monitoring', $data);
    }

    public function dailyRecap()
    {
        if (session()->get('role') !== 'guru_piket') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        // Daily recap page
        $data = [
            'title' => 'Rekap Harian',
            'pageTitle' => 'Rekap Harian',
            'pageDescription' => 'Laporan kehadiran harian',
            'activePage' => 'guru-piket/daily-recap',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Guru Piket'
            ],
        ];

        return view('guru_piket/daily_recap', $data);
    }

    public function exceptions()
    {
        if (session()->get('role') !== 'guru_piket') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        // Input exceptions page
        $data = [
            'title' => 'Input Ketidakhadiran',
            'pageTitle' => 'Input Ketidakhadiran',
            'pageDescription' => 'Input sakit, izin, atau lupa scan',
            'activePage' => 'guru-piket/exceptions',
            'user' => [
                'name' => session()->get('name'),
                'role' => 'Guru Piket'
            ],
        ];

        return view('guru_piket/exceptions', $data);
    }
}
