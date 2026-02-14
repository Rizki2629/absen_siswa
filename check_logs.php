<?php
// Direct database query
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

$result = $conn->query('SELECT * FROM attendance_logs ORDER BY att_time DESC LIMIT 10');

echo "Recent Attendance Logs:\n";
echo str_repeat('=', 80) . "\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}\n";
    echo "PIN: {$row['pin']}\n";
    echo "Time: {$row['att_time']}\n";
    echo "Status: {$row['status']}\n";
    echo "Device ID: {$row['device_id']}\n";
    echo "Student ID: " . ($row['student_id'] ?? 'NULL') . "\n";
    echo str_repeat('-', 80) . "\n";
}

echo "\nTotal logs: " . $result->num_rows . "\n";
$conn->close();
