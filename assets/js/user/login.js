document.addEventListener('DOMContentLoaded', function() {
    const loginFormContainer = document.getElementById('loginFormContainer');
    const registerFormContainer = document.getElementById('registerFormContainer');
    const forgotPasswordContainer = document.getElementById('forgotPasswordContainer');

    const showRegisterLink = document.getElementById('showRegister');
    const showLoginFromRegisterLink = document.getElementById('showLoginFromRegister');
    const showForgotPasswordLink = document.getElementById('showForgotPassword');
    const showLoginFromForgotLink = document.getElementById('showLoginFromForgot');

    function showForm(formToShow) {
        loginFormContainer.classList.add('hidden');
        registerFormContainer.classList.add('hidden');
        forgotPasswordContainer.classList.add('hidden');
        formToShow.classList.remove('hidden');
    }

    if (showRegisterLink) {
        showRegisterLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(registerFormContainer);
        });
    }

    if (showLoginFromRegisterLink) {
        showLoginFromRegisterLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(loginFormContainer);
        });
    }

    if (showForgotPasswordLink) {
        showForgotPasswordLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(forgotPasswordContainer);
        });
    }

    if (showLoginFromForgotLink) {
        showLoginFromForgotLink.addEventListener('click', function(event) {
            event.preventDefault();
            showForm(loginFormContainer);
        });
    }

    // --- Login Form Functionality ---
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const loginForm = document.getElementById('loginForm');
    const welcomeMessageLoginDiv = document.getElementById('welcomeMessageLogin');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function(e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            welcomeMessageLoginDiv.textContent = '';
            welcomeMessageLoginDiv.style.color = 'red';
            welcomeMessageLoginDiv.style.display = 'block';
            
            try {
                const response = await fetch('/raflora_enterprises/api/login.php', {
                    method: 'POST',
                    body: new FormData(this)
                });
                
                const responseText = await response.text();
                console.log('Login Response:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    throw new Error('Server returned invalid response. Please try again.');
                }
                
                if (data.status === 'success') {
                    welcomeMessageLoginDiv.textContent = 'Login successful! Redirecting...';
                    welcomeMessageLoginDiv.style.color = 'green';
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    console.log('Login Error:', data);
                    
                    // In your login form handler, update the deactivated account message:
// In your login form submission handler, update the deactivated account message:
// In your login form handler, update the deactivated account message:
if (data.recovery_info) {
    const daysRemaining = data.recovery_info.days_remaining;
    const username = document.getElementById('username').value; // Get the username
    
    welcomeMessageLoginDiv.innerHTML = `
        <div style="background: #fff3cd; color: #856404; padding: 20px; border-radius: 8px; border: 1px solid #ffeaa7; margin: 15px 0;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle" style="color: #856404; font-size: 1.5em;"></i>
                <h4 style="margin: 0; color: #856404;">Account Deactivated</h4>
            </div>
            
            <p style="margin: 0 0 15px 0; font-size: 1.1em;">
                <strong>Your account has been deactivated.</strong>
            </p>
            
            <p style="margin: 0 0 15px 0;">
                You have <strong style="color: #d9534f;">${daysRemaining} days</strong> to recover your account before it's permanently deleted.
            </p>
            
            <p style="margin: 0 0 15px 0;">
                Click the button below to send a recovery link to your email.
            </p>
            
            <button type="button" onclick="resendRecoveryEmail('${username}')" 
                    style="background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-paper-plane"></i> Send Recovery Email
            </button>
            
            <!-- Add a hidden input to store the username -->
            <input type="hidden" id="deactivatedUsername" value="${username}">
        </div>
    `;



                    } else {
                        // Regular error message
                        welcomeMessageLoginDiv.textContent = data.message || 'Login failed. Please try again.';
                    }
                    
                    welcomeMessageLoginDiv.style.color = 'red';
                    document.getElementById('password').value = '';
                }
            } catch (error) {
                console.error('Login Error:', error);
                welcomeMessageLoginDiv.textContent = error.message || 'An error occurred. Please try again.';
                welcomeMessageLoginDiv.style.color = 'red';
            }
        });
    }

    // --- Registration Form Functionality ---
    const regPasswordInput = document.getElementById('reg-password');
    const regConfirmPasswordInput = document.getElementById('reg-confirm-password');
    const regTogglePassword = document.getElementById('reg-togglePassword');
    const regToggleConfirmPassword = document.getElementById('reg-toggleConfirmPassword');
    const registrationForm = document.getElementById('registrationForm');
    const welcomeMessageRegDiv = document.getElementById('welcomeMessageRegister');

    if (regPasswordInput) {
        regPasswordInput.addEventListener('keyup', function() {
            const password = this.value;
            let strength = 0;
            if (password.length > 0) {
                if (password.length >= 8) { strength += 1; }
                if (/[A-Z]/.test(password)) { strength += 1; }
                if (/[a-z]/.test(password)) { strength += 1; }
                if (/[0-9]/.test(password)) { strength += 1; }
                if (/[^A-Za-z0-9]/.test(password)) { strength += 1; }
            }
        });
    }

    if (regTogglePassword && regPasswordInput) {
        regTogglePassword.addEventListener('click', function() {
            const type = regPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            regPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (regToggleConfirmPassword && regConfirmPasswordInput) {
        regToggleConfirmPassword.addEventListener('click', function() {
            const type = regConfirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            regConfirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const password = regPasswordInput.value;
            const confirmPassword = regConfirmPasswordInput.value;

            if (password !== confirmPassword) {
                welcomeMessageRegDiv.textContent = 'Error: Passwords do not match!';
                welcomeMessageRegDiv.style.color = 'red';
                welcomeMessageRegDiv.classList.add('show');
                return;
            }

            fetch('/raflora_enterprises/api/register.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => {
                return response.json(); 
            })
            .then(data => {
                if (data.status === 'success') {
                    welcomeMessageRegDiv.textContent = data.message;
                    welcomeMessageRegDiv.style.color = 'green';
                    welcomeMessageRegDiv.classList.add('show');
                    
                    Array.from(registrationForm.querySelectorAll('.input-box, .btn, .register-link')).forEach(child => {
                         child.style.display = 'none';
                    });
                    
                    setTimeout(() => {
                        showForm(loginFormContainer);
                        welcomeMessageLoginDiv.textContent = data.message;
                        welcomeMessageLoginDiv.style.color = 'green';
                        welcomeMessageLoginDiv.classList.add('show');
                        welcomeMessageRegDiv.textContent = "";
                    }, 2000); 
                } else {
                    welcomeMessageRegDiv.textContent = data.message;
                    welcomeMessageRegDiv.style.color = 'red';
                    welcomeMessageRegDiv.classList.add('show');
                    regPasswordInput.value = '';
                    regConfirmPasswordInput.value = '';
                    
                    Array.from(registrationForm.children).forEach(child => {
                        child.style.display = '';
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                welcomeMessageRegDiv.textContent = 'An error occurred. Please try again.';
                welcomeMessageRegDiv.style.color = 'red';
                welcomeMessageRegDiv.classList.add('show');
            });
        });
    }

    // --- Forgot Password Form Functionality ---
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const forgotPasswordMessageDiv = document.getElementById('forgotPasswordMessage');
    const forgotEmailInput = document.getElementById('forgot-email');

    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const email = forgotEmailInput.value;
            if (email.trim() !== '') {
                Array.from(forgotPasswordForm.children).forEach(child => {
                    if (child.id !== 'forgotPasswordMessage' && child.tagName !== 'SPAN') {
                        child.style.display = 'none';
                    }
                });
                forgotPasswordMessageDiv.textContent = `A password reset link has been sent to ${email}.`;
                forgotPasswordMessageDiv.style.color = 'green';
                forgotPasswordMessageDiv.classList.add('show');
                forgotPasswordForm.querySelector('h1').style.display = 'none';
                forgotPasswordForm.style.height = 'auto';
                forgotPasswordForm.style.paddingBottom = '50px';
            } else {
                forgotPasswordMessageDiv.textContent = 'Please enter your email address.';
                forgotPasswordMessageDiv.style.color = 'red';
                forgotPasswordMessageDiv.classList.add('show');
            }
        });
    }
});

