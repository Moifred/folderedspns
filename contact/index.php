<?php
require_once '../includes/config.php';

// Set page meta for SEO
$page_title = "Contact St. Philip Neri School - Get in Touch";
$meta_description = "Contact St. Philip Neri School for admissions, inquiries, or to schedule a campus tour. We're here to help with your educational journey.";
$meta_keywords = "contact school, admissions inquiry, campus tour, school information, St. Philip Neri contact";

// Process form submission
$form_submitted = false;
$form_success = false;
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $student_grade = $_POST['student_grade'] ?? '';
    $inquiry_type = $_POST['inquiry_type'] ?? '';
    $message = trim($_POST['message'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    // Validate form data
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($inquiry_type)) {
        $errors[] = "Please select an inquiry type";
    }

    if (empty($message)) {
        $errors[] = "Message is required";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long";
    }

    // If no validation errors, save to database
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO contacts (name, email, phone, student_grade, inquiry_type, message, newsletter, ip_address, user_agent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $name,
                $email,
                $phone,
                $student_grade,
                $inquiry_type,
                $message,
                $newsletter,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            ]);

            $form_submitted = true;
            $form_success = true;
            $success_message = "Thank you for your message! We'll get back to you within 24 hours.";

            // Clear form fields
            $name = $email = $phone = $student_grade = $inquiry_type = $message = '';
            $newsletter = 0;

        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $form_submitted = true;
            $form_success = false;
            $error_message = "Sorry, there was an error submitting your form. Please try again.";
        }
    } else {
        $form_submitted = true;
        $form_success = false;
        $error_message = implode("<br>", $errors);
    }
}

