<?php
// Run this daily via cron job
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn->connect_error) {
    // Permanently delete accounts where deactivation_date has passed
    $sql = "DELETE FROM accounts_tbl WHERE status = 'pending_deletion' AND deactivation_date < NOW()";
    $conn->query($sql);
    
    // Clean expired tokens
    $sql = "UPDATE accounts_tbl SET recovery_token = NULL, token_expires_at = NULL WHERE token_expires_at < NOW()";
    $conn->query($sql);
}

$conn->close();
?>