<?php
// c:/Apache24/htdocs/farm_system/includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /farm_system/login.php");
    exit;
}
