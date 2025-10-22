<?php
// guest/recover_account.php
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
$message = '';
$success = false;
$valid_token = false;
$token_expired = false;

if ($token) {
    // FIXED: Check for accounts with this recovery token, regardless of status
    $stmt = $conn->prepare("SELECT user_id, token_expires_at, status FROM accounts_tbl WHERE recovery_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $expires_at, $status);
        $stmt->fetch();
        
        $current_time = time();
        $expire_time = strtotime($expires_at);
        
        if ($expire_time > $current_time) {
            $valid_token = true;
            // Recover the account - set to active and clear all deactivation fields
            $recover_stmt = $conn->prepare("UPDATE accounts_tbl SET status = 'active', deletion_requested_at = NULL, deactivation_date = NULL, recovery_token = NULL, token_expires_at = NULL WHERE user_id = ?");
            $recover_stmt->bind_param("i", $user_id);
            
            if ($recover_stmt->execute()) {
                $success = true;
                $message = "Account recovered successfully! You can now login to your account.";
            } else {
                $message = "Failed to recover account. Please try again.";
            }
            $recover_stmt->close();
        } else {
            $valid_token = false;
            $token_expired = true;
            // Clean up expired token
            $delete_stmt = $conn->prepare("UPDATE accounts_tbl SET recovery_token = NULL, token_expires_at = NULL WHERE user_id = ?");
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    } else {
        // Token not found at all
        $valid_token = false;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery - Raflora Enterprises</title>
    <link rel="stylesheet" href="../assets/css/user/login.css">
</head>
<body>
    <div class="Login-form">
        <div class="wrapper">
            <form>
                <span class="return">
                    <a href="../guest/g-home.php" class="close-btn">X</a>
                </span>
                
                <div class="form-header">
                    <h1>Account Recovery</h1>
                </div>

                <?php if ($success): ?>
                    <div class="success-state">
                        <div class="success-icon">✓</div>
                        <h3>Account Recovered!</h3>
                        <p><?php echo $message; ?></p>
                        <a href="../guest/g-home.php" class="btn btn-primary">Continue to Login</a>
                    </div>
                <?php elseif ($token && !$valid_token): ?>
                    <div class="error-state">
                        <div class="error-icon">⚠️</div>
                        <h3><?php echo $token_expired ? 'Link Expired' : 'Invalid Link'; ?></h3>
                        <p>
                            <?php echo $token_expired 
                                ? 'This recovery link has expired. Please request a new recovery email.'
                                : 'This recovery link is invalid or has already been used.'; 
                            ?>
                        </p>
                        <div style="margin-top: 15px;">
                            <a href="../guest/g-home.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Return to Login
                            </a>
                        </div>
                    </div>
                <?php elseif (!$token): ?>
                    <div class="error-state">
                        <div class="error-icon">❌</div>
                        <h3>Missing Recovery Link</h3>
                        <p>No recovery token provided. Please check your email for the recovery link.</p>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>