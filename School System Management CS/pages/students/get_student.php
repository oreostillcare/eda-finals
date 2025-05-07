<?php
session_start();
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Student ID is required']);
    exit();
}

$id = sanitize_input($conn, $_GET['id']);

// Get student data
$query = "SELECT * FROM students WHERE id = $id";
$result = $conn->query($query);

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($student);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Student not found']);
}
?>