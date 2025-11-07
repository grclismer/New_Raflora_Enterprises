<?php
// FILE: generate_qr.php
// PURPOSE: Generates QR code data for login and stores session in database
// CALLED BY: Frontend via AJAX to get QR code data
// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');

session_start();
header('Content-Type: application/json');

// Add proper error handling for JSON
function sendJsonResponse($data) {
    echo json_encode($data);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(['status' => 'error', 'message' => 'User not logged in']);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    sendJsonResponse(['status' => 'error', 'message' => "Database connection failed"]);
}

$user_id = $_SESSION['user_id'];

// Generate unique session ID
$session_id = bin2hex(random_bytes(32));
$qr_data = json_encode([
    'session_id' => $session_id,
    'user_id' => $user_id,
    'action' => 'login',
    'timestamp' => time()
]);

// Set expiration (5 minutes from now)
$expires_at = date('Y-m-d H:i:s', time() + 300);

// Store in database
$stmt = $conn->prepare("INSERT INTO qr_login_sessions (session_id, user_id, qr_code_data, expires_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $session_id, $user_id, $qr_data, $expires_at);

if ($stmt->execute()) {
    sendJsonResponse([
        'status' => 'success', 
        'qr_data' => $qr_data,
        'session_id' => $session_id,
        'expires_at' => $expires_at
    ]);
} else {
    sendJsonResponse(['status' => 'error', 'message' => 'Failed to generate QR session']);
}

$stmt->close();
$conn->close();
?>