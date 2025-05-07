<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Handle search query
$search = isset($_GET['search']) ? sanitize_input($conn, $_GET['search']) : '';

// Prepare query
$query = "SELECT * FROM students";
if (!empty($search)) {
    $query .= " WHERE student_id LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR section LIKE '%$search%'";
}
$query .= " ORDER BY year_level ASC, section ASC, last_name ASC, first_name ASC";

// Execute query
$result = $conn->query($query);

include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-0">Students</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Students</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-students" name="search" placeholder="Search student by name, ID, or section" value="<?php echo $search; ?>">
                        <button class="btn btn-primary" type="submit" aria-label="Search">
                            <i class='bx bx-search'></i> Search
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="add.php" class="btn btn-success me-2">
                    <i class='bx bx-plus'></i> Add New Student
                </a>
                <button class="btn btn-secondary print-all-btn" id="print-all-btn">
                    <i class='bx bx-printer'></i> Print All
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Student Records</h5>
                <span class="badge bg-primary"><?php echo $result->num_rows; ?> students</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="students-table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Section</th>
                                <th>Sex</th>
                                <th>Year Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['photo']) && file_exists("../../assets/images/students/" . $row['photo'])): ?>
                                        <img src="../../assets/images/students/<?php echo $row['photo']; ?>" class="avatar" alt="Student Photo">
                                    <?php else: ?>
                                        <div class="avatar d-flex align-items-center justify-content-center bg-light">
                                            <i class='bx bx-user'></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['student_id']; ?></td>
                                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                <td><?php echo $row['section']; ?></td>
                                <td><?php echo $row['sex']; ?></td>
                                <td><?php echo $row['year_level']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info view-btn" data-bs-toggle="modal" data-bs-target="#viewModal" data-id="<?php echo $row['id']; ?>">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $row['id']; ?>" data-name="<?php echo $row['first_name'] . ' ' . $row['last_name']; ?>">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-secondary print-btn" 
                                            data-print-target="student-print-<?php echo $row['id']; ?>" 
                                            aria-label="Print <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>">
                                        <i class='bx bx-printer'></i>
                                    </button>
                                    
                                    <!-- Hidden content for printing this student -->
                                    <div id="student-print-<?php echo $row['id']; ?>" class="d-none">
                                        <div class="student-header">
                                            <?php if (!empty($row['photo']) && file_exists("../../assets/images/students/" . $row['photo'])): ?>
                                                <img src="../../assets/images/students/<?php echo $row['photo']; ?>" class="student-image" alt="Student Photo">
                                            <?php else: ?>
                                                <div class="student-image d-flex align-items-center justify-content-center bg-light">
                                                    <i class='bx bx-user' style="font-size: 50px;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <h2 class="student-name"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></h2>
                                            <p class="student-id">Student ID: <?php echo $row['student_id']; ?></p>
                                        </div>
                                        <div class="student-details">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><span class="detail-label">Section:</span> <?php echo $row['section']; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="detail-label">Year Level:</span> <?php echo $row['year_level']; ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><span class="detail-label">Sex:</span> <?php echo $row['sex']; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="detail-label">Email:</span> <?php echo $row['email'] ?? 'N/A'; ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><span class="detail-label">Phone:</span> <?php echo $row['phone'] ?? 'N/A'; ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="detail-label">Date Added:</span> <?php echo date('F d, Y', strtotime($row['created_at'])); ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <p><span class="detail-label">Address:</span> <?php echo $row['address'] ?? 'N/A'; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No student records found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Hidden printable content for "Print All" functionality -->
        <div id="printable-all-content" style="display: none;">
            <div class="print-header text-center mb-4">
                <h2>Computer Science Student Records</h2>
                <p>Date Generated: <?php echo date('F d, Y'); ?></p>
            </div>
            
            <table class="table table-bordered" id="print-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Section</th>
                        <th>Sex</th>
                        <th>Year Level</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset result pointer
                    mysqli_data_seek($result, 0);
                    if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['student_id']; ?></td>
                        <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                        <td><?php echo $row['section']; ?></td>
                        <td><?php echo $row['sex']; ?></td>
                        <td><?php echo $row['year_level']; ?></td>
                        <td><?php echo $row['email'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['phone'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['address'] ?? 'N/A'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No student records found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="print-footer text-center mt-4">
                <p>CS Student Management System &copy; <?php echo date('Y'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- View Student Modal with responsive layout -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="student-detail">
                <div class="text-center mb-4">
                    <div class="mb-3 d-flex align-items-center justify-content-center">
                        <img id="student-image" src="" class="avatar avatar-lg" alt="Student Photo">
                    </div>
                    <h5 id="student-name" class="mb-0"></h5>
                    <p id="student-id" class="text-muted"></p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Section:</strong></p>
                        <p id="student-section"></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Year Level:</strong></p>
                        <p id="student-year"></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Sex:</strong></p>
                        <p id="student-sex"></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Email:</strong></p>
                        <p id="student-email"></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Phone:</strong></p>
                        <p id="student-phone"></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Date Added:</strong></p>
                        <p id="student-date"></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Address:</strong></p>
                    <p id="student-address"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="edit-student" class="btn btn-primary">Edit</a>
                <button type="button" id="print-student" class="btn btn-info print-btn" data-print-target="student-detail">
                    <i class='bx bx-printer me-1'></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-student-name"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirm-delete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>