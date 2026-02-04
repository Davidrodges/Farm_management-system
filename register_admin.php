<?php
// c:/Apache24/htdocs/farm_system/register_admin.php
require_once 'config/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = 'admin';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "User '$username' already exists.\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "User '$username' created successfully with password '$password'.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
