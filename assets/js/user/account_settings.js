// Current active section in left column
let activeLeftSection = 'userInfo';

// Toggle Password Section
function togglePasswordSection() {
    const userInfoSection = document.getElementById('userInfoSection');
    const passwordSection = document.getElementById('passwordSection');
    const qrCodeSection = document.getElementById('qrCodeSection');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const toggleQRBtn = document.getElementById('toggleQRBtn');
    
    if (activeLeftSection !== 'password') {
        // Show password section, hide others
        userInfoSection.classList.add('hidden');
        passwordSection.classList.remove('hidden');
        qrCodeSection.classList.add('hidden');
        
        // Update button states
        togglePasswordBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
        togglePasswordBtn.style.background = '#6c757d';
        toggleQRBtn.innerHTML = '<i class="fas fa-qrcode"></i> View QR Code';
        toggleQRBtn.style.background = '';
        
        activeLeftSection = 'password';
    } else {
        // Show user info, hide password
        userInfoSection.classList.remove('hidden');
        passwordSection.classList.add('hidden');
        
        // Reset button
        togglePasswordBtn.innerHTML = '<i class="fas fa-edit"></i> Change Password';
        togglePasswordBtn.style.background = '';
        
        activeLeftSection = 'userInfo';
        clearPasswordFields();
    }
}

// Toggle QR Code Section
function toggleQRCodeSection() {
    const userInfoSection = document.getElementById('userInfoSection');
    const passwordSection = document.getElementById('passwordSection');
    const qrCodeSection = document.getElementById('qrCodeSection');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const toggleQRBtn = document.getElementById('toggleQRBtn');
    
    if (activeLeftSection !== 'qrCode') {
        // Show QR code section, hide others
        userInfoSection.classList.add('hidden');
        passwordSection.classList.add('hidden');
        qrCodeSection.classList.remove('hidden');
        
        // Update button states
        toggleQRBtn.innerHTML = '<i class="fas fa-times"></i> Close';
        toggleQRBtn.style.background = '#6c757d';
        togglePasswordBtn.innerHTML = '<i class="fas fa-edit"></i> Change Password';
        togglePasswordBtn.style.background = '';
        
        activeLeftSection = 'qrCode';
    } else {
        // Show user info, hide QR code
        userInfoSection.classList.remove('hidden');
        qrCodeSection.classList.add('hidden');
        
        // Reset button
        toggleQRBtn.innerHTML = '<i class="fas fa-qrcode"></i> View QR Code';
        toggleQRBtn.style.background = '';
        
        activeLeftSection = 'userInfo';
    }
}

// Function to clear password fields
function clearPasswordFields() {
    const currentPassword = document.getElementById('current-password');
    const newPassword = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-new-password');
    
    if (currentPassword) currentPassword.value = '';
    if (newPassword) newPassword.value = '';
    if (confirmPassword) confirmPassword.value = '';
    
    // Uncheck show password toggle
    const showPasswordToggle = document.getElementById('showPasswordToggle');
    if (showPasswordToggle) showPasswordToggle.checked = false;
    
    // Reset password fields to hidden
    if (currentPassword) currentPassword.type = 'password';
    if (newPassword) newPassword.type = 'password';
    if (confirmPassword) confirmPassword.type = 'password';
}

// Password Visibility Toggle
function setupPasswordToggle() {
    const showPasswordToggle = document.getElementById('showPasswordToggle');
    const currentPassword = document.getElementById('current-password');
    const newPassword = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-new-password');
    
    if (showPasswordToggle) {
        showPasswordToggle.addEventListener('change', function() {
            const show = this.checked;
            const type = show ? 'text' : 'password';
            
            if (currentPassword) currentPassword.type = type;
            if (newPassword) newPassword.type = type;
            if (confirmPassword) confirmPassword.type = type;
        });
    }
}

// Profile Picture Functions
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('hidden');
}

