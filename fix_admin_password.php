<?php
// Generate password hash and display it
$hash = password_hash('admin123', PASSWORD_DEFAULT);
echo "Password hash for 'admin123':\n";
echo $hash . "\n";
echo "\nRun this SQL:\n";
echo "UPDATE users SET password_hash = '$hash' WHERE username = 'admin';\n";
?>
