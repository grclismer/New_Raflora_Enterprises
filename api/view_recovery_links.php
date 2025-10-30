<?php
// api/view_recovery_links.php
echo "<h2>Recovery Links</h2>";
echo "<p>Since you're on localhost, emails aren't actually sent. Here are your recovery links:</p>";

$log_file = 'recovery_links.log';
if (file_exists($log_file)) {
    $links = file_get_contents($log_file);
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>" . htmlspecialchars($links) . "</pre>";
    
    // Extract the latest recovery link
    $lines = explode("\n", $links);
    $latest_link = '';
    foreach (array_reverse($lines) as $line) {
        if (strpos($line, 'Recovery Link:') !== false) {
            $latest_link = trim(str_replace('Recovery Link:', '', $line));
            break;
        }
    }
    
    if ($latest_link) {
        echo "<h3>Latest Recovery Link:</h3>";
        echo "<p><a href='$latest_link' target='_blank'>$latest_link</a></p>";
        echo "<button onclick=\"navigator.clipboard.writeText('$latest_link').then(() => alert('Link copied!'))\">Copy Link</button>";
    }
} else {
    echo "<p>No recovery links found yet. Try sending a recovery email first.</p>";
}

echo '<p><a href="../user/user_login.php">← Back to Login</a></p>';
?>