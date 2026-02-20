<?php

/**
 * Test script untuk verify database indexes dan performance
 * Run: php spark test:indexes
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestIndexes extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:indexes';
    protected $description = 'Test database indexes and performance';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('===========================================', 'green');
        CLI::write('DATABASE INDEXES TEST', 'green');
        CLI::write('===========================================', 'green');
        CLI::newLine();

        // Test Students table indexes
        CLI::write('📊 Testing Students Table Indexes...', 'yellow');
        $this->testTableIndexes($db, 'students', [
            'idx_students_nis',
            'idx_students_nisn',
            'idx_students_class_id',
            'idx_students_active'
        ]);

        // Test Attendance table indexes
        CLI::write('📊 Testing Attendance Logs Indexes...', 'yellow');
        $this->testTableIndexes($db, 'attendance_logs', [
            'idx_attendance_date',
            'idx_attendance_student_id',
            'idx_attendance_status',
            'idx_attendance_date_student'
        ]);

        // Test Classes table indexes
        CLI::write('📊 Testing Classes Table Indexes...', 'yellow');
        $this->testTableIndexes($db, 'classes', [
            'idx_classes_name'
        ]);

        // Performance tests
        CLI::newLine();
        CLI::write('⚡ PERFORMANCE TESTS', 'green');
        CLI::write('===========================================', 'green');
        
        $this->testQueryPerformance($db);

        CLI::newLine();
        CLI::write('✅ All tests completed!', 'green');
    }

    protected function testTableIndexes($db, string $table, array $expectedIndexes): void
    {
        $query = "SHOW INDEX FROM {$table}";
        $indexes = $db->query($query)->getResultArray();

        $foundIndexes = [];
        foreach ($indexes as $index) {
            $foundIndexes[] = $index['Key_name'];
        }

        $foundIndexes = array_unique($foundIndexes);

        CLI::write("  Table: {$table}", 'cyan');
        
        foreach ($expectedIndexes as $expected) {
            if (in_array($expected, $foundIndexes)) {
                CLI::write("    ✓ {$expected}", 'green');
            } else {
                CLI::write("    ✗ {$expected} (MISSING!)", 'red');
            }
        }

        CLI::write("  Total indexes: " . count($foundIndexes), 'white');
        CLI::newLine();
    }

    protected function testQueryPerformance($db): void
    {
        // Test 1: Student list by class
        CLI::write('Test 1: SELECT students WHERE class_id = 1', 'cyan');
        $start = microtime(true);
        $db->query("SELECT * FROM students WHERE class_id = 1 LIMIT 10")->getResultArray();
        $time1 = (microtime(true) - $start) * 1000;
        CLI::write("  Time: " . number_format($time1, 2) . "ms", $time1 < 50 ? 'green' : 'yellow');
        CLI::newLine();

        // Test 2: Student lookup by NIS
        CLI::write('Test 2: SELECT students WHERE nis = ?', 'cyan');
        $start = microtime(true);
        $db->query("SELECT * FROM students WHERE nis = '12345' LIMIT 1")->getResultArray();
        $time2 = (microtime(true) - $start) * 1000;
        CLI::write("  Time: " . number_format($time2, 2) . "ms", $time2 < 20 ? 'green' : 'yellow');
        CLI::newLine();

        // Test 3: Attendance by date
        CLI::write('Test 3: SELECT attendance WHERE date = ?', 'cyan');
        $start = microtime(true);
        $result = $db->query("SELECT * FROM attendance_logs WHERE date = CURDATE() LIMIT 10");
        if ($result) {
            $result->getResultArray();
            $time3 = (microtime(true) - $start) * 1000;
            CLI::write("  Time: " . number_format($time3, 2) . "ms", $time3 < 50 ? 'green' : 'yellow');
        } else {
            $time3 = 0;
            CLI::write("  Skipped (table empty or error)", 'yellow');
        }
        CLI::newLine();

        // Test 4: Attendance by student and date (composite index)
        CLI::write('Test 4: SELECT attendance WHERE date = ? AND student_id = ?', 'cyan');
        $start = microtime(true);
        $result = $db->query("SELECT * FROM attendance_logs WHERE date = CURDATE() AND student_id = 1 LIMIT 1");
        if ($result) {
            $result->getResultArray();
            $time4 = (microtime(true) - $start) * 1000;
            CLI::write("  Time: " . number_format($time4, 2) . "ms", $time4 < 20 ? 'green' : 'yellow');
        } else {
            $time4 = 0;
            CLI::write("  Skipped (table empty or error)", 'yellow');
        }
        CLI::newLine();

        // Summary
        $avgTime = ($time1 + $time2 + $time3 + $time4) / 4;
        CLI::write('📊 Average Query Time: ' . number_format($avgTime, 2) . 'ms', 'white');
        
        if ($avgTime < 30) {
            CLI::write('🎉 EXCELLENT! Queries are very fast!', 'green');
        } elseif ($avgTime < 100) {
            CLI::write('✓ Good performance', 'yellow');
        } else {
            CLI::write('⚠ Queries might be slow. Check indexes.', 'red');
        }
    }
}
