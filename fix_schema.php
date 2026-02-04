<?php
require_once 'config/db.php';

echo "Checking Schema for 'users' table...\n";
$stmt = $pdo->query("PRAGMA table_info(users)");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$foundEmail = false;
foreach ($columns as $col) {
    echo " - Found column: " . $col['name'] . " (" . $col['type'] . ")\n";
    if ($col['name'] === 'email') {
        $foundEmail = true;
    }
}

if (!$foundEmail) {
    echo ">> [ALERT] 'email' column is MISSING.\n";
    echo ">> Attempting to add 'email' column now...\n";
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN email TEXT UNIQUE");
        echo ">> [SUCCESS] 'email' column added.\n";
    } catch (PDOException $e) {
        echo ">> [FAIL] Could not add column: " . $e->getMessage() . "\n";
    }
} else {
    echo ">> 'email' column exists.\n";
}
