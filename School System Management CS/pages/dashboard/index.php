<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get student counts for each year level
$studentCountQuery = "SELECT year_level, COUNT(*) as count FROM students GROUP BY year_level ORDER BY year_level";
$studentCountResult = $conn->query($studentCountQuery);

$yearLabels = [];
$studentCounts = [];

// Initialize with zeros
for ($i = 1; $i <= 4; $i++) {
    $yearLabels[] = "Year $i";
    $studentCounts[$i] = 0;
}

if ($studentCountResult && $studentCountResult->num_rows > 0) {
    while ($row = $studentCountResult->fetch_assoc()) {
        $studentCounts[$row['year_level']] = (int)$row['count'];
    }
}

// Get total student count
$totalStudentsQuery = "SELECT COUNT(*) as total FROM students";
$totalStudentsResult = $conn->query($totalStudentsQuery);
$totalStudents = 0;

if ($totalStudentsResult && $totalStudentsResult->num_rows > 0) {
    $row = $totalStudentsResult->fetch_assoc();
    $totalStudents = $row['total'];
}

// Get section counts
$sectionCountQuery = "SELECT section, COUNT(*) as count FROM students GROUP BY section";
$sectionCountResult = $conn->query($sectionCountQuery);

$sectionCounts = [
    'CS1A' => 0,
    'CS2A' => 0,
    'CS3A' => 0,
    'CS4A' => 0
];

if ($sectionCountResult && $sectionCountResult->num_rows > 0) {
    while ($row = $sectionCountResult->fetch_assoc()) {
        if (isset($sectionCounts[$row['section']])) {
            $sectionCounts[$row['section']] = (int)$row['count'];
        }
    }
}

// Get gender distribution
$genderCountQuery = "SELECT sex, COUNT(*) as count FROM students GROUP BY sex";
$genderCountResult = $conn->query($genderCountQuery);

$maleCount = 0;
$femaleCount = 0;
$otherCount = 0;

if ($genderCountResult && $genderCountResult->num_rows > 0) {
    while ($row = $genderCountResult->fetch_assoc()) {
        if ($row['sex'] == 'Male') {
            $maleCount = (int)$row['count'];
        } else if ($row['sex'] == 'Female') {
            $femaleCount = (int)$row['count'];
        } else {
            $otherCount = (int)$row['count'];
        }
    }
}
?>

<?php include_once '../../includes/header.php'; ?>
<?php include_once '../../includes/sidebar.php'; ?>