function previewImage(event) {
    const input = event.target;
    const profileImage = document.getElementById('profileImage');
    const avatarIcon = document.getElementById('avatarIcon');
    const profileMenu = document.getElementById('profileMenu');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            profileImage.src = e.target.result;
            profileImage.classList.remove('hidden');
            avatarIcon.classList.add('hidden');
            profileMenu.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeProfilePicture() {
    const profileImage = document.getElementById('profileImage');
    const avatarIcon = document.getElementById('avatarIcon');
    const profileMenu = document.getElementById('profileMenu');
    const profileUpload = document.getElementById('profile-upload');
    
    profileImage.src = '';
    profileImage.classList.add('hidden');
    avatarIcon.classList.remove('hidden');
    profileMenu.classList.add('hidden');
    profileUpload.value = '';
    
    toggleProfileMenu();
}

function viewProfileImage() {
    const profileImage = document.getElementById('profileImage');
    const modal = document.getElementById('imageViewModal');
    const modalImage = document.getElementById('modalProfileImage');
    
    if (profileImage.src && !profileImage.classList.contains('hidden')) {
        modalImage.src = profileImage.src;
        modal.classList.remove('hidden');
    }
}

function closeImageView() {
    const modal = document.getElementById('imageViewModal');
    modal.classList.add('hidden');
}

// Message Modal Functions
function showMessage(title, content, showCancel = false) {
    const modal = document.getElementById('message-box');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    const cancelBtn = document.getElementById('modal-cancel');
    
    modalTitle.textContent = title;
    modalContent.textContent = content;
    
    if (showCancel) {
        cancelBtn.classList.remove('hidden');
    } else {
        cancelBtn.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
}

function closeMessageModal() {
    const modal = document.getElementById('message-box');
    modal.classList.add('hidden');
}

// QR Code Functions
function downloadQRCode() {
    const qrImage = document.getElementById('qrCodeImage');
    const link = document.createElement('a');
    link.href = qrImage.src;
    link.download = 'my-login-qr-code.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Delete Account Function - FIXED VERSION
function handleDelete() {
    showMessage(
        'Delete Account', 
        'Are you sure you want to delete your account? You will have 30 days to recover it via email.',
        true
    );
    
    // Remove any existing event listeners first
    const confirmBtn = document.querySelector('.confirm-btn');
    const cancelBtn = document.getElementById('modal-cancel');
    
    // Clear previous event listeners by cloning and replacing
    const newConfirmBtn = confirmBtn.cloneNode(true);
    const newCancelBtn = cancelBtn.cloneNode(true);
    
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
    
    // Set up new event listeners
    newConfirmBtn.onclick = function() {
        // Call the soft delete API
        fetch('../api/delete_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete'
        })
        .then(response => response.json())
        .then(data => {
            closeMessageModal();
            if (data.status === 'success') {
                showMessage('Account Deletion Requested', data.message);
                // Redirect to home page after successful deletion request
                setTimeout(() => {
                    window.location.href = '../guest/g-home.php';
                }, 3000);
            } else {
                showMessage('Error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            closeMessageModal();
            showMessage('Error', 'An error occurred while processing your request.');
        });
    };
    
    newCancelBtn.onclick = function() {
        closeMessageModal();
    };
}
// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup toggle buttons
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const toggleQRBtn = document.getElementById('toggleQRBtn');
    
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', togglePasswordSection);
    }
    
    if (toggleQRBtn) {
        toggleQRBtn.addEventListener('click', toggleQRCodeSection);
    }
    
    // Setup other functionalities
    setupPasswordToggle();
    setupPasswordForm(); // Add this line
    setupProfileForm();  // Add this line
    
    
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    });
    
    // Clear password fields on page load
    clearPasswordFields();
    
    console.log('Account settings initialized successfully');
});


// Profile Picture Upload Function
function uploadProfilePicture(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showMessage('Error', 'Please select a valid image file (JPEG, PNG, GIF, or WebP).');
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showMessage('Error', 'Image size must be less than 5MB.');
        return;
    }

    const formData = new FormData();
    formData.append('profile_picture', file);
    formData.append('user_id', '<?php echo $user_data["user_id"]; ?>');

    // Show loading state
    showMessage('Uploading', 'Please wait while we upload your profile picture...');

    fetch('../api/upload_image.php', {  // Add ../api/
    method: 'POST',
    body: formData
})
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update profile image display
            const profileImage = document.getElementById('profileImage');
            const avatarIcon = document.getElementById('avatarIcon');
            const profileMenu = document.getElementById('profileMenu');
            
            profileImage.src = '../' + data.profile_picture + '?t=' + new Date().getTime();
            profileImage.classList.remove('hidden');
            avatarIcon.classList.add('hidden');
            profileMenu.classList.remove('hidden');
            
            showMessage('Success', data.message);
        } else {
            showMessage('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error', 'An error occurred while uploading the image.');
    });
}

