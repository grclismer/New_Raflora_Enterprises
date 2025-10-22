<?php
// api/delete_account.php - Permanent deletion
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Connection failed"]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// PERMANENT DELETE - No recovery
$stmt = $conn->prepare("DELETE FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    session_destroy();
    echo json_encode([
        'status' => 'success', 
        'message' => 'Account permanently deleted. All data has been removed.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete account']);
}

$stmt->close();
$conn->close();
?>