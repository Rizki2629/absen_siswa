<?php

// Check notifications table structure
$host = 'localhost';
$dbname = 'absensi_siswa';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== NOTIFICATIONS TABLE STRUCTURE ===\n\n";

    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    $tableExists = $stmt->fetch();

    if (!$tableExists) {
        echo "❌ Table 'notifications' doesn't exist!\n";
        exit;
    }

    // Get columns
    $stmt = $pdo->query("DESCRIBE notifications");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Columns:\n";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Key']}\n";
    }

    echo "\n=== SOLUTION ===\n";
    $hasIsRead = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'is_read') {
            $hasIsRead = true;
            break;
        }
    }

    if (!$hasIsRead) {
        echo "❌ Column 'is_read' does NOT exist\n";
        echo "✅ Fix: Comment out unreadNotifications query in Student.php\n";
    } else {
        echo "✅ Column 'is_read' exists\n";
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
