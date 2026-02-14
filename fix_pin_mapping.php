<?php
// Update device_user_id mapping from 123456 to 12345
require_once __DIR__ . '/vendor/autoload.php';

// Parse DATABASE_URL from environment
$dbUrl = getenv('JAWSDB_URL');
if (!$dbUrl) {
    die("JAWSDB_URL not found\n");
}

// Parse URL: mysql://username:password@hostname/database
preg_match('/mysql:\/\/([^:]+):([^@]+)@([^\/]+)\/(.+)/', $dbUrl, $matches);
$user = $matches[1];
$pass = $matches[2];
$host = $matches[3];
$db   = $matches[4];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== UPDATE DEVICE USER MAPPING ===\n\n";

// Check current mapping
$sql = "SELECT id, device_id, device_user_id, student_id FROM device_user_maps WHERE device_user_id = '123456'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "❌ No mapping found with device_user_id = '123456'\n";
    echo "Creating new mapping with PIN 12345...\n";

    // Get student and device IDs
    $sql = "SELECT id FROM students WHERE nis = '123456' LIMIT 1";
    $studentResult = $conn->query($sql);
    $studentRow = $studentResult->fetch_assoc();

    $sql = "SELECT id FROM devices WHERE sn = 'WAE4240200013' LIMIT 1";
    $deviceResult = $conn->query($sql);
    $deviceRow = $deviceResult->fetch_assoc();

    if (!$studentRow || !$deviceRow) {
        die("❌ Student or device not found!\n");
    }

    $studentId = $studentRow['id'];
    $deviceId = $deviceRow['id'];

    // Insert new mapping
    $sql = "INSERT INTO device_user_maps (device_id, student_id, device_user_id, privilege_level, created_at, updated_at) 
            VALUES ($deviceId, $studentId, '12345', 0, NOW(), NOW())";

    if ($conn->query($sql)) {
        echo "✅ New mapping created successfully!\n";
        echo "   Device ID: $deviceId\n";
        echo "   Student ID: $studentId\n";
        echo "   PIN: 12345\n";
    } else {
        echo "❌ Failed to create mapping: " . $conn->error . "\n";
    }
} else {
    $row = $result->fetch_assoc();
    echo "Found existing mapping:\n";
    echo "  ID: " . $row['id'] . "\n";
    echo "  Device ID: " . $row['device_id'] . "\n";
    echo "  Student ID: " . $row['student_id'] . "\n";
    echo "  Current PIN: " . $row['device_user_id'] . "\n";
    echo "\nUpdating PIN from 123456 to 12345...\n\n";

    // Update the mapping
    $sql = "UPDATE device_user_maps SET device_user_id = '12345', updated_at = NOW() WHERE id = " . $row['id'];

    if ($conn->query($sql)) {
        echo "✅ Mapping updated successfully!\n";
        echo "   Old PIN: 123456\n";
        echo "   New PIN: 12345\n";
    } else {
        echo "❌ Failed to update mapping: " . $conn->error . "\n";
    }
}

echo "\n=== VERIFICATION ===\n\n";

// Verify the mapping
$sql = "SELECT dum.id, dum.device_id, dum.device_user_id, dum.student_id,
               s.nis, s.name as student_name,
               d.name as device_name
        FROM device_user_maps dum
        JOIN students s ON dum.student_id = s.id
        JOIN devices d ON dum.device_id = d.id
        WHERE dum.device_user_id = '12345'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✅ Mapping verified:\n";
    echo "   Mapping ID: " . $row['id'] . "\n";
    echo "   Device: " . $row['device_name'] . "\n";
    echo "   PIN: " . $row['device_user_id'] . "\n";
    echo "   Student: " . $row['student_name'] . " (NIS: " . $row['nis'] . ")\n";
} else {
    echo "❌ Verification failed - mapping not found\n";
}

echo "\n=== CHECKING ATTENDANCE LOGS ===\n\n";

// Check how many logs will be matched
$sql = "SELECT COUNT(*) as count FROM attendance_logs WHERE pin = '12345'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo "Found " . $row['count'] . " attendance log(s) with PIN 12345\n";
echo "These logs will now show student name in the UI!\n";

$conn->close();
