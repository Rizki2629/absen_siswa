<?php

/**
 * Master test script - runs all tests and generates report
 * Run: php spark test:all
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestAll extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:all';
    protected $description = 'Run all system tests (indexes, cache, logging)';

    public function run(array $params)
    {
        $startTime = microtime(true);

        CLI::write('╔═══════════════════════════════════════════╗', 'green');
        CLI::write('║   COMPLETE SYSTEM TEST SUITE              ║', 'green');
        CLI::write('║   Security & Performance Improvements     ║', 'green');
        CLI::write('╚═══════════════════════════════════════════╝', 'green');
        CLI::newLine();

        $results = [];

        // Test 1: Database Indexes
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::write('1️⃣  DATABASE INDEXES TEST', 'cyan');
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::newLine();
        
        try {
            $this->call('test:indexes');
            $results['indexes'] = 'PASSED';
        } catch (\Exception $e) {
            CLI::write('✗ Indexes test failed: ' . $e->getMessage(), 'red');
            $results['indexes'] = 'FAILED';
        }

        CLI::newLine(2);

        // Test 2: Caching System
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::write('2️⃣  CACHING SYSTEM TEST', 'cyan');
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::newLine();
        
        try {
            $this->call('test:cache');
            $results['cache'] = 'PASSED';
        } catch (\Exception $e) {
            CLI::write('✗ Cache test failed: ' . $e->getMessage(), 'red');
            $results['cache'] = 'FAILED';
        }

        CLI::newLine(2);

        // Test 3: Error Logging
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::write('3️⃣  ERROR LOGGING TEST', 'cyan');
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::newLine();
        
        try {
            $this->call('test:logging');
            $results['logging'] = 'PASSED';
        } catch (\Exception $e) {
            CLI::write('✗ Logging test failed: ' . $e->getMessage(), 'red');
            $results['logging'] = 'FAILED';
        }

        CLI::newLine(2);

        // Test 4: Security Configuration
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::write('4️⃣  SECURITY CONFIGURATION TEST', 'cyan');
        CLI::write('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
        CLI::newLine();
        
        $results['security'] = $this->testSecurity();

        CLI::newLine(2);

        // Generate Summary Report
        $this->generateReport($results, microtime(true) - $startTime);
    }

    protected function testSecurity(): string
    {
        $passed = true;

        // Check CSRF
        $filters = config('Filters');
        $csrfEnabled = isset($filters->globals['before']['csrf']);
        CLI::write('  CSRF Protection: ' . ($csrfEnabled ? '✓ ENABLED' : '✗ DISABLED'), $csrfEnabled ? 'green' : 'red');
        $passed = $passed && $csrfEnabled;

        // Check Secure Headers
        $headersEnabled = in_array('secureheaders', $filters->globals['after']);
        CLI::write('  Secure Headers: ' . ($headersEnabled ? '✓ ENABLED' : '✗ DISABLED'), $headersEnabled ? 'green' : 'red');
        $passed = $passed && $headersEnabled;

        // Check Environment
        $isProd = ENVIRONMENT === 'production';
        CLI::write('  Environment: ' . ENVIRONMENT . ($isProd ? ' ✓' : ' ⚠'), $isProd ? 'green' : 'yellow');

        // Check .env.production exists
        $envProdExists = file_exists(ROOTPATH . '.env.production');
        CLI::write('  .env.production: ' . ($envProdExists ? '✓ EXISTS' : '✗ NOT FOUND'), $envProdExists ? 'green' : 'yellow');

        CLI::newLine();

        return $passed ? 'PASSED' : 'WARNING';
    }

    protected function generateReport(array $results, float $totalTime): void
    {
        CLI::write('╔═══════════════════════════════════════════╗', 'green');
        CLI::write('║           TEST SUMMARY REPORT             ║', 'green');
        CLI::write('╚═══════════════════════════════════════════╝', 'green');
        CLI::newLine();

        $allPassed = true;
        foreach ($results as $test => $status) {
            $color = $status === 'PASSED' ? 'green' : ($status === 'WARNING' ? 'yellow' : 'red');
            $icon = $status === 'PASSED' ? '✓' : ($status === 'WARNING' ? '⚠' : '✗');
            
            CLI::write(sprintf('  %s %-20s: %s', $icon, ucfirst($test), $status), $color);
            
            if ($status !== 'PASSED') {
                $allPassed = false;
            }
        }

        CLI::newLine();
        CLI::write('  Total tests: ' . count($results), 'white');
        CLI::write('  Time taken: ' . number_format($totalTime, 2) . 's', 'white');
        CLI::newLine();

        if ($allPassed) {
            CLI::write('🎉 ALL TESTS PASSED! System is ready for production.', 'green');
        } else {
            CLI::write('⚠️  Some tests failed or have warnings. Please review.', 'yellow');
        }

        CLI::newLine();

        // Action items
        CLI::write('📋 ACTION ITEMS:', 'yellow');
        
        if ($results['security'] !== 'PASSED') {
            CLI::write('  • Update Heroku config vars for production', 'white');
            CLI::write('    heroku config:set CI_ENVIRONMENT=production', 'cyan');
        }

        if (ENVIRONMENT !== 'production') {
            CLI::write('  • Change environment to production before deploy', 'white');
        }

        CLI::write('  • Monitor logs: heroku logs --tail', 'white');
        CLI::write('  • Test application in browser', 'white');
        CLI::write('  • Verify performance improvements', 'white');

        CLI::newLine();
    }
}
