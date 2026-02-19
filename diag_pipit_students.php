<?php
// Quick diagnostic: pipit user ID vs classes teacher_id vs students class_id
// Run: heroku run "php /app/diag_pipit_students.php"

$url = getenv('DATABASE_URL');
if (!$url) die("DATABASE_URL not found\n");
$p = parse_url($url);
$conn = new mysqli($p['host'], $p['user'], $p['pass'], ltrim($p['path'],'/'), $p['port'] ?? 3306);
if ($conn->connect_error) die("Connect: ".$conn->connect_error."\n");

echo "=== USERS (pipit) ===\n";
$res = $conn->query("SELECT id, username, role FROM users WHERE username LIKE '%pipit%' LIMIT 5");
$pipitId = null;
while ($row = $res->fetch_assoc()) {
    echo "  id={$row['id']} username={$row['username']} role={$row['role']}\n";
    if (!$pipitId) $pipitId = $row['id'];
}
if (!$pipitId) { echo "  Tidak ditemukan!\n"; exit; }

echo "\n=== CLASSES WHERE teacher_id = {$pipitId} ===\n";
$res = $conn->query("SELECT id, name, teacher_id FROM classes WHERE teacher_id = $pipitId");
$classes = [];
while ($row = $res->fetch_assoc()) $classes[] = $row;

if (empty($classes)) {
    echo "  KOSONG! teacher_id={$pipitId} tidak ada di tabel classes\n";
    echo "\n=== ALL CLASSES (semua) ===\n";
    $res2 = $conn->query("SELECT id, name, teacher_id FROM classes LIMIT 20");
    while ($row = $res2->fetch_assoc()) {
        echo "  id={$row['id']} name={$row['name']} teacher_id=".($row['teacher_id']??'NULL')."\n";
    }
} else {
    foreach ($classes as $c) {
        echo "  class id={$c['id']} name={$c['name']}\n";
        $total = $conn->query("SELECT COUNT(*) n FROM students WHERE class_id={$c['id']} AND deleted_at IS NULL")->fetch_assoc();
        echo "  => Total siswa aktif: {$total['n']}\n";
        $res3 = $conn->query("SELECT name, nis, active FROM students WHERE class_id={$c['id']} AND deleted_at IS NULL LIMIT 5");
        while ($s = $res3->fetch_assoc()) {
            echo "     {$s['name']} (nis={$s['nis']} active={$s['active']})\n";
        }
    }
}
echo "\nDONE\n";