// QR Code Scanner Functions
let qrScanner = null;

console.log("🔄 QR Scanner functions loaded");
console.log("📚 jsQR available:", typeof jsQR !== 'undefined');

function openQRScanner() {
    console.log("🎬 Opening QR scanner...");
    const modal = document.getElementById('qrScannerModal');
    const traditionalLogin = document.querySelector('#traditional-login');
    
    if (!modal) {
        console.error("❌ QR scanner modal not found!");
        return;
    }
    
    if (traditionalLogin) {
        traditionalLogin.style.display = 'none';
    }
    
    modal.classList.remove('hidden');
    startQRScanner();
}

function closeQRScanner() {
    console.log("🛑 Closing QR scanner...");
    const modal = document.getElementById('qrScannerModal');
    const traditionalLogin = document.querySelector('#traditional-login');
    
    if (traditionalLogin) {
        traditionalLogin.style.display = 'block';
    }
    
    modal.classList.add('hidden');
    stopQRScanner();
}

async function startQRScanner() {
    try {
        console.log("📷 Starting camera...");
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        });
        
        const video = document.getElementById('qrVideo');
        video.srcObject = stream;
        
        video.onloadedmetadata = function() {
            console.log("✅ Camera stream loaded");
            video.play();
        };
        
        qrScanner = setInterval(scanQRFrame, 500);
        
    } catch (err) {
        console.error('❌ Camera error:', err);
        showScannerMessage('Cannot access camera. Please ensure camera permissions are granted.', 'error');
    }
}

