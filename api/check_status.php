<?php
// api/check_status.php
session_start();
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $stmt = $conn->prepare("SELECT user_id, user_name, status, deactivation_date, recovery_token FROM accounts_tbl WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $db_user_name, $status, $deactivation_date, $recovery_token);
    $stmt->fetch();
    
    echo "User: $db_user_name<br>";
    echo "Status: $status<br>";
    echo "Deactivation Date: $deactivation_date<br>";
    echo "Recovery Token: $recovery_token<br>";
    
    $stmt->close();
}

$conn->close();
?>