<?php
// Direct database query test
$dbConfig = getenv('JAWSDB_URL');
if ($dbConfig) {
    $url = parse_url($dbConfig);
    $host = $url['host'];
    $user = $url['user'];
    $pass = $url['pass'];
    $db = substr($url['path'], 1);
} else {
    die("JAWSDB_URL not found\n");
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Test 1: Simple query\n";
echo str_repeat('=', 80) . "\n";
$result = $conn->query('SELECT COUNT(*) as total FROM attendance_logs');
$row = $result->fetch_assoc();
echo "Total logs: {$row['total']}\n\n";

echo "Test 2: Query with DATE() function\n";
echo str_repeat('=', 80) . "\n";
$sql = "SELECT COUNT(*) as total FROM attendance_logs WHERE DATE(att_time) = '2026-02-15'";
$result = $conn->query($sql);
if (!$result) {
    echo "ERROR: " . $conn->error . "\n";
} else {
    $row = $result->fetch_assoc();
    echo "Logs on 2026-02-15: {$row['total']}\n\n";
}

echo "Test 3: Query with LEFT JOIN\n";
echo str_repeat('=', 80) . "\n";
$sql = "SELECT attendance_logs.*, students.nis, students.name as student_name, devices.name as device_name, devices.sn as device_sn 
        FROM attendance_logs 
        LEFT JOIN students ON students.id = attendance_logs.student_id 
        LEFT JOIN devices ON devices.id = attendance_logs.device_id 
        ORDER BY attendance_logs.att_time DESC 
        LIMIT 5";
$result = $conn->query($sql);
if (!$result) {
    echo "ERROR: " . $conn->error . "\n";
} else {
    echo "Success! Rows: " . $result->num_rows . "\n";
    while ($row = $result->fetch_assoc()) {
        echo "- PIN: {$row['pin']}, Time: {$row['att_time']}, Device: " . ($row['device_name'] ?? 'NULL') . "\n";
    }
}

$conn->close();
