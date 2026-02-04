<?php
require_once 'config/db.php';

echo "Attempting robust fix for 'email' column...\n";

try {
    // 1. Add column without UNIQUE constraint (SQLite limitation)
    // We add it as nullable text first.
    $pdo->exec("ALTER TABLE users ADD COLUMN email TEXT");
    echo ">> [SUCCESS] 'email' column added (without constraint).\n";

    // 2. Create UNIQUE index to enforce uniqueness
    $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_users_email ON users(email)");
    echo ">> [SUCCESS] Unique index created on 'email'.\n";

} catch (PDOException $e) {
    echo ">> [FAIL] Operation failed: " . $e->getMessage() . "\n";
}
