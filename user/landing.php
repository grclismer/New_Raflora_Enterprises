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
    // IMPORTANT: Include profile_picture in the query
    $stmt = $conn->prepare("SELECT user_name, profile_picture FROM accounts_tbl WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}
$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raflora Enterprises - Premium Event Decorations</title>
    <link rel="stylesheet" href="../assets/css/user/landing.css">
    <link rel="stylesheet" href="../assets/css/user/footer.css">
    <link rel="stylesheet" href="../assets/css/user/navbar.css">
    <link rel="stylesheet" href="../assets/css/user/dark_mode.css">
    <!-- <script src="../assets/js/user/dark_mode.js" defer></script> -->
    <script src="../assets/js/user/navbar.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <div class="about-container">
        <div class="about-container">
        <nav class="navbar">
            <img src="../assets/images/logo/raflora-logo.jpg" alt="logo" class="logo" />
            <div class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="../user/landing.php" class="nav-link">Home</a></li>
                <li><a href="../user/gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../user/about.php" class="nav-link">About</a></li>
                <li><a href="../user/booking.php" class="nav-link">Book</a></li>
                <div class="user-dropdown-toggle">
                    <div class="flex items-center">
                    <div class="navbar-profile">
                        <?php if (!empty($user_data['profile_picture'])): ?>
                            <img src="/raflora_enterprises/<?php echo ltrim($user_data['profile_picture'], '/'); ?>" 
                                alt="Profile" 
                                class="profile-picture profile-picture-small">
                        <?php else: ?>
                            <div class="profile-default-icon">
                                <i class="fa fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <span class="navbar-username"><?php echo $user_data['user_name'] ?? 'User'; ?></span>
                    </div>
                    <ul class="user-dropdown-menu">
                        <li><a href="../user/account_settings.php">Account settings</a></li>
                        <li><a href="../user/my_bookings.php">My Bookings</a></li>
                        <li><a href="../api/logout.php">Log Out</a></li>
                    </ul>
                </div>
            </ul>
        </nav>

    <!-- Hero Section -->
    <section class="modern-hero">
        <div class="hero-background"></div>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title fade-up">Raflora Enterprises</h1>
                <p class="hero-subtitle fade-up delay-1">Where Elegance Meets Creativity</p>
                <p class="hero-description fade-up delay-2">Transforming spaces into unforgettable experiences with premium event decorations</p>
                <div class="hero-buttons fade-up delay-3">
                    <a href="../user/booking.php" class="btn btn-primary">Book Now</a>
                    <a href="../user/gallery.php" class="btn btn-secondary">View Gallery</a>
                </div>
            </div>
            
            <div class="hero-image-container fade-right">
                <img src="../assets/images/Home/box-img1.jpg" alt="Event Decoration" class="hero-image">
                <div class="image-overlay"></div>
            </div>
        </div>
        
        <div class="scroll-indicator">
            <div class="scroll-line"></div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Expertise</h2>
                <p class="section-subtitle">Crafting memorable events with attention to detail</p>
            </div>
            
            <div class="about-content">
                <div class="about-text fade-left">
                    <h3>Professional Event Decorations</h3>
                    <p>With years of experience in the industry, Raflora Enterprises specializes in creating stunning visual experiences for all types of events. From intimate gatherings to grand celebrations, we bring your vision to life.</p>
                    
                    <div class="features-grid">
                        <div class="feature-item">
                            <i class="fas fa-palette"></i>
                            <h4>Creative Design</h4>
                            <p>Unique and personalized decoration concepts</p>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-award"></i>
                            <h4>Premium Quality</h4>
                            <p>High-quality materials and craftsmanship</p>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock"></i>
                            <h4>On-Time </h4>
                            <p>Punctual setup and professional execution</p>
                        </div>
                    </div>
                </div>
                
                <div class="about-images fade-right">
                    <div class="image-stack">
                        <img src="../assets/images/portrait/section1.jpg" alt="Team Work" class="stack-image main-image">
                        <img src="../assets/images/portrait/section2.jpg" alt="Event Setup" class="stack-image secondary-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <!-- <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Client Testimonials</h2>
                <p class="section-subtitle">What our clients say about our services</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card fade-up">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="testimonial-text">"Raflora Enterprises transformed our wedding venue beyond expectations. Their attention to detail and creative approach made our special day truly magical."</p>
                    </div>
                    <div class="client-info">
                        <img src="../assets/images/img/VVip.jpg" alt="John Doe" class="client-photo">
                        <div class="client-details">
                            <h4 class="client-name">John Doe</h4>
                            <p class="client-company">VVIP Client</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card fade-up delay-1">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="testimonial-text">"Professional, punctual, and incredibly talented. The team at Raflora made our corporate event look spectacular. Highly recommended!"</p>
                    </div>
                    <div class="client-info">
                        <img src="../assets/images/img/Sheraton.jpg" alt="Johnson" class="client-photo">
                        <div class="client-details">
                            <h4 class="client-name">Johnson</h4>
                            <p class="client-company">Sheraton</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card fade-up delay-2">
                    <div class="testimonial-content">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="testimonial-text">"Exceptional service from start to finish. They understood our vision perfectly and executed it flawlessly. Will definitely work with them again."</p>
                    </div>
                    <div class="client-info">
                        <img src="../assets/images/img/Okura.jpg" alt="James Smith" class="client-photo">
                        <div class="client-details">
                            <h4 class="client-name">James Smith</h4>
                            <p class="client-company">Okura</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Transform Your Event?</h2>
                <p class="cta-description">Let's create something beautiful together. Contact us today to discuss your event decoration needs.</p>
                <div class="cta-buttons">
                    <a href="../user/booking.php" class="btn btn-primary btn-large">Get Started</a>
                    <a href="../user/gallery.php" class="btn btn-secondary btn-large">View Our Work</a>
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