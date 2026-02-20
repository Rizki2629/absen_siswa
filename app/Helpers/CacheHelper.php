<?php

namespace App\Helpers;

use CodeIgniter\Cache\CacheInterface;

/**
 * Cache Helper for easy caching throughout the application
 */
class CacheHelper
{
    protected static ?CacheInterface $cache = null;

    /**
     * Get cache instance
     */
    protected static function getCacheInstance(): CacheInterface
    {
        if (self::$cache === null) {
            self::$cache = \Config\Services::cache();
        }
        return self::$cache;
    }

    /**
     * Remember: Get from cache or execute callback and store result
     * 
     * @param string $key Cache key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Function to execute if cache miss
     * @return mixed
     */
    public static function remember(string $key, int $ttl, callable $callback)
    {
        $cache = self::getCacheInstance();
        
        $data = $cache->get($key);
        
        if ($data !== null) {
            return $data;
        }

        $data = $callback();
        $cache->save($key, $data, $ttl);
        
        return $data;
    }

    /**
     * Cache list of students
     */
    public static function getStudents(int $classId = null, int $ttl = 300): array
    {
        $key = $classId ? "students_class_{$classId}" : 'students_all';
        
        return self::remember($key, $ttl, function () use ($classId) {
            $model = model('StudentModel');
            
            if ($classId) {
                return $model->where('class_id', $classId)
                            ->where('active', 1)
                            ->findAll();
            }
            
            return $model->where('active', 1)->findAll();
        });
    }

    /**
     * Cache list of classes
     */
    public static function getClasses(int $ttl = 600): array
    {
        return self::remember('classes_all', $ttl, function () {
            $model = model('ClassModel');
            return $model->orderBy('name', 'ASC')->findAll();
        });
    }

    /**
     * Cache student data by ID
     */
    public static function getStudent(int $studentId, int $ttl = 300): ?array
    {
        return self::remember("student_{$studentId}", $ttl, function () use ($studentId) {
            $model = model('StudentModel');
            return $model->find($studentId);
        });
    }

    /**
     * Cache attendance stats
     */
    public static function getAttendanceStats(int $studentId, string $month, int $ttl = 300): array
    {
        $key = "attendance_stats_{$studentId}_{$month}";
        
        return self::remember($key, $ttl, function () use ($studentId, $month) {
            $model = model('AttendanceModel');
            
            // Calculate stats (example)
            $startDate = $month . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $logs = $model->where('student_id', $studentId)
                         ->where('date >=', $startDate)
                         ->where('date <=', $endDate)
                         ->findAll();
            
            $stats = [
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'sick' => 0,
                'izin' => 0,
            ];
            
            foreach ($logs as $log) {
                if (isset($stats[$log['status']])) {
                    $stats[$log['status']]++;
                }
            }
            
            return $stats;
        });
    }

    /**
     * Clear cache by key
     */
    public static function forget(string $key): bool
    {
        return self::getCacheInstance()->delete($key);
    }

    /**
     * Clear cache by pattern (e.g., "students_*")
     */
    public static function forgetByPattern(string $pattern): void
    {
        $cache = self::getCacheInstance();
        
        // Get all cache keys (file-based cache)
        if (method_exists($cache, 'getCacheInfo')) {
            $cacheInfo = $cache->getCacheInfo();
            
            foreach ($cacheInfo as $key => $data) {
                if (fnmatch($pattern, $key)) {
                    $cache->delete($key);
                }
            }
        }
    }

    /**
     * Clear student-related cache (call after student update)
     */
    public static function clearStudentCache(int $studentId = null): void
    {
        if ($studentId) {
            self::forget("student_{$studentId}");
            
            // Clear attendance stats for this student
            self::forgetByPattern("attendance_stats_{$studentId}_*");
        }
        
        // Clear students lists
        self::forget('students_all');
        self::forgetByPattern('students_class_*');
    }

    /**
     * Clear class-related cache (call after class update)
     */
    public static function clearClassCache(): void
    {
        self::forget('classes_all');
        self::forget('students_all');
        self::forgetByPattern('students_class_*');
    }

    /**
     * Clear attendance cache (call after attendance update)
     */
    public static function clearAttendanceCache(int $studentId = null): void
    {
        if ($studentId) {
            self::forgetByPattern("attendance_stats_{$studentId}_*");
        } else {
            self::forgetByPattern('attendance_stats_*');
        }
    }

    /**
     * Clear all cache
     */
    public static function flush(): bool
    {
        return self::getCacheInstance()->clean();
    }
}
