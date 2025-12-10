<?php
require_once '../includes/config.php';

// Set page meta for SEO
$page_title = "Our Story - St. Philip Neri School Since 2013";
$meta_description = "Discover the inspiring journey of St. Philip Neri School since 2013. From humble beginnings to educational excellence, learn about our mission, vision, and milestones.";
$meta_keywords = "St. Philip Neri history, school story, educational journey, since 2013, school milestones, academic excellence";

// Get story data from database
$story_sections = [];
$timeline_events = [];
$milestones = [];
$leadership = [];

try {
    // Get story sections
    $stmt = $pdo->prepare("SELECT * FROM our_story WHERE is_active = 1 ORDER BY display_order");
    $stmt->execute();
    $story_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get timeline events
    $stmt = $pdo->prepare("SELECT * FROM story_timeline WHERE is_active = 1 ORDER BY year ASC, display_order");
    $stmt->execute();
    $timeline_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get milestones
    $stmt = $pdo->prepare("SELECT * FROM story_milestones WHERE is_active = 1 ORDER BY display_order");
    $stmt->execute();
    $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get leadership team
    $stmt = $pdo->prepare("SELECT * FROM story_leadership WHERE is_active = 1 ORDER BY display_order");
    $stmt->execute();
    $leadership = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Error fetching story data: " . $e->getMessage());
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Extended CSS with story page specific styles */
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
            --color-orange: #f97316;
            --color-pink: #ec4899;
            --color-teal: #0d9488;
            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-secondary: linear-gradient(135deg, var(--color-purple), var(--color-blue));
            --gradient-gold: linear-gradient(135deg, var(--color-gold), #eab308);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 15px 40px rgba(0,0,0,0.2);
            --border-radius: 16px;
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
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
            scroll-behavior: smooth;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section */
        .story-hero {
            background: var(--gradient-primary);
            color: white;
            padding: 140px 0 100px;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .story-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"/></svg>');
            background-size: cover;
            animation: float 6s ease-in-out infinite;
        }

        .story-hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-block;
            padding: 12px 28px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .story-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin-bottom: 24px;
            font-weight: 800;
            line-height: 1.1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .story-hero p {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.6;
        }

        /* Story Sections */
        .story-section {
            padding: 100px 0;
        }

        .section-dark {
            background: var(--color-gray-dark);
            color: white;
        }

        .section-light {
            background: var(--color-gray-light);
        }

        .section-pattern {
            background: linear-gradient(135deg, #fdf2ff 0%, #f0f9ff 100%);
            position: relative;
            overflow: hidden;
        }

        .section-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(220, 38, 38, 0.05)"><circle cx="100" cy="50" r="30"/><circle cx="400" cy="20" r="20"/><circle cx="700" cy="80" r="25"/><circle cx="900" cy="40" r="15"/></svg>');
            background-size: cover;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-badge {
            display: inline-block;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .badge-primary {
            background: var(--gradient-primary);
            color: white;
        }

        .badge-secondary {
            background: var(--gradient-secondary);
            color: white;
        }

        .badge-gold {
            background: var(--gradient-gold);
            color: white;
        }

        .section-title {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.1;
        }

        .section-subtitle {
            font-size: 1.3rem;
            color: var(--color-gray);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .section-dark .section-subtitle {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Content Layout */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .content-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--color-gray-dark);
        }

        .section-dark .content-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .content-image {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }

        .content-image:hover {
            transform: translateY(-10px) rotate(2deg);
        }

        .content-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: var(--transition);
        }

        .content-image:hover img {
            transform: scale(1.05);
        }

        /* Values Grid */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .value-card {
            background: var(--color-white);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border-top: 4px solid var(--color-red);
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .value-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
        }

        .value-title {
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 700;
            color: var(--color-black);
        }

        .value-description {
            color: var(--color-gray);
            line-height: 1.6;
        }

        /* Timeline Section */
        .timeline-section {
            position: relative;
            padding: 100px 0;
        }

        .timeline-container {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 4px;
            background: var(--gradient-primary);
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 80px;
            width: 100%;
        }

        .timeline-item:nth-child(odd) {
            padding-right: calc(50% + 40px);
            text-align: right;
        }

        .timeline-item:nth-child(even) {
            padding-left: calc(50% + 40px);
            text-align: left;
        }

        .timeline-year {
            position: absolute;
            top: 0;
            width: 100px;
            height: 100px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.3rem;
            box-shadow: var(--shadow-md);
            z-index: 2;
        }

        .timeline-item:nth-child(odd) .timeline-year {
            right: calc(50% - 50px);
        }

        .timeline-item:nth-child(even) .timeline-year {
            left: calc(50% - 50px);
        }

        .timeline-content {
            background: var(--color-white);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            position: relative;
        }

        .timeline-content:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .timeline-content::before {
            content: '';
            position: absolute;
            top: 30px;
            width: 20px;
            height: 20px;
            background: var(--color-white);
            transform: rotate(45deg);
        }

        .timeline-item:nth-child(odd) .timeline-content::before {
            right: -10px;
        }

        .timeline-item:nth-child(even) .timeline-content::before {
            left: -10px;
        }

        .timeline-title {
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 700;
            color: var(--color-black);
        }

        .timeline-description {
            color: var(--color-gray);
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .timeline-image {
            border-radius: 12px;
            overflow: hidden;
            margin-top: 15px;
        }

        .timeline-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: var(--transition);
        }

        .timeline-image:hover img {
            transform: scale(1.05);
        }

        /* Milestones Section */
        .milestones-section {
            background: var(--gradient-secondary);
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .milestones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .milestone-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .milestone-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .milestone-card:hover::before {
            left: 100%;
        }

        .milestone-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
        }

        .milestone-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 15px;
            display: block;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .milestone-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .milestone-description {
            font-size: 0.95rem;
            opacity: 0.9;
            line-height: 1.5;
        }

        /* Leadership Section */
        .leadership-section {
            padding: 100px 0;
            background: var(--color-white);
        }

        .leadership-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .leader-card {
            background: var(--color-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            text-align: center;
        }

        .leader-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .leader-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
        }

        .leader-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .leader-card:hover .leader-image img {
            transform: scale(1.1);
        }

        .leader-info {
            padding: 30px 25px;
        }

        .leader-name {
            font-size: 1.4rem;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--color-black);
        }

        .leader-position {
            color: var(--color-red);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .leader-bio {
            color: var(--color-gray);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* CTA Section */
        .story-cta {
            background: var(--color-gray-dark);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
        }

        .story-cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.05)"><polygon points="1000,0 0,100 1000,100"/></svg>');
            background-size: cover;
        }

        .cta-buttons {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-align: center;
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-lg);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 30px rgba(220, 38, 38, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.95);
            color: var(--color-black);
            border: 2px solid transparent;
            box-shadow: var(--shadow-md);
        }

        .btn-secondary:hover {
            background: var(--color-white);
            border-color: var(--color-red);
            transform: translateY(-3px);
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Animation Classes */
        .fade-in {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-in-left {
            opacity: 0;
            transform: translateX(-60px);
            transition: all 0.8s ease;
        }

        .slide-in-left.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .slide-in-right {
            opacity: 0;
            transform: translateX(60px);
            transition: all 0.8s ease;
        }

        .slide-in-right.visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* Progress Scroll Indicator */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: var(--gradient-primary);
            z-index: 1001;
            transition: width 0.3s ease;
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .timeline-container::before {
                left: 30px;
            }

            .timeline-item:nth-child(odd),
            .timeline-item:nth-child(even) {
                padding: 0 0 0 80px;
                text-align: left;
            }

            .timeline-item:nth-child(odd) .timeline-year,
            .timeline-item:nth-child(even) .timeline-year {
                left: 0;
                right: auto;
            }

            .timeline-item:nth-child(odd) .timeline-content::before,
            .timeline-item:nth-child(even) .timeline-content::before {
                left: -10px;
                right: auto;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }

            .story-hero {
                padding: 120px 0 80px;
                min-height: 70vh;
            }

            .story-section {
                padding: 80px 0;
            }

            .values-grid,
            .milestones-grid,
            .leadership-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }

            .btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }

            .timeline-container::before {
                left: 20px;
            }

            .timeline-item:nth-child(odd),
            .timeline-item:nth-child(even) {
                padding: 0 0 0 60px;
            }
        }

        @media (max-width: 480px) {
            .story-hero h1 {
                font-size: 2.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .timeline-year {
                width: 80px;
                height: 80px;
                font-size: 1.1rem;
            }

            .leader-image {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Scroll Progress Indicator -->
    <div class="scroll-progress"></div>

    <?php include '../includes/header.php'; ?>

    <!-- Story Hero Section -->
    <section class="story-hero">
        <div class="container">
            <div class="story-hero-content fade-in">
                <div class="hero-badge floating">
                    <i class="fas fa-history"></i>
                    Our Journey Since 2013
                </div>
                <h1>Our Story</h1>
                <p>A decade of excellence in education, nurturing young minds and building future leaders at St. Philip Neri School</p>
                <div class="cta-buttons">
                    <a href="#timeline" class="btn btn-secondary">
                        <i class="fas fa-scroll"></i>
                        Explore Timeline
                    </a>
                    <a href="#leadership" class="btn btn-primary">
                        <i class="fas fa-users"></i>
                        Meet Our Team
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="story-section section-light">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge badge-primary">Our Foundation</span>
                <h2 class="section-title">Building Excellence Since 2013</h2>
                <p class="section-subtitle">From humble beginnings to becoming a beacon of educational excellence</p>
            </div>

            <div class="content-grid">
                <div class="content-text slide-in-left">
                    <?php
                    $about_section = array_filter($story_sections, function($section) {
                        return $section['section_type'] === 'about';
                    });
                    $about_content = !empty($about_section) ? array_values($about_section)[0] : null;
                    ?>

                    <?php if ($about_content): ?>
                        <p><?php echo nl2br(htmlspecialchars($about_content['section_content'])); ?></p>
                    <?php else: ?>
                        <p>Established in 2013, St. Philip Neri School was founded on the principles of academic excellence, character development, and community service. Our founders envisioned an institution that would not only impart knowledge but also instill strong moral values and prepare students for the challenges of a rapidly changing world.</p>
                    <?php endif; ?>

                    <div style="margin-top: 30px;">
                        <div class="content-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 0;">
                            <div>
                                <h4 style="color: var(--color-red); margin-bottom: 10px;">
                                    <i class="fas fa-bullseye"></i> Our Focus
                                </h4>
                                <p style="font-size: 0.95rem;">Holistic education that balances academic rigor with character building and extracurricular development.</p>
                            </div>
                            <div>
                                <h4 style="color: var(--color-green); margin-bottom: 10px;">
                                    <i class="fas fa-heart"></i> Our Commitment
                                </h4>
                                <p style="font-size: 0.95rem;">Creating a nurturing environment where every student can discover and develop their unique potential.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-image slide-in-right">
                    <img src="images/story/foundation.jpg" alt="St. Philip Neri School Foundation"
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDYwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI2MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjZGM2NjI2Ii8+Cjx0ZXh0IHg9IjMwMCIgeT0iMjAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIj5TY2hvb2wgRm91bmRhdGlvbjwvdGV4dD4KPC9zdmc+Cg=='">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="story-section section-dark">
        <div class="container">
            <div class="content-grid">
                <div class="slide-in-left">
                    <span class="section-badge badge-secondary">Our Mission</span>
                    <h3 class="section-title" style="text-align: left; margin-bottom: 20px;">Our Purpose</h3>
                    <?php
                    $mission_section = array_filter($story_sections, function($section) {
                        return $section['section_type'] === 'mission';
                    });
                    $mission_content = !empty($mission_section) ? array_values($mission_section)[0] : null;
                    ?>

                    <?php if ($mission_content): ?>
                        <p class="content-text"><?php echo nl2br(htmlspecialchars($mission_content['section_content'])); ?></p>
                    <?php else: ?>
                        <p class="content-text">To provide a transformative educational experience that empowers students to achieve their fullest potential, cultivate lifelong learning, and become responsible global citizens who contribute positively to society.</p>
                    <?php endif; ?>
                </div>

                <div class="slide-in-right">
                    <span class="section-badge badge-gold">Our Vision</span>
                    <h3 class="section-title" style="text-align: left; margin-bottom: 20px;">Our Aspiration</h3>
                    <?php
                    $vision_section = array_filter($story_sections, function($section) {
                        return $section['section_type'] === 'vision';
                    });
                    $vision_content = !empty($vision_section) ? array_values($vision_section)[0] : null;
                    ?>

                    <?php if ($vision_content): ?>
                        <p class="content-text"><?php echo nl2br(htmlspecialchars($vision_content['section_content'])); ?></p>
                    <?php else: ?>
                        <p class="content-text">To be a leading educational institution recognized for academic excellence, innovative teaching methods, and developing well-rounded individuals who make meaningful contributions to their communities and the world.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="story-section section-pattern">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge badge-primary">Our Values</span>
                <h2 class="section-title">The Principles That Guide Us</h2>
                <p class="section-subtitle">Core values that shape our educational philosophy and community culture</p>
            </div>

            <div class="values-grid">
                <?php
                $values_section = array_filter($story_sections, function($section) {
                    return $section['section_type'] === 'values';
                });
                $values_content = !empty($values_section) ? array_values($values_section)[0] : null;
                $values_list = $values_content ? explode("\n", $values_content['section_content']) : [
                    'Excellence: Striving for the highest standards in all endeavors',
                    'Integrity: Upholding honesty and ethical principles',
                    'Respect: Valuing diversity and treating all with dignity',
                    'Innovation: Embracing creativity and forward-thinking approaches',
                    'Community: Fostering collaboration and social responsibility'
                ];
                ?>

                <?php foreach ($values_list as $index => $value_line): ?>
                    <?php
                    $parts = explode(':', $value_line, 2);
                    $title = trim($parts[0]);
                    $description = isset($parts[1]) ? trim($parts[1]) : '';
                    $icons = ['fas fa-star', 'fas fa-shield-alt', 'fas fa-handshake', 'fas fa-lightbulb', 'fas fa-users'];
                    ?>
                    <div class="value-card fade-in">
                        <div class="value-icon">
                            <i class="<?php echo $icons[$index] ?? 'fas fa-star'; ?>"></i>
                        </div>
                        <h3 class="value-title"><?php echo htmlspecialchars($title); ?></h3>
                        <p class="value-description"><?php echo htmlspecialchars($description); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section id="timeline" class="timeline-section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge badge-primary">Our Journey</span>
                <h2 class="section-title">A Decade of Growth & Achievement</h2>
                <p class="section-subtitle">Milestones and achievements that mark our journey since 2013</p>
            </div>

            <div class="timeline-container">
                <?php if (!empty($timeline_events)): ?>
                    <?php foreach ($timeline_events as $event): ?>
                        <div class="timeline-item fade-in">
                            <div class="timeline-year"><?php echo htmlspecialchars($event['year']); ?></div>
                            <div class="timeline-content">
                                <h3 class="timeline-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="timeline-description"><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php if ($event['image']): ?>
                                    <div class="timeline-image">
                                        <img src="<?php echo $event['image']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>"
                                             onerror="this.style.display='none'">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback timeline events -->
                    <?php
                    $fallback_events = [
                        ['2013', 'The Beginning', 'St. Philip Neri School was established with 50 students and 8 dedicated staff members.'],
                        ['2014', 'First Graduating Class', 'We celebrated our first kindergarten graduating class and expanded our facilities.'],
                        ['2015', 'Academic Excellence', 'Our students achieved outstanding results in national examinations.'],
                        ['2016', 'Sports Complex', 'State-of-the-art sports complex opened for athletic development.'],
                        ['2017', 'International Recognition', 'Received accreditation from international educational bodies.'],
                        ['2018', 'Technology Integration', 'Implemented comprehensive digital learning platforms.'],
                        ['2019', 'Arts Center', 'Opened dedicated arts center for creative programs.'],
                        ['2020', 'Pandemic Response', 'Successfully transitioned to hybrid learning models.'],
                        ['2021', 'Sustainability', 'Launched comprehensive environmental programs.'],
                        ['2022', 'Community Outreach', 'Extended community service programs impacting thousands.'],
                        ['2023', 'Decade Celebration', 'Celebrated 10 years of educational excellence.'],
                        ['2024', 'Future Ready', 'Launched AI and robotics labs for technological education.']
                    ];
                    ?>
                    <?php foreach ($fallback_events as $event): ?>
                        <div class="timeline-item fade-in">
                            <div class="timeline-year"><?php echo $event[0]; ?></div>
                            <div class="timeline-content">
                                <h3 class="timeline-title"><?php echo $event[1]; ?></h3>
                                <p class="timeline-description"><?php echo $event[2]; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Milestones Section -->
    <section class="milestones-section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge badge-gold">By The Numbers</span>
                <h2 class="section-title">Our Impact in Numbers</h2>
                <p class="section-subtitle">Quantifying our journey of growth and excellence over the years</p>
            </div>

            <div class="milestones-grid">
                <?php if (!empty($milestones)): ?>
                    <?php foreach ($milestones as $milestone): ?>
                        <div class="milestone-card fade-in">
                            <span class="milestone-number"><?php echo htmlspecialchars($milestone['milestone_number']); ?></span>
                            <h3 class="milestone-title"><?php echo htmlspecialchars($milestone['title']); ?></h3>
                            <p class="milestone-description"><?php echo htmlspecialchars($milestone['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback milestones -->
                    <div class="milestone-card fade-in">
                        <span class="milestone-number">1500+</span>
                        <h3 class="milestone-title">Successful Alumni</h3>
                        <p class="milestone-description">Students who have graduated and are making their mark worldwide</p>
                    </div>
                    <div class="milestone-card fade-in">
                        <span class="milestone-number">95%</span>
                        <h3 class="milestone-title">University Acceptance</h3>
                        <p class="milestone-description">Our graduates accepted into top universities globally</p>
                    </div>
                    <div class="milestone-card fade-in">
                        <span class="milestone-number">50+</span>
                        <h3 class="milestone-title">National Awards</h3>
                        <p class="milestone-description">Recognition for academic and extracurricular excellence</p>
                    </div>
                    <div class="milestone-card fade-in">
                        <span class="milestone-number">25+</span>
                        <h3 class="milestone-title">Qualified Faculty</h3>
                        <p class="milestone-description">Dedicated teachers with advanced degrees and training</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Leadership Section -->
    <section id="leadership" class="story-section leadership-section">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-badge badge-primary">Our Leadership</span>
                <h2 class="section-title">Meet Our Visionary Team</h2>
                <p class="section-subtitle">Dedicated educators and administrators guiding our journey</p>
            </div>

            <div class="leadership-grid">
                <?php if (!empty($leadership)): ?>
                    <?php foreach ($leadership as $leader): ?>
                        <div class="leader-card fade-in">
                            <div class="leader-image">
                                <img src="<?php echo $leader['image']; ?>" alt="<?php echo htmlspecialchars($leader['name']); ?>"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDMwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjZDNkM2QzIi8+CjxjaXJjbGUgY3g9IjE1MCIgY3k9IjEyMCIgcj0iNDAiIGZpbGw9IiM2YjcyODAiLz4KPHBhdGggZD0iTTE1MCAyMDBDMTAwIDIwMCA4MCAyNTAgODAgMjUwSDIyMEMyMjAgMjUwIDIwMCAyMDAgMTUwIDIwMFoiIGZpbGw9IiM2YjcyODAiLz4KPC9zdmc+Cg=='">
                            </div>
                            <div class="leader-info">
                                <h3 class="leader-name"><?php echo htmlspecialchars($leader['name']); ?></h3>
                                <div class="leader-position"><?php echo htmlspecialchars($leader['position']); ?></div>
                                <p class="leader-bio"><?php echo htmlspecialchars($leader['bio']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback leadership -->

                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="story-cta">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Become Part of Our Story</h2>
                <p class="section-subtitle">Join our community and continue the legacy of excellence at St. Philip Neri School</p>
            </div>
            <div class="cta-buttons">
                <a href="admissions.php" class="btn btn-primary">
                    <i class="fas fa-user-graduate"></i>
                    Apply Now
                </a>
                <a href="contact.php" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i>
                    Contact Us
                </a>
                <a href="contact.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt"></i>
                    Schedule Tour
                </a>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll Progress Indicator
            const scrollProgress = document.querySelector('.scroll-progress');

            window.addEventListener('scroll', () => {
                const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (window.scrollY / windowHeight) * 100;
                scrollProgress.style.width = `${scrolled}%`;
            });

            // Intersection Observer for animations
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

            const slideLeftObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            const slideRightObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe elements
            document.querySelectorAll('.fade-in').forEach(el => fadeObserver.observe(el));
            document.querySelectorAll('.slide-in-left').forEach(el => slideLeftObserver.observe(el));
            document.querySelectorAll('.slide-in-right').forEach(el => slideRightObserver.observe(el));

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

            // Add loading animation for images
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                    this.style.transform = 'scale(1)';
                });

                // Set initial state
                img.style.opacity = '0';
                img.style.transform = 'scale(0.95)';
                img.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });
        });
    </script>
</body>
</html>
