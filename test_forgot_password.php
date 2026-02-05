<?php
// test_forgot_password.php
require_once __DIR__ . '/config/db.php';

echo "Simulating Forgot Password for David Rodgers (david.rodgy@gmail.com)...\n";

$_POST['email'] = 'david.rodgy@gmail.com';
$_SERVER['REQUEST_METHOD'] = 'POST';

// We need to capture output because forgot_password.php outputs HTML
ob_start();
include 'forgot_password.php';
$html = ob_get_clean();

if (strpos($html, 'Reset link sent') !== false || strpos($html, 'Simulated Email') !== false) {
    echo "SUCCESS: Forgot password logic triggered.\n";
    
    // Check DB for token
    $user = $pdo->query("SELECT reset_token FROM users WHERE email = 'david.rodgy@gmail.com'")->fetch();
    if ($user['reset_token']) {
        echo "TOKEN FOUND: {$user['reset_token']}\n";
    } else {
        echo "FAIL: Token NOT found in DB.\n";
    }
} else {
    echo "FAIL: Logic did not trigger success message.\n";
    if (strpos($html, 'Email not found') !== false) echo "REASON: Email not found in DB check.\n";
}

echo "\nChecking BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'UNDEFINED') . "\n";
