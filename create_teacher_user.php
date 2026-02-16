<?php

/**
 * Script untuk membuat user teacher untuk testing
 * Jalankan dengan: php create_teacher_user.php
 */

// Konfigurasi database (sesuaikan dengan app/Config/Database.php)
$host = 'localhost';
$dbname = 'absensi_siswa';
$username = 'root';
$password = '';

try {
    // Connect ke database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Koneksi database berhasil!\n\n";

    // Data user teacher
    $userData = [
        'username' => 'guru1',
        'email' => 'guru1@sekolah.com',
        'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'teacher',
        'full_name' => 'Budi Santoso',
        'nip' => '197501012000031001',
        'phone' => '081234567890',
        'is_active' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
    $stmt->execute([
        'username' => $userData['username'],
        'email' => $userData['email']
    ]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "❌ User sudah ada!\n";
        echo "Username: {$existingUser['username']}\n";
        echo "Email: {$existingUser['email']}\n";
        echo "Role: {$existingUser['role']}\n\n";
        echo "Anda bisa login dengan:\n";
        echo "Username: {$existingUser['username']}\n";
        echo "Password: (gunakan password yang sudah dibuat sebelumnya)\n";
        exit(1);
    }

    // Insert user
    $stmt = $pdo->prepare("
    INSERT INTO users (username, email, password_hash, role, full_name, nip, phone, is_active, created_at, updated_at)
    VALUES (:username, :email, :password_hash, :role, :full_name, :nip, :phone, :is_active, :created_at, :updated_at)
");

    $stmt->execute($userData);
    $userId = $pdo->lastInsertId();

    if ($userId) {
        echo "✅ User teacher berhasil dibuat!\n\n";
        echo "=================================\n";
        echo "Detail Login:\n";
        echo "=================================\n";
        echo "URL      : http://localhost:8080\n";
        echo "Username : guru1\n";
        echo "Password : password123\n";
        echo "Role     : Teacher\n";
        echo "=================================\n\n";
        echo "Setelah login, Anda akan diarahkan ke: /teacher/dashboard\n\n";
        echo "⚠️  NOTE: User ini belum menjadi wali kelas.\n";
        echo "   Untuk menjadi wali kelas, set teacher_id di tabel classes.\n";
    } else {
        echo "❌ Gagal membuat user!\n";
        exit(1);
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
