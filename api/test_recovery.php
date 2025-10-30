<?php
// api/test_recovery_links.php - FOR TESTING ONLY
echo "<h1>Recovery Links Log</h1>";
echo "<p>This file is for testing purposes only. Remove in production.</p>";

if (file_exists('recovery_links.log')) {
    $links = file_get_contents('recovery_links.log');
    echo "<pre>" . htmlspecialchars($links) . "</pre>";
} else {
    echo "<p>No recovery links logged yet.</p>";
}

echo '<p><a href="#" onclick="window.location.href=\'../user/user_login.php\'">Go to Login</a></p>';
?>