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
    <title>Gallery - Raflora Enterprises</title>
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
                <li class="active"><a href="../guest/g-gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="../guest/g-about.php" class="nav-link">About</a></li>
                <li><a href="../user/user_login.php" class="nav-link">Log-in</a></li>
            </ul>
        </nav>

    <!-- Gallery Hero Section -->
    <section class="page-hero">
        <div class="hero-background" style="background-image: url('../assets/images/Gallery/event/entrance1.jpg');"></div>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title fade-up">Our Gallery</h1>
                <p class="hero-subtitle fade-up delay-1">A showcase of our finest work</p>
                <p class="hero-description fade-up delay-2">Explore our portfolio of beautifully decorated events and discover the possibilities for your special occasion.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Filter -->
    <section class="gallery-filter-section">
        <div class="container">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="wedding">Weddings</button>
                <button class="filter-btn" data-filter="backdrop">Backdrops</button>
                <button class="filter-btn" data-filter="flowers">Flowers</button>
                <button class="filter-btn" data-filter="christmas">Christmas</button>
                <button class="filter-btn" data-filter="birthday">Birthdays</button>
                <button class="filter-btn" data-filter="funeral">Funerals</button>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-section">
        <div class="container">
            <div class="gallery-grid">
                <!-- Wedding Images -->
                <div class="gallery-item wedding fade-up" data-category="wedding">
                    <img src="../assets/images/Gallery/wedding/wed6.jpg" alt="Elegant Wedding Decoration" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Wedding Celebration</h3>
                            <p>Elegant floral arrangements</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/wedding/wed6.jpg" data-title="Wedding Celebration">
                                <i class="fas fa-expand"></i>
                                
                            </button>
                            
                        </div>
                    </div>
                </div>

                <div class="gallery-item wedding fade-up delay-1" data-category="wedding">
                    <img src="../assets/images/Gallery/wedding/wed1.jpg" alt="Wedding Floral Arrangement" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Bridal Bouquet</h3>
                            <p>Custom floral design</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/wedding/wed1.jpg" data-title="Bridal Bouquet">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Backdrop Images -->
                <div class="gallery-item backdrop fade-up" data-category="backdrop">
                    <img src="../assets/images/Gallery/backdrop/backdrop1.jpg" alt="Event Backdrop" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Event Backdrop</h3>
                            <p>Custom designed backdrop</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/backdrop/backdrop1.jpg" data-title="Event Backdrop">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="gallery-item backdrop fade-up delay-1" data-category="backdrop">
                    <img src="../assets/images/Gallery/backdrop/backdrop2.jpg" alt="Stage Backdrop" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Stage Design</h3>
                            <p>Professional backdrop setup</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/backdrop/backdrop2.jpg" data-title="Stage Design">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Flower Images -->
                <div class="gallery-item flowers fade-up" data-category="flowers">
                    <img src="../assets/images/Gallery/flowers/flowerset3.jpg" alt="Floral Arrangement" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Floral Centerpiece</h3>
                            <p>Beautiful flower arrangement</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/flowers/flowerset3.jpg" data-title="Floral Centerpiece">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="gallery-item flowers fade-up delay-1" data-category="flowers">
                    <img src="../assets/images/Gallery/flowers/flowerset1.jpg" alt="Flower Bouquet" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Flower Bouquet</h3>
                            <p>Handcrafted bouquet design</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/flowers/flowerset1.jpg" data-title="Flower Bouquet">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Christmas Images -->
                <div class="gallery-item christmas fade-up" data-category="christmas">
                    <img src="../assets/images/Gallery/xmass/xmass4.jpg" alt="Christmas Decoration" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Christmas Theme</h3>
                            <p>Festive holiday decoration</p>
                            <button class="view-btn" data-src=
                            "../assets/images/Gallery/xmass/xmass4.jpg" data-title="Christmas Theme">

                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Birthday Images -->
                <div class="gallery-item birthday fade-up delay-1" data-category="birthday">
                    <img src="../assets/images/Gallery/Birthday/kik1.jpg" alt="Birthday Party" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Birthday Celebration</h3>
                            <p>Colorful party setup</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/Birthday/kik1.jpg" data-title="Birthday Celebration">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Funeral Images -->
                <div class="gallery-item funeral fade-up" data-category="funeral">
                    <img src="../assets/images/Gallery/Funeral/funeral1.jpg" alt="Funeral Flowers" class="gallery-image">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h3>Sympathy Arrangement</h3>
                            <p>Respectful floral tribute</p>
                            <button class="view-btn" data-src="../assets/images/Gallery/Funeral/funeral1.jpg" data-title="Sympathy Arrangement">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add more images following the same pattern for each category -->
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Inspired by Our Work?</h2>
                <p class="cta-description">Let us create something equally beautiful for your special event.</p>
                <div class="cta-buttons">
                    <a href="../user/user_login.php" class="btn btn-primary btn-large">Book Your Event</a>
                    <a href="../guest/g-about.php" class="btn btn-secondary btn-large">Learn More About Us</a>
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
                        <a href="#" class="social-link">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Raflora Enterprises. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="lightbox">
        <div class="lightbox-content">
            <span class="lightbox-close">&times;</span>
            <img src="" alt="" class="lightbox-image">
            <div class="lightbox-caption"></div>
            <button class="lightbox-nav lightbox-prev">&#10094;</button>
            <button class="lightbox-nav lightbox-next">&#10095;</button>
        </div>
    </div>

    <script>
        // Gallery Filter Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    button.classList.add('active');
                    
                    const filterValue = button.getAttribute('data-filter');
                    
                    galleryItems.forEach(item => {
                        if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                            item.style.display = 'block';
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'scale(1)';
                            }, 100);
                        } else {
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                item.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });

            // Lightbox functionality
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = lightbox.querySelector('.lightbox-image');
            const lightboxCaption = lightbox.querySelector('.lightbox-caption');
            const viewButtons = document.querySelectorAll('.view-btn');
            const closeLightbox = lightbox.querySelector('.lightbox-close');
            const prevButton = lightbox.querySelector('.lightbox-prev');
            const nextButton = lightbox.querySelector('.lightbox-next');

            let currentImageIndex = 0;
            const allImages = Array.from(viewButtons);

            viewButtons.forEach((button, index) => {
                button.addEventListener('click', () => {
                    currentImageIndex = index;
                    openLightbox(currentImageIndex);
                });
            });

            function openLightbox(index) {
                const button = allImages[index];
                const src = button.getAttribute('data-src');
                const title = button.getAttribute('data-title');
                
                lightboxImg.src = src;
                lightboxCaption.textContent = title;
                lightbox.style.display = 'flex';
                
                // Add fade-in animation
                setTimeout(() => {
                    lightbox.classList.add('active');
                }, 50);
            }

            function closeLightboxFunc() {
                lightbox.classList.remove('active');
                setTimeout(() => {
                    lightbox.style.display = 'none';
                }, 300);
            }

            function navigateLightbox(direction) {
                currentImageIndex += direction;
                if (currentImageIndex >= allImages.length) currentImageIndex = 0;
                if (currentImageIndex < 0) currentImageIndex = allImages.length - 1;
                openLightbox(currentImageIndex);
            }

            closeLightbox.addEventListener('click', closeLightboxFunc);
            prevButton.addEventListener('click', () => navigateLightbox(-1));
            nextButton.addEventListener('click', () => navigateLightbox(1));

            // Close lightbox when clicking outside the image
            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) {
                    closeLightboxFunc();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (lightbox.style.display === 'flex') {
                    if (e.key === 'Escape') closeLightboxFunc();
                    if (e.key === 'ArrowLeft') navigateLightbox(-1);
                    if (e.key === 'ArrowRight') navigateLightbox(1);
                }
            });

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