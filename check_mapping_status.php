<?php
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

echo "=== CHECKING DEVICE USER MAPPINGS ===\n\n";

// Check mappings
$sql = "SELECT dum.id, dum.device_id, dum.device_user_id, dum.privilege_level, 
               dum.student_id, s.nis, s.name as student_name,
               d.name as device_name, d.sn
        FROM device_user_maps dum
        LEFT JOIN students s ON dum.student_id = s.id
        LEFT JOIN devices d ON dum.device_id = d.id
        ORDER BY dum.id DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " mapping(s):\n";
    echo str_repeat("-", 100) . "\n";
    while ($row = $result->fetch_assoc()) {
        echo "Mapping ID: " . $row['id'] . "\n";
        echo "  Device: " . $row['device_name'] . " (ID: " . $row['device_id'] . ", SN: " . $row['sn'] . ")\n";
        echo "  Device User ID (PIN): " . $row['device_user_id'] . "\n";
        echo "  Privilege Level: " . $row['privilege_level'] . "\n";
        echo "  Student: " . ($row['student_name'] ?? 'NULL') . " (ID: " . $row['student_id'] . ", NIS: " . ($row['nis'] ?? 'NULL') . ")\n";
        echo str_repeat("-", 100) . "\n";
    }
} else {
    echo "❌ NO MAPPINGS FOUND! User may not have successfully created the mapping.\n";
}

echo "\n=== CHECKING ATTENDANCE LOGS ===\n\n";

// Check all logs
$sql = "SELECT al.id, al.pin, al.check_time, al.status, al.device_id, al.student_id,
               s.nis, s.name as student_name,
               d.name as device_name
        FROM attendance_logs al
        LEFT JOIN students s ON al.student_id = s.id
        LEFT JOIN devices d ON al.device_id = d.id
        ORDER BY al.check_time DESC
        LIMIT 10";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Recent " . $result->num_rows . " log(s):\n";
    echo str_repeat("-", 100) . "\n";
    while ($row = $result->fetch_assoc()) {
        echo "Log ID: " . $row['id'] . "\n";
        echo "  PIN: " . $row['pin'] . "\n";
        echo "  Time: " . $row['check_time'] . "\n";
        echo "  Device: " . $row['device_name'] . " (ID: " . $row['device_id'] . ")\n";
        echo "  Student ID: " . ($row['student_id'] ?? 'NULL') . "\n";
        echo "  Student: " . ($row['student_name'] ?? 'NO STUDENT LINKED') . " (NIS: " . ($row['nis'] ?? 'N/A') . ")\n";
        echo str_repeat("-", 100) . "\n";
    }
} else {
    echo "❌ NO LOGS FOUND!\n";
}

echo "\n=== MATCHING CHECK ===\n\n";

// Check if there are logs that should match mappings
$sql = "SELECT al.id, al.pin, al.check_time, al.student_id,
               dum.id as mapping_id, dum.device_user_id, dum.student_id as mapped_student_id,
               s.name as student_name
        FROM attendance_logs al
        LEFT JOIN device_user_maps dum ON al.device_id = dum.device_id AND al.pin = dum.device_user_id
        LEFT JOIN students s ON dum.student_id = s.id
        WHERE al.student_id IS NULL
        ORDER BY al.check_time DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " log(s) with NULL student_id:\n";
    while ($row = $result->fetch_assoc()) {
        echo "\nLog ID " . $row['id'] . " (PIN: " . $row['pin'] . ", Time: " . $row['check_time'] . "):\n";
        if ($row['mapping_id']) {
            echo "  ⚠️ MAPPING EXISTS! Mapping ID: " . $row['mapping_id'] . "\n";
            echo "  → Should be linked to: " . $row['student_name'] . " (Student ID: " . $row['mapped_student_id'] . ")\n";
            echo "  ❌ But log.student_id is still NULL - needs UPDATE!\n";
        } else {
            echo "  ℹ️ No mapping found for this PIN on this device\n";
        }
    }
}

$conn->close();
