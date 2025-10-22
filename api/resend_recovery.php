<?php
// api/resend_recovery.php - AUTO-FIX VERSION
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
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

$search_username = $_POST['username'] ?? '';

if (empty($search_username)) {
    echo json_encode(['status' => 'error', 'message' => 'Username is required']);
    exit();
}

// Find the account regardless of status
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, recovery_token, deactivation_date, status FROM accounts_tbl WHERE user_name = ? OR email = ?");
$stmt->bind_param("ss", $search_username, $search_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Account not found']);
    exit();
}

$stmt->bind_result($user_id, $first_name, $last_name, $user_email, $recovery_token, $deactivation_date, $status);
$stmt->fetch();
$stmt->close();

// AUTO-FIX: If deactivation date is in the past or doesn't exist, set a new one
$current_time = time();
$deactivation_timestamp = $deactivation_date ? strtotime($deactivation_date) : 0;

if ($deactivation_timestamp < $current_time) {
    // Date is in the past or doesn't exist, set new 30-day period
    $new_deactivation_date = date('Y-m-d H:i:s', strtotime('+30 days'));
    $deactivation_date = $new_deactivation_date;
    
    $update_stmt = $conn->prepare("UPDATE accounts_tbl SET deactivation_date = ?, status = 'deactivated' WHERE user_id = ?");
    $update_stmt->bind_param("si", $new_deactivation_date, $user_id);
    $update_stmt->execute();
    $update_stmt->close();
    
    error_log("✅ Fixed deactivation date for user $user_id - new date: $new_deactivation_date");
}

// Generate new token (always generate fresh token when resending)
$recovery_token = bin2hex(random_bytes(32));
$token_expires = date('Y-m-d H:i:s', strtotime('+30 days'));

$update_stmt = $conn->prepare("UPDATE accounts_tbl SET recovery_token = ?, token_expires_at = ?, status = 'deactivated' WHERE user_id = ?");
$update_stmt->bind_param("ssi", $recovery_token, $token_expires, $user_id);
$update_stmt->execute();
$update_stmt->close();

// Send email
if (sendRecoveryEmail($user_email, $first_name, $recovery_token, $deactivation_date)) {
    echo json_encode(['status' => 'success', 'message' => 'Recovery email sent successfully! Check your inbox.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send recovery email. Please try again.']);
}

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
                    <p>If you didn't request account deactivation, please recover your account immediately.</p>
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