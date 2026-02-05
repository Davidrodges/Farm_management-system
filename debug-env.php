<?php
// debug-env.php
echo "<h1>Environment Variables Debug</h1>";

// Check DB variables
$dbVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT'];
foreach ($dbVars as $var) {
    $val = getenv($var);
    $source = "getenv";
    if (!$val && isset($_ENV[$var])) { $val = $_ENV[$var]; $source = "\$_ENV"; }
    if (!$val && isset($_SERVER[$var])) { $val = $_SERVER[$var]; $source = "\$_SERVER"; }
    
    echo "<b>$var:</b> " . ($val ? "✅ SET (via $source)" : "❌ NOT SET") . "<br>";
    if ($val) {
        // Show masked value
        $masked = (strlen($val) > 3) ? substr($val, 0, 3) . "..." : "***";
        echo "Value Start: $masked<br>";
    }
}

echo "<hr>";

// Check Railway variables
$railwayVars = ['MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD', 'MYSQLPORT', 'MYSQL_URL'];
foreach ($railwayVars as $var) {
    $val = getenv($var) ?: ($_ENV[$var] ?? ($_SERVER[$var] ?? null));
    echo "<b>$var:</b> " . ($val ? "✅ SET" : "❌ NOT SET") . "<br>";
    if ($val && $var == 'MYSQL_URL') {
        echo "Format Check: " . (strpos($val, 'mysql://') === 0 ? "Valid URL Format" : "Unexpected Format") . "<br>";
    }
}

echo "<hr>";
echo "<b>PHP Version:</b> " . phpversion() . "<br>";
echo "<b>Available Drivers:</b> " . implode(', ', PDO::getAvailableDrivers()) . "<br>";
?>
