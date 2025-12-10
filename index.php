<?php
require_once 'includes/config.php';

// Set page meta for SEO
$page_title = "St. Philip Neri School Joska - Excellence in Education";
$meta_description = "St. Philip Neri School offers world-class education with state-of-the-art facilities. Together we Achieve the extraordinary.";
$meta_keywords = "premium school, quality education, St. Philip Neri, modern facilities, academic excellence, school, education, learning, St. Philip Neri School, St. Philip Neri School Joska, St. Philip Neri School, St. Philip Neri Joska, Philip Neri School, SPN Joska, Private schools in Joska, Schools in Joska Kenya, Best schools Kenya, Best schools Joska, Schools off Kangundo Road, Day and boarding school Joska, Schools near Joska, Education in Joska, CBC school Kenya, Competency Based Curriculum school, CBE curriculum Kenya, CBC hub, Junior Secondary School (JSS) Joska, Senior School, 21st century learning, Future-ready curriculum, Kenyan curriculum school, Modern private school Kenya, State of the art facilities school, Modern learning tools, Technology in classroom Kenya, STEM school Kenya, STEM facilities Kenya, ICT integration school, Digital learning Kenya, Smart classrooms Kenya, Modern labs school, Holistic education Kenya, Child-centered learning, Best primary school Joska, Best private primary school, Best day school Joska, Best boarding school Joska, School fees structure Joska, Admissions Joska, School with good facilities, Safe school environment, School for all-round development, Christian values school, Catholic private school Kenya, Talent nurturing school Kenya, Co-curricular activities school, Music school Kenya, Football academy Joska, Sports academy Kenya, Netball team Kenya, Performing arts school, Kenya Music Festivals champions, Drama and festivals school, Art and talent development";

// Get data from database
$carousel_items = getCarouselItems('homepage');
$recent_blogs = getRecentBlogs(6);
$recent_events = getRecentEvents(6);
$testimonials = getTestimonials(3);

