<?php
// c:/Apache24/htdocs/farm_system/logout.php
require_once 'config/db.php';
session_start();
session_destroy();
header("Location: " . BASE_URL . "/login.php");
exit;
