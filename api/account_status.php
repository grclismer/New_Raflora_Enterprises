<?php
// api/check_account_status.php - For testing
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id'])) {
    echo "Not logged in";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user_name, status, deactivation_date FROM accounts_tbl WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $status, $deactivation_date);
$stmt->fetch();

echo "User: $username<br>";
echo "Status: $status<br>";
echo "Deactivation Date: $deactivation_date<br>";

$stmt->close();
$conn->close();
?>