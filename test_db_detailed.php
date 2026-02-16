<?php
// test_db_detailed.php
// A standalone diagnostic script for Railway deployment

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Connection Diagnostic</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check for PDO
echo "<h2>PDO Drivers</h2>";
if (extension_loaded('pdo')) {
    echo "<p>✅ PDO Extension Loaded</p>";
    $drivers = PDO::getAvailableDrivers();
    echo "<p>Available Drivers: " . implode(', ', $drivers) . "</p>";
    
    if (in_array('mysql', $drivers)) {
        echo "<p>✅ MySQL Driver Detected</p>";
    } else {
        echo "<p>❌ MySQL Driver MISSING!</p>";
    }
} else {
    echo "<p>❌ PDO Extension NOT Loaded</p>";
}

// Check Environment Variables
echo "<h2>Environment Variables</h2>";
$vars = [
    'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT',
    'MYSQLHOST', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD', 'MYSQLPORT',
    'RAILWAY_ENVIRONMENT', 'PORT'
];

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Variable</th><th>getenv()</th><th>\$_ENV</th><th>\$_SERVER</th></tr>";

foreach ($vars as $var) {
    $val_getenv = getenv($var);
    $val_env = isset($_ENV[$var]) ? $_ENV[$var] : null;
    $val_server = isset($_SERVER[$var]) ? $_SERVER[$var] : null;

    // Mask passwords
    if (strpos($var, 'PASSWORD') !== false) {
        $val_getenv = $val_getenv ? substr($val_getenv, 0, 3) . '***' : $val_getenv;
        $val_env = $val_env ? substr($val_env, 0, 3) . '***' : $val_env;
        $val_server = $val_server ? substr($val_server, 0, 3) . '***' : $val_server;
    }

    echo "<tr>";
    echo "<td>$var</td>";
    echo "<td>" . ($val_getenv !== false && $val_getenv !== null ? $val_getenv : '<span style="color:red">NULL</span>') . "</td>";
    echo "<td>" . ($val_env !== null ? $val_env : '<span style="color:red">NULL</span>') . "</td>";
    echo "<td>" . ($val_server !== null ? $val_server : '<span style="color:red">NULL</span>') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Attempt Connection
echo "<h2>Connection Attempt</h2>";

// Logic copied/adapted from intended db.php fix to test isolation
function get_test_var($name) {
    return getenv($name) ?: ($_ENV[$name] ?? ($_SERVER[$name] ?? null));
}

$dbHost = get_test_var('DB_HOST') ?: get_test_var('MYSQLHOST');
$dbName = get_test_var('DB_NAME') ?: get_test_var('MYSQLDATABASE');
$dbUser = get_test_var('DB_USER') ?: get_test_var('MYSQLUSER');
$dbPass = get_test_var('DB_PASSWORD') ?: get_test_var('MYSQLPASSWORD');
$dbPort = get_test_var('DB_PORT') ?: get_test_var('MYSQLPORT') ?: 3306;

if ($dbHost) {
    echo "<p>Attempting connection to <strong>$dbHost</strong>...</p>";
    try {
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color:green; font-weight:bold; font-size:1.2em'>✅ Connection Successful!</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red; font-weight:bold; font-size:1.2em'>❌ Connection Failed</p>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
} else {
    echo "<p>No DB_HOST or MYSQLHOST detected. Assuming Local SQLite or Misconfiguration.</p>";
    // Test SQLite
    $dbPath = __DIR__ . '/farm.db';
    echo "<p>Checking for local SQLite at: $dbPath</p>";
    if (file_exists($dbPath)) {
        echo "<p>✅ farm.db exists (" . filesize($dbPath) . " bytes)</p>";
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            echo "<p style='color:green'>✅ SQLite Connection Successful</p>";
        } catch (Exception $e) {
            echo "<p style='color:red'>❌ SQLite Connection Failed: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ farm.db NOT found</p>";
    }
}
?>