// Get recent blogs and events for the sidebar
try {
    $recent_blogs = $pdo->query("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    $recent_events = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recent_blogs = [];
    $recent_events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        /* Inherit styles from your main system */
        :root {
            --color-red: #dc2626;
            --color-green: #16a34a;
            --color-black: #000000;
            --color-white: #ffffff;
            --color-gray-light: #f8fafc;
            --color-gray: #64748b;
            --color-gray-dark: #1e293b;
            --color-gold: #f59e0b;
            --color-purple: #8b5cf6;
            --color-blue: #3b82f6;

            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-secondary: linear-gradient(135deg, var(--color-purple), var(--color-blue));
            --gradient-dark: linear-gradient(135deg, var(--color-black), var(--color-gray-dark));
            --gradient-gold: linear-gradient(135deg, var(--color-gold), #fbbf24);

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.2);
            --shadow-xl: 0 25px 50px rgba(0,0,0,0.25);

            --border-radius: 12px;
            --border-radius-lg: 20px;
            --border-radius-xl: 30px;

            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --transition-slow: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.7;
            color: var(--color-gray-dark);
            background: var(--color-white);
            overflow-x: hidden;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Contact Hero Section */
        .contact-hero {
            background: var(--gradient-primary);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .contact-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        .contact-hero p {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .btn {
            padding: 16px 35px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Contact Layout */
        .contact-layout {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 60px;
            padding: 80px 0;
        }

        @media (max-width: 1024px) {
            .contact-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        /* Contact Information Cards */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .info-card {
            background: var(--color-white);
            padding: 40px 30px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            text-align: center;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .info-card:hover::before {
            transform: scaleX(1);
        }

        .info-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            transition: var(--transition);
        }

        .info-card:hover .info-icon {
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
        }

        .info-title {
            font-size: 1.4rem;
            color: var(--color-black);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .info-content {
            color: var(--color-gray);
            line-height: 1.7;
        }

        .info-contact {
            margin-top: 15px;
            font-weight: 600;
            color: var(--color-red);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
            font-size: 1.2rem;
        }

        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
        }

        /* Contact Form */
        .contact-form-container {
            background: var(--color-white);
            padding: 50px 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .contact-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .form-title {
            font-size: 2rem;
            color: var(--color-black);
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--color-gray-dark);
            font-weight: 600;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--color-white);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--color-red);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .checkbox-group input {
            width: 18px;
            height: 18px;
        }

        .checkbox-group label {
            font-size: 0.95rem;
            color: var(--color-gray);
        }

        .form-submit {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: block;
            width: 100%;
            margin-top: 30px;
            position: relative;
            overflow: hidden;
        }

        .form-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .form-submit:hover::before {
            left: 100%;
        }

        .form-submit:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* Success/Error Messages */
        .form-message {
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }

        .form-success {
            background: rgba(22, 163, 74, 0.1);
            color: var(--color-green);
            border: 1px solid rgba(22, 163, 74, 0.3);
        }

        .form-error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--color-red);
            border: 1px solid rgba(220, 38, 38, 0.3);
        }

        /* Map Section */
        .map-section {
            padding: 80px 0;
            background: var(--color-gray-light);
        }

        .map-container {
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            height: 500px;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .map-overlay {
            position: absolute;
            bottom: 30px;
            left: 30px;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            max-width: 300px;
            backdrop-filter: blur(10px);
        }

        .map-overlay h3 {
            color: var(--color-black);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .map-overlay p {
            color: var(--color-gray);
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        /* Sidebar Sections */
        .sidebar-section {
            background: var(--color-white);
            padding: 30px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 30px;
        }

        .sidebar-title {
            font-size: 1.3rem;
            color: var(--color-black);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--color-red);
            font-weight: 700;
        }

        .events-list, .blogs-list {
            list-style: none;
        }

        .event-item, .blog-item {
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .event-item:hover, .blog-item:hover {
            transform: translateX(10px);
        }

        .event-item:last-child, .blog-item:last-child {
            border-bottom: none;
        }

        .event-date, .blog-date {
            display: block;
            color: var(--color-red);
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .event-title, .blog-title {
            color: var(--color-black);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: block;
            margin-bottom: 5px;
        }

        .event-title:hover, .blog-title:hover {
            color: var(--color-red);
        }

        .event-venue, .blog-excerpt {
            color: var(--color-gray);
            font-size: 0.9rem;
        }

        /* Section Title Styles */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }

        .section-title h2 {
            font-size: 3rem;
            color: var(--color-black);
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
            font-weight: 800;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 5px;
            background: var(--gradient-primary);
            border-radius: 5px;
        }

        .section-title p {
            font-size: 1.3rem;
            color: var(--color-gray);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2.5rem;
            }

            .contact-hero p {
                font-size: 1.1rem;
            }

            .contact-form-container {
                padding: 30px 25px;
            }

            .map-overlay {
                position: relative;
                bottom: auto;
                left: auto;
                max-width: 100%;
                margin: 20px;
            }

            .section-title h2 {
                font-size: 2.5rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-gray-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-primary);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-red);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Contact Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero-content fade-in">
                <h1>Get In Touch With Us</h1>
                <p>We're here to answer your questions and help you discover the exceptional educational experience at St. Philip Neri School.</p>
                <div class="carousel-buttons">
                    <a href="#contact-form" class="btn btn-primary">
                        <i class="fas fa-comment-alt"></i>
                        Send a Message
                    </a>
                    <a href="https://maps.app.goo.gl/jn4S4RuKSS8JfvKeA" class="btn btn-secondary">
                        <i class="fas fa-map-marker-alt"></i>
                        Visit Our Campus
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Contact Section -->
    <section class="contact-main">
        <div class="container">
            <div class="contact-layout">
                <!-- Contact Information & Sidebar -->
                <div class="contact-sidebar">
                    <div class="contact-info">
                        <div class="info-card stagger-item">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h3 class="info-title">Our Location</h3>
                            <p class="info-content">Visit our beautiful campus and experience our world-class facilities firsthand.</p>
                            <p class="info-contact">Joska, Off Kangundo Road</p>
                        </div>

                        <div class="info-card stagger-item">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <h3 class="info-title">Call Us</h3>
                            <p class="info-content">Our admissions team is available to answer your questions during office hours.</p>
                            <p class="info-contact">+254 719 221 401</p>
                        </div>

                        <div class="info-card stagger-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3 class="info-title">Email Us</h3>
                            <p class="info-content">Send us your inquiries and we'll respond within 24 hours during weekdays.</p>
                            <p class="info-contact">info@spns.sc.ke</p>
                        </div>

                        <div class="info-card stagger-item">
                            <div class="info-icon">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <h3 class="info-title">Connect With Us</h3>
                            <p class="info-content">Follow us on social media to stay updated with school news and events.</p>
                            <div class="social-links">
                                <a href="#" class="social-link">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Events Sidebar -->

                </div>

                <!-- Contact Form -->
                <div class="contact-form-container stagger-item" id="contact-form">
                    <h2 class="form-title">Send Us a Message</h2>

                    <?php if ($form_submitted && $form_success): ?>
                        <div class="form-message form-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                        </div>
                    <?php elseif ($form_submitted && !$form_success): ?>
                        <div class="form-message form-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-input" required
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-input" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input"
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="student_grade" class="form-label">Student Grade Level</label>
                                <select id="student_grade" name="student_grade" class="form-select">
                                    <option value="">Select Grade Level</option>
                                    <option value="playgroup" <?php echo (isset($_POST['student_grade']) && $_POST['student_grade'] == 'playgroup') ? 'selected' : ''; ?>>Playgroup</option>
                                    <option value="kindergarten" <?php echo (isset($_POST['student_grade']) && $_POST['student_grade'] == 'kindergarten') ? 'selected' : ''; ?>>Kindergarten</option>
                                    <option value="lower-primary" <?php echo (isset($_POST['student_grade']) && $_POST['student_grade'] == 'lower-primary') ? 'selected' : ''; ?>>Lower Primary</option>
                                    <option value="upper-primary" <?php echo (isset($_POST['student_grade']) && $_POST['student_grade'] == 'upper-primary') ? 'selected' : ''; ?>>Upper Primary</option>
                                    <option value="upper-primary" <?php echo (isset($_POST['student_grade']) && $_POST['student_grade'] == 'junior-school') ? 'selected' : ''; ?>>Junior School</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inquiry_type" class="form-label">Inquiry Type *</label>
                            <select id="inquiry_type" name="inquiry_type" class="form-select" required>
                                <option value="">Select Inquiry Type</option>
                                <option value="admissions" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] == 'admissions') ? 'selected' : ''; ?>>Admissions Information</option>
                                <option value="tour" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] == 'tour') ? 'selected' : ''; ?>>Schedule a Campus Tour</option>
                                <option value="academics" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] == 'academics') ? 'selected' : ''; ?>>Academic Programs</option>
                                <option value="facilities" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] == 'facilities') ? 'selected' : ''; ?>>Facilities Information</option>
                                <option value="other" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] == 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Your Message *</label>
                            <textarea id="message" name="message" class="form-textarea" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter" value="1"
                                   <?php echo (isset($_POST['newsletter']) && $_POST['newsletter'] == '1') ? 'checked' : ''; ?>>
                            <label for="newsletter">Subscribe to our newsletter for updates and school news</label>
                        </div>

                        <button type="submit" class="form-submit">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <!-- <section class="map-section" id="map">
        <div class="container">



            <div class="section-title fade-in">
                <h2>Visit Our Campus</h2>
                <p>Experience our world-class facilities and inspiring learning environment in person</p>
            </div>

            <div class="map-container stagger-item">
                <div id="map"></div>
                <div class="map-overlay">
                    <h3>St. Philip Neri School</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Joska, Off Kangundo Road</p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.762903013836!2d37.087208!3d-1.3179009!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f6f7c56a4977b%3A0xadadf116484003b!2sSt.%20Philip%20Neri%20Primary%20and%20Junior%20Secondary%20School%20Joska!5e0!3m2!1sen!2ske!4v1763996134817!5m2!1sen!2ske" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <p><i class="fas fa-phone-alt"></i> +254 719 221 401</p>
                    <p><i class="fas fa-envelope"></i> info@stphilipnerischool.sc.ke</p>
                </div>
            </div>
        </div>
    </section> -->

    <?php include '../includes/footer.php'; ?>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Map
            // try {
                // const map = L.map('map').setView([-1.3179009, 37.087208], 15); // Example coordinates

                // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                //     attribution: '© OpenStreetMap contributors'
                // }).addTo(map);

                // Add school marker
            //     const schoolMarker = L.marker([-1.3179009, 37.087208]).addTo(map)
            //         .bindPopup('<b>St. Philip Neri School</b><br>Joska, Off Kangundo Road')
            //         .openPopup();
            // } catch (e) {
            //     console.error('Map initialization failed:', e);
            //     document.getElementById('map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:linear-gradient(135deg, var(--color-red), var(--color-green));color:white;"><div><i class="fas fa-map-marked-alt" style="font-size:3rem;margin-bottom:20px;"></i><h3>St. Philip Neri School</h3><p>123 Education Avenue, Knowledge City</p></div></div>';
            // }

            // Form enhancement
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('.form-submit');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                    submitBtn.disabled = true;

                    // Form will submit normally, this is just for UX
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                });
            }

            // Scroll animations
            const fadeElements = document.querySelectorAll('.fade-in, .stagger-item');

            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            fadeElements.forEach(element => {
                fadeObserver.observe(element);
            });

            // Stagger animation for grid items
            const staggerItems = document.querySelectorAll('.stagger-item');
            staggerItems.forEach((item, index) => {
                item.style.transitionDelay = `${index * 0.1}s`;
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Real-time form validation
            const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
            formInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.style.borderColor = 'var(--color-red)';
                    } else {
                        this.style.borderColor = '';
                    }

                    // Email validation on blur
                    if (this.type === 'email' && this.value) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(this.value)) {
                            this.style.borderColor = 'var(--color-red)';
                        } else {
                            this.style.borderColor = '';
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
