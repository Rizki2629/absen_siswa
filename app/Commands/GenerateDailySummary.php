<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\AttendanceService;

class GenerateDailySummary extends BaseCommand
{
    protected $group       = 'Attendance';
    protected $name        = 'attendance:generate-summary';
    protected $description = 'Generate daily attendance summary for all students';

    public function run(array $params)
    {
        $date = $params[0] ?? date('Y-m-d');

        CLI::write("Generating attendance summary for date: {$date}", 'yellow');

        $attendanceService = new AttendanceService();
        $result = $attendanceService->generateDailySummary($date);

        if ($result['success']) {
            CLI::write("✅ Successfully processed {$result['processed']} students", 'green');
        } else {
            CLI::write("❌ Failed to generate summary", 'red');
        }
    }
}
