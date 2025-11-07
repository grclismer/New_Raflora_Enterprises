<?php
// verify_qr_login.php - UPDATED (no last_login)
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['user_id'] ?? 0;

if (empty($user_id) || $user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Get user data
$stmt = $conn->prepare("SELECT user_id, user_name, role, status, deactivation_date FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

$user = $result->fetch_assoc();

// Check account status
if ($user['status'] === 'deactivated') {
    $days_remaining = 30;
    if ($user['deactivation_date']) {
        $deactivation_time = strtotime($user['deactivation_date']);
        $current_time = time();
        $days_passed = floor(($current_time - $deactivation_time) / (60 * 60 * 24));
        $days_remaining = max(0, 30 - $days_passed);
    }
    
    echo json_encode([
        'status' => 'error', 
        'message' => 'Your account has been deactivated.',
        'recovery_info' => ['days_remaining' => $days_remaining],
        'user_id' => $user_id
    ]);
    exit();
}

if ($user['status'] !== 'active') {
    echo json_encode(['status' => 'error', 'message' => 'Account is not active']);
    exit();
}

// REMOVED: last_login update since column doesn't exist

// Set session
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['user_name'];
$_SESSION['is_logged_in'] = true;
$_SESSION['role'] = $user['role'];

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