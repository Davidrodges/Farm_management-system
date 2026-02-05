<?php
// test_db_connection.php
header('Content-Type: text/plain');

echo "--- Database Connection Test ---\n";

try {
    require_once __DIR__ . '/config/db.php';
    
    if (isset($pdo)) {
        echo "✅ Database connected successfully!\n";
        
        // Show environment info (Sanitized)
        echo "Host: " . (getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'local') . "\n";
        echo "Database: " . (getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'sqlite') . "\n";
        
        // Test query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Query test: " . ($result['test'] == 1 ? "✅ SUCCESS" : "❌ FAILURE") . "\n";
        
        // Check if on Railway
        $is_railway = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false) || getenv('RAILWAY_ENVIRONMENT');
        echo "Environment: " . ($is_railway ? "Railway (Production)" : "Local / Other") . "\n";

    } else {
        echo "❌ Error: \$pdo variable is not defined after including db.php\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "\nDebug Env Variables:\n";
    echo "MYSQLHOST=" . (getenv('MYSQLHOST') ? 'YES' : 'NO') . "\n";
    echo "MYSQLDATABASE=" . (getenv('MYSQLDATABASE') ? 'YES' : 'NO') . "\n";
    echo "DB_HOST=" . (getenv('DB_HOST') ? 'YES' : 'NO') . "\n";
}
?>
