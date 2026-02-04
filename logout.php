<?php
// c:/Apache24/htdocs/farm_system/logout.php
session_start();
session_destroy();
header("Location: login.php");
exit;
