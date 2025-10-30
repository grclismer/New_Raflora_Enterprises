<?php
// guest/test_link.php
echo "<h2>Link Test</h2>";
echo "<p>Current URL: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>Project base: http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/</p>";

// Test the recover_account.php path
$test_link = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php";
echo "<p>Test Link: <a href='$test_link'>$test_link</a></p>";

echo "<p><a href='../user/user_login.php'>Back to Login</a></p>";
?>