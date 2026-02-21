#!/usr/bin/env php
<?php
/**
 * Testing Script untuk Aplikasi Absensi Siswa
 * Test: Edit User, Device Connection, Attendance, Habits
 */

// Configuration
$baseUrl = 'http://localhost:8080';
$apiUrl = $baseUrl . '/api/admin';

// Test results
$results = [];
$totalTests = 0;
$passedTests = 0;

// Helper function untuk HTTP request
function apiRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies.txt');
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

function testResult($testName, $passed, $message = '') {
    global $results, $totalTests, $passedTests;
    $totalTests++;
    if ($passed) $passedTests++;
    
    $status = $passed ? '✅ PASS' : '❌ FAIL';
    $results[] = [
        'test' => $testName,
        'passed' => $passed,
        'message' => $message,
        'status' => $status
    ];
    
    echo "$status | $testName";
    if ($message) echo " | $message";
    echo "\n";
}

echo "\n";
echo "==============================================\n";
echo "  TESTING APLIKASI ABSENSI SISWA\n";
echo "==============================================\n\n";

// 1. Login Test
echo "📝 TEST 1: Login Admin\n";
echo "----------------------------------------------\n";
$loginResponse = apiRequest($baseUrl . '/api/auth/login', 'POST', [
    'username' => 'admin',
    'password' => 'admin123'
]);

if ($loginResponse['http_code'] === 200 && isset($loginResponse['body']['status']) && $loginResponse['body']['status'] === 'success') {
    testResult('Login Admin', true, 'Login berhasil');
    $token = $loginResponse['body']['data']['token'] ?? null;
} else {
    testResult('Login Admin', false, 'Login gagal - ' . ($loginResponse['body']['message'] ?? 'Unknown error'));
    echo "\n❌ Testing dihentikan karena login gagal.\n";
    exit(1);
}

echo "\n";

// 2. Get Users Test
echo "📝 TEST 2: Get Users List\n";
echo "----------------------------------------------\n";
$usersResponse = apiRequest($apiUrl . '/users', 'GET', null, $token);

if ($usersResponse['http_code'] === 200 && isset($usersResponse['body']['data'])) {
    $users = $usersResponse['body']['data'];
    testResult('Get Users', true, count($users) . ' users found');
    
    // Ambil user pertama untuk test edit
    $testUserId = $users[0]['id'] ?? null;
    $testUserEmail = $users[0]['email'] ?? null;
} else {
    testResult('Get Users', false, 'Gagal mengambil data users');
    $testUserId = null;
}

echo "\n";

// 3. Edit User Test
if ($testUserId) {
    echo "📝 TEST 3: Edit User (ID: $testUserId)\n";
    echo "----------------------------------------------\n";
    
    // Get user detail
    $userDetailResponse = apiRequest($apiUrl . "/users/$testUserId", 'GET', null, $token);
    
    if ($userDetailResponse['http_code'] === 200) {
        testResult('Get User Detail', true, 'Data user berhasil diambil');
        
        $userData = $userDetailResponse['body']['data'];
        
        // Update user dengan data yang sama (test duplicate email fix)
        $updateResponse = apiRequest($apiUrl . "/users/$testUserId", 'PUT', [
            'name' => $userData['full_name'] ?? $userData['name'],
            'username' => $userData['username'],
            'email' => $userData['email'], // Email yang sama
            'role' => $userData['role'],
            'is_active' => 1
        ], $token);
        
        if ($updateResponse['http_code'] === 200 && $updateResponse['body']['status'] === 'success') {
            testResult('Update User (same email)', true, 'Update berhasil tanpa error duplicate email');
        } else {
            testResult('Update User (same email)', false, $updateResponse['body']['message'] ?? 'Unknown error');
        }
        
    } else {
        testResult('Get User Detail', false, 'Gagal mengambil detail user');
    }
} else {
    testResult('Edit User', false, 'Tidak ada user untuk ditest');
}

echo "\n";

// 4. Reset Password Test
if ($testUserId) {
    echo "📝 TEST 4: Reset Password (ID: $testUserId)\n";
    echo "----------------------------------------------\n";
    
    $resetResponse = apiRequest($apiUrl . "/users/$testUserId/reset-password", 'POST', [
        'new_password' => 'test123456'
    ], $token);
    
    if ($resetResponse['http_code'] === 200 && $resetResponse['body']['status'] === 'success') {
        testResult('Reset Password', true, 'Password berhasil direset');
    } else {
        testResult('Reset Password', false, $resetResponse['body']['message'] ?? 'Unknown error');
    }
}

echo "\n";

// 5. Device Connection Test
echo "📝 TEST 5: Device Connection & Setup\n";
echo "----------------------------------------------\n";

$devicesResponse = apiRequest($apiUrl . '/devices', 'GET', null, $token);

