<?php
// debug-env.php
echo "<h1>Environment Variables Debug</h1>";

// Check DB variables
$dbVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT'];
foreach ($dbVars as $var) {
    $value = getenv($var);
    echo "<b>$var:</b> " . ($value ? "✅ SET" : "❌ NOT SET") . "<br>";
    if ($value) {
        // Show masked value
        $masked = (strlen($value) > 3) ? substr($value, 0, 3) . "..." : "***";
        echo "Value Start: $masked<br>";
    }
}

echo "<hr>";

// Check Railway variables
$railwayVars = ['MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD', 'MYSQLPORT', 'MYSQL_URL'];
foreach ($railwayVars as $var) {
    $value = getenv($var);
    echo "<b>$var:</b> " . ($value ? "✅ SET" : "❌ NOT SET") . "<br>";
    if ($value && $var == 'MYSQL_URL') {
        echo "Format Check: " . (strpos($value, 'mysql://') === 0 ? "Valid URL Format" : "Unexpected Format") . "<br>";
    }
}

echo "<hr>";
echo "<b>PHP Version:</b> " . phpversion() . "<br>";
echo "<b>Available Drivers:</b> " . implode(', ', PDO::getAvailableDrivers()) . "<br>";
?>
