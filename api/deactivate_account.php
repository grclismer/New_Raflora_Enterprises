<?php
// api/deactivate_account.php - FIXED VERSION
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Connection failed"]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user data for email
$user_stmt = $conn->prepare("SELECT first_name, last_name, email FROM accounts_tbl WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($first_name, $last_name, $user_email);
$user_stmt->fetch();
$user_stmt->close();

// Generate recovery token
$recovery_token = bin2hex(random_bytes(32));
$deactivation_date = date('Y-m-d H:i:s', strtotime('+30 days'));
$token_expires = date('Y-m-d H:i:s', strtotime('+30 days'));

// FIXED: Set status to 'deactivated' and all other fields
$stmt = $conn->prepare("UPDATE accounts_tbl SET status = 'deactivated', deletion_requested_at = NOW(), deactivation_date = ?, recovery_token = ?, token_expires_at = ? WHERE user_id = ?");
$stmt->bind_param("sssi", $deactivation_date, $recovery_token, $token_expires, $user_id);

if ($stmt->execute()) {
    // DEBUG: Verify the update worked
    error_log("✅ DEACTIVATION SUCCESS - User ID: $user_id");
    error_log("✅ Set status to 'deactivated', deactivation_date: $deactivation_date");
    
    // Verify the update
    $verify_stmt = $conn->prepare("SELECT status, deactivation_date, recovery_token FROM accounts_tbl WHERE user_id = ?");
    $verify_stmt->bind_param("i", $user_id);
    $verify_stmt->execute();
    $verify_stmt->bind_result($new_status, $new_deactivation_date, $new_recovery_token);
    $verify_stmt->fetch();
    $verify_stmt->close();
    
    error_log("✅ VERIFICATION - Status: $new_status, Deactivation Date: $new_deactivation_date, Token: " . ($new_recovery_token ? "EXISTS" : "MISSING"));
    
    // Send recovery email
    if (sendRecoveryEmail($user_email, $first_name, $recovery_token, $deactivation_date)) {
        // Logout user
        session_destroy();
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Account deactivated. You cannot login for 30 days unless you recover your account. Check your email for recovery link.'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Account deactivated but failed to send recovery email. Contact support.']);
    }
} else {
    error_log("❌ DEACTIVATION FAILED - User ID: $user_id");
    echo json_encode(['status' => 'error', 'message' => 'Failed to deactivate account']);
}

$stmt->close();
$conn->close();

function sendRecoveryEmail($email, $name, $token, $deactivation_date) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'enterprisesraflora@gmail.com';
        $mail->Password   = 'kmklwrltbmthicfh';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('enterprisesraflora@gmail.com', 'Raflora Enterprises');
        $mail->addAddress($email, $name);
        
        $recoveryLink = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php?token=" . $token;
        $days_remaining = ceil((strtotime($deactivation_date) - time()) / (60 * 60 * 24));
        
        $mail->isHTML(true);
        $mail->Subject = 'Account Recovery - Raflora Enterprises';
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                .container { max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
                .footer { padding: 20px; text-align: center; background: #ddd; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌺 Raflora Enterprises</h1>
                    <h2>Account Recovery</h2>
                </div>
                <div class='content'>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>Your account has been deactivated and will be permanently deleted on $deactivation_date.</p>
                    <p>You have <strong>$days_remaining days</strong> to recover your account.</p>
                    <p>Click the button below to recover your account immediately:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='$recoveryLink' class='button'>🔓 Recover My Account</a>
                    </p>
                    <p>Or copy and paste this link in your browser:</p>
                    <div style='background: #eee; padding: 10px; border-radius: 5px; word-break: break-all;'>$recoveryLink</div>
                    <p style='color: #d9534f;'><strong>⚠️ This recovery link expires in 30 days.</strong></p>
                    <p>If you didn't deactivate your account, please recover it immediately to secure your account.</p>
                </div>
                <div class='footer'>
                    <p>Need help? Contact us at enterprisesraflora@gmail.com</p>
                    <p>&copy; " . date('Y') . " Raflora Enterprises. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Hello $name, Recover your account here: $recoveryLink (expires in 30 days)";
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("PHPMailer Exception: " . $e->getMessage());
        return false;
    }
}
?>