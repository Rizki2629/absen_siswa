<?php

/**
 * Test script untuk verify caching system
 * Run: php spark test:cache
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Helpers\CacheHelper;

class TestCache extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:cache';
    protected $description = 'Test caching system functionality and performance';

    public function run(array $params)
    {
        CLI::write('===========================================', 'green');
        CLI::write('CACHING SYSTEM TEST', 'green');
        CLI::write('===========================================', 'green');
        CLI::newLine();

        // Test 1: Basic caching
        CLI::write('Test 1: Basic Cache Operations', 'yellow');
        $this->testBasicCaching();

        // Test 2: Cache helper methods
        CLI::write('Test 2: CacheHelper Methods', 'yellow');
        $this->testCacheHelpers();

        // Test 3: Performance comparison
        CLI::write('Test 3: Performance Comparison (Cached vs Non-cached)', 'yellow');
        $this->testPerformance();

        // Test 4: Cache invalidation
        CLI::write('Test 4: Cache Invalidation', 'yellow');
        $this->testInvalidation();

        CLI::newLine();
        CLI::write('✅ All cache tests completed!', 'green');
    }

    protected function testBasicCaching(): void
    {
        $cache = \Config\Services::cache();

        // Save test
        $result = $cache->save('test_key', 'test_value', 60);
        CLI::write('  ✓ Cache save: ' . ($result ? 'SUCCESS' : 'FAILED'), $result ? 'green' : 'red');

        // Get test
        $value = $cache->get('test_key');
        CLI::write('  ✓ Cache get: ' . ($value === 'test_value' ? 'SUCCESS' : 'FAILED'), $value === 'test_value' ? 'green' : 'red');

        // Delete test
        $result = $cache->delete('test_key');
        CLI::write('  ✓ Cache delete: ' . ($result ? 'SUCCESS' : 'FAILED'), $result ? 'green' : 'red');

        $value = $cache->get('test_key');
        CLI::write('  ✓ Verify deletion: ' . ($value === null ? 'SUCCESS' : 'FAILED'), $value === null ? 'green' : 'red');

        CLI::newLine();
    }

    protected function testCacheHelpers(): void
    {
        // Clear cache first
        CacheHelper::flush();

        // Test remember function
        $start = microtime(true);
        $result = CacheHelper::remember('test_remember', 300, function() {
            usleep(100000); // Simulate slow operation (100ms)
            return ['data' => 'test'];
        });
        $time1 = (microtime(true) - $start) * 1000;
        CLI::write('  First call (cache miss): ' . number_format($time1, 2) . 'ms', 'yellow');

        // Second call should be from cache (fast)
        $start = microtime(true);
        $result = CacheHelper::remember('test_remember', 300, function() {
            usleep(100000);
            return ['data' => 'test'];
        });
        $time2 = (microtime(true) - $start) * 1000;
        CLI::write('  Second call (cache hit): ' . number_format($time2, 2) . 'ms', 'green');

        if ($time2 < $time1 / 2) {
            CLI::write('  ✓ Cache is working! ' . number_format(($time1 - $time2) / $time1 * 100, 1) . '% faster', 'green');
        } else {
            CLI::write('  ✗ Cache might not be working properly', 'red');
        }

        CLI::newLine();
    }

    protected function testPerformance(): void
    {
        $model = model('StudentModel');
        CacheHelper::flush();

        // Without cache
        CLI::write('  Without cache:', 'cyan');
        $times = [];
        for ($i = 0; $i < 3; $i++) {
            $start = microtime(true);
            $students = $model->findAll();
            $time = (microtime(true) - $start) * 1000;
            $times[] = $time;
            CLI::write('    Run ' . ($i + 1) . ': ' . number_format($time, 2) . 'ms', 'white');
        }
        $avgWithoutCache = array_sum($times) / count($times);

        CLI::newLine();

        // With cache
        CLI::write('  With cache:', 'cyan');
        $times = [];
        for ($i = 0; $i < 3; $i++) {
            $start = microtime(true);
            $students = CacheHelper::getStudents();
            $time = (microtime(true) - $start) * 1000;
            $times[] = $time;
            CLI::write('    Run ' . ($i + 1) . ': ' . number_format($time, 2) . 'ms', 'white');
        }
        $avgWithCache = array_sum($times) / count($times);

        CLI::newLine();
        CLI::write('  📊 Average without cache: ' . number_format($avgWithoutCache, 2) . 'ms', 'white');
        CLI::write('  📊 Average with cache: ' . number_format($avgWithCache, 2) . 'ms', 'white');
        
        $improvement = (($avgWithoutCache - $avgWithCache) / $avgWithoutCache) * 100;
        CLI::write('  🚀 Performance improvement: ' . number_format($improvement, 1) . '%', 'green');

        CLI::newLine();
    }

    protected function testInvalidation(): void
    {
        // Set some test cache
        CacheHelper::remember('student_1', 300, fn() => ['id' => 1, 'name' => 'Test']);
        CacheHelper::remember('students_all', 300, fn() => [['id' => 1]]);

        // Check cache exists
        $cache = \Config\Services::cache();
        $before = $cache->get('student_1') !== null;
        CLI::write('  Cache before clear: ' . ($before ? 'EXISTS' : 'NOT FOUND'), $before ? 'green' : 'red');

        // Clear student cache
        CacheHelper::clearStudentCache(1);

        // Check cache cleared
        $after = $cache->get('student_1') === null;
        CLI::write('  Cache after clear: ' . ($after ? 'CLEARED' : 'STILL EXISTS'), $after ? 'green' : 'red');

        CLI::newLine();
    }
}
