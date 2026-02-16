<?php

// Simple database check
$host = 'localhost';
$dbname = 'absensi_siswa';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== ALL USERS IN DATABASE ===\n\n";

    $stmt = $pdo->query("SELECT id, username, role, student_id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Username: {$user['username']}\n";
        echo "Role: {$user['role']}\n";
        echo "Student ID: " . ($user['student_id'] ?: 'NULL') . "\n";

        // If has student_id, get NISN
        if ($user['student_id']) {
            $stmt2 = $pdo->prepare("SELECT nama, nis FROM students WHERE id = ?");
            $stmt2->execute([$user['student_id']]);
            $student = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($student) {
                echo "Student Name: {$student['nama']}\n";
                echo "NISN: {$student['nis']}\n";
            }
        }
        echo "\n";
    }

    echo "=== ISSUE ANALYSIS ===\n";
    echo "Expected role for student: 'student'\n";
    echo "Login should redirect to: /student/dashboard\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
