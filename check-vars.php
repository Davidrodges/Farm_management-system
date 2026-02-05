<?php
// check-vars.php
echo "<h1>Checking environment variables:</h1>";

$vars = [
    'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT',
    'MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD', 'MYSQLPORT'
];

echo "<h3>Using getenv():</h3>";
foreach ($vars as $var) {
    $value = getenv($var);
    if ($value) {
        $masked = (strlen($value) > 3) ? substr($value, 0, 3) . "***" : "***";
        echo "✅ $var = $masked<br>";
    } else {
        echo "❌ $var = NOT SET<br>";
    }
}

// Also check if variables are in $_ENV
echo "<h3>Checking \$_ENV / \$_SERVER:</h3>";
foreach ($vars as $var) {
    $env_set = isset($_ENV[$var]);
    $server_set = isset($_SERVER[$var]);
    if ($env_set || $server_set) {
        echo "✅ $var is set in " . ($env_set ? "\$_ENV " : "") . ($server_set ? "\$_SERVER" : "") . "<br>";
    } else {
        echo "❌ $var is NOT in superglobals<br>";
    }
}
?>
