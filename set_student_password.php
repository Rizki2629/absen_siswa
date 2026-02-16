<?php

/**
 * Script untuk set password semua siswa menjadi "siswa123"
 * Jalankan dengan: php set_student_password.php
 */

// Konfigurasi database
$host = 'localhost';
$dbname = 'absensi_siswa';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Koneksi database berhasil!\n\n";
    
    // Hash password "siswa123"
    $passwordHash = password_hash('siswa123', PASSWORD_DEFAULT);
    
    // Get all users with role student
    $stmt = $pdo->prepare("SELECT id, username, full_name, role FROM users WHERE role IN ('student', 'siswa')");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($students)) {
        echo "❌ Tidak ada user siswa di database!\n";
        echo "Buat user siswa terlebih dahulu.\n";
        exit(1);
    }
    
    echo "📋 Daftar Siswa yang akan diupdate:\n";
    echo "=====================================\n";
    foreach ($students as $student) {
        echo "- {$student['full_name']} (Username: {$student['username']})\n";
    }
    echo "=====================================\n";
    echo "Total: " . count($students) . " siswa\n\n";
    
    // Update all student passwords
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE role IN ('student', 'siswa')");
    $stmt->execute(['password_hash' => $passwordHash]);
    
    $updatedCount = $stmt->rowCount();
    
    if ($updatedCount > 0) {
        echo "✅ Berhasil mengupdate password {$updatedCount} siswa!\n\n";
        echo "=================================\n";
        echo "📝 Cara Login Siswa:\n";
        echo "=================================\n";
        echo "URL      : http://localhost:8080\n";
        echo "Username : [NISN Siswa] atau [Username]\n";
        echo "Password : siswa123\n";
        echo "=================================\n\n";
        
        // Show example with first student's NISN
        $stmtExample = $pdo->prepare("
            SELECT s.nis, s.name, u.username 
            FROM students s 
            LEFT JOIN users u ON u.student_id = s.id 
            WHERE u.role IN ('student', 'siswa')
            LIMIT 1
        ");
        $stmtExample->execute();
        $example = $stmtExample->fetch(PDO::FETCH_ASSOC);
        
        if ($example) {
            echo "📌 Contoh Login:\n";
            echo "Siswa: {$example['name']}\n";
            echo "Login dengan:\n";
            echo "  - NISN     : {$example['nis']}\n";
            if ($example['username']) {
                echo "  - Username : {$example['username']}\n";
            }
            echo "  - Password : siswa123\n\n";
        }
        
        echo "✅ Semua siswa sekarang bisa login dengan password: siswa123\n";
        echo "💡 Siswa bisa login menggunakan NISN atau Username\n";
    } else {
        echo "❌ Gagal mengupdate password!\n";
        exit(1);
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
