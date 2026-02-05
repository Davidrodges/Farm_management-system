<?php
// c:/Apache24/htdocs/farm_system/register.php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check availability
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or Email already exists.";
        } else {
            // Create User
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hash])) {
                $success = "Account created! You can now <a href='" . BASE_URL . "/login.php'>Login</a>";
            } else {
                $error = "Error creating account.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Farm System</title>
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
        <h2 style="text-align: center; color: var(--dark-green);">Create Account</h2>
        
        <?php if($error): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if($success): ?>
            <p style="color: green; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if(!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Register</button>
        </form>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 1rem;">
            Already have an account? <a href="<?php echo BASE_URL; ?>/login.php" style="color: var(--earth-brown);">Login</a>
        </p>
    </div>
</body>
</html>
