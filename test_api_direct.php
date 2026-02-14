<?php
// Test API endpoint directly
$url = 'https://absensi-siswa-688f8bff946c.herokuapp.com/api/admin/attendance-logs?limit=10';

echo "Testing API endpoint: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIE, ""); // No cookies - simulating no session
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response Length: " . strlen($response) . " bytes\n";
echo "Response:\n";
echo $response;
echo "\n";
?>
