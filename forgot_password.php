<?php
// c:/Apache24/htdocs/farm_system/forgot_password.php
session_start();
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32)); // 64 char random token
        // E.g. expire in 1 hour
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expiry, $user['id']]);

        // Email logic
        $resetLink = "http://localhost/farm_system/reset_password.php?token=" . $token;
        $subject = "Password Reset Request";
        $body = "Click this link to reset your password: " . $resetLink;
        $headers = "From: no-reply@farmsystem.local";

        // Try sending email, if fail (common on localhost without SMTP), show link
        if (@mail($email, $subject, $body, $headers)) {
            $message = "<span style='color:green'>Reset link sent to your email. check spam folder.</span>";
        } else {
            // FALLBACK FOR DEMO / LOCALHOST
            $message = "<span style='color:orange'>Simulated Email (Localhost): <a href='$resetLink'>Click here to Reset Password</a></span>";
        }
    } else {
        $message = "<span style='color:red'>Email not found.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
        <h2 style="text-align: center; color: var(--dark-green);">Reset Password</h2>
        <p style="text-align: center; margin-bottom: 1rem;">Enter your email to receive a reset link.</p>

        <?php if($message): ?>
            <p style="text-align: center; margin-bottom: 1rem; padding: 10px; background: #eee; border-radius: 4px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Send Reset Link</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem;">
            <a href="login.php" style="color: var(--earth-brown);">Back to Login</a>
        </p>
    </div>
</body>
</html>
