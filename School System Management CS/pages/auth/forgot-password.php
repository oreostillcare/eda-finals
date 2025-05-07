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

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($conn, $_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } else {
        // Check if the email exists in the database
        $sql = "SELECT * FROM admin WHERE email = '$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Generate a secure token
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
            
            // Store the token in the database
            $sql = "UPDATE admin SET reset_token = '$token', reset_expiry = '$expiry' WHERE id = " . $admin['id'];
            
            if ($conn->query($sql) === TRUE) {
                // For a real application, send an email with a password reset link
                // For this demo, we'll just show a message with the reset link
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/School System Management CS/pages/auth/reset-password.php?token=$token";
                
                $success = "Password reset link has been generated. In a production environment, this would be emailed to you.<br>For demo purposes, <a href='$resetLink'>click here</a> to reset your password.";
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        } else {
            $error = "No account found with that email address";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CS Student Management System</title>
    
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
                <h2>Forgot Password</h2>
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
            <?php else: ?>
            <p class="text-center mb-4">Enter your email address to receive a password reset link.</p>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </div>
                
                <div class="text-center">
                    <a href="login.php" class="text-decoration-none">Back to Login</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>