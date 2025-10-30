<?php
// api/deactivate_account.php - 30 DAYS VERSION
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

// 30 DAYS - Normal version
$recovery_token = bin2hex(random_bytes(32));
$deactivation_date = date('Y-m-d H:i:s', strtotime('+30 days'));
$token_expires = date('Y-m-d H:i:s', strtotime('+30 days'));

// Set status to 'deactivated' and all other fields
$stmt = $conn->prepare("UPDATE accounts_tbl SET status = 'deactivated', deletion_requested_at = NOW(), deactivation_date = ?, recovery_token = ?, token_expires_at = ? WHERE user_id = ?");
$stmt->bind_param("sssi", $deactivation_date, $recovery_token, $token_expires, $user_id);

if ($stmt->execute()) {
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
        $days_remaining = max(1, ceil((strtotime($deactivation_date) - time()) / (60 * 60 * 24)));
        
        $mail->isHTML(true);
        $mail->Subject = 'Account Recovery - Raflora Enterprises';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: #667eea; color: white; padding: 30px; text-align: center; }
                .content { padding: 30px; }
                .button { background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 16px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌺 Raflora Enterprises</h1>
                    <h2>Account Recovery Required</h2>
                </div>
                <div class='content'>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>Your account has been deactivated. To recover your account, you need to set a new password.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='$recoveryLink' class='button' style='color: white; text-decoration: none;'>🔓 Recover My Account</a>
                    </div>
                    
                    <p>Or copy this link:</p>
                    <div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px;'>$recoveryLink</div>
                    
                    <p><strong>What happens next:</strong></p>
                    <ol>
                        <li>Click the link above</li>
                        <li>Set a new password for your account</li>
                        <li>Your account will be automatically reactivated</li>
                        <li>Login with your new password</li>
                    </ol>
                    
                    <p><small>This link will expire in <strong>$days_remaining days</strong>.</small></p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Recover your account: $recoveryLink (expires in $days_remaining days)";
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("PHPMailer Exception: " . $e->getMessage());
        return false;
    }
}
?>