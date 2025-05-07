<?php
session_start();
require_once '../../config/database.php';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: ../dashboard/index.php");
    exit();
}

$error = '';
$success = '';
$validToken = false;
$token = '';

// Check if token is valid
if (isset($_GET['token'])) {
    $token = sanitize_input($conn, $_GET['token']);
    
    // Add reset_token and reset_expiry columns if they don't exist
    $checkColumns = $conn->query("SHOW COLUMNS FROM admin LIKE 'reset_token'");
    if ($checkColumns->num_rows === 0) {
        $conn->query("ALTER TABLE admin ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL");
        $conn->query("ALTER TABLE admin ADD COLUMN reset_expiry DATETIME DEFAULT NULL");
    }
    
    // Check if token exists and is valid
    $sql = "SELECT * FROM admin WHERE reset_token = '$token' AND reset_expiry > NOW()";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $validToken = true;
    } else {
        $error = "Invalid or expired token. Please try again.";
    }
} else {
    $error = "Token is required to reset password.";
}

// Handle reset password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Please enter both password fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update the password and clear the token
        $sql = "UPDATE admin SET password = '$hashedPassword', reset_token = NULL, reset_expiry = NULL WHERE reset_token = '$token'";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Your password has been reset successfully. You can now <a href='login.php'>login</a> with your new password.";
            $validToken = false; // Hide the form
        } else {
            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CS Student Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card slide-in">
            <div class="login-logo">
                <h2>Reset Password</h2>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($validToken): ?>
            <p class="text-center mb-4">Enter your new password below.</p>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                    </div>
                    <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </div>
            </form>
            <?php else: ?>
            <?php if (empty($success)): ?>
            <div class="text-center">
                <p>The reset link is invalid or expired.</p>
                <a href="forgot-password.php" class="btn btn-primary mt-3">Request New Reset Link</a>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Back to Login</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordConfirm = document.getElementById('confirm_password');
            if (passwordConfirm) {
                const password = document.getElementById('password');
                
                passwordConfirm.addEventListener('input', function() {
                    if (this.value !== password.value) {
                        this.setCustomValidity('Passwords do not match');
                    } else {
                        this.setCustomValidity('');
                    }
                });
                
                password.addEventListener('input', function() {
                    if (passwordConfirm.value !== this.value) {
                        passwordConfirm.setCustomValidity('Passwords do not match');
                    } else {
                        passwordConfirm.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>
</html>