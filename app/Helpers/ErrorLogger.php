<?php

namespace App\Helpers;

/**
 * Custom Error Logger for Production
 * Logs errors with context and prevents information leakage
 */
class ErrorLogger
{
    /**
     * Log error with context
     */
    public static function logError(\Throwable $e, array $context = []): void
    {
        $message = sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s\nContext: %s",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString(),
            json_encode($context, JSON_PRETTY_PRINT)
        );

        log_message('error', $message);
    }

    /**
     * Get safe error message for user (no sensitive info)
     */
    public static function getSafeMessage(\Throwable $e): string
    {
        if (ENVIRONMENT === 'production') {
            // Generic messages for production
            $safeMessages = [
                'Database' => 'Terjadi kesalahan koneksi database. Silakan coba lagi.',
                'Permission' => 'Anda tidak memiliki izin untuk mengakses resource ini.',
                'Validation' => 'Data yang dikirim tidak valid.',
                'NotFound' => 'Data tidak ditemukan.',
            ];

            $className = class_basename($e);
            
            foreach ($safeMessages as $key => $message) {
                if (stripos($className, $key) !== false) {
                    return $message;
                }
            }

            return 'Terjadi kesalahan sistem. Tim kami akan segera memperbaikinya.';
        }

        // Show detailed errors in development
        return $e->getMessage();
    }

    /**
     * Log database query performance
     */
    public static function logSlowQuery(string $query, float $executionTime): void
    {
        if ($executionTime > 1.0) { // Log queries slower than 1 second
            log_message('warning', sprintf(
                "Slow Query (%.2fs): %s",
                $executionTime,
                $query
            ));
        }
    }

    /**
     * Log authentication attempts
     */
    public static function logAuthAttempt(string $username, bool $success, string $ip): void
    {
        $status = $success ? 'SUCCESS' : 'FAILED';
        log_message('info', sprintf(
            "Auth attempt [%s] - User: %s, IP: %s",
            $status,
            $username,
            $ip
        ));

        // Log failed attempts with warning level
        if (!$success) {
            log_message('warning', sprintf(
                "Failed login attempt - User: %s, IP: %s",
                $username,
                $ip
            ));
        }
    }

    /**
     * Log suspicious activity
     */
    public static function logSuspiciousActivity(string $activity, array $data): void
    {
        log_message('warning', sprintf(
            "SUSPICIOUS ACTIVITY: %s\nData: %s",
            $activity,
            json_encode($data, JSON_PRETTY_PRINT)
        ));
    }
}
