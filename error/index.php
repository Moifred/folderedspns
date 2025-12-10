<?php
session_start();
require_once '../includes/config.php';

// Get error code from URL or default to 404
$error_code = isset($_GET['code']) ? intval($_GET['code']) : 404;
$error_messages = [
    400 => ['title' => 'Bad Request', 'message' => 'The server cannot process the request due to a client error.'],
    401 => ['title' => 'Unauthorized', 'message' => 'Authentication is required to access this resource.'],
    403 => ['title' => 'Forbidden', 'message' => 'You do not have permission to access this resource.'],
    404 => ['title' => 'Page Not Found', 'message' => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.'],
    500 => ['title' => 'Internal Server Error', 'message' => 'The server encountered an internal error and was unable to complete your request.'],
    503 => ['title' => 'Service Unavailable', 'message' => 'The server is currently unable to handle the request due to temporary maintenance.']
];

// Default to 404 if error code not found
if (!isset($error_messages[$error_code])) {
    $error_code = 404;
}

$error_title = $error_messages[$error_code]['title'];
$error_message = $error_messages[$error_code]['message'];

// Log the error for admin review
try {
    $sql = "INSERT INTO error_logs (error_code, error_message, page_url, user_agent, ip_address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $error_code,
        $error_title . ': ' . $error_message,
        $_SERVER['REQUEST_URI'] ?? 'Unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
        $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
    ]);
} catch (Exception $e) {
    // Silently fail if logging doesn't work
    error_log("Error logging failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $error_code . ' - ' . $error_title; ?> | St. Philip Neri School</title>
    <meta name="description" content="An error occurred while processing your request.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1a365d;
            --color-secondary: #2d3748;
            --color-accent: #e53e3e;
            --color-accent-light: #fed7d7;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;

            --gradient-hero: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #e53e3e, #dd6b20);
            --gradient-subtle: linear-gradient(135deg, #f7fafc, #edf2f7);

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.1);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.15);

            --border-radius: 12px;
            --border-radius-lg: 20px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-background);
            color: var(--color-text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header & Navigation */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--color-border);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            color: var(--color-accent);
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--color-text);
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--color-accent);
        }

        /* Error Content */
        .error-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 2rem 80px;
            margin-top: 80px;
        }

        .error-content {
            text-align: center;
            max-width: 600px;
            padding: 3rem;
        }

        .error-animation {
            margin-bottom: 2rem;
        }

        .error-code {
            font-family: 'Playfair Display', serif;
            font-size: 8rem;
            font-weight: 900;
            color: var(--color-accent);
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 0 rgba(0,0,0,0.1);
        }

        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--color-primary);
        }

        .error-message {
            font-size: 1.2rem;
            color: var(--color-text-light);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--color-accent);
            color: white;
        }

        .btn-primary:hover {
            background: #c53030;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--color-surface);
            color: var(--color-text);
            border: 2px solid var(--color-border);
        }

        .btn-secondary:hover {
            background: var(--color-background);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Error Details */
        .error-details {
            background: var(--color-surface);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-top: 2rem;
            text-align: left;
        }

        .error-details summary {
            font-weight: 600;
            cursor: pointer;
            padding: 0.5rem 0;
            color: var(--color-text-light);
        }

        .error-details pre {
            background: var(--color-background);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            overflow-x: auto;
            font-size: 0.9rem;
            color: var(--color-text-light);
            border-left: 4px solid var(--color-accent);
        }

        /* Animated Elements */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-element {
            position: absolute;
            opacity: 0.1;
            color: var(--color-accent);
            font-size: 2rem;
            animation: float 20s infinite linear;
        }

        .floating-element:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; }
        .floating-element:nth-child(2) { top: 20%; right: 10%; animation-delay: -5s; }
        .floating-element:nth-child(3) { bottom: 30%; left: 15%; animation-delay: -10s; }
        .floating-element:nth-child(4) { bottom: 20%; right: 5%; animation-delay: -15s; }
        .floating-element:nth-child(5) { top: 50%; left: 50%; animation-delay: -7s; }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, 20px) rotate(90deg); }
            50% { transform: translate(0, 40px) rotate(180deg); }
            75% { transform: translate(-20px, 20px) rotate(270deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }

        /* Search Box */
        .search-box {
            max-width: 400px;
            margin: 0 auto 2rem;
        }

        .search-form {
            display: flex;
            gap: 0.5rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--color-border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        .search-btn {
            background: var(--color-accent);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            background: #c53030;
        }

        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--color-surface);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--color-text);
            transition: var(--transition);
        }

        .quick-link:hover {
            background: var(--color-background);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .quick-link i {
            color: var(--color-accent);
            font-size: 1.5rem;
        }

        /* Footer */
        .footer {
            background: var(--color-primary);
            color: white;
            padding: 4rem 2rem 2rem;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .footer-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #cbd5e0;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 3rem auto 0;
            padding-top: 2rem;
            border-top: 1px solid #4a5568;
            text-align: center;
            color: #cbd5e0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }

            .error-title {
                font-size: 2rem;
            }

            .nav-links {
                display: none;
            }

            .error-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .search-form {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 100px 1rem 60px;
            }

            .error-content {
                padding: 2rem 1rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-message {
                font-size: 1rem;
            }
        }

        /* Animation for error page */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-content > * {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .error-code { animation-delay: 0.1s; }
        .error-title { animation-delay: 0.2s; }
        .error-message { animation-delay: 0.3s; }
        .search-box { animation-delay: 0.4s; }
        .error-actions { animation-delay: 0.5s; }
        .quick-links { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-elements">
        <div class="floating-element"><i class="fas fa-book"></i></div>
        <div class="floating-element"><i class="fas fa-pencil-alt"></i></div>
        <div class="floating-element"><i class="fas fa-graduation-cap"></i></div>
        <div class="floating-element"><i class="fas fa-school"></i></div>
        <div class="floating-element"><i class="fas fa-question-circle"></i></div>
    </div>

    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-school logo-icon"></i>
                St. Philip Neri
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="all_blogs.php">Blog</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Error Content -->
    <div class="error-container">
        <div class="error-content">
            <div class="error-animation">
                <div class="error-code"><?php echo $error_code; ?></div>
            </div>

            <h1 class="error-title"><?php echo $error_title; ?></h1>

            <p class="error-message"><?php echo $error_message; ?></p>

            <!-- Search Box -->
            <div class="search-box">
                <form action="all_blogs.php" method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="Search our website...">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Go Home
                </a>
                <a href="all_blogs.php" class="btn btn-secondary">
                    <i class="fas fa-newspaper"></i> Browse Blog
                </a>
                <a href="contact.php" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
                <button onclick="history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Go Back
                </button>
            </div>

            <!-- Quick Links -->
            <div class="quick-links">
                <a href="about.php" class="quick-link">
                    <i class="fas fa-info-circle"></i>
                    <span>About Our School</span>
                </a>
                <a href="all_blogs.php" class="quick-link">
                    <i class="fas fa-book-open"></i>
                    <span>Latest Articles</span>
                </a>
                <a href="contact.php" class="quick-link">
                    <i class="fas fa-phone-alt"></i>
                    <span>Contact Information</span>
                </a>
                <a href="index.php#events" class="quick-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Upcoming Events</span>
                </a>
            </div>

            <!-- Error Details (for debugging) -->
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin')): ?>
            <details class="error-details">
                <summary>Technical Details (Admin Only)</summary>
                <pre><?php
                    $debug_info = [
                        'Error Code' => $error_code,
                        'Error Message' => $error_title . ': ' . $error_message,
                        'Request URI' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
                        'Referrer' => $_SERVER['HTTP_REFERER'] ?? 'None',
                        'User Agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                        'IP Address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                        'Request Time' => date('Y-m-d H:i:s'),
                        'PHP Version' => PHP_VERSION,
                        'Session ID' => session_id()
                    ];

                    echo "Error Debug Information:\n\n";
                    foreach ($debug_info as $key => $value) {
                        echo "$key: $value\n";
                    }
                ?></pre>
            </details>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>St. Philip Neri School</h3>
                <p>Dedicated to providing quality education and nurturing future leaders through innovative teaching methods.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="all_blogs.php">Blog</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Education Street, City</li>
                    <li><i class="fas fa-phone"></i> (555) 123-4567</li>
                    <li><i class="fas fa-envelope"></i> info@stphilipneri.edu</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 St. Philip Neri School. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Add loading state to search form
            const searchForm = document.querySelector('.search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function() {
                    const button = this.querySelector('button[type="submit"]');
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;
                });
            }

            // Add typewriter effect to error message
            const errorMessage = document.querySelector('.error-message');
            if (errorMessage) {
                const text = errorMessage.textContent;
                errorMessage.textContent = '';
                let i = 0;

                function typeWriter() {
                    if (i < text.length) {
                        errorMessage.textContent += text.charAt(i);
                        i++;
                        setTimeout(typeWriter, 30);
                    }
                }

                // Start typewriter after a delay
                setTimeout(typeWriter, 1000);
            }
        });

        // Simple 404 page animation
        const errorCode = document.querySelector('.error-code');
        if (errorCode) {
            errorCode.style.transform = 'scale(0.8)';
            errorCode.style.opacity = '0';

            setTimeout(() => {
                errorCode.style.transition = 'all 0.5s ease-out';
                errorCode.style.transform = 'scale(1)';
                errorCode.style.opacity = '1';
            }, 300);
        }
    </script>
</body>
</html>
