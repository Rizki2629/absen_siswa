<?php
// Quick diagnostic: pipit user ID vs classes teacher_id vs students class_id
// Run: heroku run "php /app/diag_pipit_students.php"

require_once __DIR__ . '/vendor/autoload.php';
$app = \Config\Services::createRequest(\Config\App::class);

$db = \Config\Database::connect();

// 1. Find pipit user
$user = $db->query("SELECT id, username, role FROM users WHERE username LIKE '%pipit%' LIMIT 5")->getResultArray();
echo "=== USERS (pipit) ===\n";
foreach ($user as $u) {
    echo "  id={$u['id']} username={$u['username']} role={$u['role']}\n";
}
$pipitId = $user[0]['id'] ?? null;

// 2. Classes with that teacher_id
echo "\n=== CLASSES WHERE teacher_id = {$pipitId} ===\n";
$classes = $db->query("SELECT id, name, teacher_id FROM classes WHERE teacher_id = ?", [$pipitId])->getResultArray();
if (empty($classes)) {
    echo "  KOSONG! Tidak ada kelas dengan teacher_id = {$pipitId}\n";
    echo "\n=== ALL CLASSES teacher_id samples ===\n";
    $allCls = $db->query("SELECT id, name, teacher_id FROM classes LIMIT 20")->getResultArray();
    foreach ($allCls as $c) {
        echo "  id={$c['id']} name={$c['name']} teacher_id={$c['teacher_id']}\n";
    }
} else {
    foreach ($classes as $c) {
        echo "  id={$c['id']} name={$c['name']} teacher_id={$c['teacher_id']}\n";
        // 3. Students in that class
        $students = $db->query("SELECT id, name, nis, class_id FROM students WHERE class_id = ? AND deleted_at IS NULL LIMIT 5", [$c['id']])->getResultArray();
        echo "  => Students (first 5): " . count($students) . "\n";
        foreach ($students as $s) {
            echo "     {$s['name']} (class_id={$s['class_id']})\n";
        }
    }
}

echo "\n=== DONE ===\n";
