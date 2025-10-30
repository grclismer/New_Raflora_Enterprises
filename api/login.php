<?php
// login.php - 30 DAYS VERSION
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => "Database connection failed"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $loginUsername = $_POST['username'] ?? '';
    $loginPassword = $_POST['password'] ?? '';

    if (empty($loginUsername) || empty($loginPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, user_name, password, role, status, deactivation_date FROM accounts_tbl WHERE user_name = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => "Database error"]);
        exit();
    }
    
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $db_user_name, $hashed_password, $db_role, $status, $deactivation_date);
        $stmt->fetch();

        if (password_verify($loginPassword, $hashed_password)) {
            $status = strtolower(trim($status));
            $current_time = time();
            
            // Check if account should be deleted (expired - 30 days passed)
            if ($status === 'deactivated' && $deactivation_date && strtotime($deactivation_date) < $current_time) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'This account has been permanently deleted due to inactivity.'
                ]);
                exit();
            }
            
            // Check if account is deactivated
            if ($status === 'deactivated') {
                // Calculate days remaining
                $days_remaining = 30;
                if ($deactivation_date) {
                    $deactivation_time = strtotime($deactivation_date);
                    $days_remaining = max(1, ceil(($deactivation_time - $current_time) / (60 * 60 * 24)));
                }
                
                // Generate recovery token
                $recovery_token = bin2hex(random_bytes(32));
                $token_expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                $update_stmt = $conn->prepare("UPDATE accounts_tbl SET recovery_token = ?, token_expires_at = ? WHERE user_id = ?");
                $update_stmt->bind_param("ssi", $recovery_token, $token_expires, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
                
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Your account has been deactivated. Please check your email for recovery instructions.',
                    'recovery_info' => [
                        'days_remaining' => $days_remaining
                    ]
                ]);
                exit();
            }

            // Password rehash check
            if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT)) {
                $newHash = password_hash($loginPassword, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $newHash, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            // Set session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_user_name;
            $_SESSION['is_logged_in'] = true;
            $_SESSION['role'] = $db_role;

            // Remember me cookie
            if (isset($_POST['remember_me'])) {
                setcookie('user_id', $user_id, time() + (86400 * 30), "/"); 
                setcookie('username', $db_user_name, time() + (86400 * 30), "/");
            }
            
            // Redirect
            $redirect_url = '';
            if ($db_role === 'admin_type') {
                $redirect_url = '/raflora_enterprises/admin_dashboard/inventory.php';
            } elseif ($db_role === 'client_type') {
                $redirect_url = '/raflora_enterprises/user/landing.php';
            } else {
                $redirect_url = '/raflora_enterprises/user/landing.php';
            }
            
            echo json_encode(['status' => 'success', 'redirect_url' => $redirect_url]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>