// Remove Profile Picture Function
function removeProfilePicture() {
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        return;
    }

    const formData = new FormData();
    formData.append('user_id', '<?php echo $user_data["user_id"]; ?>');

    showMessage('Removing', 'Please wait while we remove your profile picture...');

    fetch('../api/remove_profile.php', {  // Add ../api/
    method: 'POST',
    body: formData
})
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update profile image display
            const profileImage = document.getElementById('profileImage');
            const avatarIcon = document.getElementById('avatarIcon');
            const profileMenu = document.getElementById('profileMenu');
            
            profileImage.src = '';
            profileImage.classList.add('hidden');
            avatarIcon.classList.remove('hidden');
            profileMenu.classList.add('hidden');
            
            showMessage('Success', data.message);
            toggleProfileMenu();
        } else {
            showMessage('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error', 'An error occurred while removing the profile picture.');
    });
}


// Add this function to handle password form submission
function setupPasswordForm() {
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault(); // This prevents the normal form submission
            
            const formData = new FormData(this);
            
            showMessage('Updating', 'Please wait while we update your password...');
            
            fetch('../api/change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('Success', data.message);
                    // Clear password fields and switch back to user info after success
                    clearPasswordFields();
                    setTimeout(() => {
                        togglePasswordSection(); // Go back to user info section
                    }, 2000);
                } else {
                    showMessage('Error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Error', 'An error occurred while updating password.');
            });
        });
    }
}

// Also update the main profile form to prevent double submission
function setupProfileForm() {
    const profileForm = document.getElementById('settingsForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // Let this form submit normally since it redirects back to account_settings.php
            // with success/error messages in session
        });
    }
}


// Handle profile image click - different behavior based on whether image exists
function handleProfileImageClick() {
    const profileImage = document.getElementById('profileImage');
    
    // If no profile image exists, trigger file input directly
    if (profileImage.classList.contains('hidden')) {
        document.getElementById('profile-upload').click();
    } else {
        // If profile image exists, show the full image (view mode)
        viewProfileImage();
    }
}

// Toggle profile menu (only exists when image is present)
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Upload profile picture
function uploadProfilePicture(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showMessage('Error', 'Please select a valid image file (JPEG, PNG, GIF, or WebP).');
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showMessage('Error', 'Image size must be less than 5MB.');
        return;
    }

    const formData = new FormData();
    formData.append('profile_picture', file);
    formData.append('user_id', '<?php echo $user_data["user_id"]; ?>');

    showMessage('Uploading', 'Please wait while we upload your profile picture...');

    fetch('../api/upload_image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage('Success', data.message);
            // Reload the page to update the interface (show 3-dot menu now)
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error', 'An error occurred while uploading the image.');
    });
}

// Remove profile picture
function removeProfilePicture() {
    if (!confirm('Are you sure you want to remove your profile picture?')) {
        return;
    }

    const formData = new FormData();
    formData.append('user_id', '<?php echo $user_data["user_id"]; ?>');

    showMessage('Removing', 'Please wait while we remove your profile picture...');

    fetch('../api/remove_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage('Success', data.message);
            // Reload the page to update the interface (remove 3-dot menu)
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage('Error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error', 'An error occurred while removing the profile picture.');
    });
}

// Close dropdown when clicking outside (only when dropdown exists)
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    const profileContainer = document.querySelector('.profile-container');
    
    if (dropdown && !profileContainer.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});


// Validate phone number input - only allow numbers and enforce 11 digits
function validatePhoneNumber(input) {
    // Remove any non-digit characters
    input.value = input.value.replace(/\D/g, '');
    
    // Limit to 11 digits
    if (input.value.length > 11) {
        input.value = input.value.slice(1, 11);
    }
    
    // Visual feedback
    const isValid = input.value.length === 11;
    if (input.value.length > 0) {
        if (isValid) {
            input.style.borderColor = '#28a745'; // Green for valid
        } else {
            input.style.borderColor = '#dc3545'; // Red for invalid
        }
    } else {
        input.style.borderColor = '#e9ecef'; // Reset to default
    }
}


