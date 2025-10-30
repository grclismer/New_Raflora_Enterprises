<?php
// reset_password.php - UPDATED FOR RECOVERY SUPPORT
date_default_timezone_set('Asia/Manila');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed");
}

$token = $_GET['token'] ?? '';
$is_recovery = isset($_GET['recovery']);
$message = '';
$success = false;
$valid_token = false;
$email = '';
$token_expired = false;

// Validate token
if ($token) {
    // Check password_resets table (used for both forgot password and recovery)
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $expires_at);
        $stmt->fetch();
        
        $current_time = time();
        $expire_time = strtotime($expires_at);
        
        if ($expire_time > $current_time) {
            $valid_token = true;
        } else {
            $valid_token = false;
            $token_expired = true;
            // Clean up expired token
            $cleanup_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $cleanup_stmt->bind_param("s", $token);
            $cleanup_stmt->execute();
            $cleanup_stmt->close();
        }
    }
    $stmt->close();
}

// Handle password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid_token) {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $message = "Please fill in all fields";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match";
    } else {
        // Update password in accounts_tbl
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
        
        if ($update_stmt->execute()) {
            // If this is a recovery, also activate the account
            if ($is_recovery) {
                $activate_stmt = $conn->prepare("UPDATE accounts_tbl SET status = 'active', deactivation_date = NULL WHERE email = ?");
                $activate_stmt->bind_param("s", $email);
                $activate_stmt->execute();
                $activate_stmt->close();
            }
            
            // Delete used token
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delete_stmt->bind_param("s", $token);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            $success = true;
            $message = $is_recovery 
                ? "Account recovered successfully! Your password has been reset. You can now login with your new password."
                : "Password reset successfully! You can now login with your new password.";
        } else {
            $message = "Failed to reset password. Please try again.";
        }
        $update_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_recovery ? 'Account Recovery' : 'Reset Password'; ?> - Raflora Enterprises</title>
    <link rel="stylesheet" href="assets/css/user/login.css">
    <link rel="stylesheet" href="assets/css/user/reset_password.css">
</head>
<body>
    <div class="Login-form">
        <div class="wrapper">
            <form method="POST" id="resetPasswordForm">
                <span class="return">
                    <a href="user/user_login.php" class="close-btn">X</a>
                </span>
                
                <div class="form-header">
                    <div class="logo-container">
                        <h1><?php echo $is_recovery ? 'Account Recovery' : 'Reset Password'; ?></h1>
                    </div>
                    <p class="form-subtitle"><?php echo $is_recovery ? 'Set a new password to recover your account' : 'Secure your account with a new password'; ?></p>
                </div>
                
                <!-- Messages -->
                <?php if ($message): ?>
                    <div class="message-container <?php echo $success ? 'success' : 'error'; ?>">
                        <div class="message-icon">
                            <?php if ($success): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle"></i>
                            <?php endif; ?>
                        </div>
                        <div class="message-content">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Reset Password Form -->
                <?php if (!$success && $valid_token): ?>
                    <div class="form-body">
                        <div class="input-group">
                            <div class="input-box">
                                <input type="password" id="password" name="password" placeholder=" " required minlength="6">
                                <label for="password">
                                    <i class="fas fa-lock"></i>
                                    New Password
                                </label>
                            </div>
                           
                            <div class="input-box">
                                <input type="password" id="confirm_password" name="confirm_password" placeholder=" " required minlength="6">
                                <label for="confirm_password">
                                    <i class="fas fa-lock"></i>
                                    Confirm New Password
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-key"></i>
                            <?php echo $is_recovery ? 'Recover Account' : 'Reset Password'; ?>
                        </button>
                    </div>
                    
                <?php elseif ($token && !$valid_token): ?>
                    <!-- Token exists but is invalid/expired -->
                    <div class="error-state">
                        <div class="error-icon">
                            <?php if ($token_expired): ?>
                                <i class="fas fa-clock"></i>
                            <?php else: ?>
                                <i class="fas fa-link-slash"></i>
                            <?php endif; ?>
                        </div>
                        <div class="error-content">
                            <h3>
                                <?php if ($token_expired): ?>
                                    Link Expired
                                <?php else: ?>
                                    Invalid Link
                                <?php endif; ?>
                            </h3>
                            <p>
                                <?php if ($token_expired): ?>
                                    This <?php echo $is_recovery ? 'recovery' : 'reset'; ?> link has expired.
                                <?php else: ?>
                                    This <?php echo $is_recovery ? 'recovery' : 'reset'; ?> link is invalid or has already been used.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="error-actions">
                            <a href="user/user_login.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Return to Login
                            </a>
                        </div>
                    </div>
                    
                <?php elseif (!$token): ?>
                    <!-- No token provided -->
                    <div class="error-state">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="error-content">
                            <h3>Missing <?php echo $is_recovery ? 'Recovery' : 'Reset'; ?> Link</h3>
                            <p>No <?php echo $is_recovery ? 'recovery' : 'reset'; ?> token was provided.</p>
                        </div>
                        <div class="error-actions">
                            <a href="user/user_login.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i>
                                Return to Login
                            </a>
                        </div>
                    </div>
                    
                <?php endif; ?>

                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="success-state">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="success-content">
                            <h3><?php echo $is_recovery ? 'Account Recovered!' : 'Password Updated!'; ?></h3>
                            <p><?php echo $message; ?></p>
                        </div>
                        <div class="success-actions">
                            <a href="user/user_login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i>
                                Continue to Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
                    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
</body>
</html>