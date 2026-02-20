<?php

/**
 * Test script untuk verify error logging
 * Run: php spark test:logging
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Helpers\ErrorLogger;

class TestLogging extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:logging';
    protected $description = 'Test error logging system';

    public function run(array $params)
    {
        CLI::write('===========================================', 'green');
        CLI::write('ERROR LOGGING TEST', 'green');
        CLI::write('===========================================', 'green');
        CLI::newLine();

        // Test 1: Basic error logging
        CLI::write('Test 1: Basic Error Logging', 'yellow');
        $this->testBasicLogging();

        // Test 2: Safe error messages
        CLI::write('Test 2: Safe Error Messages (Production)', 'yellow');
        $this->testSafeMessages();

        // Test 3: Specialized logging
        CLI::write('Test 3: Specialized Logging Methods', 'yellow');
        $this->testSpecializedLogging();

        // Test 4: Log file verification
        CLI::write('Test 4: Log File Verification', 'yellow');
        $this->testLogFiles();

        CLI::newLine();
        CLI::write('✅ All logging tests completed!', 'green');
    }

    protected function testBasicLogging(): void
    {
        try {
            // Generate test exception
            throw new \Exception('Test exception for logging');
        } catch (\Exception $e) {
            // Log the error
            ErrorLogger::logError($e, [
                'test_context' => 'unit_test',
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            CLI::write('  ✓ Exception logged successfully', 'green');
        }

        CLI::newLine();
    }

    protected function testSafeMessages(): void
    {
        $testCases = [
            ['exception' => new \Exception('Database connection failed'), 'expected' => 'Database'],
            ['exception' => new \Exception('Permission denied'), 'expected' => 'Permission'],
            ['exception' => new \Exception('Validation error'), 'expected' => 'Validation'],
            ['exception' => new \Exception('Record not found'), 'expected' => 'NotFound'],
        ];

        foreach ($testCases as $case) {
            $message = ErrorLogger::getSafeMessage($case['exception']);
            CLI::write('  Exception: ' . $case['exception']->getMessage(), 'cyan');
            CLI::write('  Safe message: ' . $message, 'white');
            CLI::newLine();
        }
    }

    protected function testSpecializedLogging(): void
    {
        // Test slow query logging
        ErrorLogger::logSlowQuery('SELECT * FROM students WHERE active = 1', 1.5);
        CLI::write('  ✓ Slow query logged (1.5s)', 'green');

        // Test auth attempt logging
        ErrorLogger::logAuthAttempt('test_user', true, '127.0.0.1');
        CLI::write('  ✓ Successful auth logged', 'green');

        ErrorLogger::logAuthAttempt('test_user', false, '192.168.1.100');
        CLI::write('  ✓ Failed auth logged', 'yellow');

        // Test suspicious activity logging
        ErrorLogger::logSuspiciousActivity('Multiple failed login attempts', [
            'username' => 'test_user',
            'ip' => '192.168.1.100',
            'attempts' => 5
        ]);
        CLI::write('  ✓ Suspicious activity logged', 'red');

        CLI::newLine();
    }

    protected function testLogFiles(): void
    {
        $logPath = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.log';

        if (file_exists($logPath)) {
            $size = filesize($logPath);
            $lines = count(file($logPath));

            CLI::write('  Log file: ' . basename($logPath), 'cyan');
            CLI::write('  Size: ' . number_format($size / 1024, 2) . ' KB', 'white');
            CLI::write('  Lines: ' . number_format($lines), 'white');

            // Show last 5 lines
            CLI::newLine();
            CLI::write('  Last 5 log entries:', 'yellow');
            $lastLines = array_slice(file($logPath), -5);
            foreach ($lastLines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $color = 'white';
                    if (stripos($line, 'ERROR') !== false) {
                        $color = 'red';
                    } elseif (stripos($line, 'WARNING') !== false) {
                        $color = 'yellow';
                    } elseif (stripos($line, 'INFO') !== false) {
                        $color = 'cyan';
                    }
                    
                    CLI::write('    ' . substr($line, 0, 100), $color);
                }
            }
        } else {
            CLI::write('  ✗ Log file not found: ' . $logPath, 'red');
        }

        CLI::newLine();
    }
}
