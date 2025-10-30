<?php
// guest/recover_account.php - REDIRECT TO PASSWORD RESET VERSION
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
$user_id = null;
$email = '';

if ($token) {
    // Check for valid recovery token
    $stmt = $conn->prepare("SELECT user_id, token_expires_at, email FROM accounts_tbl WHERE recovery_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $expires_at, $email);
        $stmt->fetch();
        
        $current_time = time();
        $expire_time = strtotime($expires_at);
        
        if ($expire_time > $current_time) {
            $valid_token = true;
            
            // Generate a password reset token (similar to forgot_password.php)
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', time() + (24 * 60 * 60)); // 24 hours
            
            // Store reset token in password_resets table
            $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete_stmt->bind_param("s", $email);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            $insert_stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $email, $reset_token, $reset_expires);
            
            if ($insert_stmt->execute()) {
                // Clean up recovery token and redirect to password reset
                $update_stmt = $conn->prepare("UPDATE accounts_tbl SET recovery_token = NULL, token_expires_at = NULL WHERE user_id = ?");
                $update_stmt->bind_param("i", $user_id);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Redirect to password reset page
                header("Location: ../reset_password.php?token=" . $reset_token . "&recovery=1");
                exit();
            } else {
                $message = "Failed to process recovery. Please try again.";
            }
            $insert_stmt->close();
            
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
    <style>
        .success-state, .error-state {
            text-align: center;
            padding: 30px;
        }
        .success-icon, .error-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .success-icon {
            color: #28a745;
        }
        .error-icon {
            color: #dc3545;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 10px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
    </style>
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
                        <a href="../user/user_login.php" class="btn">Continue to Login</a>
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
                            <a href="../user/user_login.php" class="btn">
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
                        <div style="margin-top: 15px;">
                            <a href="../user/user_login.php" class="btn">
                                <i class="fas fa-arrow-left"></i>
                                Return to Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>