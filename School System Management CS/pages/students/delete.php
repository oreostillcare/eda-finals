<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if student ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = sanitize_input($conn, $_GET['id']);

// Get student photo before deletion
$query = "SELECT photo FROM students WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    
    // Delete student photo if exists
    if (!empty($student['photo'])) {
        $photo_path = "../../assets/images/students/" . $student['photo'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
    
    // Delete student record
    $deleteQuery = "DELETE FROM students WHERE id = $id";
    
    if ($conn->query($deleteQuery) === TRUE) {
        $_SESSION['success_message'] = "Student deleted successfully";
    } else {
        $_SESSION['error_message'] = "Error deleting student: " . $conn->error;
    }
} else {
    $_SESSION['error_message'] = "Student not found";
}

// Redirect back to students list
header("Location: index.php");
exit();
?>