// Deactivate Account (Temporary)
function handleDeactivate() {
    showMessage(
        'Deactivate Account', 
        'Your account will be deactivated immediately. You will be logged out and cannot login again for 30 days unless you recover your account via email. Continue?',
        true
    );
    
    const confirmBtn = document.querySelector('.confirm-btn');
    const cancelBtn = document.getElementById('modal-cancel');
    
    confirmBtn.textContent = 'Deactivate';
    cancelBtn.textContent = 'Cancel';
    
    confirmBtn.onclick = function() {
        fetch('../api/deactivate_account.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            closeMessageModal();
            if (data.status === 'success') {
                showMessage('Account Deactivated', data.message);
                setTimeout(() => {
                    window.location.href = '../guest/g-home.php';
                }, 3000);
            } else {
                showMessage('Error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error', 'An error occurred while deactivating account.');
        });
    };
    
    cancelBtn.onclick = closeMessageModal;
}

// Delete Account (Permanent)
function handleDelete() {
    showMessage(
        'Permanently Delete Account', 
        '⚠️ WARNING: This will immediately and permanently delete your account and all data. This action cannot be undone!',
        true
    );
    
    const confirmBtn = document.querySelector('.confirm-btn');
    const cancelBtn = document.getElementById('modal-cancel');
    
    confirmBtn.textContent = 'Delete Forever';
    confirmBtn.style.background = '#e53e3e';
    cancelBtn.textContent = 'Cancel';
    
    confirmBtn.onclick = function() {
        fetch('../api/delete_account.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            closeMessageModal();
            if (data.status === 'success') {
                showMessage('Account Deleted', data.message);
                setTimeout(() => {
                    window.location.href = '../guest/g-home.php';
                }, 3000);
            } else {
                showMessage('Error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error', 'An error occurred while deleting account.');
        });
    };
    
    cancelBtn.onclick = closeMessageModal;
}


if (loginForm) {
    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        welcomeMessageLoginDiv.textContent = ''; // Clear previous message
        welcomeMessageLoginDiv.style.color = 'red';
        
        try {
            const response = await fetch('/raflora_enterprises/api/login.php', {
                method: 'POST',
                body: new FormData(this)
            });
            
            const responseText = await response.text();
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Server returned invalid response. Please try again.');
            }
            
            if (data.status === 'success') {
                welcomeMessageLoginDiv.textContent = 'Login successful! Redirecting...';
                welcomeMessageLoginDiv.style.color = 'green';
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            } else {
                // Show deactivation message with recovery info
                let errorMessage = data.message;
                
                // If there's recovery info, create a more detailed message
                if (data.recovery_info) {
                    const recoveryLink = data.recovery_info.recovery_link;
                    const daysRemaining = data.recovery_info.days_remaining;
                    
                    errorMessage += `\n\nDays remaining: ${daysRemaining}`;
                    
                    // Create a clickable recovery link
                    welcomeMessageLoginDiv.innerHTML = `
                        <div style="text-align: left;">
                            <p style="margin-bottom: 10px;">${data.message.split('.')[0]}.</p>
                            <p style="margin-bottom: 15px;"><strong>${daysRemaining} days remaining</strong></p>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <a href="${recoveryLink}" class="btn" style="background: #667eea; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">
                                    <i class="fas fa-envelope"></i> Recover Account
                                </a>
                                <button onclick="resendRecoveryEmail('${loginUsername}')" class="btn" style="background: #ed8936; color: white; padding: 8px 16px; border: none; border-radius: 5px; font-size: 0.9rem; cursor: pointer;">
                                    <i class="fas fa-redo"></i> Resend Email
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    welcomeMessageLoginDiv.textContent = errorMessage;
                }
                
                welcomeMessageLoginDiv.style.color = 'red';
                document.getElementById('password').value = '';
            }
        } catch (error) {
            welcomeMessageLoginDiv.textContent = error.message || 'An error occurred. Please try again.';
            welcomeMessageLoginDiv.style.color = 'red';
        }
    });
}

// Function to resend recovery email
async function resendRecoveryEmail(username) {
    try {
        const response = await fetch('/raflora_enterprises/api/resend_recovery.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${encodeURIComponent(username)}`
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            alert('Recovery email sent! Check your inbox.');
        } else {
            alert('Failed to send recovery email: ' + result.message);
        }
    } catch (error) {
        alert('Error sending recovery email: ' + error.message);
    }
}