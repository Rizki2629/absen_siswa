<?php

/**
 * Script untuk set guru sebagai wali kelas
 * Jalankan dengan: php set_teacher_class.php
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

    // Get user ID guru1
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE username = 'guru1' AND role = 'teacher'");
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teacher) {
        echo "❌ User guru1 tidak ditemukan! Jalankan create_teacher_user.php terlebih dahulu.\n";
        exit(1);
    }

    echo "Guru ditemukan:\n";
    echo "- ID: {$teacher['id']}\n";
    echo "- Nama: {$teacher['full_name']}\n\n";

    // Get all classes
    $stmt = $pdo->query("SELECT id, name, teacher_id FROM classes ORDER BY name");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($classes)) {
        echo "❌ Tidak ada kelas di database! Buat kelas terlebih dahulu.\n";
        exit(1);
    }

    echo "Daftar Kelas:\n";
    echo "---------------------------------------\n";
    foreach ($classes as $class) {
        $teacherStatus = $class['teacher_id'] ? "Sudah ada wali kelas (ID: {$class['teacher_id']})" : "Belum ada wali kelas";
        echo "ID {$class['id']}: {$class['name']} - {$teacherStatus}\n";
    }
    echo "---------------------------------------\n\n";

    // Set guru1 sebagai wali kelas untuk kelas pertama yang belum punya wali kelas
    $targetClass = null;
    foreach ($classes as $class) {
        if (!$class['teacher_id']) {
            $targetClass = $class;
            break;
        }
    }

    if (!$targetClass) {
        // Jika semua kelas sudah punya wali kelas, ambil kelas pertama
        $targetClass = $classes[0];
        echo "⚠️  Semua kelas sudah punya wali kelas. Akan update kelas pertama.\n\n";
    }

    // Update teacher_id
    $stmt = $pdo->prepare("UPDATE classes SET teacher_id = :teacher_id WHERE id = :class_id");
    $stmt->execute([
        'teacher_id' => $teacher['id'],
        'class_id' => $targetClass['id']
    ]);

    echo "✅ Berhasil set wali kelas!\n\n";
    echo "=================================\n";
    echo "Detail:\n";
    echo "=================================\n";
    echo "Kelas      : {$targetClass['name']}\n";
    echo "Wali Kelas : {$teacher['full_name']}\n";
    echo "=================================\n\n";
    echo "✅ Guru sekarang bisa login dan melihat data kelas {$targetClass['name']}\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
