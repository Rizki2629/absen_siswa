<?php
// Check attendance logs with date comparison
require_once __DIR__ . '/vendor/autoload.php';

// Parse DATABASE_URL from environment
$dbUrl = getenv('JAWSDB_URL');
if (!$dbUrl) {
    die("JAWSDB_URL not found\n");
}

preg_match('/mysql:\/\/([^:]+):([^@]+)@([^\/]+)\/(.+)/', $dbUrl, $matches);
$user = $matches[1];
$pass = $matches[2];
$host = $matches[3];
$db   = $matches[4];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== CHECKING ATTENDANCE LOGS DATA ===\n\n";

// Get all logs with date formatting
$sql = "SELECT id, pin, att_time, DATE(att_time) as date_only, status, device_id, student_id 
        FROM attendance_logs 
        ORDER BY att_time DESC 
        LIMIT 10";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " logs:\n";
    echo str_repeat("-", 100) . "\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "  PIN: " . $row['pin'] . "\n";
        echo "  att_time: " . $row['att_time'] . "\n";
        echo "  DATE(att_time): " . $row['date_only'] . "\n";
        echo "  Status: " . $row['status'] . "\n";
        echo "  Student ID: " . ($row['student_id'] ?? 'NULL') . "\n";
        echo str_repeat("-", 100) . "\n";
    }
} else {
    echo "No logs found!\n";
}

echo "\n=== TESTING DATE FILTERS ===\n\n";

// Test with different date formats
$testDates = ['2026-02-15', '2026-02-14', '2026-02-13'];

foreach ($testDates as $testDate) {
    $sql = "SELECT COUNT(*) as count FROM attendance_logs WHERE DATE(att_time) = '$testDate'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "Date '$testDate': " . $row['count'] . " logs\n";
}

echo "\n=== TESTING TODAY'S LOGS ===\n\n";

// Get today in different formats
$sql = "SELECT NOW() as now, CURDATE() as curdate, DATE(NOW()) as date_now";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo "Server NOW(): " . $row['now'] . "\n";
echo "Server CURDATE(): " . $row['curdate'] . "\n";
echo "Server DATE(NOW()): " . $row['date_now'] . "\n";

$conn->close();
