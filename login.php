<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Davina and Rodgers Solution LTD</title>
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
        .brand-logo {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark-green);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-logo">
            <h2>Davina and Rodgers Solution LTD</h2>
            <p>Farm Management System Login</p>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
            <p style="color: red; text-align: center; margin-bottom: 1rem;">Invalid Credentials</p>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem; font-size: 0.9rem;">
            <a href="forgot_password.php" style="color: #7f8c8d; text-decoration: none;">Forgot Password?</a>
            <br><br>
            <a href="register.php" style="color: var(--earth-brown); font-weight: bold;">Create an Account</a>
        </div>
    </div>
</body>
</html>
