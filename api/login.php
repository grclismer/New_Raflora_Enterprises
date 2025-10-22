<?php
// api/login.php
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

    // Get ALL user data including status
    $stmt = $conn->prepare("SELECT user_id, user_name, password, role, status, recovery_token, deactivation_date FROM accounts_tbl WHERE user_name = ? OR email = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => "Database error"]);
        exit();
    }
    
    $stmt->bind_param("ss", $loginUsername, $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $db_user_name, $hashed_password, $db_role, $status, $recovery_token, $deactivation_date);
        $stmt->fetch();

        // DEBUG: Log everything
        error_log("=== LOGIN DEBUG ===");
        error_log("User: $db_user_name");
        error_log("Status: " . ($status ? $status : "EMPTY/NULL"));
        error_log("Deactivation Date: " . ($deactivation_date ? $deactivation_date : "EMPTY"));
        error_log("Recovery Token: " . ($recovery_token ? "EXISTS" : "NULL"));
        error_log("Password Match: " . (password_verify($loginPassword, $hashed_password) ? "YES" : "NO"));

        // CHECK FOR DEACTIVATED ACCOUNT - BLOCK LOGIN
        if ($status === 'deactivated') {
            error_log("✅ ACCOUNT IS DEACTIVATED - SHOWING RECOVERY LINKS");
            $days_remaining = ceil((strtotime($deactivation_date) - time()) / (60 * 60 * 24));
            $recovery_link = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php?token=" . $recovery_token;
            
            echo json_encode([
                'status' => 'error', 
                'message' => "Hello $db_user_name! Your account is currently deactivated. You have $days_remaining days remaining to recover your account. Check your email for the recovery link or click here to resend.",
                'recovery_info' => [
                    'days_remaining' => $days_remaining,
                    'recovery_link' => $recovery_link
                ]
            ]);
            $stmt->close();
            $conn->close();
            exit();
        }

        // CHECK FOR PENDING DELETION ACCOUNT
        if ($status === 'pending_deletion') {
            error_log("✅ ACCOUNT IS PENDING DELETION - SHOWING RECOVERY LINKS");
            $days_remaining = ceil((strtotime($deactivation_date) - time()) / (60 * 60 * 24));
            $recovery_link = "http://" . $_SERVER['HTTP_HOST'] . "/raflora_enterprises/guest/recover_account.php?token=" . $recovery_token;
            
            echo json_encode([
                'status' => 'error', 
                'message' => "Hello $db_user_name! Your account is scheduled for deletion. You have $days_remaining days remaining to recover your account.",
                'recovery_info' => [
                    'days_remaining' => $days_remaining,
                    'recovery_link' => $recovery_link
                ]
            ]);
            $stmt->close();
            $conn->close();
            exit();
        }

        // Only allow login if account is ACTIVE
        if ($status !== 'active') {
            error_log("❌ ACCOUNT STATUS IS NOT ACTIVE: " . ($status ? $status : "EMPTY/NULL"));
            echo json_encode(['status' => 'error', 'message' => 'Account is not active. Please contact support.']);
            $stmt->close();
            $conn->close();
            exit();
        }

        // Now check password (only for active accounts)
        error_log("✅ ACCOUNT IS ACTIVE - CHECKING PASSWORD");
        if (password_verify($loginPassword, $hashed_password)) {
            
            if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT)) {
                $newHash = password_hash($loginPassword, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE accounts_tbl SET password = ? WHERE user_id = ?");
                $update_stmt->bind_param("si", $newHash, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_user_name;
            $_SESSION['is_logged_in'] = true;
            $_SESSION['role'] = $db_role;

            if (isset($_POST['remember_me'])) {
                setcookie('user_id', $user_id, time() + (86400 * 30), "/"); 
                setcookie('username', $db_user_name, time() + (86400 * 30), "/");
            }
            
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