<?php
// fix_user_emails.php
require_once __DIR__ . '/config/db.php';

echo "Updating users without emails...\n";

try {
    // 1. Update admin
    $stmt = $pdo->prepare("UPDATE users SET email = 'admin@farmsystem.local' WHERE username = 'admin' AND (email IS NULL OR email = '')");
    $stmt->execute();
    echo "Admin updated.\n";

    // 2. Update others with a placeholder
    $stmt = $pdo->prepare("UPDATE users SET email = username || '@farmsystem.local' WHERE (email IS NULL OR email = '')");
    $stmt->execute();
    echo "Other users updated with placeholders.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "Current User List:\n";
foreach($pdo->query("SELECT id, username, email FROM users") as $row) {
    echo "ID: {$row['id']} | User: {$row['username']} | Email: {$row['email']}\n";
}
