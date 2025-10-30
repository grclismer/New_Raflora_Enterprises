<?php
// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================
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

$input = json_decode(file_get_contents('php://input'), true);
$qr_data = $input['qr_data'] ?? '';

error_log("QR Data received: " . substr($qr_data, 0, 100));

if (empty($qr_data)) {
    error_log("QR Login: No QR data provided");
    echo json_encode(['status' => 'error', 'message' => 'QR data required']);
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

// Parse QR data
$qr_object = json_decode($qr_data, true);

if (!$qr_object || !isset($qr_object['user_id']) || $qr_object['system'] !== 'raflora_enterprises') {
    error_log("QR Login: Invalid QR format");
    echo json_encode(['status' => 'error', 'message' => 'Invalid QR code format']);
    exit();
}

$user_id = $qr_object['user_id'];
error_log("QR Login: Processing user ID - " . $user_id);

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
        $days_remaining = max(1, ceil(($deactivation_time - $current_time) / (60 * 60 * 24)));
    }
    
    error_log("QR Login: Account deactivated - Days: " . $days_remaining);
    
    echo json_encode([
        'status' => 'error', 
        'message' => 'Your account has been deactivated. Please check your email for recovery instructions.',
        'recovery_info' => [
            'days_remaining' => $days_remaining
        ]
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