if ($devicesResponse['http_code'] === 200) {
    $devices = $devicesResponse['body']['data'] ?? [];
    testResult('Get Devices', true, count($devices) . ' devices found');
    
    if (count($devices) > 0) {
        $testDevice = $devices[0];
        testResult('Device Available', true, "Device: {$testDevice['name']} ({$testDevice['ip_address']})");
        
        // Test device detail
        $deviceDetailResponse = apiRequest($apiUrl . "/devices/{$testDevice['id']}", 'GET', null, $token);
        
        if ($deviceDetailResponse['http_code'] === 200) {
            testResult('Get Device Detail', true, 'Detail device berhasil diambil');
        } else {
            testResult('Get Device Detail', false, 'Gagal mengambil detail device');
        }
    } else {
        testResult('Device Available', false, 'Tidak ada device terdaftar');
        echo "ℹ️  Tambahkan device di /admin/devices untuk test koneksi\n";
    }
} else {
    testResult('Get Devices', false, 'Gagal mengambil data devices');
}

echo "\n";

// 6. Attendance Recording Test
echo "📝 TEST 6: Attendance Recording\n";
echo "----------------------------------------------\n";

$attendanceResponse = apiRequest($apiUrl . '/attendance', 'GET', null, $token);

if ($attendanceResponse['http_code'] === 200) {
    $attendances = $attendanceResponse['body']['data'] ?? [];
    testResult('Get Attendance Records', true, count($attendances) . ' attendance records found');
    
    if (count($attendances) > 0) {
        $testAttendance = $attendances[0];
        testResult('Attendance Data Available', true, "Student: {$testAttendance['student_name']}, Date: {$testAttendance['date']}");
    } else {
        testResult('Attendance Data Available', false, 'Tidak ada data absensi');
        echo "ℹ️  Test recording dengan tap fingerprint di device\n";
    }
} else {
    testResult('Get Attendance Records', false, 'Gagal mengambil data attendance');
}

// Test attendance logs
$logsResponse = apiRequest($apiUrl . '/attendance-logs', 'GET', null, $token);

if ($logsResponse['http_code'] === 200) {
    $logs = $logsResponse['body']['data'] ?? [];
    testResult('Get Attendance Logs', true, count($logs) . ' logs found');
} else {
    testResult('Get Attendance Logs', false, 'Endpoint logs tidak tersedia atau error');
}

echo "\n";

// 7. Habits Daily Input Test
echo "📝 TEST 7: Habits Daily Input\n";
echo "----------------------------------------------\n";

$habitsResponse = apiRequest($apiUrl . '/habits', 'GET', null, $token);

if ($habitsResponse['http_code'] === 200) {
    $habits = $habitsResponse['body']['data'] ?? [];
    testResult('Get Habits', true, count($habits) . ' habit categories found');
    
    if (count($habits) > 0) {
        testResult('Habits Categories Available', true, 'Ada kategori kebiasaan yang bisa digunakan');
    } else {
        testResult('Habits Categories Available', false, 'Tidak ada kategori kebiasaan');
        echo "ℹ️  Tambahkan kategori kebiasaan di /admin/habits\n";
    }
} else {
    testResult('Get Habits', false, 'Gagal mengambil data habits');
}

// Test habits daily
$habitsDailyResponse = apiRequest($apiUrl . '/habits-daily', 'GET', null, $token);

if ($habitsDailyResponse['http_code'] === 200) {
    $habitsDaily = $habitsDailyResponse['body']['data'] ?? [];
    testResult('Get Habits Daily Records', true, count($habitsDaily) . ' daily records found');
} else {
    testResult('Get Habits Daily Records', false, 'Endpoint habits-daily tidak tersedia atau error');
}

echo "\n";

// Summary
echo "==============================================\n";
echo "  TEST SUMMARY\n";
echo "==============================================\n\n";

echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests ✅\n";
echo "Failed: " . ($totalTests - $passedTests) . " ❌\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

// Detailed results
echo "==============================================\n";
echo "  DETAILED RESULTS\n";
echo "==============================================\n\n";

foreach ($results as $result) {
    echo $result['status'] . " | " . $result['test'];
    if ($result['message']) {
        echo "\n    └─ " . $result['message'];
    }
    echo "\n\n";
}

echo "==============================================\n";
echo "  RECOMMENDATIONS\n";
echo "==============================================\n\n";

// Recommendations berdasarkan hasil test
if ($passedTests < $totalTests) {
    echo "⚠️  Ada " . ($totalTests - $passedTests) . " test yang gagal.\n\n";
    
    echo "Langkah perbaikan:\n";
    echo "1. Cek error log: writable/logs/\n";
    echo "2. Verifikasi database connection\n";
    echo "3. Pastikan semua API endpoints sudah di-register di Routes.php\n";
    echo "4. Test manual di browser untuk debugging\n\n";
}

if ($passedTests === $totalTests) {
    echo "🎉 Semua test berhasil!\n\n";
    echo "Next steps:\n";
    echo "1. Test device connection dengan device fisik\n";
    echo "2. Test attendance recording dengan fingerprint\n";
    echo "3. Test habits input di UI\n";
    echo "4. Load testing dengan multiple users\n\n";
}

echo "==============================================\n\n";

// Cleanup
if (file_exists(__DIR__ . '/cookies.txt')) {
    unlink(__DIR__ . '/cookies.txt');
}