function stopQRScanner() {
    console.log("⏹️ Stopping scanner...");
    if (qrScanner) {
        clearInterval(qrScanner);
        qrScanner = null;
    }
    
    const video = document.getElementById('qrVideo');
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => {
            console.log("🛑 Stopping camera track");
            track.stop();
        });
    }
}

function scanQRFrame() {
    const video = document.getElementById('qrVideo');
    const canvas = document.getElementById('qrCanvas');
    
    if (!video || !canvas) {
        console.error("❌ Video or canvas element not found");
        return;
    }
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        
        if (typeof jsQR === 'undefined') {
            console.error("❌ jsQR library not loaded!");
            stopQRScanner();
            return;
        }
        
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            console.log("✅ QR Code detected:", code.data);
            stopQRScanner();
            handleScannedQR(code.data);
        }
    }
}

function handleQRFileUpload(event) {
    console.log("📁 File upload triggered");
    const file = event.target.files[0];
    if (!file) {
        console.log("❌ No file selected");
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        console.log("🖼️ File loaded, processing image...");
        const img = new Image();
        img.onload = function() {
            const canvas = document.getElementById('qrCanvas');
            const context = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            context.drawImage(img, 0, 0);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            
            if (typeof jsQR === 'undefined') {
                console.error("❌ jsQR library not loaded!");
                showMessage('QR scanner library not loaded', 'error');
                return;
            }
            
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code) {
                console.log("✅ QR Code found in file:", code.data);
                handleScannedQR(code.data);
            } else {
                console.log("❌ No QR code found in image");
                showMessage('No QR code found in the image', 'error');
            }
        };
        img.onerror = function() {
            console.error("❌ Error loading image");
            showMessage('Error loading image', 'error');
        };
        img.src = e.target.result;
    };
    reader.onerror = function() {
        console.error("❌ Error reading file");
        showMessage('Error reading file', 'error');
    };
    reader.readAsDataURL(file);
}

