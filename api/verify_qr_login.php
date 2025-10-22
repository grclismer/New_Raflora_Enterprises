<?php
// api/verify_qr_login.php - UPDATED WITH STATUS CHECKS
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Get the POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['qr_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'No QR data provided']);
    exit();
}

$qrData = $data['qr_data'];
$qrObject = json_decode($qrData, true);

if (!$qrObject || !isset($qrObject['user_id']) || $qrObject['system'] !== 'raflora_enterprises' || $qrObject['method'] !== 'qr_login') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid QR code format']);
    exit();
}

$user_id = $qrObject['user_id'];

// Check if account exists and get ALL status info (SAME AS login.php)
$stmt = $conn->prepare("SELECT user_id, user_name, role, status, recovery_token, deactivation_date FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

$stmt->bind_result($db_user_id, $db_user_name, $db_role, $status, $recovery_token, $deactivation_date);
$stmt->fetch();
$stmt->close();

// DEBUG: Log the status
error_log("QR Login Attempt - User: $db_user_name, Status: " . ($status ? $status : "EMPTY/NULL"));

// CHECK FOR DEACTIVATED ACCOUNT - BLOCK LOGIN (SAME AS login.php)
if ($status === 'deactivated') {
    error_log("✅ QR LOGIN BLOCKED - Account deactivated");
    $days_remaining = ceil((strtotime($deactivation_date) - time()) / (60 * 60 * 24));
    $recovery_link = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php?token=" . $recovery_token;
    
    echo json_encode([
        'status' => 'error', 
        'message' => "Hello $db_user_name! Your account is currently deactivated. You have $days_remaining days remaining to recover your account.",
        'recovery_info' => [
            'days_remaining' => $days_remaining,
            'recovery_link' => $recovery_link,
            'user_name' => $db_user_name
        ]
    ]);
    exit();
}

// CHECK FOR PENDING DELETION ACCOUNT (SAME AS login.php)
if ($status === 'pending_deletion') {
    error_log("✅ QR LOGIN BLOCKED - Account pending deletion");
    $days_remaining = ceil((strtotime($deactivation_date) - time()) / (60 * 60 * 24));
    $recovery_link = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php?token=" . $recovery_token;
    
    echo json_encode([
        'status' => 'error', 
        'message' => "Hello $db_user_name! Your account is scheduled for deletion. You have $days_remaining days remaining to recover your account.",
        'recovery_info' => [
            'days_remaining' => $days_remaining,
            'recovery_link' => $recovery_link,
            'user_name' => $db_user_name
        ]
    ]);
    exit();
}

// Only allow login if account is ACTIVE (SAME AS login.php)
if ($status !== 'active') {
    error_log("❌ QR LOGIN BLOCKED - Account not active: " . ($status ? $status : "EMPTY/NULL"));
    echo json_encode(['status' => 'error', 'message' => 'Account is not active. Please contact support.']);
    exit();
}

// If account is active, proceed with login
error_log("✅ QR LOGIN SUCCESS - Account active");
$_SESSION['user_id'] = $db_user_id;
$_SESSION['username'] = $db_user_name;
$_SESSION['is_logged_in'] = true;
$_SESSION['role'] = $db_role;

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'user' => [
        'id' => $db_user_id,
        'name' => $db_user_name,
        'role' => $db_role
    ]
]);

$conn->close();
?>