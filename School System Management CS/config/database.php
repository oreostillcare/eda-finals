<?php
/**
 * Database Configuration File
 * School Management System for Computer Science Students
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Default XAMPP username
define('DB_PASS', '');      // Default XAMPP password (empty)
define('DB_NAME', 'school_management');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

// Create admin table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating admin table: " . $conn->error);
}

// Check if admin user exists, if not create default admin
$checkAdmin = $conn->query("SELECT * FROM admin WHERE username = 'lcc_admin'");
if ($checkAdmin->num_rows == 0) {
    // Hash the default password
    $defaultPassword = password_hash('lcc1947', PASSWORD_DEFAULT);
    
    // Insert default admin
    $sql = "INSERT INTO admin (username, password, name, email) VALUES ('lcc_admin', '$defaultPassword', 'System Administrator', 'admin@lcc.edu')";
    
    if ($conn->query($sql) === FALSE) {
        die("Error creating default admin: " . $conn->error);
    }
}

// Create students table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    section ENUM('CS1A', 'CS2A', 'CS3A', 'CS4A') NOT NULL,
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    year_level INT(1) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating students table: " . $conn->error);
}

// Function to secure and sanitize input data
function sanitize_input($conn, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Function for checking if user is logged in
function check_login() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}
?>