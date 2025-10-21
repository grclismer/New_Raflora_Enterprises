<?php
// update_password_only.php
session_start();
date_default_timezone_set('Asia/Manila');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $_SESSION['error'] = "Database connection failed";
    header("Location: account_settings.php");
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "User not logged in";
    header("Location: account_settings.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get ONLY password data
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Debug: Check what we're receiving
error_log("Password Update - User: $user_id, Current: " . (!empty($current_password) ? "set" : "empty") . ", New: " . (!empty($new_password) ? "set" : "empty") . ", Confirm: " . (!empty($confirm_password) ? "set" : "empty"));

// Validate ALL password fields are filled
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error'] = "All password fields are required.";
    header("Location: account_settings.php");
    exit();
}

// Check if new passwords match
if ($new_password !== $confirm_password) {
    $_SESSION['error'] = "New passwords do not match.";
    header("Location: account_settings.php");
    exit();
}

// Check password length
if (strlen($new_password) < 6) {
    $_SESSION['error'] = "New password must be at least 6 characters long.";
    header("Location: account_settings.php");
    exit();
}

// Get current password from database
$stmt = $conn->prepare("SELECT password FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($hashed_password);
$stmt->fetch();
$stmt->close();

// Verify current password
if (!password_verify($current_password, $hashed_password)) {
    $_SESSION['error'] = "Current password is incorrect.";
    header("Location: account_settings.php");
    exit();
}

// Hash new password
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in database
$update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE user_id = ?");
$update_stmt->bind_param("si", $new_hashed_password, $user_id);

if ($update_stmt->execute()) {
    $_SESSION['success'] = "Password updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update password. Please try again.";
}

$update_stmt->close();
$conn->close();

// Redirect back to account settings
header("Location: account_settings.php");
exit();
?>