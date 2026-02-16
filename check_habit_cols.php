<?php
$pdo = new PDO("mysql:host=localhost;dbname=absensi_siswa", "root", "");
$stmt = $pdo->query("DESCRIBE student_habits");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== student_habits columns ===\n";
foreach ($cols as $c) {
    echo $c['Field'] . " (" . $c['Type'] . ")\n";
}