// Handle newsletter subscription
$newsletter_message = '';
$news_message_class = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    if ($email) {
        // Check if email already exists
        $check_stmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
        $check_stmt->execute([$email]);

        if ($check_stmt->rowCount() > 0) {
            // Update existing subscription
            $stmt = $pdo->prepare("UPDATE newsletter_subscriptions SET is_active = 1, subscribed_at = NOW() WHERE email = ?");
            if ($stmt->execute([$email])) {
                $newsletter_message = "You're already subscribed! We've reactivated your subscription.";
                $news_message_class = 'newsletter-success';
            }
        } else {
            // Insert new subscription
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscriptions (email, is_active, subscribed_at) VALUES (?, 1, NOW())");
            if ($stmt->execute([$email])) {
                $newsletter_message = "Thank you for subscribing! Welcome to our community.";
                $news_message_class = 'newsletter-success';
            }
        }
    } else {
        $newsletter_message = "Please enter a valid email address.";
        $news_message_class = 'newsletter-error';
    }
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
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <style>
        /* Optimized CSS with critical styles first */
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
            --color-past: #94a3b8;
            --color-ongoing: #fbbf24;
            --color-upcoming: #10b981;
            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-secondary: linear-gradient(135deg, var(--color-purple), var(--color-blue));
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            color: var(--color-gray-dark);
            background: var(--color-white);
            overflow-x: hidden;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Optimized Hero Carousel */
        .hero-carousel {
            position: relative;
            height: 70vh;
            min-height: 500px;
            overflow: hidden;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.4));
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .carousel-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 40px 30px;
            text-align: center;
            max-width: 800px;
        }

        .carousel-content h2 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .carousel-content p {
            font-size: clamp(1rem, 2.5vw, 1.3rem);
            margin-bottom: 30px;
            opacity: 0.95;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .carousel-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(220, 38, 38, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.95);
            color: var(--color-black);
            border: 2px solid transparent;
        }

        .btn-secondary:hover {
            background: var(--color-white);
            border-color: var(--color-red);
            transform: translateY(-2px);
        }

        .carousel-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 3;
        }

        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: var(--transition);
        }

        .indicator.active {
            background: var(--color-white);
            transform: scale(1.3);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        /* Optimized Stats Section - Reduced Height */
        .stats-section {
            background: var(--color-gray-dark);
            color: white;
            padding: 40px 0;
            position: relative;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 20px 10px;
            position: relative;
        }

        .stat-item::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 60%;
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-item:last-child::after {
            display: none;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--color-red);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 5px;
            color: var(--color-white);
            font-family: 'Inter', sans-serif;
        }

        .stat-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Optimized Features Grid */
        .why-choose-us {
            background: var(--color-gray-light);
            padding: 80px 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .feature-card {
            background: var(--color-white);
            padding: 40px 25px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
            border-top: 4px solid var(--color-red);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .feature-icon-wrapper {
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
        }

        .feature-title {
            font-size: 1.3rem;
            color: var(--color-black);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .feature-description {
            color: var(--color-gray);
            line-height: 1.6;
        }

        /* Optimized Programs Section */
        .programs-section {
            background: var(--color-gray-dark);
            color: var(--color-white);
            padding: 80px 0;
        }

        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .program-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 35px 25px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .program-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--color-red);
        }

        .program-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
        }

        .program-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--color-white);
            font-weight: 700;
        }

        .program-description {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .program-link {
            color: var(--color-green);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .program-link:hover {
            color: var(--color-white);
            gap: 12px;
        }

        /* Optimized Facilities */
        .facilities-showcase {
            padding: 80px 0;
            background: var(--color-white);
        }

        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .facility-card {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            height: 350px;
        }

        .facility-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .facility-card:hover .facility-image {
            transform: scale(1.05);
        }

        .facility-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
            color: white;
            padding: 25px 20px;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .facility-card:hover .facility-overlay {
            transform: translateY(-5px);
        }

        .facility-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--color-white);
            font-weight: 700;
        }

        .facility-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        /* Optimized Blogs & Events */
        .blogs-events-section {
            background: var(--color-gray-light);
            padding: 80px 0;
        }

        .blogs-events-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        .blogs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .blog-card {
            background: var(--color-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .blog-image-container {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .blog-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .blog-card:hover .blog-image {
            transform: scale(1.05);
        }

        .blog-date {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--gradient-primary);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .blog-content {
            padding: 25px;
        }

        .blog-title {
            font-size: 1.2rem;
            color: var(--color-black);
            margin-bottom: 12px;
            line-height: 1.4;
            font-weight: 700;
        }

        .blog-excerpt {
            color: var(--color-gray);
            margin-bottom: 15px;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        /* Events Sidebar */
        .events-sidebar {
            background: var(--color-white);
            padding: 30px 25px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }

        .events-title {
            font-size: 1.3rem;
            color: var(--color-black);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--color-red);
            font-weight: 700;
        }

        .events-list {
            list-style: none;
        }

        .event-item {
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            position: relative;
            padding-left: 45px;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-date {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .event-status {
            position: absolute;
            left: 0;
            top: 15px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .status-past {
            background: var(--color-past);
        }

        .status-ongoing {
            background: var(--color-ongoing);
        }

        .status-upcoming {
            background: var(--color-upcoming);
        }

        .event-date-text {
            color: var(--color-gray);
            font-size: 0.8rem;
        }

        .event-title {
            color: var(--color-black);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: block;
            margin-bottom: 5px;
            transition: var(--transition);
        }

        .event-title:hover {
            color: var(--color-red);
        }

        .event-venue {
            color: var(--color-gray);
            font-size: 0.85rem;
        }

        /* Optimized Testimonials */
        .testimonials-section {
            padding: 80px 0;
            background: var(--gradient-primary);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 30px 25px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .testimonial-content {
            font-style: italic;
            margin-bottom: 20px;
            line-height: 1.6;
            color: white;
            font-size: 1rem;
            quotes: "“" "„" "‚" "‛";
        }

        .testimonial-content::before {
            content: open-quote;
            font-size: 3rem;
            line-height: 0;
            vertical-align: -0.4em;
            margin-right: 5px;
            opacity: 0.8;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .author-info h4 {
            color: var(--color-white);
            margin-bottom: 3px;
            font-weight: 700;
            font-size: 1rem;
        }

        .author-info p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Optimized Newsletter */
        .newsletter-section {
            background: var(--gradient-secondary);
            color: white;
            text-align: center;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .newsletter-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s linear infinite;
            opacity: 0.3;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50px, -50px) rotate(360deg); }
        }

        .newsletter-content {
            position: relative;
            z-index: 2;
        }

        .newsletter-content h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            margin-bottom: 15px;
            font-weight: 800;
        }

        .newsletter-content p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .newsletter-form {
            max-width: 450px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
        }

        .newsletter-input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            transition: var(--transition);
        }

        .newsletter-input:focus {
            background: var(--color-white);
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        }

        .newsletter-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            white-space: nowrap;
            min-width: 120px;
        }

        .newsletter-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4);
        }

        .newsletter-message {
            margin-top: 15px;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .newsletter-success {
            background: rgba(22, 163, 74, 0.2);
            color: #bbf7d0;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .newsletter-error {
            background: rgba(239, 68, 68, 0.2);
            color: #fecaca;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Optimized CTA Section */
        .cta-section {
            background: var(--color-gold);
            color: white;
            text-align: center;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 65%, rgba(255,255,255,0.1) 65%, rgba(255,255,255,0.1) 70%, transparent 70%),
                        linear-gradient(-45deg, transparent 65%, rgba(255,255,255,0.1) 65%, rgba(255,255,255,0.1) 70%, transparent 70%);
            background-size: 50px 50px;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-content h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            margin-bottom: 20px;
            font-weight: 800;
        }

        .cta-content p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            margin-bottom: 30px;
            opacity: 0.95;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-outline:hover {
            background: white;
            color: var(--color-black);
        }

        /* Section Titles */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            color: var(--color-black);
            margin-bottom: 15px;
            font-weight: 800;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .section-title p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--color-gray);
            max-width: 600px;
            margin: 30px auto 0;
            line-height: 1.5;
        }

        .section-title-dark h2 {
            color: white;
        }

        .section-title-dark h2::after {
            background: var(--color-white);
        }

        .section-title-dark p {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .hero-carousel {
                height: 60vh;
                min-height: 400px;
            }

            .blogs-events-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .events-sidebar {
                position: static;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .stat-item::after {
                display: none;
            }

            .stat-item:nth-child(2n)::after {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 12px;
            }

            .hero-carousel {
                height: 50vh;
                min-height: 350px;
            }

            .carousel-content {
                padding: 30px 20px;
            }

            .carousel-buttons {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
                padding: 12px 24px;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stats-section {
                padding: 30px 0;
            }

            .stat-item {
                padding: 15px 10px;
                background: rgba(255,255,255,0.05);
                border-radius: 8px;
            }

            .stat-icon {
                font-size: 1.8rem;
                margin-bottom: 8px;
            }

            .stat-number {
                font-size: 1.8rem;
            }

            .stat-label {
                font-size: 0.8rem;
            }

            .features-grid,
            .programs-grid,
            .facilities-grid,
            .blogs-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .newsletter-form {
                flex-direction: column;
                gap: 10px;
            }

            .newsletter-input,
            .newsletter-btn {
                width: 100%;
                border-radius: 50px;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .facility-card {
                height: 300px;
            }

            .feature-card,
            .program-card {
                padding: 25px 20px;
            }
        }

        @media (max-width: 480px) {
            .hero-carousel {
                height: 45vh;
                min-height: 300px;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .stat-item {
                padding: 12px 8px;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .carousel-content h2 {
                font-size: 1.6rem;
            }

            .section-title h2 {
                font-size: 1.6rem;
            }

            .feature-card,
            .program-card {
                padding: 20px 15px;
            }

            .event-item {
                padding-left: 40px;
            }

            .event-status {
                width: 30px;
                height: 30px;
                font-size: 0.6rem;
            }
        }

        @media (max-width: 360px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }

            .stat-item {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Optimized Hero Carousel -->
    <section class="hero-carousel" id="heroCarousel">
        <?php
        if (!empty($carousel_items)):
            $active = true;
            foreach ($carousel_items as $index => $item):
        ?>
                <div class="carousel-slide <?php echo $active ? 'active' : ''; ?>"
                     style="background-image: url('<?php echo htmlspecialchars($item['image_path']); ?>')"
                     data-index="<?php echo $index; ?>">
                    <div class="carousel-content">
                        <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="carousel-buttons">
                            <?php if (!empty($item['button1_text'])): ?>
                                <a href="<?php echo htmlspecialchars($item['button1_link']); ?>" class="btn btn-primary">
                                    <i class="fas fa-<?php echo strpos($item['button1_text'], 'Enquiry') !== false ? 'comment-alt' : 'arrow-right'; ?>"></i>
                                    <?php echo htmlspecialchars($item['button1_text']); ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($item['button2_text'])): ?>
                                <a href="<?php echo htmlspecialchars($item['button2_link']); ?>" class="btn btn-secondary">
                                    <i class="fas fa-<?php echo strpos($item['button2_text'], 'Apply') !== false ? 'edit' : 'arrow-right'; ?>"></i>
                                    <?php echo htmlspecialchars($item['button2_text']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php $active = false; ?>
            <?php endforeach; ?>

            <div class="carousel-indicators">
                <?php foreach ($carousel_items as $index => $item): ?>
                    <div class="indicator <?php echo $index === 0 ? 'active' : ''; ?>"
                         data-index="<?php echo $index; ?>"
                         aria-label="Go to slide <?php echo $index + 1; ?>"></div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Default carousel slide -->
            <div class="carousel-slide active" style="background: linear-gradient(135deg, var(--color-red), var(--color-green));">
                <div class="carousel-content">
                    <h2>Welcome to St. Philip Neri School</h2>
                    <p>Where Excellence Meets Innovation in Education. Together we Achieve the extraordinary.</p>
                    <div class="carousel-buttons">
                        <a href="enquiry" class="btn btn-primary">
                            <i class="fas fa-comment-alt"></i>
                            Make Enquiry
                        </a>
                        <a href="application" class="btn btn-secondary">
                            <i class="fas fa-edit"></i>
                            Apply Now
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Quick Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="quick-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-number" data-count="1500">0</div>
                    <div class="stat-label">Successful Graduates</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-number" data-count="85">0</div>
                    <div class="stat-label">Expert Faculty</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-number" data-count="12">0</div>
                    <div class="stat-label">Years of Excellence</div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number" data-count="12">0</div>
                    <div class="stat-label">Modern Facilities</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose St. Philip Neri?</h2>
                <p>Experience the difference that sets us apart in the world of education</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="feature-title">Academic Excellence</h3>
                    <p class="feature-description">Our students consistently achieve outstanding results with personalized learning approaches.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Expert Faculty</h3>
                    <p class="feature-description">Highly qualified educators dedicated to nurturing each student's unique potential.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h3 class="feature-title">Modern Facilities</h3>
                    <p class="feature-description">State-of-the-art laboratories and learning spaces designed for innovation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Academic Programs -->
    <section class="programs-section">
        <div class="container">
            <div class="section-title section-title-dark">
                <h2>Academic Programs</h2>
                <p>Comprehensive educational pathways designed for excellence</p>
            </div>

            <div class="programs-grid">
                <div class="program-card">
                    <span class="program-icon">👶</span>
                    <h3 class="program-title">Playgroup</h3>
                    <p class="program-description">Early childhood development through play-based learning.</p>
                    <a href="sections" class="program-link">
                        Discover More
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="program-card">
                    <span class="program-icon">🎨</span>
                    <h3 class="program-title">Kindergarten</h3>
                    <p class="program-description">Foundation building with creative activities and basic skills.</p>
                    <a href="sections" class="program-link">
                        Discover More
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="program-card">
                    <span class="program-icon">📚</span>
                    <h3 class="program-title">Primary and Junior School (JSS)</h3>
                    <p class="program-description">Strong fundamentals in core subjects through interactive learning.</p>
                    <a href="sections" class="program-link">
                        Discover More
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Showcase -->
    <section class="facilities-showcase">
        <div class="container">
            <div class="section-title">
                <h2>World-Class Facilities</h2>
                <p>Explore our state-of-the-art infrastructure</p>
            </div>

            <div class="facilities-grid">
                <div class="facility-card">
                    <img src="images/sports-complex.jpg" alt="Sports Complex" class="facility-image"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjMTZhMzRhIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iMjAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIj5TcG9ydHMgQ29tcGxleDwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="facility-overlay">
                        <h3 class="facility-title">Sports Complex</h3>
                        <p class="facility-description">Modern facilities for comprehensive physical development.</p>
                        <a href="sports-complex" class="program-link">
                            Explore Facility
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="facility-card">
                    <img src="images/science-lab.jpg" alt="Science Complex" class="facility-image"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjZGM2NjI2Ii8+Cjx0ZXh0IHg9IjIwMCIgeT0iMjAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIj5TY2llbmNlIExhYjwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="facility-overlay">
                        <h3 class="facility-title">Science Labs</h3>
                        <p class="facility-description">Cutting-edge laboratories for hands-on learning.</p>
                        <a href="#" class="program-link">
                            Explore Facility
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="facility-card">
                    <img src="images/arts-center.jpg" alt="Arts Center" class="facility-image"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjMDAwMDAwIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iMjAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIj5BcnRzIENlbnRlcjwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="facility-overlay">
                        <h3 class="facility-title">Arts Center</h3>
                        <p class="facility-description">Inspiring spaces for creative expression.</p>
                        <a href="#" class="program-link">
                            Explore Facility
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blogs and Events Section -->
    <section class="blogs-events-section">
        <div class="container">
            <div class="section-title">
                <h2>Latest News & Events</h2>
                <p>Stay informed about our school community</p>
            </div>

            <div class="blogs-events-container">
                <div class="blogs-section">
                    <h3 style="margin-bottom: 30px; color: var(--color-black); font-size: 1.5rem; font-weight: 700; border-bottom: 3px solid var(--color-red); padding-bottom: 10px; display: inline-block;">Recent Articles</h3>
                    <div class="blogs-grid">
                        <?php if (!empty($recent_blogs)): ?>
                            <?php foreach ($recent_blogs as $blog): ?>
                                <article class="blog-card">
                                    <div class="blog-image-container">
                                        <img src="<?php echo $blog['featured_image'] ?: 'images/blog-default.jpg'; ?>"
                                             alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                             class="blog-image"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDQwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjZDNkM2QzIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM2YjcyODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPkJsb2cgSW1hZ2U8L3RleHQ+Cjwvc3ZnPgo='">
                                        <div class="blog-date">
                                            <?php echo date('M j, Y', strtotime($blog['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="blog-content">
                                        <h3 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                                        <p class="blog-excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                                        <a href="blogs/blog_single.php?id=<?php echo $blog['id']; ?>" class="program-link">
                                            Continue Reading
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--color-gray); padding: 40px; grid-column: 1 / -1;">
                                No blogs available at the moment. Check back soon!
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <aside class="events-sidebar">
                    <h3 class="events-title">Upcoming Events</h3>
                    <ul class="events-list">
                        <?php
                        if (!empty($recent_events)):
                            $today = date('Y-m-d');
                            foreach ($recent_events as $event):
                                $event_date = date('Y-m-d', strtotime($event['event_date']));
                                $status = '';
                                $status_text = '';

                                if ($event_date < $today) {
                                    $status = 'past';
                                    $status_text = 'PST';
                                } elseif ($event_date == $today) {
                                    $status = 'ongoing';
                                    $status_text = 'NOW';
                                } else {
                                    $status = 'upcoming';
                                    $status_text = 'UP';
                                }
                        ?>
                                <li class="event-item">
                                    <span class="event-status status-<?php echo $status; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                    <span class="event-date">
                                        <?php echo date('D, M j', strtotime($event['event_date'])); ?>
                                    </span>
                                    <a href="events/single_event.php?id=<?php echo $event['id']; ?>" class="event-title">
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </a>
                                    <div class="event-venue">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($event['venue']); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="event-item" style="padding-left: 0; text-align: center;">
                                <p style="color: var(--color-gray); font-size: 0.95rem;">
                                    No upcoming events scheduled.<br>
                                    <a href="events" style="color: var(--color-red); text-decoration: none; font-weight: 600;">View past events →</a>
                                </p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </aside>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title section-title-dark">
                <h2>What Our Community Says</h2>
                <p>Hear from parents, students, and alumni</p>
            </div>

            <div class="testimonials-grid">
                <?php if (!empty($testimonials)): ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <?php echo htmlspecialchars($testimonial['content']); ?>
                            </div>
                            <div class="testimonial-author">
                                <?php if (!empty($testimonial['image_path'])): ?>
                                    <img src="<?php echo $testimonial['image_path']; ?>"
                                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                                         class="author-image"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjUiIGN5PSIyNSIgcj0iMjUiIGZpbGw9IiNlY2VjZWMiLz4KPHBhdGggZD0iTTI1IDI4QzI4LjMxMzcgMjggMzEgMjUuMzEzNyAzMSAyMkMzMSAxOC42ODYzIDI4LjMxMzcgMTYgMjUgMTZDMjEuNjg2MyAxNiAxOSAxOC42ODYzIDE5IDIyQzE5IDI1LjMxMzcgMjEuNjg2MyAyOCAyNSAyOFoiIGZpbGw9IiNiYmJiYmIiLz4KPHBhdGggZD0iTTI1IDM0QzE5LjQ3NzEgMzQgMTUgMzUuNzkwOSAxNSAzOEgzNUMzNSAzNS43OTA5IDMwLjUyMjkgMzQgMjUgMzRaIiBmaWxsPSIjYmJiYmJiIi8+Cjwvc3ZnPgo='">
                                <?php endif; ?>
                                <div class="author-info">
                                    <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($testimonial['role']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            "St. Philip Neri School has transformed my child's learning experience. The dedicated teachers and facilities have nurtured both academic excellence and personal growth."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>Sarah Johnson</h4>
                                <p>Parent of Grade 6 Student</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Connected</h2>
                <p>Subscribe for updates and educational insights</p>
                <form class="newsletter-form" method="POST" id="newsletterForm">
                    <input type="email" name="email" class="newsletter-input" placeholder="Enter your email" required>
                    <button type="submit" class="newsletter-btn">
                        <i class="fas fa-paper-plane"></i>
                        Subscribe
                    </button>
                </form>
                <?php if ($newsletter_message): ?>
                    <div class="newsletter-message <?php echo $news_message_class; ?>" id="newsletterMessage">
                        <?php echo $newsletter_message; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Begin Your Journey</h2>
                <p>Join our community of learners and discover excellence in education.</p>
                <div class="cta-buttons">
                    <a href="enquiry" class="btn btn-outline">
                        <i class="fas fa-info-circle"></i>
                        Request Info
                    </a>
                    <a href="application" class="btn btn-primary">
                        <i class="fas fa-user-graduate"></i>
                        Apply Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Optimized JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Carousel functionality
            const carousel = document.getElementById('heroCarousel');
            if (carousel) {
                const slides = carousel.querySelectorAll('.carousel-slide');
                const indicators = carousel.querySelectorAll('.indicator');
                let currentSlide = 0;
                let slideInterval;

                function showSlide(index) {
                    // Remove active class from all slides and indicators
                    slides.forEach(slide => slide.classList.remove('active'));
                    indicators.forEach(indicator => indicator.classList.remove('active'));

                    // Add active class to current slide and indicator
                    slides[index].classList.add('active');
                    indicators[index].classList.add('active');
                    currentSlide = index;
                }

                function nextSlide() {
                    let next = (currentSlide + 1) % slides.length;
                    showSlide(next);
                }

                function startSlideShow() {
                    if (slides.length > 1) {
                        slideInterval = setInterval(nextSlide, 5000);
                    }
                }

                // Initialize carousel
                if (slides.length > 0) {
                    startSlideShow();

                    // Add click events to indicators
                    indicators.forEach((indicator, index) => {
                        indicator.addEventListener('click', () => {
                            clearInterval(slideInterval);
                            showSlide(index);
                            startSlideShow();
                        });
                    });

                    // Pause on hover
                    carousel.addEventListener('mouseenter', () => {
                        clearInterval(slideInterval);
                    });

                    carousel.addEventListener('mouseleave', () => {
                        startSlideShow();
                    });
                }
            }

            // Animated counter for stats
            const counters = document.querySelectorAll('.stat-number');

            const animateCounter = (counter) => {
                const target = +counter.getAttribute('data-count');
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };

                updateCounter();
            };

            // Start counters when element is in viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            counters.forEach(counter => {
                observer.observe(counter);
            });

            // Newsletter form submission feedback
            const newsletterForm = document.getElementById('newsletterForm');
            const newsletterMessage = document.getElementById('newsletterMessage');

            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    const emailInput = this.querySelector('input[name="email"]');
                    const submitBtn = this.querySelector('button[type="submit"]');

                    if (!emailInput.value.trim()) {
                        e.preventDefault();
                        return;
                    }

                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
                    submitBtn.disabled = true;

                    // Simulate form submission (in production, this would be handled by PHP)
                    // Remove this setTimeout in production
                    setTimeout(() => {
                        submitBtn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                        setTimeout(() => {
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Subscribe';
                            submitBtn.disabled = false;
                        }, 2000);
                    }, 1500);
                });
            }

            // Auto-hide newsletter message after 5 seconds
            if (newsletterMessage) {
                setTimeout(() => {
                    newsletterMessage.style.opacity = '0';
                    newsletterMessage.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        newsletterMessage.style.display = 'none';
                    }, 300);
                }, 5000);
            }

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;

                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add animation to elements when they come into view
            const animatedElements = document.querySelectorAll('.feature-card, .program-card, .facility-card, .blog-card, .testimonial-card');

            const fadeInObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        fadeInObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            animatedElements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                fadeInObserver.observe(element);
            });
        });
    </script>
</body>
</html>
