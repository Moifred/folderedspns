<?php
require_once '../includes/config.php';

// Set page meta for SEO
$page_title = "Co-curricular Programs - St. Philip Neri School";
$meta_description = "Explore our diverse co-curricular programs at St. Philip Neri School. Sports, arts, clubs, and more to develop well-rounded students.";
$meta_keywords = "co-curricular activities, student clubs, sports teams, arts programs, school activities, extracurricular";

// Get co-curricular data from database
$co_curricular_categories = getCoCurricularCategories();
$co_curricular_items = getCoCurricularItems();

// Handle newsletter subscription
if ($_POST['email'] && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        $stmt = $pdo->prepare("INSERT INTO subscriptions (email, subscribed_at, ip_address) VALUES (?, NOW(), ?)");
        $stmt->execute([$email, $_SERVER['REMOTE_ADDR']]);
        $newsletter_success = "Thank you for subscribing!";
    } else {
        $newsletter_error = "Please enter a valid email address.";
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Extended CSS with compact co-curricular specific styles */
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
            --gradient-sports: linear-gradient(135deg, #dc2626, #f97316);
            --gradient-arts: linear-gradient(135deg, #ec4899, #8b5cf6);
            --gradient-academic: linear-gradient(135deg, #3b82f6, #0ea5e9);
            --gradient-leadership: linear-gradient(135deg, #16a34a, #0d9488);
            --gradient-technology: linear-gradient(135deg, #8b5cf6, #3b82f6);
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

        /* Hero Section for Co-curricular Page */
        .co-curricular-hero {
            background: var(--gradient-secondary);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .co-curricular-hero::before {
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

        .co-curricular-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .co-curricular-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin-bottom: 24px;
            font-weight: 800;
            line-height: 1.1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .co-curricular-hero p {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.6;
        }

        /* Category Navigation */
        .category-nav {
            background: var(--color-white);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .category-nav-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .category-nav-btn {
            padding: 14px 28px;
            border: none;
            border-radius: 50px;
            background: var(--color-gray-light);
            color: var(--color-gray-dark);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }

        .category-nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-secondary);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .category-nav-btn.active,
        .category-nav-btn:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .category-nav-btn.active::before,
        .category-nav-btn:hover::before {
            left: 0;
        }

        /* COMPACT Programs Showcase Section */
        .programs-showcase {
            padding: 100px 0;
            background: linear-gradient(135deg, #f0f9ff 0%, #fdf2ff 100%);
            position: relative;
        }

        /* More compact grid with smaller cards */
        .programs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .program-card {
            background: var(--color-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .program-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .program-card:hover::before {
            left: 100%;
        }

        .program-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        /* Smaller image container */
        .program-image-container {
            position: relative;
            overflow: hidden;
            height: 180px;
            flex-shrink: 0;
        }

        .program-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .program-card:hover .program-image {
            transform: scale(1.08);
        }

        .program-category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .badge-sports { background: var(--gradient-sports); }
        .badge-arts { background: var(--gradient-arts); }
        .badge-academic { background: var(--gradient-academic); }
        .badge-leadership { background: var(--gradient-leadership); }
        .badge-technology { background: var(--gradient-technology); }

        .program-card:hover .program-category-badge {
            transform: scale(1.05) rotate(3deg);
        }

        /* Compact program content */
        .program-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .program-title {
            font-size: 1.25rem;
            color: var(--color-black);
            margin-bottom: 12px;
            font-weight: 700;
            line-height: 1.3;
        }

        .program-description {
            color: var(--color-gray);
            margin-bottom: 15px;
            line-height: 1.5;
            font-size: 0.9rem;
            flex-grow: 1;
        }

        /* Compact details layout */
        .program-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: var(--color-gray-light);
            border-radius: 10px;
        }

        .program-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--color-gray-dark);
            font-size: 0.85rem;
        }

        .program-detail i {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            color: white;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .detail-schedule i { background: var(--color-purple); }
        .detail-location i { background: var(--color-blue); }
        .detail-instructor i { background: var(--color-green); }
        .detail-level i { background: var(--color-orange); }

        /* Compact buttons */
        .program-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
            position: relative;
            overflow: hidden;
            flex: 1;
            justify-content: center;
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
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4);
        }

        .btn-secondary {
            background: var(--color-gray-light);
            color: var(--color-gray-dark);
            border: 2px solid transparent;
        }

        .btn-secondary:hover {
            background: var(--color-white);
            border-color: var(--color-purple);
            transform: translateY(-2px);
        }

        /* Benefits Section */
        .benefits-section {
            background: var(--gradient-primary);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .benefits-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,0 1000,100 0,100"/></svg>');
            background-size: cover;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 50px;
            position: relative;
            z-index: 2;
        }

        .benefit-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 35px 25px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .benefit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .benefit-card:hover::before {
            left: 100%;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .benefit-icon {
            font-size: 2.8rem;
            margin-bottom: 20px;
            display: block;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .benefit-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .benefit-description {
            line-height: 1.6;
            opacity: 0.95;
            font-size: 0.95rem;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #fefce8 0%, #f0fdf4 100%);
            padding: 80px 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }

        .stat-item {
            background: var(--color-white);
            padding: 30px 20px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border-top: 4px solid transparent;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-sports { border-top-color: #dc2626; }
        .stat-arts { border-top-color: #ec4899; }
        .stat-academic { border-top-color: #3b82f6; }
        .stat-leadership { border-top-color: #16a34a; }

        .stat-icon {
            font-size: 2.2rem;
            margin-bottom: 15px;
            display: block;
        }

        .icon-sports { color: #dc2626; }
        .icon-arts { color: #ec4899; }
        .icon-academic { color: #3b82f6; }
        .icon-leadership { color: #16a34a; }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--color-gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* CTA Section */
        .co-curricular-cta {
            background: var(--color-gray-dark);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
        }

        .co-curricular-cta::before {
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
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 35px;
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
            font-size: clamp(2rem, 4vw, 3rem);
            color: var(--color-black);
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.2;
        }

        .section-title p {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            color: var(--color-gray);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .section-title-dark h2 {
            color: white;
        }

        .section-title-dark p {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Programs Empty State */
        .programs-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-gray);
            grid-column: 1 / -1;
        }

        .programs-empty i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--color-gray);
            opacity: 0.7;
        }

        .programs-empty h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
            color: var(--color-gray-dark);
        }

        .programs-empty p {
            font-size: 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .programs-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }

            .co-curricular-hero {
                padding: 100px 0 60px;
                min-height: 70vh;
            }

            .category-nav-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .category-nav-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .programs-showcase {
                padding: 70px 0;
            }

            .programs-grid {
                grid-template-columns: 1fr;
            }

            .program-details {
                grid-template-columns: 1fr;
            }

            .program-actions {
                flex-direction: column;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }

            .btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }

            .benefits-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .co-curricular-hero h1 {
                font-size: 2.2rem;
            }

            .program-content {
                padding: 18px;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }

            .benefit-card {
                padding: 25px 20px;
            }

            .program-image-container {
                height: 160px;
            }
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

        /* Progress Scroll Indicator */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: var(--gradient-secondary);
            z-index: 1001;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Scroll Progress Indicator -->
    <div class="scroll-progress"></div>

    <?php include '../includes/header.php'; ?>

    <!-- Co-curricular Hero Section -->
    <!-- <section class="co-curricular-hero">
        <div class="container">
            <div class="co-curricular-hero-content">
                <h1 class="fade-in">Beyond the Classroom</h1>
                <p class="fade-in">Discover our vibrant co-curricular programs that nurture talents, build character, and create unforgettable experiences for every student.</p>
                <div class="cta-buttons fade-in">
                    <a href="#programs" class="btn btn-secondary">
                        <i class="fas fa-arrow-down"></i>
                        Explore Programs
                    </a>
                    <a href="apply.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Join Today
                    </a>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Category Navigation -->
    <!-- <nav class="category-nav">
        <div class="container">
            <div class="category-nav-container">
                <button class="category-nav-btn active" data-category="all">
                    <i class="fas fa-star"></i>
                    All Programs
                </button>
                <button class="category-nav-btn" data-category="sports">
                    <i class="fas fa-running"></i>
                    Sports & Athletics
                </button>
                <button class="category-nav-btn" data-category="arts">
                    <i class="fas fa-palette"></i>
                    Arts & Creativity
                </button>
                <button class="category-nav-btn" data-category="academic">
                    <i class="fas fa-flask"></i>
                    Academic Clubs
                </button>
                <button class="category-nav-btn" data-category="leadership">
                    <i class="fas fa-users"></i>
                    Leadership & Service
                </button>
                <button class="category-nav-btn" data-category="technology">
                    <i class="fas fa-laptop-code"></i>
                    Technology & Innovation
                </button>
            </div>
        </div>
    </nav> -->

    <!-- Programs Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Our Co-curricular Impact</h2>
                <p>Engaging students across diverse interests and talents</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item stat-sports fade-in">
                    <i class="fas fa-running stat-icon icon-sports"></i>
                    <div class="stat-number">18</div>
                    <div class="stat-label">Sports Teams</div>
                </div>

                <div class="stat-item stat-arts fade-in">
                    <i class="fas fa-palette stat-icon icon-arts"></i>
                    <div class="stat-number">12</div>
                    <div class="stat-label">Arts Programs</div>
                </div>

                <div class="stat-item stat-academic fade-in">
                    <i class="fas fa-flask stat-icon icon-academic"></i>
                    <div class="stat-number">15</div>
                    <div class="stat-label">Academic Clubs</div>
                </div>

                <div class="stat-item stat-leadership fade-in">
                    <i class="fas fa-users stat-icon icon-leadership"></i>
                    <div class="stat-number">8</div>
                    <div class="stat-label">Leadership Groups</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Showcase -->
    <section class="programs-showcase" id="programs">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Featured Programs</h2>
                <p>Discover activities that complement your academic journey and ignite your passions</p>
            </div>

            <div class="programs-grid">
                <?php if (!empty($co_curricular_items)): ?>
                    <?php foreach ($co_curricular_items as $program): ?>
                        <div class="program-card fade-in" data-category="<?php echo htmlspecialchars($program['category']); ?>">
                            <div class="program-image-container">
                                <img src="<?php echo $program['image_path'] ?: 'images/program-default.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($program['title']); ?>"
                                     class="program-image"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjE4MCIgdmlld0JveD0iMCAwIDQwMCAxODAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iMTgwIiBmaWxsPSIjZDNkM2QzIi8+Cjx0ZXh0IHg9IjIwMCIgeT0iOTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNiIgZmlsbD0iIzZiNzI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSI+Q28tY3VycmljdWxhciBQcm9ncmFtPC90ZXh0Pgo8L3N2Zz4K'">
                                <div class="program-category-badge badge-<?php echo htmlspecialchars($program['category']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($program['category'])); ?>
                                </div>
                            </div>
                            <div class="program-content">
                                <h3 class="program-title"><?php echo htmlspecialchars($program['title']); ?></h3>
                                <p class="program-description"><?php echo htmlspecialchars($program['description']); ?></p>

                                <div class="program-details">
                                    <div class="program-detail detail-schedule">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo htmlspecialchars($program['schedule']); ?></span>
                                    </div>
                                    <div class="program-detail detail-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($program['location']); ?></span>
                                    </div>
                                    <div class="program-detail detail-instructor">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($program['instructor']); ?></span>
                                    </div>
                                    <div class="program-detail detail-level">
                                        <i class="fas fa-signal"></i>
                                        <span><?php echo htmlspecialchars($program['level'] ?? 'All Levels'); ?></span>
                                    </div>
                                </div>

                                <div class="program-actions">
                                    <a href="program_detail.php?id=<?php echo $program['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-info-circle"></i>
                                        Details
                                    </a>
                                    <a href="apply.php?program=<?php echo $program['id']; ?>" class="btn btn-secondary">
                                        <i class="fas fa-user-plus"></i>
                                        Join
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="programs-empty">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>No Programs Available</h3>
                        <p>We're currently updating our co-curricular offerings. Please check back soon for exciting new programs!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-title section-title-dark fade-in">
                <h2>Why Co-curricular Matters</h2>
                <p>Developing well-rounded individuals through diverse experiences</p>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card fade-in">
                    <i class="fas fa-brain benefit-icon"></i>
                    <h3 class="benefit-title">Enhanced Learning</h3>
                    <p class="benefit-description">Apply classroom knowledge in practical settings and develop critical thinking skills through hands-on experiences.</p>
                </div>

                <div class="benefit-card fade-in">
                    <i class="fas fa-users benefit-icon"></i>
                    <h3 class="benefit-title">Social Development</h3>
                    <p class="benefit-description">Build meaningful friendships, teamwork skills, and learn to collaborate with diverse peers in supportive environments.</p>
                </div>

                <div class="benefit-card fade-in">
                    <i class="fas fa-trophy benefit-icon"></i>
                    <h3 class="benefit-title">Confidence Building</h3>
                    <p class="benefit-description">Develop self-esteem through achievement, performance, and recognition in non-academic areas of interest.</p>
                </div>

                <div class="benefit-card fade-in">
                    <i class="fas fa-heartbeat benefit-icon"></i>
                    <h3 class="benefit-title">Health & Wellness</h3>
                    <p class="benefit-description">Promote physical health through sports activities and mental wellness through creative expression and social connection.</p>
                </div>

                <div class="benefit-card fade-in">
                    <i class="fas fa-graduation-cap benefit-icon"></i>
                    <h3 class="benefit-title">College Preparation</h3>
                    <p class="benefit-description">Build a strong profile for college applications with diverse experiences, leadership roles, and specialized skills.</p>
                </div>

                <div class="benefit-card fade-in">
                    <i class="fas fa-balance-scale benefit-icon"></i>
                    <h3 class="benefit-title">Time Management</h3>
                    <p class="benefit-description">Learn to balance academic responsibilities with extracurricular commitments, preparing for future challenges.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="co-curricular-cta">
        <div class="container">
            <div class="section-title section-title-dark fade-in">
                <h2>Ready to Get Involved?</h2>
                <p>Join one of our co-curricular programs and discover your passions beyond the classroom</p>
            </div>
            <div class="cta-buttons">
                <!-- <a href="apply.php" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i>
                    Join a Program
                </a> -->
                <a href="contact.php" class="btn btn-primary">
                    <i class="fas fa-question-circle"></i>
                    Ask Questions
                </a>
                <!-- <a href="tour.php" class="btn btn-outline">
                    <i class="fas fa-calendar-alt"></i>
                    Schedule a Visit
                </a> -->
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

            // Category Navigation
            const navButtons = document.querySelectorAll('.category-nav-btn');
            const programCards = document.querySelectorAll('.program-card');

            navButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetCategory = button.getAttribute('data-category');

                    // Update active button
                    navButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Filter programs
                    programCards.forEach(card => {
                        if (targetCategory === 'all' || card.getAttribute('data-category') === targetCategory) {
                            card.style.display = 'flex';
                            // Add animation for reappearing cards
                            card.classList.remove('fade-in', 'visible');
                            setTimeout(() => {
                                card.classList.add('fade-in', 'visible');
                            }, 50);
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
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

            // Observe elements
            document.querySelectorAll('.fade-in').forEach(el => fadeObserver.observe(el));

            // Enhanced hover effects
            const statItems = document.querySelectorAll('.stat-item');
            statItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Parallax effect for hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.co-curricular-hero');
                if (hero) {
                    hero.style.transform = `translateY(${scrolled * 0.5}px)`;
                }
            });

            // Add loading animation for images
            const images = document.querySelectorAll('.program-image');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                    this.style.transform = 'scale(1)';
                });

                // Set initial state
                img.style.opacity = '0';
                img.style.transform = 'scale(1.1)';
                img.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });

            // Smooth scrolling for all anchor links
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

            // Animate stats counting
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const target = parseInt(stat.textContent);
                let current = 0;
                const increment = target / 30;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }, 50);
            });
        });
    </script>
</body>
</html>
