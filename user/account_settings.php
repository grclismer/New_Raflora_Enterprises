<?php
session_start();

// Debug session
error_log("Account Settings - Session: " . print_r($_SESSION, true));

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'] ?? '';
        $_SESSION['is_logged_in'] = true;
    } else {
        header("Location: ../guest/login.php");
        exit();
    }
}

// Now get user data directly from database for the page
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

$conn = new mysqli($servername, $username, $password, $dbname);
$user_data = [];

if (!$conn->connect_error) {
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, user_name, email, mobile_number, address, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
        // Combine first and last name for full name display
        $user_data['full_name'] = trim($user_data['first_name'] . ' ' . $user_data['last_name']);
    }
    $stmt->close();
    $conn->close();
}

// Check for success/error messages
$success_message = '';
$error_message = '';

if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../assets/css/user/account_settings.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Account Settings Container -->
    <div id="settings-card" class="settings-card">
        <button class="home-btn">
            <a href="../user/landing.php"><i class="fas fa-times"></i></a>
        </button>
        <h2 class="settings-title">Account Settings</h2>

        <!-- Display Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

    `   <!-- Profile Picture Upload -->
        <div class="profile-picture-section">
            <div class="profile-container">
                <div class="profile-image-container" onclick="handleProfileImageClick()">
                    <i id="avatarIcon" class="fas fa-user profile-placeholder-icon <?php echo !empty($user_data['profile_picture']) ? 'hidden' : ''; ?>"></i>
                    <img id="profileImage" src="<?php echo !empty($user_data['profile_picture']) ? '../' . $user_data['profile_picture'] : ''; ?>" alt="Profile" class="profile-image <?php echo empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                    
                    <!-- Only show 3-dot menu when there's an image -->
                    <?php if (!empty($user_data['profile_picture'])): ?>
                    <div class="profile-menu">
                        <button type="button" onclick="event.stopPropagation(); toggleProfileMenu()" class="profile-menu-button">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Upload hint overlay for when no image exists -->
                    <div id="uploadOverlay" class="upload-overlay <?php echo !empty($user_data['profile_picture']) ? 'hidden' : ''; ?>">
                        <i class="fas fa-camera"></i>
                        <span>Click to Upload Photo</span>
                    </div>
                </div>
                
                <!-- Dropdown menu (only for when image exists) -->
                <?php if (!empty($user_data['profile_picture'])): ?>
                <div id="profileDropdown" class="profile-dropdown hidden">
                    <button type="button" onclick="document.getElementById('profile-upload').click()" class="profile-dropdown-item">
                        <i class="fas fa-camera profile-dropdown-icon profile-dropdown-icon-change"></i>
                        Change Photo
                    </button>
                    <button type="button" onclick="removeProfilePicture()" class="profile-dropdown-item">
                        <i class="fas fa-trash profile-dropdown-icon profile-dropdown-icon-remove"></i>
                        Remove Photo
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <!-- File input -->
            <input type="file" id="profile-upload" class="profile-upload-input" accept="image/*" onchange="uploadProfilePicture(event)">
        </div>
`
        <!-- Landscape Layout Container -->
        <div class="landscape-layout">
            <!-- Left Column -->
            <div class="left-column">
                <!-- User Info Section (visible by default) -->
                <!-- User Info Section -->
        <div id="userInfoSection">
            <form id="settingsForm" method="POST" action="update_account.php">
            <input type="hidden" name="user_id" value="<?php echo $user_data['user_id']; ?>">
            
            <!-- Username and Full Name Group -->
            <div class="form-grid">
                <div class="form-group form-group-icon">
                    <input type="text" placeholder="Username" name="username" value="<?php echo $user_data['user_name'] ?? 'Loading...'; ?>" 
                            class="form-input" required autocomplete="username">
                    <i class="fa fa-user form-icon"></i>
                </div>
                <div class="form-group form-group-icon">
                    <input type="text" id="fullname" name="fullname" placeholder="Full Name" maxlength="50" required 
                            value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>"
                            class="form-input" autocomplete="name">
                    <i class="fa-solid fa-signature form-icon"></i>
                </div>
            </div>

            <!-- Email and Contact Number Group -->
            <div class="form-grid">
                    <div class="form-group form-group-icon">
                        <input type="email" id="reg-email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required
                            class="form-input" autocomplete="email">
                        <i class="fa fa-envelope form-icon"></i>
                    </div>
                    <div class="form-group form-group-icon">
                        <input type="tel" id="reg-phone" name="phone" placeholder="Contact Number" 
                            pattern="[0-9]{11}" title="Please enter exactly 11 digits (e.g., 09123456789)"
                            maxlength="11" 
                            value="<?php echo htmlspecialchars($user_data['mobile_number'] ?? ''); ?>"
                            class="form-input" autocomplete="tel" oninput="validatePhoneNumber(this)">
                        <i class="fa-solid fa-phone form-icon"></i>
                    </div>
                </div>
                
                <div class="form-group form-group-icon address-group">
                    <input type="text" id="reg-address" name="address" placeholder="Address (Optional)" maxlength="50"
                            value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>"
                            class="form-input" autocomplete="street-address">
                    <i class="fa-solid fa-map-location-dot form-icon"></i>
                </div>

                <!-- Save Button -->
                <button type="submit" class="save-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </form>
        </div>

                <!-- Password Section (hidden by default) -->
                <div id="passwordSection" class="hidden">
                    <form id="passwordForm" method="POST" action="update_password_only.php">
                        <input type="hidden" name="user_id" value="<?php echo $user_data['user_id']; ?>">
                        
                        <h3 class="section-title">Change Password</h3>
                        
                        <div class="password-group">
                            <div class="form-group form-group-icon">
                                <input type="password" id="current-password" name="current_password" placeholder="Current Password" 
                                    class="form-input" required autocomplete="current-password">
                                <i class="fa fa-lock form-icon"></i>
                            </div>
                            <div class="form-group form-group-icon">
                                <input type="password" id="new-password" name="new_password" placeholder="New Password" 
                                    class="form-input" required autocomplete="new-password">
                                <i class="fa fa-lock form-icon"></i>
                            </div>
                            <div class="form-group form-group-icon">
                                <input type="password" id="confirm-new-password" name="confirm_password" placeholder="Confirm New Password" 
                                    class="form-input" required autocomplete="new-password">
                                <i class="fa fa-lock form-icon"></i>
                            </div>
                            
                            <!-- Show Password Toggle -->
                            <div class="show-password-toggle">
                                <input type="checkbox" id="showPasswordToggle">
                                <label for="showPasswordToggle">
                                    <i class="fas fa-eye"></i> Show Passwords
                                </label>
                            </div>
                        </div>
                        
                        <div class="password-actions">
                            <button type="submit" class="save-btn">
                                <i class="fa-solid fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- QR Code Section (hidden by default) -->
                <div id="qrCodeSection" class="hidden">
                    <h3 class="section-title">QR Code Login</h3>
                    
                    <div class="qr-content">
                        <p class="qr-description">Use this QR code to log in quickly.<br> Save it to your phone and scan it on the login page.</p>
                        
                        <!-- QR Code Display -->
                        <div class="qr-code-container">
                            <div class="qr-code-display">
                                <img id="qrCodeImage" 
                                     src="../api/get_user_qr.php?user_id=<?php echo $user_data['user_id']; ?>" 
                                     alt="Your Login QR Code" 
                                     class="qr-image">
                            </div>
                            
                            <!-- Download Button -->
                            <button onclick="downloadQRCode()" class="download-qr-btn">
                                <i class="fas fa-download"></i> Save QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Security Section -->
                <div class="section-title security-title">Security</div>
                
                <!-- Change Password Button -->
                <div class="toggle-section">
                    <div class="toggle-header">
                        <h4 class="toggle-title">Change Password</h4>
                        <button type="button" id="togglePasswordBtn" class="toggle-btn">
                            <i class="fas fa-edit"></i> Change Password
                        </button>
                    </div>
                    <p class="toggle-description">Update your account password for enhanced security.</p>
                </div>
                
                <!-- QR Code Button -->
                <div class="toggle-section">
                    <div class="toggle-header">
                        <h4 class="toggle-title">QR Code Login</h4>
                        <button type="button" id="toggleQRBtn" class="toggle-btn">
                            <i class="fas fa-qrcode"></i> View QR Code
                        </button>
                    </div>
                    <p class="toggle-description">Access your QR code for quick mobile login.</p>
                </div>
                
                <!-- Danger Zone -->
                <div class="section-title danger-title">Account Actions</div>

                <!-- Deactivate Account -->
                <div class="action-section">
                    <div class="action-content">
                        <span class="action-title">Temporarily Deactivate Account</span>
                        <p class="action-description">Your account will be deactivated for 30 days.<br> You'll need to recover it via email to login again.</p>
                    <button onclick="handleDeactivate()" class="action-btn deactivate-btn">
                        Deactivate
                    </button>
                    </div>
                    <div class="action-content">
                        <span class="action-title">Permanently Delete Account</span>
                        <p class="action-description">Permanently remove your account<br> and all data. This action cannot be undone.</p>
                    <button onclick="handleDelete()" class="action-btn delete-btn">
                        Delete
                    </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Image View Modal -->
    <div id="imageViewModal" class="image-view-modal hidden">
        <div class="image-modal-content">
            <button onclick="closeImageView()" class="image-modal-close">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalProfileImage" src="" alt="Profile Preview" class="image-modal-preview">
        </div>
    </div>
    
    <!-- Message Modal (for alerts) -->
    <div id="message-box" class="message-modal hidden">
        <div class="modal-content">
            <h3 id="modal-title" class="modal-title">Message</h3>
            <p id="modal-content" class="modal-text">Content here.</p>
            <div class="modal-actions">
                <button onclick="closeMessageModal()" class="modal-btn confirm-btn">OK</button>
                <button id="modal-cancel" onclick="closeMessageModal()" class="modal-btn cancel-btn hidden">Cancel</button>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/user/account_settings.js"></script>
</body>
</html>