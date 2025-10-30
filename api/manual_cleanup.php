<?php
// api/manual_cleanup.php - FOR TESTING 30 SECOND EXPIRY
date_default_timezone_set('Asia/Manila');

echo "<h2>🚨 30-Second Account Cleanup Test</h2>";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "❌ Database connection failed";
    exit();
}

$current_time = date('Y-m-d H:i:s');
echo "<p>Current time: $current_time</p>";

// Find expired accounts (deactivated more than 30 seconds ago)
$stmt = $conn->prepare("SELECT user_id, user_name, email, deactivation_date FROM accounts_tbl WHERE status = 'deactivated' AND deactivation_date < ?");
$stmt->bind_param("s", $current_time);
$stmt->execute();
$stmt->store_result();

echo "<p>Found " . $stmt->num_rows . " expired accounts (30+ seconds old)</p>";

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $user_name, $email, $deactivation_date);
    
    echo "<ul>";
    while ($stmt->fetch()) {
        $seconds_old = time() - strtotime($deactivation_date);
        echo "<li>EXPIRED: $user_name ($email) - deactivated " . $seconds_old . " seconds ago</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No expired accounts found (all are within 30 seconds).</p>";
}

$stmt->close();

// Show all deactivated accounts
echo "<h3>All Deactivated Accounts:</h3>";
$all_stmt = $conn->prepare("SELECT user_id, user_name, email, deactivation_date, status FROM accounts_tbl WHERE status = 'deactivated'");
$all_stmt->execute();
$all_stmt->bind_result($user_id, $user_name, $email, $deactivation_date, $status);

echo "<ul>";
while ($all_stmt->fetch()) {
    $seconds_remaining = strtotime($deactivation_date) - time();
    $status_text = $seconds_remaining > 0 ? "⏰ " . $seconds_remaining . " seconds remaining" : "❌ EXPIRED";
    echo "<li>$user_name - Deactivation: $deactivation_date - $status_text</li>";
}
echo "</ul>";
$all_stmt->close();

$conn->close();

echo '<p><a href="../user/user_login.php">Back to Login</a></p>';
?>