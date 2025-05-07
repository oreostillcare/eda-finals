<?php
// Get the current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Sidebar Navigation - Enhanced with collapsible functionality -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>CS Management</h3>
        <div class="sidebar-toggle d-md-none">
            <i class='bx bx-x'></i>
        </div>
        <div class="sidebar-collapse d-none d-md-flex">
            <i class='bx bx-chevron-left'></i>
        </div>
    </div>
    
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/School System Management CS/pages/dashboard/index.php" class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                    <i class='bx bxs-dashboard'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/School System Management CS/pages/students/index.php" class="nav-link <?php echo (strpos($current_page, 'student') !== false) ? 'active' : ''; ?>">
                    <i class='bx bxs-graduation'></i>
                    <span>Students</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/School System Management CS/pages/profile/index.php" class="nav-link <?php echo ($current_page == 'profile' || $current_page == 'index' && strpos($_SERVER['PHP_SELF'], 'profile') !== false) ? 'active' : ''; ?>">
                    <i class='bx bxs-user-circle'></i>
                    <span>Profile</span>
                </a>
            </li>
            
            <!-- Theme Toggle Switch for Desktop -->
            <li class="nav-item mt-3">
                <div class="px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Dark Mode</span>
                        <div class="theme-switch-wrapper">
                            <label class="theme-switch" for="sidebar-checkbox">
                                <input type="checkbox" id="sidebar-checkbox" class="theme-switch-input" />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </li>
            
            <li class="nav-item mt-auto">
                <a href="/School System Management CS/pages/auth/logout.php" class="nav-link">
                    <i class='bx bx-log-out'></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Dark overlay for mobile sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>