<?php
// Reset semua password siswa yang rusak (password_hash = NULL / kosong)
// Run: heroku run "php /app/fix_student_passwords.php"
$url = getenv('JAWSDB_URL') ?: getenv('DATABASE_URL');
if (!$url) die("DB URL not found\n");
$p = parse_url($url);
$conn = new mysqli($p['host'], $p['user'], $p['pass'], ltrim($p['path'],'/'), $p['port'] ?? 3306);
if ($conn->connect_error) die("Connect: ".$conn->connect_error."\n");

// Cari semua akun siswa dengan password_hash NULL atau kosong
$res = $conn->query("SELECT id, username, full_name FROM users WHERE role='siswa' AND (password_hash IS NULL OR password_hash='') AND deleted_at IS NULL");
$broken = [];
while ($row = $res->fetch_assoc()) $broken[] = $row;

echo "Akun siswa dengan password rusak: " . count($broken) . "\n";

if (empty($broken)) {
    echo "Tidak ada. Semua akun siswa sudah punya password.\n";
    $conn->close();
    exit;
}

// Reset ke password default 'siswa123'
$newHash = password_hash('siswa123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
$fixed = 0;
foreach ($broken as $user) {
    $stmt->bind_param('si', $newHash, $user['id']);
    $stmt->execute();
    echo "  Fixed: {$user['username']} ({$user['full_name']})\n";
    $fixed++;
}

echo "\nTotal diperbaiki: {$fixed} akun\n";
echo "Password default: siswa123\n";
echo "DONE\n";
$conn->close();
