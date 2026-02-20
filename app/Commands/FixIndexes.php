<?php

/**
 * Manual script to add indexes
 * Run: php spark fix:indexes
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixIndexes extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'fix:indexes';
    protected $description = 'Manually add performance indexes to database';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Adding performance indexes...', 'yellow');
        CLI::newLine();

        $indexes = [
            // Students
            ['table' => 'students', 'name' => 'idx_students_nis', 'columns' => 'nis'],
            ['table' => 'students', 'name' => 'idx_students_nisn', 'columns' => 'nisn'],
            ['table' => 'students', 'name' => 'idx_students_class_id', 'columns' => 'class_id'],
            ['table' => 'students', 'name' => 'idx_students_active', 'columns' => 'active'],
            
            // Attendance
            ['table' => 'attendance_logs', 'name' => 'idx_attendance_date', 'columns' => 'date'],
            ['table' => 'attendance_logs', 'name' => 'idx_attendance_student_id', 'columns' => 'student_id'],
            ['table' => 'attendance_logs', 'name' => 'idx_attendance_status', 'columns' => 'status'],
            ['table' => 'attendance_logs', 'name' => 'idx_attendance_date_student', 'columns' => 'date, student_id'],
            
            // Classes
            ['table' => 'classes', 'name' => 'idx_classes_name', 'columns' => 'name'],
            
            // Users
            ['table' => 'users', 'name' => 'idx_users_username', 'columns' => 'username'],
        ];

        // Add habits indexes if table exists
        if ($db->tableExists('student_habits')) {
            $indexes[] = ['table' => 'student_habits', 'name' => 'idx_habits_student_date', 'columns' => 'student_id, date'];
            $indexes[] = ['table' => 'student_habits', 'name' => 'idx_habits_date', 'columns' => 'date'];
        }

        $added = 0;
        $skipped = 0;

        foreach ($indexes as $index) {
            try {
                $sql = "CREATE INDEX {$index['name']} ON {$index['table']}({$index['columns']})";
                $db->query($sql);
                CLI::write("✓ Added: {$index['name']}", 'green');
                $added++;
            } catch (\Exception $e) {
                if (stripos($e->getMessage(), 'Duplicate key name') !== false) {
                    CLI::write("- Exists: {$index['name']}", 'yellow');
                    $skipped++;
                } else {
                    CLI::write("✗ Failed: {$index['name']} - {$e->getMessage()}", 'red');
                }
            }
        }

        CLI::newLine();
        CLI::write("Summary:", 'cyan');
        CLI::write("  Added: {$added}", 'green');
        CLI::write("  Skipped (already exists): {$skipped}", 'yellow');
        CLI::write("  Total: " . count($indexes), 'white');
        CLI::newLine();
        CLI::write("✓ Done!", 'green');
    }
}
