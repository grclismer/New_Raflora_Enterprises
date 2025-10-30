<?php
// api/resend_recovery.php - 30 DAYS VERSION
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

$input = json_decode(file_get_contents('php://input'), true);
$search_username = $input['username'] ?? '';

if (empty($search_username)) {
    echo json_encode(['status' => 'error', 'message' => 'Username is required']);
    exit();
}

// Find the account
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, deactivation_date FROM accounts_tbl WHERE user_name = ?");
$stmt->bind_param("s", $search_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Account not found']);
    exit();
}

$stmt->bind_result($user_id, $first_name, $last_name, $user_email, $deactivation_date);
$stmt->fetch();
$stmt->close();

// 30 DAYS - Normal version
$recovery_token = bin2hex(random_bytes(32));
$token_expires = date('Y-m-d H:i:s', strtotime('+30 days'));

// Update account with recovery token
$update_stmt = $conn->prepare("UPDATE accounts_tbl SET recovery_token = ?, token_expires_at = ? WHERE user_id = ?");
$update_stmt->bind_param("ssi", $recovery_token, $token_expires, $user_id);
$update_stmt->execute();
$update_stmt->close();

// Send email
if (sendRecoveryEmail($user_email, $first_name, $recovery_token, $token_expires)) {
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
        
        // Simple URL construction
        $recoveryLink = "http://localhost/raflora_enterprises/guest/recover_account.php?token=" . $token;
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
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}
?>