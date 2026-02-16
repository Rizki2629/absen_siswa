<?php
// Quick test to check if API works
$ch = curl_init('http://localhost:8080/student/api/habits/today');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: $code\n";
echo "Response: " . substr($resp, 0, 500) . "\n";
