<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';
$admin_id = $_SESSION['admin_id'];

// Get current admin details
$adminQuery = "SELECT * FROM admin WHERE id = $admin_id";
$adminResult = $conn->query($adminQuery);
$admin = $adminResult->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = sanitize_input($conn, $_POST['name']);
    $email = sanitize_input($conn, $_POST['email']);
    
    // Validate inputs
    if (empty($name) || empty($email)) {
        $error = "Name and email are required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if email already exists for another admin
        $checkEmailQuery = "SELECT id FROM admin WHERE email = '$email' AND id != $admin_id";
        $checkEmailResult = $conn->query($checkEmailQuery);
        
        if ($checkEmailResult->num_rows > 0) {
            $error = "Email already in use by another account";
        } else {
            // Process profile image if uploaded
            $profile_image = $admin['profile_image']; // Default to current image
            
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $file_name = $_FILES['profile_image']['name'];
                $file_size = $_FILES['profile_image']['size'];
                $file_tmp = $_FILES['profile_image']['tmp_name'];
                $file_type = $_FILES['profile_image']['type'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Allowed extensions
                $extensions = array("jpeg", "jpg", "png");
                
                if (!in_array($file_ext, $extensions)) {
                    $error = "Extension not allowed, please choose a JPEG or PNG file.";
                } elseif ($file_size > 5242880) { // 5MB in bytes
                    $error = "File size must be less than 5MB";
                } else {
                    // Create uploads directory if it doesn't exist
                    $upload_dir = "../../assets/images/profiles/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Delete old image if exists
                    if ($profile_image && file_exists($upload_dir . $profile_image)) {
                        unlink($upload_dir . $profile_image);
                    }
                    
                    // Generate a unique filename to prevent overwriting
                    $new_file_name = "admin_" . $admin_id . "_" . time() . "." . $file_ext;
                    
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                        $profile_image = $new_file_name;
                    } else {
                        $error = "Failed to upload image";
                    }
                }
            }
            
            // If no errors, update the profile
            if (empty($error)) {
                $updateProfileQuery = "UPDATE admin SET 
                    name = '$name', 
                    email = '$email'";
                
                if ($profile_image !== $admin['profile_image']) {
                    $updateProfileQuery .= ", profile_image = '$profile_image'";
                }
                
                $updateProfileQuery .= " WHERE id = $admin_id";
                
                if ($conn->query($updateProfileQuery) === TRUE) {
                    $_SESSION['admin_name'] = $name;
                    $success = "Profile updated successfully";
                    
                    // Refresh admin data
                    $adminResult = $conn->query($adminQuery);
                    $admin = $adminResult->fetch_assoc();
                } else {
                    $error = "Error updating profile: " . $conn->error;
                }
            }
        }
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        // Verify current password
        if (password_verify($current_password, $admin['password'])) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the password
            $updatePasswordQuery = "UPDATE admin SET password = '$hashed_password' WHERE id = $admin_id";
            
            if ($conn->query($updatePasswordQuery) === TRUE) {
                $success = "Password changed successfully";
            } else {
                $error = "Error changing password: " . $conn->error;
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>

<?php include_once '../../includes/header.php'; ?>
<?php include_once '../../includes/sidebar.php'; ?>

<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-0">Profile Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Profile Information Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Update Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="text-center mb-4">
                                <?php if (!empty($admin['profile_image']) && file_exists("../../assets/images/profiles/" . $admin['profile_image'])): ?>
                                    <img src="../../assets/images/profiles/<?php echo $admin['profile_image']; ?>" id="profile_image-preview" class="avatar avatar-lg mb-3" alt="Profile Image">
                                <?php else: ?>
                                    <div class="avatar avatar-lg mb-3 d-flex align-items-center justify-content-center bg-light">
                                        <i class='bx bx-user-circle' style="font-size: 50px;"></i>
                                        <span class="position-absolute" style="bottom: 0;">No image uploaded</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input class="form-control" type="file" id="profile_image" name="profile_image" accept="image/jpeg, image/png">
                                    <div class="form-text text-danger"></div>
                                    <div class="form-text">Max file size: 5MB. Accepted formats: JPEG, PNG</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $admin['name']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" value="<?php echo $admin['username']; ?>" disabled readonly>
                                <div class="form-text">Username cannot be changed</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Change Password Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                        <i class='bx bx-show'></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                        <i class='bx bx-show'></i>
                                    </button>
                                </div>
                                <div class="form-text">Password must be at least 8 characters long</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                        <i class='bx bx-show'></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="change_password" class="btn btn-danger">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Info Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Account Created</span>
                                <span><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Last Updated</span>
                                <span><?php echo date('M d, Y', strtotime($admin['updated_at'])); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Account Type</span>
                                <span><span class="badge bg-primary">Administrator</span></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        });
    });
});
</script>