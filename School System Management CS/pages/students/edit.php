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

// Check if student ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = sanitize_input($conn, $_GET['id']);

// Get student data
$query = "SELECT * FROM students WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows !== 1) {
    header("Location: index.php");
    exit();
}

$student = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $student_id = sanitize_input($conn, $_POST['student_id']);
    $first_name = sanitize_input($conn, $_POST['first_name']);
    $last_name = sanitize_input($conn, $_POST['last_name']);
    $section = sanitize_input($conn, $_POST['section']);
    $sex = sanitize_input($conn, $_POST['sex']);
    $year_level = sanitize_input($conn, $_POST['year_level']);
    $email = !empty($_POST['email']) ? sanitize_input($conn, $_POST['email']) : null;
    $phone = !empty($_POST['phone']) ? sanitize_input($conn, $_POST['phone']) : null;
    $address = !empty($_POST['address']) ? sanitize_input($conn, $_POST['address']) : null;
    
    // Validate inputs
    if (empty($student_id) || empty($first_name) || empty($last_name) || empty($section) || empty($sex) || empty($year_level)) {
        $error = "Please fill in all required fields";
    } else {
        // Check if student ID already exists for another student
        $checkQuery = "SELECT * FROM students WHERE student_id = '$student_id' AND id != $id";
        $checkResult = $conn->query($checkQuery);
        
        if ($checkResult->num_rows > 0) {
            $error = "Student ID already exists";
        } else {
            // Process photo if uploaded
            $photo = $student['photo']; // Default to current photo
            
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                $file_name = $_FILES['photo']['name'];
                $file_size = $_FILES['photo']['size'];
                $file_tmp = $_FILES['photo']['tmp_name'];
                $file_type = $_FILES['photo']['type'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Allowed extensions
                $extensions = array("jpeg", "jpg", "png");
                
                if (!in_array($file_ext, $extensions)) {
                    $error = "Extension not allowed, please choose a JPEG or PNG file.";
                } elseif ($file_size > 5242880) { // 5MB in bytes
                    $error = "File size must be less than 5MB";
                } else {
                    // Create uploads directory if it doesn't exist
                    $upload_dir = "../../assets/images/students/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    // Delete old photo if exists
                    if ($photo && file_exists($upload_dir . $photo)) {
                        unlink($upload_dir . $photo);
                    }
                    
                    // Generate a unique filename to prevent overwriting
                    $new_file_name = "student_" . $student_id . "_" . time() . "." . $file_ext;
                    
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                        $photo = $new_file_name;
                    } else {
                        $error = "Failed to upload image";
                    }
                }
            }
            
            // If no errors, update the student
            if (empty($error)) {
                $updateQuery = "UPDATE students SET 
                    student_id = '$student_id', 
                    first_name = '$first_name', 
                    last_name = '$last_name', 
                    section = '$section', 
                    sex = '$sex', 
                    year_level = $year_level";
                
                if ($photo !== $student['photo']) {
                    $updateQuery .= ", photo = '$photo'";
                }
                
                $updateQuery .= ", email = " . ($email ? "'$email'" : "NULL");
                $updateQuery .= ", phone = " . ($phone ? "'$phone'" : "NULL");
                $updateQuery .= ", address = " . ($address ? "'$address'" : "NULL");
                $updateQuery .= " WHERE id = $id";
                
                if ($conn->query($updateQuery) === TRUE) {
                    $success = "Student updated successfully";
                    
                    // Refresh student data
                    $result = $conn->query($query);
                    $student = $result->fetch_assoc();
                } else {
                    $error = "Error updating student: " . $conn->error;
                }
            }
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
                <h1 class="mb-0">Edit Student</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Students</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
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
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Student Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Student Photo Upload -->
                        <div class="col-md-12 text-center mb-4">
                            <div class="avatar avatar-lg mb-3 d-inline-flex align-items-center justify-content-center bg-light" id="photo-preview">
                                <?php if (!empty($student['photo']) && file_exists("../../assets/images/students/" . $student['photo'])): ?>
                                    <img src="../../assets/images/students/<?php echo $student['photo']; ?>" class="avatar avatar-lg" alt="Student Photo">
                                <?php else: ?>
                                    <i class='bx bx-user' style="font-size: 50px;"></i>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="photo" class="form-label">Student Photo</label>
                                <input class="form-control" type="file" id="photo" name="photo" accept="image/jpeg, image/png">
                                <div class="form-text text-danger"></div>
                                <div class="form-text">Max file size: 5MB. Accepted formats: JPEG, PNG</div>
                            </div>
                        </div>
                        
                        <!-- Student ID -->
                        <div class="col-md-4 mb-3">
                            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo $student['student_id']; ?>" required>
                        </div>
                        
                        <!-- First Name -->
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $student['first_name']; ?>" required>
                        </div>
                        
                        <!-- Last Name -->
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $student['last_name']; ?>" required>
                        </div>
                        
                        <!-- Section -->
                        <div class="col-md-4 mb-3">
                            <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                            <select class="form-select" id="section" name="section" required>
                                <option value="CS1A" <?php echo ($student['section'] === 'CS1A') ? 'selected' : ''; ?>>CS1A</option>
                                <option value="CS2A" <?php echo ($student['section'] === 'CS2A') ? 'selected' : ''; ?>>CS2A</option>
                                <option value="CS3A" <?php echo ($student['section'] === 'CS3A') ? 'selected' : ''; ?>>CS3A</option>
                                <option value="CS4A" <?php echo ($student['section'] === 'CS4A') ? 'selected' : ''; ?>>CS4A</option>
                            </select>
                        </div>
                        
                        <!-- Sex -->
                        <div class="col-md-4 mb-3">
                            <label for="sex" class="form-label">Sex <span class="text-danger">*</span></label>
                            <select class="form-select" id="sex" name="sex" required>
                                <option value="Male" <?php echo ($student['sex'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($student['sex'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($student['sex'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <!-- Year Level -->
                        <div class="col-md-4 mb-3">
                            <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="year_level" name="year_level" required>
                                <option value="1" <?php echo ($student['year_level'] == 1) ? 'selected' : ''; ?>>1</option>
                                <option value="2" <?php echo ($student['year_level'] == 2) ? 'selected' : ''; ?>>2</option>
                                <option value="3" <?php echo ($student['year_level'] == 3) ? 'selected' : ''; ?>>3</option>
                                <option value="4" <?php echo ($student['year_level'] == 4) ? 'selected' : ''; ?>>4</option>
                            </select>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>">
                        </div>
                        
                        <!-- Phone -->
                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $student['phone']; ?>">
                        </div>
                        
                        <!-- Address -->
                        <div class="col-md-4 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="1"><?php echo $student['address']; ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Save Changes
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class='bx bx-arrow-back'></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview uploaded photo
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    
    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                photoPreview.innerHTML = `<img src="${e.target.result}" class="avatar avatar-lg" alt="Student Photo Preview">`;
            };
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Auto-select year level based on section
    const sectionSelect = document.getElementById('section');
    const yearLevelSelect = document.getElementById('year_level');
    
    sectionSelect.addEventListener('change', function() {
        const section = this.value;
        const yearLevel = section.charAt(2);
        
        yearLevelSelect.value = yearLevel;
    });
});
</script>