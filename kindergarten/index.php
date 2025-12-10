<?php
require_once '../includes/config.php';

// Set page meta for SEO
$page_title = "Kindergarten - St. Philip Neri School";
$meta_description = "Discover our exceptional kindergarten program at St. Philip Neri School. Nurturing young minds through play-based learning and holistic development.";
$meta_keywords = "kindergarten, early childhood education, preschool, St. Philip Neri, PP1, PP2, play-based learning";

// Initialize variables
$kindergarten_head_message = '';
$kindergarten_classes = [];
$kindergarten_gallery = [];

// Get kindergarten data from database
try {
    // Get head message from database
    $head_sql = "SELECT * FROM kindergarten_head WHERE active = 1 LIMIT 1";
    $head_stmt = $pdo->prepare($head_sql);
    $head_stmt->execute();
    $head_data = $head_stmt->fetch(PDO::FETCH_ASSOC);

    // Get classes from database
    $classes_sql = "SELECT * FROM kindergarten_classes WHERE active = 1 ORDER BY class_level, display_order";
    $classes_stmt = $pdo->prepare($classes_sql);
    $classes_stmt->execute();
    $kindergarten_classes = $classes_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get gallery images from database
    $gallery_sql = "SELECT * FROM kindergarten_gallery WHERE active = 1 ORDER BY display_order, created_at DESC LIMIT 8";
    $gallery_stmt = $pdo->prepare($gallery_sql);
    $gallery_stmt->execute();
    $kindergarten_gallery = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Error getting kindergarten data: " . $e->getMessage());
    // Use default fallback data
    $head_data = [
        'name' => 'Mrs. Elizabeth Thompson',
        'title' => 'Head of Kindergarten Section',
        'message' => "At St. Philip Neri Kindergarten, we believe that the early years of a child's education lay the foundation for a lifetime of learning. Our dedicated team of educators creates a nurturing, stimulating environment where children can explore, discover, and grow.\n\nOur play-based curriculum is designed to develop the whole child - intellectually, socially, emotionally, and physically. We focus on building strong foundational skills while fostering curiosity, creativity, and a love for learning that will serve children throughout their educational journey.\n\nWe understand that each child is unique, with individual strengths, interests, and learning styles. Our small class sizes and personalized approach ensure that every child receives the attention and support they need to thrive.",
        'image_path' => null
    ];

    $kindergarten_classes = [
        [
            'class_level' => 'PP1',
            'class_name' => 'White Lilies',
            'description' => 'A nurturing environment focused on developing foundational skills through creative play and exploration.',
            'features' => '["Language & Communication Skills", "Basic Numeracy Concepts", "Creative Arts & Crafts", "Social Skills Development", "Fine Motor Skills Activities"]',
            'icon' => 'fas fa-seedling'
        ],
        [
            'class_level' => 'PP1',
            'class_name' => 'Purple Lilies',
            'description' => 'An engaging classroom environment that encourages curiosity and early literacy through interactive activities.',
            'features' => '["Phonics & Early Reading", "Hands-on Math Activities", "Music & Movement", "Sensory Play", "Outdoor Exploration"]',
            'icon' => 'fas fa-leaf'
        ],
        [
            'class_level' => 'PP2',
            'class_name' => 'Purple Roses',
            'description' => 'Preparing children for primary school with advanced literacy, numeracy, and critical thinking skills.',
            'features' => '["Advanced Reading & Writing", "Mathematical Reasoning", "Science Exploration", "Problem-Solving Activities", "Team Projects"]',
            'icon' => 'fas fa-flower'
        ],
        [
            'class_level' => 'PP2',
            'class_name' => 'White Roses',
            'description' => 'Focusing on holistic development and school readiness through structured and imaginative learning.',
            'features' => '["Comprehensive Literacy Program", "Mathematical Concepts", "Creative Expression", "Social-Emotional Learning", "Primary School Preparation"]',
            'icon' => 'fas fa-spa'
        ]
    ];

    $kindergarten_gallery = [
        ['image_path' => 'images/kindergarten-classroom.jpg', 'caption' => 'Our vibrant classroom environment'],
        ['image_path' => 'images/kindergarten-play.jpg', 'caption' => 'Learning through play'],
        ['image_path' => 'images/kindergarten-art.jpg', 'caption' => 'Creative expression through art'],
        ['image_path' => 'images/kindergarten-music.jpg', 'caption' => 'Music and movement activities'],
        ['image_path' => 'images/kindergarten-story.jpg', 'caption' => 'Engaging story time sessions'],
        ['image_path' => 'images/kindergarten-science.jpg', 'caption' => 'Hands-on science exploration'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <style>
        /* Kindergarten Specific Styles */
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
            --color-pink: #ec4899;
            --color-lilac: #c084fc;
            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-secondary: linear-gradient(135deg, var(--color-purple), var(--color-blue));
            --gradient-pp1: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            --gradient-pp2: linear-gradient(135deg, #fdf2f8, #fce7f3);
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

        /* Kindergarten Hero Section */
        .kindergarten-hero {
            position: relative;
            height: 60vh;
            min-height: 500px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .kindergarten-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23f59e0b" opacity="0.1"><circle cx="20" cy="20" r="5"/><circle cx="50" cy="30" r="7"/><circle cx="80" cy="20" r="4"/><circle cx="30" cy="70" r="6"/><circle cx="70" cy="60" r="5"/><circle cx="90" cy="80" r="8"/></svg>');
            background-size: 300px 300px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: var(--color-gray-dark);
            padding: 40px 30px;
            max-width: 800px;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.2;
            color: var(--color-red);
        }

        .hero-content p {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
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
        }

        /* Classes Section */
        .classes-section {
            padding: 80px 0;
            background: var(--color-white);
        }

        .classes-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .class-category {
            margin-bottom: 50px;
        }

        .class-category h3 {
            font-size: 1.8rem;
            color: var(--color-red);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }

        .class-category h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .class-card {
            background: var(--color-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: var(--transition);
            border-top: 5px solid;
        }

        .class-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .class-card.pp1 {
            border-color: var(--color-blue);
        }

        .class-card.pp2 {
            border-color: var(--color-pink);
        }

        .class-header {
            padding: 25px 25px 15px;
            text-align: center;
            color: white;
        }

        .class-header.pp1 {
            background: var(--color-blue);
        }

        .class-header.pp2 {
            background: var(--color-pink);
        }

        .class-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .class-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .class-level {
            font-size: 1rem;
            opacity: 0.9;
        }

        .class-body {
            padding: 25px;
        }

        .class-features {
            list-style: none;
            margin-bottom: 20px;
        }

        .class-features li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .class-features li:last-child {
            border-bottom: none;
        }

        .class-features i {
            color: var(--color-green);
            font-size: 0.9rem;
        }

        .class-actions {
            display: flex;
            gap: 10px;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid;
            flex: 1;
            text-align: center;
            justify-content: center;
        }

        .btn-outline.pp1 {
            border-color: var(--color-blue);
            color: var(--color-blue);
        }

        .btn-outline.pp1:hover {
            background: var(--color-blue);
            color: white;
        }

        .btn-outline.pp2 {
            border-color: var(--color-pink);
            color: var(--color-pink);
        }

        .btn-outline.pp2:hover {
            background: var(--color-pink);
            color: white;
        }

        /* Head of Section */
        .head-section {
            padding: 80px 0;
            background: var(--color-gray-light);
        }

        .head-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 50px;
            align-items: center;
        }

        .head-image {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .head-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .head-message h3 {
            font-size: 1.8rem;
            color: var(--color-red);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .head-message .title {
            color: var(--color-gray);
            font-weight: 600;
            margin-bottom: 25px;
            display: block;
            font-size: 1.1rem;
        }

        .head-message p {
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .head-signature {
            margin-top: 30px;
            font-weight: 700;
            color: var(--color-black);
        }

        /* Gallery Section */
        .gallery-section {
            padding: 80px 0;
            background: var(--color-white);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 50px;
        }

        .gallery-item {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
            aspect-ratio: 1;
            position: relative;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
            padding: 20px;
            transform: translateY(100%);
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
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
        }

        .section-title p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--color-gray);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .head-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .head-image {
                max-width: 400px;
                margin: 0 auto;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 12px;
            }

            .kindergarten-hero {
                height: 50vh;
                min-height: 400px;
            }

            .hero-content {
                padding: 30px 20px;
            }

            .class-grid {
                grid-template-columns: 1fr;
            }

            .class-actions {
                flex-direction: column;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .kindergarten-hero {
                height: 45vh;
                min-height: 350px;
            }

            .class-card {
                margin-bottom: 20px;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php
    // Include header with error handling
    if (file_exists('header.php')) {
        include 'header.php';
    } else {
        echo '<header style="padding: 20px; background: var(--color-gray-dark); color: white; text-align: center;">St. Philip Neri School</header>';
    }
    ?>

    <!-- Kindergarten Hero Section -->
    <section class="kindergarten-hero">
        <div class="hero-content">
            <h1>Kindergarten Program</h1>
            <p>Nurturing young minds through play-based learning, creativity, and holistic development in a safe and stimulating environment.</p>
            <a href="#classes" class="btn btn-primary">
                <i class="fas fa-arrow-down"></i>
                Explore Our Classes
            </a>
        </div>
    </section>

    <!-- Classes Offered Section -->
    <section class="classes-section" id="classes">
        <div class="container">
            <div class="section-title">
                <h2>Our Kindergarten Classes</h2>
                <p>Age-appropriate learning environments designed to foster curiosity, creativity, and social development</p>
            </div>

            <?php if (!empty($kindergarten_classes)): ?>
                <!-- Group classes by level -->
                <?php
                $pp1_classes = array_filter($kindergarten_classes, function($class) {
                    return $class['class_level'] === 'PP1';
                });

                $pp2_classes = array_filter($kindergarten_classes, function($class) {
                    return $class['class_level'] === 'PP2';
                });
                ?>

                <!-- PP1 Classes -->
                <?php if (!empty($pp1_classes)): ?>
                    <div class="class-category">
                        <h3>PP1 - Pre-Primary 1 (Ages 4-5)</h3>
                        <div class="class-grid">
                            <?php foreach ($pp1_classes as $class):
                                $features = json_decode($class['features'] ?? '[]', true);
                            ?>
                                <div class="class-card pp1">
                                    <div class="class-header pp1">
                                        <div class="class-icon">
                                            <i class="<?php echo htmlspecialchars($class['icon']); ?>"></i>
                                        </div>
                                        <h4 class="class-name"><?php echo htmlspecialchars($class['class_name']); ?></h4>
                                        <div class="class-level">PP1 Class</div>
                                    </div>
                                    <div class="class-body">
                                        <p><?php echo htmlspecialchars($class['description']); ?></p>
                                        <?php if (!empty($features)): ?>
                                            <ul class="class-features">
                                                <?php foreach ($features as $feature): ?>
                                                    <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <div class="class-actions">
                                            <a href="enroll.php?class=<?php echo urlencode(strtolower(str_replace(' ', '_', $class['class_name']))); ?>" class="btn btn-outline pp1">
                                                <i class="fas fa-info-circle"></i>
                                                Learn More
                                            </a>
                                            <a href="tour.php?class=<?php echo urlencode(strtolower(str_replace(' ', '_', $class['class_name']))); ?>" class="btn btn-outline pp1">
                                                <i class="fas fa-calendar"></i>
                                                Schedule Tour
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- PP2 Classes -->
                <?php if (!empty($pp2_classes)): ?>
                    <div class="class-category">
                        <h3>PP2 - Pre-Primary 2 (Ages 5-6)</h3>
                        <div class="class-grid">
                            <?php foreach ($pp2_classes as $class):
                                $features = json_decode($class['features'] ?? '[]', true);
                            ?>
                                <div class="class-card pp2">
                                    <div class="class-header pp2">
                                        <div class="class-icon">
                                            <i class="<?php echo htmlspecialchars($class['icon']); ?>"></i>
                                        </div>
                                        <h4 class="class-name"><?php echo htmlspecialchars($class['class_name']); ?></h4>
                                        <div class="class-level">PP2 Class</div>
                                    </div>
                                    <div class="class-body">
                                        <p><?php echo htmlspecialchars($class['description']); ?></p>
                                        <?php if (!empty($features)): ?>
                                            <ul class="class-features">
                                                <?php foreach ($features as $feature): ?>
                                                    <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <div class="class-actions">
                                            <a href="enroll.php?class=<?php echo urlencode(strtolower(str_replace(' ', '_', $class['class_name']))); ?>" class="btn btn-outline pp2">
                                                <i class="fas fa-info-circle"></i>
                                                Learn More
                                            </a>
                                            <a href="tour.php?class=<?php echo urlencode(strtolower(str_replace(' ', '_', $class['class_name']))); ?>" class="btn btn-outline pp2">
                                                <i class="fas fa-calendar"></i>
                                                Schedule Tour
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--color-gray);">
                    <i class="fas fa-chalkboard-teacher" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p>No classes available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Head of Kindergarten Section -->
    <section class="head-section">
        <div class="container">
            <div class="section-title">
                <h2>Message from Our Kindergarten Head</h2>
                <p>Leadership dedicated to nurturing young minds and fostering a love for learning</p>
            </div>

            <div class="head-container">
                <div class="head-image">
                    <?php if (!empty($head_data['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($head_data['image_path']); ?>" alt="<?php echo htmlspecialchars($head_data['name'] ?? 'Kindergarten Head'); ?>"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDQwMCA1MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNTAwIiBmaWxsPSIjZTNlM2UzIi8+CjxjaXJjbGUgY3g9IjIwMCIgY3k9IjE4MCIgcj0iNjAiIGZpbGw9IiNiZWJlYmUiLz4KPHBhdGggZD0iTTIwMCAyODBDMTQwIDI4MCA5MCAzMzAgOTAgMzkwVjQ1MEgzMTBWMzkwQzMxMCAzMzAgMjYwIDI4MCAyMDAgMjgwWiIgZmlsbD0iI2JlYmViZSIvPgo8L3N2Zz4K'">
                    <?php else: ?>
                        <img src="images/kindergarten-head.jpg" alt="Kindergarten Head of Section"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDQwMCA1MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNTAwIiBmaWxsPSIjZTNlM2UzIi8+CjxjaXJjbGUgY3g9IjIwMCIgY3k9IjE4MCIgcj0iNjAiIGZpbGw9IiNiZWJlYmUiLz4KPHBhdGggZD0iTTIwMCAyODBDMTQwIDI4MCA5MCAzMzAgOTAgMzkwVjQ1MEgzMTBWMzkwQzMxMCAzMzAgMjYwIDI4MCAyMDAgMjgwWiIgZmlsbD0iI2JlYmViZSIvPgo8L3N2Zz4K'">
                    <?php endif; ?>
                </div>
                <div class="head-message">
                    <h3>Welcome to Our Kindergarten</h3>
                    <span class="title"><?php echo htmlspecialchars($head_data['title'] ?? 'Head of Kindergarten Section'); ?></span>

                    <?php if (!empty($head_data['message'])): ?>
                        <?php
                        $message_paragraphs = explode("\n", $head_data['message']);
                        foreach ($message_paragraphs as $paragraph):
                            if (trim($paragraph) !== ''):
                        ?>
                            <p><?php echo nl2br(htmlspecialchars(trim($paragraph))); ?></p>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    <?php else: ?>
                        <p>At St. Philip Neri Kindergarten, we believe that the early years of a child's education lay the foundation for a lifetime of learning. Our dedicated team of educators creates a nurturing, stimulating environment where children can explore, discover, and grow.</p>
                        <p>Our play-based curriculum is designed to develop the whole child - intellectually, socially, emotionally, and physically. We focus on building strong foundational skills while fostering curiosity, creativity, and a love for learning that will serve children throughout their educational journey.</p>
                        <p>We understand that each child is unique, with individual strengths, interests, and learning styles. Our small class sizes and personalized approach ensure that every child receives the attention and support they need to thrive.</p>
                    <?php endif; ?>

                    <div class="head-signature">
                        <p><?php echo htmlspecialchars($head_data['name'] ?? 'Mrs. Elizabeth Thompson'); ?></p>
                        <p><?php echo htmlspecialchars($head_data['title'] ?? 'Head of Kindergarten Section'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kindergarten Gallery -->
    <section class="gallery-section">
        <div class="container">
            <div class="section-title">
                <h2>Kindergarten Gallery</h2>
                <p>Take a peek into our vibrant kindergarten classrooms and activities</p>
            </div>

            <div class="gallery-grid">
                <?php if (!empty($kindergarten_gallery)): ?>
                    <?php foreach ($kindergarten_gallery as $image): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['caption'] ?? 'Kindergarten Activity'); ?>"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPktpbmRlcmdhcnRlbiBJbWFnZTwvdGV4dD4KPC9zdmc+Cg=='">
                            <?php if (!empty($image['caption'])): ?>
                                <div class="gallery-overlay">
                                    <p><?php echo htmlspecialchars($image['caption']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default gallery images -->
                    <div class="gallery-item">
                        <img src="images/kindergarten-classroom.jpg" alt="Kindergarten Classroom"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPkNsYXNzcm9vbSBBY3Rpdml0eTwvdGV4dD4KPC9zdmc+Cg=='">
                        <div class="gallery-overlay">
                            <p>Our vibrant classroom environment</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="images/kindergarten-play.jpg" alt="Children Playing"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPk91dGRvb3IgUGxheTwvdGV4dD4KPC9zdmc+Cg=='">
                        <div class="gallery-overlay">
                            <p>Learning through play</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="images/kindergarten-art.jpg" alt="Art Activity"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPkFydCBhbmQgQ3JhZnRzPC90ZXh0Pgo8L3N2Zz4K'">
                        <div class="gallery-overlay">
                            <p>Creative expression through art</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="images/kindergarten-music.jpg" alt="Music Class"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPk11c2ljIGFuZCBNb3ZlbWVudDwvdGV4dD4KPC9zdmc+Cg=='">
                        <div class="gallery-overlay">
                            <p>Music and movement activities</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="images/kindergarten-story.jpg" alt="Story Time"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPlN0b3J5IFRpbWU8L3RleHQ+Cjwvc3ZnPgo='">
                        <div class="gallery-overlay">
                            <p>Engaging story time sessions</p>
                        </div>
                    </div>
                    <div class="gallery-item">
                        <img src="images/kindergarten-science.jpg" alt="Science Exploration"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjI1MCIgdmlld0JveD0iMCAwIDI1MCAyNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyNTAiIGhlaWdodD0iMjUwIiBmaWxsPSIjZTNlM2UzIi8+Cjx0ZXh0IHg9IjEyNSIgeT0iMTI1IiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTk5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiPlNjaWVuY2UgRXhwbG9yYXRpb248L3RleHQ+Cjwvc3ZnPgo='">
                        <div class="gallery-overlay">
                            <p>Hands-on science exploration</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    // Include footer with error handling
    if (file_exists('footer.php')) {
        include 'footer.php';
    } else {
        echo '<footer style="padding: 20px; background: var(--color-gray-dark); color: white; text-align: center;">© ' . date('Y') . ' St. Philip Neri School. All rights reserved.</footer>';
    }
    ?>

    <script>
        // Simple JavaScript for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Add animation to gallery items on scroll
            const galleryItems = document.querySelectorAll('.gallery-item');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            galleryItems.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(item);
            });
        });
    </script>
</body>
</html>
