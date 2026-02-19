<?php
require 'vendor/autoload.php';
$url = parse_url(getenv('JAWSDB_URL'));
$db = new mysqli(
    $url['host'],
    $url['user'],
    $url['pass'],
    ltrim($url['path'], '/'),
    $url['port'] ?? 3306
);
if ($db->connect_error) {
    die("Connect error: " . $db->connect_error);
}
$res = $db->query("SELECT id, name, nisn, parent_phone FROM students WHERE nisn='3148663143' OR name LIKE '%Mirza%'");
while ($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | Nama: {$row['name']} | NISN: {$row['nisn']} | Phone: {$row['parent_phone']}\n";
}
$db->close();
