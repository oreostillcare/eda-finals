<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use absolute path for database include
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/School System Management CS/';
include_once $root_path . 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS Student Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Poppins Font - Preload for better performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS - using absolute path -->
    <link rel="stylesheet" href="<?php echo '/School System Management CS/assets/css/style.css'; ?>">
</head>
<body>
    <!-- Mobile Navigation Bar (visible only on small screens) -->
    <div class="mobile-navbar d-md-none">
        <div class="d-flex align-items-center">
            <button type="button" class="sidebar-toggle-btn btn btn-link text-dark p-0 me-3" aria-label="Toggle navigation" 
                    tabindex="0">
                <i class='bx bx-menu' style="font-size: 24px;"></i>
            </button>
            <h5 class="m-0 fw-bold">CS Student Management</h5>
        </div>
        <div class="d-flex align-items-center">
            <!-- Theme Toggle Switch -->
            <div class="theme-switch-wrapper">
                <label class="theme-switch" for="checkbox">
                    <input type="checkbox" id="checkbox" class="theme-switch-input" />
                    <span class="slider"></span>
                </label>
                <i class='bx bx-moon slider-icon'></i>
            </div>
            
            <?php if(isset($_SESSION['admin_id'])): ?>
            <div class="dropdown">
                <button class="btn btn-link p-0 text-dark" type="button" id="mobileProfileMenu" data-bs-toggle="dropdown" 
                        aria-expanded="false" aria-label="Profile options">
                    <i class='bx bx-user-circle' style="font-size: 24px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileProfileMenu">
                    <li><a class="dropdown-item" href="/School System Management CS/pages/profile/index.php">
                        <i class='bx bx-user me-2'></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/School System Management CS/pages/auth/logout.php">
                        <i class='bx bx-log-out me-2'></i> Logout</a></li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content Container with wrapper for mobile -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar will be included separately -->