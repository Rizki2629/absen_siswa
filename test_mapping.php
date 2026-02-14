<?php
// Test device mapping creation
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

echo "Test 1: Check if device_user_map table exists\n";
echo str_repeat('=', 80) . "\n";
$result = $conn->query("SHOW TABLES LIKE 'device_user_map'");
if ($result->num_rows > 0) {
    echo "✓ Table device_user_map exists\n\n";
} else {
    echo "✗ Table device_user_map NOT FOUND!\n\n";
    $conn->close();
    exit(1);
}

echo "Test 2: Check table structure\n";
echo str_repeat('=', 80) . "\n";
$result = $conn->query("DESCRIBE device_user_map");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
}
echo "\n";

echo "Test 3: Check existing mappings\n";
echo str_repeat('=', 80) . "\n";
$result = $conn->query("SELECT COUNT(*) as total FROM device_user_map");
$row = $result->fetch_assoc();
echo "Total mappings: {$row['total']}\n\n";

echo "Test 4: Check device and student data\n";
echo str_repeat('=', 80) . "\n";
$result = $conn->query("SELECT id, name, sn FROM devices LIMIT 5");
echo "Devices:\n";
while ($row = $result->fetch_assoc()) {
    echo "  Device ID: {$row['id']}, Name: {$row['name']}, SN: {$row['sn']}\n";
}
echo "\n";

$result = $conn->query("SELECT id, nis, name FROM students LIMIT 5");
echo "Students:\n";
while ($row = $result->fetch_assoc()) {
    echo "  Student ID: {$row['id']}, NIS: {$row['nis']}, Name: {$row['name']}\n";
}
echo "\n";

echo "Test 5: Try to insert test mapping\n";
echo str_repeat('=', 80) . "\n";
$sql = "INSERT INTO device_user_map (device_id, student_id, device_user_id, privilege_level) 
        VALUES (1, 1, 123456, 0)";
if ($conn->query($sql)) {
    echo "✓ Insert successful! ID: " . $conn->insert_id . "\n";
    // Clean up test data
    $conn->query("DELETE FROM device_user_map WHERE id = " . $conn->insert_id);
    echo "✓ Test data cleaned up\n";
} else {
    echo "✗ Insert failed: " . $conn->error . "\n";
}

$conn->close();
