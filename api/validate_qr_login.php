<?php
// verify_qr_login.php - FIXED VERSION
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

// Enable detailed error reporting for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("QR Login Attempt: " . date('Y-m-d H:i:s'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("QR Login: Invalid request method");
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Get the raw input
$input = json_decode(file_get_contents('php://input'), true);
$qr_data = $input['qr_data'] ?? '';
$user_id = $input['user_id'] ?? 0;

error_log("QR Login - Raw QR Data: " . substr($qr_data, 0, 100));
error_log("QR Login - Direct User ID: " . $user_id);

// Try to get user_id from multiple sources
if (!$user_id && !empty($qr_data)) {
    $qr_object = json_decode($qr_data, true);
    if ($qr_object && isset($qr_object['user_id'])) {
        $user_id = $qr_object['user_id'];
        error_log("QR Login - User ID from QR object: " . $user_id);
    } elseif (is_numeric($qr_data)) {
        $user_id = intval($qr_data);
        error_log("QR Login - User ID from numeric QR: " . $user_id);
    }
}

if (empty($user_id) || $user_id <= 0) {
    error_log("QR Login: No valid user ID found");
    echo json_encode(['status' => 'error', 'message' => 'Invalid QR code: No user ID found']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("QR Login: Database connection failed - " . $conn->connect_error);
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Get user data with account status check
$stmt = $conn->prepare("
    SELECT user_id, user_name, role, status, deactivation_date 
    FROM accounts_tbl 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("QR Login: User not found - " . $user_id);
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

$user = $result->fetch_assoc();
error_log("QR Login: User found - " . $user['user_name'] . ", Status: " . $user['status']);

// Check account status
if ($user['status'] === 'deactivated') {
    // Calculate days remaining
    $days_remaining = 30;
    if ($user['deactivation_date']) {
        $deactivation_time = strtotime($user['deactivation_date']);
        $current_time = time();
        $days_passed = floor(($current_time - $deactivation_time) / (60 * 60 * 24));
        $days_remaining = max(0, 30 - $days_passed);
    }
    
    error_log("QR Login: Account deactivated - Days remaining: " . $days_remaining);
    
    echo json_encode([
        'status' => 'error', 
        'message' => 'Your account has been deactivated. Please check your email for recovery instructions.',
        'recovery_info' => [
            'days_remaining' => $days_remaining
        ],
        'user_id' => $user_id
    ]);
    exit();
}

if ($user['status'] !== 'active') {
    error_log("QR Login: Account not active - Status: " . $user['status']);
    echo json_encode(['status' => 'error', 'message' => 'Account is not active']);
    exit();
}

// Update last login timestamp
$update_stmt = $conn->prepare("UPDATE accounts_tbl SET last_login = NOW() WHERE user_id = ?");
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
$update_stmt->close();

// Set session for the logged-in user
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['user_name'];
$_SESSION['is_logged_in'] = true;
$_SESSION['role'] = $user['role'];

error_log("QR Login: Success - User logged in: " . $user['user_name']);

echo json_encode([
    'status' => 'success', 
    'message' => 'Login successful',
    'user' => [
        'user_id' => $user['user_id'],
        'username' => $user['user_name'],
        'role' => $user['role']
    ]
]);

$stmt->close();
$conn->close();
?>