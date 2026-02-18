<?php
// Reset password untuk user tertentu
$url = getenv('JAWSDB_URL');
if (!$url) { echo "JAWSDB_URL not set\n"; exit(1); }
$p = parse_url($url);
$pdo = new PDO(
    'mysql:host=' . $p['host'] . ';dbname=' . ltrim($p['path'], '/') . ';charset=utf8',
    $p['user'], $p['pass']
);

// Reset semua user yang password_hash-nya kosong/null ke password default
$teachers = $pdo->query("SELECT id, username, full_name FROM users WHERE (password_hash IS NULL OR password_hash = '') AND role = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);

if (empty($teachers)) {
    echo "Tidak ada guru dengan password kosong\n";
} else {
    foreach ($teachers as $t) {
        $newHash = password_hash('guru123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHash, $t['id']]);
        echo "Reset password guru ID:{$t['id']} [{$t['username']}] [{$t['full_name']}] -> 'guru123456'\n";
    }
}

// Juga cek khusus pipit
$stmt = $pdo->prepare("SELECT id, username, full_name, LEFT(password_hash,20) AS hash FROM users WHERE username = 'pipit.raniasih'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo "\nCek pipit setelah reset: ID:{$row['id']} hash_preview:{$row['hash']}\n";
    $verify = password_verify('guru123456', $pdo->query("SELECT password_hash FROM users WHERE username='pipit.raniasih'")->fetchColumn());
    echo "Verifikasi 'guru123456': " . ($verify ? 'OK BERHASIL' : 'MASIH GAGAL') . "\n";
}
