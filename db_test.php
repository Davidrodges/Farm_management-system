<?php
// db_test.php - Isolated Database Connection Test

// Use the exact credentials you put in config/db.php
$host = "sql107.infinityfree.com";
$dbname = "if0_41076298_farmsystem";
$username = "if0_41076298";
$password = "LaptopYangu2024";

echo "<p><strong>Testing connection with:</strong></p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Database: $dbname</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . (strlen($password) > 20 ? "Still using placeholder - EDIT THIS!" : str_repeat("*", strlen($password))) . "</li>";
echo "</ul>";
echo "<hr>";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1>Database Connected Successfully! 🎉</h1>";
} catch (PDOException $e) {
    echo "<h1>Database Connection Failed ❌</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<hr>";
    echo "<h3>Troubleshooting Tips:</h3>";
    echo "<ul>";
    echo "<li>Check if your password is correct in this file (and in config/db.php)</li>";
    echo "<li>Ensure the Host is correct (is it sql107.infinityfree.com or sql107.byetcluster.com?)</li>";
    echo "<li>Confirm the database name '$dbname' matches exactly what is in cPanel.</li>";
    echo "</ul>";
}
?>
