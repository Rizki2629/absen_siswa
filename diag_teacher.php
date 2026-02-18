<?php
// Diagnostic: check teacher login
$url = getenv('JAWSDB_URL');
if (!$url) {
    // Try local config
    echo "JAWSDB_URL not set, trying local config\n";
    exit(1);
}
$p = parse_url($url);
$pdo = new PDO(
    'mysql:host=' . $p['host'] . ';dbname=' . ltrim($p['path'], '/') . ';charset=utf8',
    $p['user'],
    $p['pass']
);

echo "=== ALL TEACHERS ===\n";
$rows = $pdo->query(
    "SELECT id, username, full_name, role, is_active, LEFT(password_hash,20) AS hash_preview, deleted_at 
     FROM users WHERE role='teacher' ORDER BY id DESC LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo "NO TEACHERS FOUND\n";
} else {
    foreach ($rows as $r) {
        echo "ID:{$r['id']} username:[{$r['username']}] name:[{$r['full_name']}] active:{$r['is_active']} deleted:{$r['deleted_at']} hash:{$r['hash_preview']}\n";
    }
}

echo "\n=== SEARCH PIPIT ===\n";
$stmt = $pdo->prepare(
    "SELECT id, username, full_name, role, is_active, password_hash, deleted_at 
     FROM users WHERE username LIKE '%pipit%' OR full_name LIKE '%pipit%'"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "PIPIT NOT FOUND IN USERS TABLE\n";
} else {
    foreach ($rows as $r) {
        echo "ID:{$r['id']} username:[{$r['username']}] name:[{$r['full_name']}] active:{$r['is_active']} deleted:[{$r['deleted_at']}]\n";
        $test = password_verify('guru123456', $r['password_hash'] ?? '');
        echo "  password_verify('guru123456') = " . ($test ? 'TRUE OK' : 'FALSE WRONG') . "\n";
        echo "  hash: {$r['password_hash']}\n";
    }
}
