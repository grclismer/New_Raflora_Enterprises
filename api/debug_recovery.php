<?php
// api/debug_recovery.php
echo "<h2>Recovery System Debug Info</h2>";

// Check if recovery_links.log exists and show contents
if (file_exists('recovery_links.log')) {
    echo "<h3>Recovery Links Log:</h3>";
    echo "<pre>" . htmlspecialchars(file_get_contents('recovery_links.log')) . "</pre>";
} else {
    echo "<p>No recovery links logged yet.</p>";
}

// Check error log location
echo "<h3>PHP Error Log:</h3>";
echo "<p>Error log location: " . ini_get('error_log') . "</p>";

// Test email function
echo "<h3>Test Email Function:</h3>";
echo "<p>HTTP Host: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>Is localhost: " . (($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) ? 'YES' : 'NO') . "</p>";

echo '<p><a href="../user/user_login.php">Go to Login</a></p>';
?>