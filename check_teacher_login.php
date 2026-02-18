<?php
// Load env
$env = parse_ini_file(__DIR__ . '/env');
$host = $env['database.default.hostname'] ?? 'localhost';
$db   = $env['database.default.database'] ?? '';
$user = $env['database.default.username'] ?? '';
$pass = $env['database.default.password'] ?? '';
$port = $env['database.default.port'] ?? 3306;

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

echo "=== USERS (latest 10) ===\n";
$rows = $pdo->query("
    SELECT u.id, u.username, u.email, u.active,
           i.secret AS password_hash, i.type,
           g.id as groups_id
    FROM users u
    LEFT JOIN auth_identities i ON i.user_id = u.id AND i.type = 'email_password'
    LEFT JOIN auth_groups_users g ON g.user_id = u.id
    ORDER BY u.id DESC LIMIT 10
")->fetchAll();

foreach ($rows as $r) {
    echo "ID: {$r['id']} | Username: {$r['username']} | Email: {$r['email']} | active: {$r['active']} | group: {$r['groups_id']} | hash: " . substr($r['password_hash'] ?? 'NULL', 0, 30) . "...\n";
}

echo "\n=== CHECK USER 'pipit' ===\n";
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.email, u.active,
           i.secret AS password_hash,
           g.group AS user_group
    FROM users u
    LEFT JOIN auth_identities i ON i.user_id = u.id AND i.type = 'email_password'
    LEFT JOIN auth_groups_users g ON g.user_id = u.id
    WHERE u.username LIKE '%pipit%' OR u.email LIKE '%pipit%'
");
$stmt->execute();
$rows = $stmt->fetchAll();

if (empty($rows)) {
    echo "USER PIPIT TIDAK DITEMUKAN!\n";
} else {
    foreach ($rows as $r) {
        echo "ID: {$r['id']} | Username: {$r['username']} | Email: {$r['email']} | active: {$r['active']} | group: {$r['user_group']}\n";
        echo "Password hash: {$r['password_hash']}\n";
        
        // Test password verify
        $testPw = 'guru123456';
        $hash = $r['password_hash'] ?? '';
        $verify = password_verify($testPw, $hash);
        echo "password_verify('guru123456', hash) = " . ($verify ? 'TRUE ✓' : 'FALSE ✗') . "\n";
    }
}

echo "\n=== AUTH SYSTEM: Check how login works ===\n";
$stmt = $pdo->prepare("SELECT type, secret, extra_data FROM auth_identities WHERE user_id IN (SELECT id FROM users WHERE username LIKE '%pipit%' OR email LIKE '%pipit%') LIMIT 5");
$stmt->execute();
$rows = $stmt->fetchAll();
foreach ($rows as $r) {
    echo "type: {$r['type']} | secret: " . substr($r['secret'] ?? '', 0, 40) . " | extra: {$r['extra_data']}\n";
}
