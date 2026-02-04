<?php
// c:/Apache24/htdocs/farm_system/reset_password.php
session_start();
require_once 'config/db.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
$message = '';
$validToken = false;

// Validate Token
if ($token) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > datetime('now')"); // SQLite datetime comparison
    // Note: SQLite 'now' is UTC usually. Ensure DB stores proper time. PHP date() uses local. 
    // Best practice for SQLite is comparing string timestamps. 'reset_expires' > date('Y-m-d H:i:s') from PHP.
    
    $currentDate = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > ?");
    $stmt->execute([$token, $currentDate]);
    $user = $stmt->fetch();

    if ($user) {
        $validToken = true;
    } else {
        $message = "<span style='color:red'>Invalid or expired token.</span>";
    }
} else {
    $message = "<span style='color:red'>No token provided.</span>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $pass = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($pass === $confirm) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        // Update password and clear token
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);
        $message = "<span style='color:green'>Password updated! <a href='login.php'>Login here</a></span>";
        $validToken = false; // Hide form
    } else {
        $message = "<span style='color:red'>Passwords do not match.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
        }
        .login-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .form-group { margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 style="text-align: center; color: var(--dark-green);">New Password</h2>

        <?php if($message): ?>
            <p style="text-align: center; margin-bottom: 1rem; padding: 10px; background: #eee; border-radius: 4px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if($validToken): ?>
        <form method="POST">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Change Password</button>
        </form>
        <?php endif; ?>
        
        <?php if(!$validToken && empty($message)): // Only show if pure error state without post ?>
             <p style="text-align: center; color: red;">Token is invalid or missing.</p>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 1rem;">
            <a href="login.php" style="color: var(--earth-brown);">Back to Login</a>
        </p>
    </div>
</body>
</html>
