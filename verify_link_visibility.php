<?php
// verify_link_visibility.php
require_once __DIR__ . '/config/db.php';

// Mock POST for admin email
$_POST['email'] = 'admin@farmsystem.local';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Testing Forgot Password logic for admin@farmsystem.local...\n";

ob_start();
include 'forgot_password.php';
$html = ob_get_clean();

if (strpos($html, 'Simulated Email (Development Mode)') !== false) {
    echo "SUCCESS: Simulated Email link is visible.\n";
    preg_match('/href=\'(.*?)\'/', $html, $matches);
    if (isset($matches[1])) {
        echo "Detected Link: " . $matches[1] . "\n";
    } else {
         // try double quotes
         preg_match('/href="(.*?)"/', $html, $matches);
         if (isset($matches[1])) echo "Detected Link: " . $matches[1] . "\n";
    }
} else {
    echo "FAIL: Simulated Email link NOT found in output.\n";
    echo "HTML snippet:\n" . substr($html, strpos($html, 'login-card'), 500) . "\n";
}
