<?php
// api/upload_image.php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Check if file was uploaded
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== 0) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
    exit;
}

// Get current profile picture to delete later
$oldPicturePath = null;
$stmt = $conn->prepare("SELECT profile_picture FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $oldPicturePath = $user['profile_picture'];
}
$stmt->close();

// Validate file
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$fileType = $_FILES['profile_picture']['type'];

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.']);
    exit;
}

// Check file size (5MB max)
if ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'File size too large. Maximum size is 5MB.']);
    exit;
}

// Create upload directory if it doesn't exist
$uploadDir = '../uploads/profile_pictures/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
$fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
$filePath = $uploadDir . $fileName;

// Move uploaded file
if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $filePath)) {
    $profilePicturePath = 'uploads/profile_pictures/' . $fileName;
    
    // Update database
    $stmt = $conn->prepare("UPDATE accounts_tbl SET profile_picture = ? WHERE user_id = ?");
    $stmt->bind_param("si", $profilePicturePath, $userId);
    
    if ($stmt->execute()) {
        // Delete old profile picture if it exists
        if (!empty($oldPicturePath) && $oldPicturePath !== $profilePicturePath) {
            $oldFullPath = '../' . ltrim($oldPicturePath, '/');
            if (file_exists($oldFullPath) && is_file($oldFullPath)) {
                unlink($oldFullPath);
            }
        }
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Profile picture updated successfully!',
            'profile_picture' => $profilePicturePath
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
}

$conn->close();
?>