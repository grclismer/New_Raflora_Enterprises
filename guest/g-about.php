<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raflora_enterprises";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!$conn->connect_error && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Raflora Enterprises</title>
    <link rel="stylesheet" href="../assets/css/user/landing.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <script src="../assets/js/user/navbar.js" defer></script>
    <script src="../assets/js/user/dark_mode.js" defer></script>
    <link rel="stylesheet" href="../assets/css/user/dark_mode.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li><a href="../guest/g-home.php" class="nav-link">Home</a></li>
                <li><a href="../guest/g-gallery.php" class="nav-link">Gallery</a></li>
                <li class="active"><a href="../guest/g-about.php" class="nav-link">About</a></li>
                <li><a href="../user/user_login.php" class="nav-link">Log-in</a></li>
            </ul>
        </nav>

    <!-- About Hero Section -->
    <section class="page-hero">
        <div class="hero-background" style="background-image: url('../assets/images/Gallery/event/event1.jpg');"></div>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title fade-up">About Raflora</h1>
                <p class="hero-subtitle fade-up delay-1">Meet the team behind your beautiful events</p>
                <p class="hero-description fade-up delay-2">Passionate professionals dedicated to creating unforgettable experiences through exquisite decorations.</p>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Team</h2>
                <p class="section-subtitle">The creative minds transforming your vision into reality</p>
            </div>

            <div class="team-members">
                <!-- Team Member 1 -->
                <div class="team-member fade-up">
                    <div class="member-content">
                        <div class="member-text">
                            <h3>Antonio A. Adriatico Jr.</h3>
                            <span class="member-role">CREATIVE DIRECTOR</span>
                            <p>With over 15 years of experience in event design and decoration, Antonio brings creativity and innovation to every project. His artistic vision and attention to detail ensure that each event is uniquely beautiful and memorable.</p>
                            <p>Specializing in floral arrangements and spatial design, Antonio transforms ordinary spaces into extraordinary experiences that captivate and inspire.</p>
                            
                            <div class="member-expertise">
                                <h4>Areas of Expertise:</h4>
                                <ul>
                                    <li>Floral Design & Arrangements</li>
                                    <li>Spatial Planning & Layout</li>
                                    <li>Creative Concept Development</li>
                                    <li>Color Theory & Coordination</li>
                                </ul>
                            </div>
                        </div>
                        <div class="member-image">
                            <img src="../assets/images/portrait/antionio.jpg" alt="Antonio A. Adriatico Jr">
                            <div class="image-overlay"></div>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="team-member fade-up delay-1">
                    <div class="member-content reverse">
                        <div class="member-text">
                            <h3>Raffy Christian Zamora</h3>
                            <span class="member-role">PROPRIETOR & MANAGING DIRECTOR</span>
                            <p>As the founder and driving force behind Raflora Enterprises, Raffy combines business acumen with a passion for beautiful events. His leadership ensures that every client receives exceptional service and attention.</p>
                            <p>With a background in business management and event coordination, Raffy oversees operations to guarantee seamless execution and client satisfaction from concept to completion.</p>
                            
                            <div class="member-expertise">
                                <h4>Areas of Expertise:</h4>
                                <ul>
                                    <li>Business Management & Strategy</li>
                                    <li>Client Relations & Coordination</li>
                                    <li>Event Planning & Execution</li>
                                    <li>Quality Control & Assurance</li>
                                </ul>
                            </div>
                        </div>
                        <div class="member-image">
                            <img src="../assets/images/portrait/Rafael.jpg" alt="Raffy Christian Zamora">
                            <div class="image-overlay"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Values</h2>
                <p class="section-subtitle">What drives us to deliver excellence</p>
            </div>
            
            <div class="values-grid">
                <div class="value-card fade-up">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Passion</h3>
                    <p>We pour our hearts into every project, ensuring each event reflects our love for beautiful design and attention to detail.</p>
                </div>
                
                <div class="value-card fade-up delay-1">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Creativity</h3>
                    <p>Innovative solutions and unique designs that transform ordinary spaces into extraordinary experiences.</p>
                </div>
                
                <div class="value-card fade-up delay-2">
                    <div class="value-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Excellence</h3>
                    <p>Commitment to the highest standards of quality in materials, craftsmanship, and service delivery.</p>
                </div>
                
                <div class="value-card fade-up delay-3">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Trust</h3>
                    <p>Building lasting relationships through reliability, transparency, and exceptional client service.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Work With Us?</h2>
                <p class="cta-description">Let's discuss how we can bring your vision to life with our expertise and creative touch.</p>
                <div class="cta-buttons">
                    <a href="../user/user_login.php" class="btn btn-primary btn-large">Start Your Project</a>
                    <a href="../guest/g-gallery.php" class="btn btn-secondary btn-large">See Our Work</a>
                    
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="../assets/images/logo/raflora-logo.jpg" alt="Raflora Enterprises" class="footer-logo">
                    <p class="footer-description">Creating unforgettable events through exquisite decorations and professional service.</p>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-heading">Contact Us</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:raflora18@gmail.com">raflora18@gmail.com</a>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+63 9558659685</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Manila, Philippines</span>
                        </div>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-heading">Follow Us</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com/RafloraEnterprises" target="_blank" class="social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/raflora18/" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <!-- <a href="#" class="social-link">
                            <i class="fab fa-linkedin-in"></i>
                        </a> -->
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Raflora Enterprises. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Scroll animations
        window.addEventListener('scroll', () => {
            document.querySelectorAll('.fade-up, .fade-left, .fade-right').forEach(el => {
                const rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight - 100) {
                    el.classList.add('visible');
                }
            });
        });

        // Initialize animations on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.fade-up, .fade-left, .fade-right').forEach(el => {
                const rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight - 100) {
                    el.classList.add('visible');
                }
            });
        });
    </script>
</body>
</html>