async function handleScannedQR(qrData) {
    console.log("🔍 [DEBUG] Starting QR processing...");
    console.log("📄 QR Data received:", qrData);
    
    const messageDiv = document.getElementById('welcomeMessageLogin');
    
    try {
        const qrObject = JSON.parse(qrData);
        console.log("✅ [DEBUG] QR parsed successfully:", qrObject);
        
        if (qrObject.method === 'qr_login' && qrObject.system === 'raflora_enterprises') {
            console.log("🚀 [DEBUG] Valid QR format, sending to API...");
            
            const apiUrl = '/raflora_enterprises/api/verify_qr_login.php';
            console.log("📡 [DEBUG] API endpoint:", apiUrl);
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData })
            });
            
            console.log("📊 [DEBUG] API Response status:", response.status);
            
            const responseText = await response.text();
            console.log("📄 [DEBUG] Raw API response:", responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log("✅ [DEBUG] API Response parsed:", result);
            } catch (parseError) {
                console.error("❌ [DEBUG] JSON parse error:", parseError);
                throw new Error('Invalid server response format');
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            if (result.status === 'success') {
                console.log('✅ [DEBUG] Login successful!');
                
                if (messageDiv) {
                    messageDiv.innerHTML = `
                        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">
                            <strong>Success:</strong> Login successful! Redirecting...
                        </div>
                    `;
                }
                
                closeQRScanner();
                
                setTimeout(() => {
                    console.log("🔄 [DEBUG] Redirecting user...");
                    if (result.user.role === 'admin_type') {
                        window.location.href = '/raflora_enterprises/admin_dashboard/inventory.php';
                    } else {
                        window.location.href = '/raflora_enterprises/user/landing.php';
                    }
                }, 1500);
                
            } else {
                console.log("❌ [DEBUG] Login failed:", result.message);
                
                if (messageDiv) {
                    // In the handleScannedQR function, update the recovery button:
if (result.recovery_info) {
    const daysRemaining = result.recovery_info.days_remaining;
    const userId = result.user_id || '';
    
    messageDiv.innerHTML = `
        <div style="background: #fff3cd; color: #856404; padding: 20px; border-radius: 8px; border: 1px solid #ffeaa7; margin: 15px 0;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle" style="color: #856404; font-size: 1.5em;"></i>
                <h4 style="margin: 0; color: #856404;">Account Deactivated</h4>
            </div>
            
            <p style="margin: 0 0 10px 0; font-size: 1.1em;">
                <strong>Your account has been deactivated.</strong>
            </p>
            
            <p style="margin: 0 0 15px 0;">
                You have <strong style="color: #d9534f;">${daysRemaining} days</strong> to recover your account before it's permanently deleted.
            </p>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <p style="margin: 0 0 10px 0; font-weight: bold;">To recover your account:</p>
                <ol style="margin: 0; padding-left: 20px;">
                    <li>Click the button below to resend the recovery email</li>
                    <li>Check your email inbox for the recovery link</li>
                    <li>Click the link in the email to reactivate your account</li>
                </ol>
            </div>
            
            <button type="button" onclick="resendRecoveryEmailByUserId('${userId}')" 
                    style="background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
                <i class="fas fa-paper-plane"></i> Resend Recovery Email
            </button>
            
            <p style="margin: 15px 0 0 0; font-size: 0.9em; color: #666;">
                <i class="fas fa-info-circle"></i> Can't find the email? Check your spam folder.
            </p>
        </div>
    `;
} else {
                        messageDiv.innerHTML = `
                            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                                <strong>Error:</strong> ${result.message || 'QR code login failed.'}
                            </div>
                        `;
                    }
                }
            }
        } else {
            console.log("❌ [DEBUG] Invalid QR format for this system");
            if (messageDiv) {
                messageDiv.innerHTML = `
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                        <strong>Error:</strong> This QR code is not for Raflora Enterprises login.
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('💥 [DEBUG] Error processing QR code:', error);
        if (messageDiv) {
            messageDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        }
    }
}
// Add this new function to handle recovery by user ID
async function resendRecoveryEmailByUserId(userId) {
    const messageDiv = document.getElementById('welcomeMessageLogin');
    
    console.log('🔍 [DEBUG] resendRecoveryEmailByUserId called with userId:', userId);
    
    if (!userId || userId.trim() === '') {
        if (messageDiv) {
            messageDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                    <strong>Error:</strong> User ID not found.
                </div>
            `;
        }
        return;
    }
    
    // Show loading state
    if (messageDiv) {
        messageDiv.innerHTML = `
            <div style="background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; display: flex; align-items: center; gap: 10px;">
                <div class="spinner" style="width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #0c5460; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <span>Sending recovery email...</span>
            </div>
        `;
    }
    
    try {
        console.log('📡 [DEBUG] Sending request to /raflora_enterprises/api/resend_recovery_by_id.php');
        
        const response = await fetch('/raflora_enterprises/api/resend_recovery_by_id.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        console.log('📊 [DEBUG] Response status:', response.status);
        
        const responseText = await response.text();
        console.log('📄 [DEBUG] Raw response:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error('Server returned invalid JSON response');
        }
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        if (messageDiv) {
            if (result.status === 'success') {
                messageDiv.innerHTML = `
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;">
                        <strong>Success:</strong> ${result.message}
                    </div>
                `;
            } else {
                messageDiv.innerHTML = `
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                        <strong>Error:</strong> ${result.message}
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('💥 [DEBUG] Recovery email error:', error);
        if (messageDiv) {
            messageDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        }
    }
}
function showScannerMessage(message, type = 'info') {
    const messageDiv = document.getElementById('welcomeMessageLogin');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.style.color = type === 'error' ? 'red' : 'green';
        messageDiv.style.display = 'block';
        
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
}

// Forgot Password Form Handling
document.getElementById('forgotPasswordForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageDiv = document.getElementById('forgotPasswordMessage');
    
    messageDiv.innerHTML = '<div class="info-message">Processing your request...</div>';
    
    try {
        console.log('Sending forgot password request...');
        
        const response = await fetch('../api/forgot_password.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Response received:', response);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Result:', result);
        
        if (result.status === 'success') {
            messageDiv.innerHTML = `<div class="success-message">${result.message}</div>`;
            this.reset();
        } else {
            messageDiv.innerHTML = `<div class="error-message">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        messageDiv.innerHTML = `<div class="error-message">Network error: ${error.message}. Please check console for details.</div>`;
    }
});

// Keep only this recovery function in your login.js
async function resendRecoveryEmail(username) {
    const messageDiv = document.getElementById('welcomeMessageLogin');
    
    if (!username || username.trim() === '') {
        if (messageDiv) {
            messageDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                    <strong>Error:</strong> Please enter your username first.
                </div>
            `;
        }
        return;
    }
    
    // Show loading state
    if (messageDiv) {
        messageDiv.innerHTML = `
            <div style="background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; display: flex; align-items: center; gap: 10px;">
                <div class="spinner" style="width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #0c5460; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <span>Sending recovery email...</span>
            </div>
        `;
    }
    
    try {
        const response = await fetch('/raflora_enterprises/api/resend_recovery.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username: username })
        });
        
        const result = await response.json();
        
        if (messageDiv) {
            if (result.status === 'success') {
                messageDiv.innerHTML = `
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;">
                        <strong>Success:</strong> ${result.message}
                    </div>
                `;
            } else {
                messageDiv.innerHTML = `
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                        <strong>Error:</strong> ${result.message}
                    </div>
                `;
            }
        }
    } catch (error) {
        console.error('Recovery email error:', error);
        if (messageDiv) {
            messageDiv.innerHTML = `
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
                    <strong>Error:</strong> Failed to send recovery email. Please try again.
                </div>
            `;
        }
    }
}

// Add CSS for spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);