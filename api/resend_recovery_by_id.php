<?php
// api/resend_recovery_by_id.php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['user_id'] ?? '';

if (empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit();
}
// Check if account is actually deactivated - FLEXIBLE VERSION
$trimmed_status = trim($status);
$lower_status = strtolower($trimmed_status);

// Allow for different case variations
if (!in_array($lower_status, ['deactivated', 'inactive', 'disabled'])) {
    error_log("DEBUG - Status check failed. Status: '$lower_status'");
    echo json_encode([
        'status' => 'error', 
        'message' => 'This account is not deactivated. Current status: ' . $status
    ]);
    exit();
}
// Find the account by user_id
$stmt = $conn->prepare("SELECT user_id, user_name, first_name, last_name, email, status, deactivation_date FROM accounts_tbl WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Account not found.']);
    exit();
}

$stmt->bind_result($user_id, $username, $first_name, $last_name, $user_email, $status, $deactivation_date);
$stmt->fetch();
$stmt->close();

// Check if account is actually deactivated
if ($status !== 'deactivated') {
    echo json_encode(['status' => 'error', 'message' => 'This account is not deactivated.']);
    exit();
}

// Generate new recovery token
$recovery_token = bin2hex(random_bytes(32));
$token_expires = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));

// Update the account with new token
$update_stmt = $conn->prepare("UPDATE accounts_tbl SET recovery_token = ?, token_expires_at = ? WHERE user_id = ?");
$update_stmt->bind_param("ssi", $recovery_token, $token_expires, $user_id);

if (!$update_stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to generate recovery link. Please try again.']);
    exit();
}
$update_stmt->close();

// Calculate days remaining
$days_remaining = 30;
if ($deactivation_date) {
    $deactivation_time = strtotime($deactivation_date);
    $current_time = time();
    $days_remaining = max(1, ceil(($deactivation_time - $current_time) / (60 * 60 * 24)));
}

// Create recovery link
$recovery_link = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php?token=" . $recovery_token;

// Send email (for testing, you can set this to true)
$email_sent = sendRecoveryEmail($user_email, $first_name, $recovery_link, $deactivation_date, $days_remaining);

if ($email_sent) {
    echo json_encode([
        'status' => 'success', 
        'message' => 'Recovery email sent successfully! Please check your email for the recovery link.'
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Failed to send recovery email. Please try again or contact support.'
    ]);
}

$conn->close();

function sendRecoveryEmail($to_email, $first_name, $recovery_link, $deactivation_date, $days_remaining) {
    // Check if we're on localhost - you can keep this for development
    $is_localhost = ($_SERVER['HTTP_HOST'] === 'localhost' || 
                    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
    
    if ($is_localhost) {
        // Localhost behavior - save to file (optional, you can remove this if you want real emails even locally)
        $log_message = "[" . date('Y-m-d H:i:s') . "] RECOVERY LINK\n";
        $log_message .= "For: $first_name ($to_email)\n";
        $log_message .= "Recovery Link: $recovery_link\n";
        $log_message .= "Days Remaining: $days_remaining\n";
        $log_message .= "----------------------------------------\n";
        
        file_put_contents('recovery_links.log', $log_message, FILE_APPEND);
        error_log("LOCALHOST: Recovery link saved to file: $recovery_link");
        
        // If you want to test real emails even on localhost, remove the return true below
        return true;
    }
    
    // PRODUCTION: Use PHPMailer for reliable email delivery
    try {
        // Include PHPMailer - adjust the path based on where you have PHPMailer
        require_once '../vendor/autoload.php'; // Adjust path as needed
        // Or if you have manual include:
        // require_once '../path/to/PHPMailer/src/PHPMailer.php';
        // require_once '../path/to/PHPMailer/src/SMTP.php';
        // require_once '../path/to/PHPMailer/src/Exception.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'enterprisesraflora@gmail.com'; // Your Gmail
        $mail->Password = 'your-app-password-here'; // Gmail App Password (see setup below)
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Enable verbose debugging for troubleshooting
        // $mail->SMTPDebug = 2; // Uncomment for debugging
        // $mail->Debugoutput = 'error_log'; // Uncomment for debugging
        
        // Recipients
        $mail->setFrom('noreply@raflora.com', 'Raflora Enterprises');
        $mail->addAddress($to_email, $first_name);
        $mail->addReplyTo('enterprisesraflora@gmail.com', 'Raflora Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Raflora Enterprises - Account Recovery';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; color: #856404; }
                .link-box { background: #e9ecef; padding: 15px; border-radius: 5px; word-break: break-all; font-family: monospace; font-size: 12px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌺 Raflora Enterprises</h1>
                    <h2>Account Recovery</h2>
                </div>
                
                <div class='content'>
                    <h3>Hello $first_name,</h3>
                    
                    <p>Your account has been deactivated and will be permanently deleted in <strong>$days_remaining days</strong>.</p>
                    
                    <p>Click the button below to recover your account immediately:</p>
                    
                    <p style='text-align: center;'>
                        <a href='$recovery_link' class='button'>🔓 Recover My Account</a>
                    </p>
                    
                    <p>Or copy and paste this link in your browser:</p>
                    <div class='link-box'>$recovery_link</div>
                    
                    <div class='warning'>
                        <strong>⚠️ This recovery link expires in 30 days.</strong>
                    </div>
                    
                    <p>If you didn't deactivate your account, please recover it immediately to secure your account.</p>
                    
                    <p>Need help? Contact us at enterprisesraflora@gmail.com</p>
                </div>
                
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Raflora Enterprises. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Plain text version for email clients that don't support HTML
        $mail->AltBody = "Hello $first_name,\n\nYour account has been deactivated and will be permanently deleted in $days_remaining days.\n\nRecovery Link: $recovery_link\n\nThis recovery link expires in 30 days.\n\nIf you didn't deactivate your account, please recover it immediately.\n\nNeed help? Contact enterprisesraflora@gmail.com";
        
        $mail->send();
        error_log("Recovery email sent successfully to: $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed for $to_email: " . $e->getMessage());
        return false;
    }
}
?>