<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-1">Dashboard</h1>
                <p class="text-muted mb-0">Welcome to the CS Student Management System</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-primary p-2">Academic Year: 2025-2026</span>
            </div>
        </div>
        
        <!-- Stats Cards Row -->
        <div class="card-grid">
            <!-- Total Students Card -->
            <div class="card stats-card bg-gradient">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-uppercase mb-2 text-muted">Total Students</h6>
                            <h2 class="mb-0 fw-bold display-6"><?php echo $totalStudents; ?></h2>
                            <p class="text-muted mb-0 mt-2">
                                <i class='bx bxs-up-arrow me-1 text-success'></i>
                                <small>Enrolled this semester</small>
                            </p>
                        </div>
                        <div class="icon bg-primary bg-opacity-25 text-primary rounded-circle p-3">
                            <i class='bx bxs-graduation'></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Male Students Card -->
            <div class="card stats-card bg-gradient">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-uppercase mb-2 text-muted">Male Students</h6>
                            <h2 class="mb-0 fw-bold display-6"><?php echo $maleCount; ?></h2>
                            <p class="text-muted mb-0 mt-2">
                                <small><?php echo ($totalStudents > 0) ? round(($maleCount / $totalStudents) * 100) : 0; ?>% of total students</small>
                            </p>
                        </div>
                        <div class="icon bg-success bg-opacity-25 text-success rounded-circle p-3">
                            <i class='bx bx-male'></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Female Students Card -->
            <div class="card stats-card bg-gradient">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-uppercase mb-2 text-muted">Female Students</h6>
                            <h2 class="mb-0 fw-bold display-6"><?php echo $femaleCount; ?></h2>
                            <p class="text-muted mb-0 mt-2">
                                <small><?php echo ($totalStudents > 0) ? round(($femaleCount / $totalStudents) * 100) : 0; ?>% of total students</small>
                            </p>
                        </div>
                        <div class="icon bg-danger bg-opacity-25 text-danger rounded-circle p-3">
                            <i class='bx bx-female'></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sections Card -->
            <div class="card stats-card bg-gradient">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-uppercase mb-2 text-muted">Total Sections</h6>
                            <h2 class="mb-0 fw-bold display-6">4</h2>
                            <p class="text-muted mb-0 mt-2">
                                <small>CS Department</small>
                            </p>
                        </div>
                        <div class="icon bg-info bg-opacity-25 text-info rounded-circle p-3">
                            <i class='bx bxs-book'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chart and Section Stats Row -->
        <div class="row mt-4">
            <!-- Student Chart Column -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Student Distribution by Year Level</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" id="chartOptions" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chartOptions">
                                <li><a class="dropdown-item" href="#">View Details</a></li>
                                <li><a class="dropdown-item" href="#">Export as PDF</a></li>
                                <li><a class="dropdown-item" href="#">Print Chart</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                            <canvas id="studentChart" class="chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section Stats Column -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Section Distribution</h5>
                        <span class="badge bg-light text-dark"><?php echo $totalStudents; ?> Total</span>
                    </div>
                    <div class="card-body">
                        <div class="section-stats">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-medium">CS1A</span>
                                    <span class="badge bg-primary rounded-pill"><?php echo $sectionCounts['CS1A']; ?> students</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo ($totalStudents > 0) ? ($sectionCounts['CS1A'] / $totalStudents * 100) : 0; ?>%" aria-valuenow="<?php echo $sectionCounts['CS1A']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-medium">CS2A</span>
                                    <span class="badge bg-success rounded-pill"><?php echo $sectionCounts['CS2A']; ?> students</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($totalStudents > 0) ? ($sectionCounts['CS2A'] / $totalStudents * 100) : 0; ?>%" aria-valuenow="<?php echo $sectionCounts['CS2A']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-medium">CS3A</span>
                                    <span class="badge bg-danger rounded-pill"><?php echo $sectionCounts['CS3A']; ?> students</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo ($totalStudents > 0) ? ($sectionCounts['CS3A'] / $totalStudents * 100) : 0; ?>%" aria-valuenow="<?php echo $sectionCounts['CS3A']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-medium">CS4A</span>
                                    <span class="badge bg-info rounded-pill"><?php echo $sectionCounts['CS4A']; ?> students</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo ($totalStudents > 0) ? ($sectionCounts['CS4A'] / $totalStudents * 100) : 0; ?>%" aria-valuenow="<?php echo $sectionCounts['CS4A']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Links</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="../students/index.php" class="list-group-item list-group-item-action d-flex align-items-center border-0 py-3 px-4">
                                <div class="d-inline-flex align-items-center justify-content-center me-3 bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 40px; height: 40px;">
                                    <i class='bx bxs-user-detail'></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Manage Students</h6>
                                    <small class="text-muted">View, add, edit or delete student records</small>
                                </div>
                                <i class='bx bx-chevron-right ms-auto text-muted'></i>
                            </a>
                            <a href="../profile/index.php" class="list-group-item list-group-item-action d-flex align-items-center border-0 py-3 px-4">
                                <div class="d-inline-flex align-items-center justify-content-center me-3 bg-success bg-opacity-10 text-success rounded-circle" style="width: 40px; height: 40px;">
                                    <i class='bx bxs-user-circle'></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Update Profile</h6>
                                    <small class="text-muted">Manage your account information</small>
                                </div>
                                <i class='bx bx-chevron-right ms-auto text-muted'></i>
                            </a>
                            <a href="../students/index.php?print=all" class="list-group-item list-group-item-action d-flex align-items-center border-0 py-3 px-4">
                                <div class="d-inline-flex align-items-center justify-content-center me-3 bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 40px; height: 40px;">
                                    <i class='bx bxs-printer'></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Print All Records</h6>
                                    <small class="text-muted">Generate a printable version of all students</small>
                                </div>
                                <i class='bx bx-chevron-right ms-auto text-muted'></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gender Distribution Card -->
        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gender Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-4 text-center mb-4 mb-lg-0">
                                <canvas id="genderChart" height="200"></canvas>
                            </div>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="d-inline-flex align-items-center justify-content-center mb-3 text-primary rounded-circle" style="width: 50px; height: 50px; background-color: rgba(76, 110, 245, 0.1);">
                                                <i class='bx bxs-group' style="font-size: 24px;"></i>
                                            </div>
                                            <h3 class="fw-bold mb-1"><?php echo $totalStudents; ?></h3>
                                            <p class="text-muted mb-0">Total Students</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="d-inline-flex align-items-center justify-content-center mb-3 text-info rounded-circle" style="width: 50px; height: 50px; background-color: rgba(51, 154, 240, 0.1);">
                                                <i class='bx bx-male' style="font-size: 24px;"></i>
                                            </div>
                                            <h3 class="fw-bold mb-1"><?php echo $maleCount; ?></h3>
                                            <p class="text-muted mb-0">Male Students</p>
                                            <span class="badge bg-light text-dark mt-1"><?php echo ($totalStudents > 0) ? round(($maleCount / $totalStudents) * 100) : 0; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="d-inline-flex align-items-center justify-content-center mb-3 text-danger rounded-circle" style="width: 50px; height: 50px; background-color: rgba(250, 82, 82, 0.1);">
                                                <i class='bx bx-female' style="font-size: 24px;"></i>
                                            </div>
                                            <h3 class="fw-bold mb-1"><?php echo $femaleCount; ?></h3>
                                            <p class="text-muted mb-0">Female Students</p>
                                            <span class="badge bg-light text-dark mt-1"><?php echo ($totalStudents > 0) ? round(($femaleCount / $totalStudents) * 100) : 0; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>

<script>
// Initialize the student chart with PHP data
document.addEventListener('DOMContentLoaded', function() {
    // Student distribution chart
    initStudentChart(
        <?php echo json_encode($yearLabels); ?>,
        <?php echo json_encode(array_values($studentCounts)); ?>
    );
    
    // Gender distribution chart
    const genderData = {
        labels: ['Male', 'Female'],
        datasets: [{
            data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
            backgroundColor: ['#339af0', '#fa5252'],
            borderColor: ['#fff', '#fff'],
            borderWidth: 2,
            hoverOffset: 4
        }]
    };
    
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderChart = new Chart(genderCtx, {
        type: 'doughnut',
        data: genderData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });
});

function initStudentChart(labels, data) {
    const ctx = document.getElementById('studentChart').getContext('2d');
    const chartData = {
        labels: labels,
        datasets: [{
            label: 'Number of Students',
            data: data,
            backgroundColor: 'rgba(76, 110, 245, 0.6)',
            borderColor: '#4c6ef5',
            borderWidth: 2,
            borderRadius: 5,
            hoverBackgroundColor: 'rgba(76, 110, 245, 0.8)'
        }]
    };
    
    const studentChart = new Chart(ctx, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>