<?php
// api/check_status.php - Debug script
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = 'test_account';
$stmt = $conn->prepare("SELECT user_id, user_name, status, LENGTH(status) as status_length, HEX(status) as status_hex FROM accounts_tbl WHERE user_name = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $user_name, $status, $status_length, $status_hex);
    $stmt->fetch();
    
    echo "<h2>Status Debug for: $user_name</h2>";
    echo "<p>Status: '$status'</p>";
    echo "<p>Status Length: $status_length</p>";
    echo "<p>Status in Hex: $status_hex</p>";
    echo "<p>Trimmed Status: '" . trim($status) . "'</p>";
    echo "<p>Lowercase Status: '" . strtolower(trim($status)) . "'</p>";
    
    // Test comparison
    $test_status = strtolower(trim($status));
    if ($test_status === 'deactivated') {
        echo "<p style='color: green;'>✓ Status matches 'deactivated'</p>";
    } else {
        echo "<p style='color: red;'>✗ Status does NOT match 'deactivated'</p>";
        echo "<p>Expected: 'deactivated'</p>";
        echo "<p>Got: '$test_status'</p>";
    }
} else {
    echo "User not found";
}

$stmt->close();
$conn->close();
?>