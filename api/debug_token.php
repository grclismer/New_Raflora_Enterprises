<?php
// api/debug_token.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

// Get all accounts with recovery tokens
$result = $conn->query("SELECT user_id, user_name, recovery_token, token_expires_at, status FROM accounts_tbl WHERE recovery_token IS NOT NULL");

echo "<h3>Accounts with Recovery Tokens:</h3>";
while ($row = $result->fetch_assoc()) {
    echo "User: " . $row['user_name'] . "<br>";
    echo "Token: " . substr($row['recovery_token'], 0, 20) . "...<br>";
    echo "Expires: " . $row['token_expires_at'] . "<br>";
    echo "Status: " . $row['status'] . "<br>";
    echo "Valid: " . (strtotime($row['token_expires_at']) > time() ? "YES" : "NO") . "<br>";
    echo "<hr>";
}

$conn->close();
?>