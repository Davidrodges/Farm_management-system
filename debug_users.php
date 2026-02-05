<?php
require_once __DIR__ . '/config/db.php';
$stmt = $pdo->query("SELECT id, username, email FROM users");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | User: " . $row['username'] . " | Email: [" . $row['email'] . "]\n";
}
