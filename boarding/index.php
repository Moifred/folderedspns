<?php
session_start();
require_once '../includes/config.php';

// Check if user is admin or superadmin
$is_admin = isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'superadmin');

// Get boarding content from database
try {
    // Get main boarding content
    $boarding_sql = "SELECT * FROM boarding_content WHERE is_active = 1 ORDER BY display_order";
    $boarding_stmt = $pdo->query($boarding_sql);
    $boarding_content = $boarding_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get testimonials
    $testimonials_sql = "SELECT * FROM boarding_testimonials WHERE is_approved = 1 ORDER BY display_order";
    $testimonials_stmt = $pdo->query($testimonials_sql);
    $testimonials = $testimonials_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get facilities
    $facilities_sql = "SELECT * FROM boarding_facilities WHERE is_active = 1 ORDER BY display_order";
    $facilities_stmt = $pdo->query($facilities_sql);
    $facilities = $facilities_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get gallery images
    $gallery_sql = "SELECT * FROM boarding_gallery WHERE is_active = 1 ORDER BY display_order LIMIT 6";
    $gallery_stmt = $pdo->query($gallery_sql);
    $gallery = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error in boarding.php: " . $e->getMessage());
    $boarding_content = [];
    $testimonials = [];
    $facilities = [];
    $gallery = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boarding - St. Philip Neri School</title>
    <meta name="description" content="Discover our exceptional boarding facilities at St. Philip Neri School - A home away from home for students.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1a365d;
            --color-secondary: #2d3748;
            --color-accent: #e53e3e;
            --color-accent-light: #fed7d7;
            --color-accent-dark: #c53030;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;
            --color-success: #38a169;
            --color-warning: #d69e2e;

            --gradient-primary: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            --gradient-accent: linear-gradient(135deg, #e53e3e, #dd6b20);
            --gradient-subtle: linear-gradient(135deg, #f7fafc, #edf2f7);
            --gradient-dark: linear-gradient(135deg, #2d3748, #4a5568);

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
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-primary);
            color: white;
            padding: 140px 2rem 100px;
            text-align: center;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.05"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            position: relative;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
            position: relative;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--color-accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--color-accent-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        .btn-edit {
            background: var(--color-warning);
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-edit:hover {
            background: #b7791f;
        }

        /* Section Styles */
        .section {
            padding: 5rem 2rem;
        }

        .section-alt {
            background: var(--color-surface);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--color-primary);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--color-accent);
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--color-text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Content Sections */
        .content-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            margin-bottom: 4rem;
        }

        .content-section:nth-child(even) .content-image {
            order: 2;
        }

        .content-image {
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .content-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: var(--transition);
        }

        .content-image:hover img {
            transform: scale(1.05);
        }

        .content-text h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--color-primary);
        }

        .content-text p {
            margin-bottom: 1.5rem;
            color: var(--color-text-light);
        }

        .content-features {
            list-style: none;
            margin-top: 1.5rem;
        }

        .content-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .content-features i {
            color: var(--color-success);
            font-size: 1.1rem;
        }

        /* Facilities Grid */
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .facility-card {
            background: white;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .facility-icon {
            background: var(--gradient-accent);
            color: white;
            font-size: 2rem;
            padding: 1.5rem;
            text-align: center;
        }

        .facility-content {
            padding: 1.5rem;
        }

        .facility-title {
            font-size: 1.3rem;
            margin-bottom: 0.75rem;
            color: var(--color-primary);
        }

        .facility-description {
            color: var(--color-text-light);
            font-size: 0.95rem;
        }

        /* Testimonials */
        .testimonials-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }

        .testimonial-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            gap: 2rem;
            padding: 1rem 0;
        }

        .testimonial-slider::-webkit-scrollbar {
            display: none;
        }

        .testimonial-card {
            flex: 0 0 100%;
            scroll-snap-align: start;
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            position: relative;
        }

        .testimonial-card::before {
            content: '"';
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            color: var(--color-accent-light);
            position: absolute;
            top: -10px;
            left: 20px;
            line-height: 1;
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1.5rem;
            color: var(--color-text-light);
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .author-info h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .author-info p {
            font-size: 0.9rem;
            color: var(--color-text-light);
        }

        .slider-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .slider-btn {
            background: var(--color-surface);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .slider-btn:hover {
            background: var(--color-accent);
            color: white;
        }

        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .gallery-item {
            border-radius: var(--border-radius);
            overflow: hidden;
            position: relative;
            aspect-ratio: 1;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-view-btn {
            background: white;
            color: var(--color-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .gallery-view-btn:hover {
            background: var(--color-accent);
            color: white;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-dark);
            color: white;
            text-align: center;
            padding: 5rem 2rem;
            border-radius: var(--border-radius-lg);
            margin: 4rem auto;
            max-width: 1000px;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Admin Edit Controls */
        .admin-controls {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 1000;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 1rem;
            display: <?php echo $is_admin ? 'block' : 'none'; ?>;
        }

        .edit-section-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--color-warning);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .edit-section-btn:hover {
            background: #b7791f;
        }

        .edit-section-btn:last-child {
            margin-bottom: 0;
        }

        .edit-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--color-warning);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            cursor: pointer;
            opacity: 0;
            transition: var(--transition);
        }

        .editable-section:hover .edit-indicator {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .content-section {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .content-section:nth-child(even) .content-image {
                order: 0;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .section {
                padding: 3rem 1rem;
            }

            .hero-section {
                padding: 120px 1rem 80px;
            }

            .facilities-grid {
                grid-template-columns: 1fr;
            }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .testimonial-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Admin Controls (Visible only to admins) -->
    <?php if ($is_admin): ?>
    <div class="admin-controls">
        <button class="edit-section-btn" onclick="window.location.href='admin_boarding.php?section=hero'">
            <i class="fas fa-edit"></i> Edit Hero
        </button>
        <button class="edit-section-btn" onclick="window.location.href='admin_boarding.php?section=content'">
            <i class="fas fa-edit"></i> Edit Content
        </button>
        <button class="edit-section-btn" onclick="window.location.href='admin_boarding.php?section=facilities'">
            <i class="fas fa-edit"></i> Edit Facilities
        </button>
        <button class="edit-section-btn" onclick="window.location.href='admin_boarding.php?section=testimonials'">
            <i class="fas fa-edit"></i> Edit Testimonials
        </button>
        <button class="edit-section-btn" onclick="window.location.href='admin_boarding.php?section=gallery'">
            <i class="fas fa-edit"></i> Edit Gallery
        </button>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Boarding at St. Philip Neri</h1>
            <p class="hero-subtitle">A home away from home where students thrive academically, socially, and personally in a nurturing community.</p>
            <div class="hero-actions">
                <a href="#facilities" class="btn btn-primary">
                    <i class="fas fa-home"></i> Explore Facilities
                </a>
                <a href="#contact" class="btn btn-secondary">
                    <i class="fas fa-info-circle"></i> Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Welcome to Our Boarding Community</h2>
                <p class="section-subtitle">We provide a safe, structured, and supportive environment where students can flourish.</p>
            </div>

            <!-- Dynamic Content Sections -->
            <?php if (!empty($boarding_content)): ?>
                <?php foreach ($boarding_content as $index => $content): ?>
                    <div class="content-section editable-section" data-id="<?php echo $content['id']; ?>">
                        <?php if ($is_admin): ?>
                            <div class="edit-indicator" title="Edit Section" onclick="window.location.href='admin_boarding.php?edit_content=<?php echo $content['id']; ?>'">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                        <?php endif; ?>
                        <div class="content-image">
                            <?php if (!empty($content['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($content['image_url']); ?>" alt="<?php echo htmlspecialchars($content['title']); ?>">
                            <?php else: ?>
                                <div style="height: 400px; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="font-size: 3rem; color: var(--color-text-light);"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="content-text">
                            <h3><?php echo htmlspecialchars($content['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($content['content'])); ?></p>

                            <?php if (!empty($content['features'])): ?>
                                <ul class="content-features">
                                    <?php
                                    $features = explode(',', $content['features']);
                                    foreach ($features as $feature):
                                        if (!empty(trim($feature))):
                                    ?>
                                        <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(trim($feature)); ?></li>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </ul>
                            <?php endif; ?>

                            <?php if (!empty($content['cta_text']) && !empty($content['cta_link'])): ?>
                                <a href="<?php echo htmlspecialchars($content['cta_link']); ?>" class="btn btn-primary">
                                    <?php echo htmlspecialchars($content['cta_text']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default content if no database content -->
                <div class="content-section">
                    <div class="content-image">
                        <div style="height: 400px; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="font-size: 3rem; color: var(--color-text-light);"></i>
                        </div>
                    </div>
                    <div class="content-text">
                        <h3>Our Boarding Philosophy</h3>
                        <p>At St. Philip Neri, we believe that boarding life should be an extension of the family home. Our dedicated staff create a warm, inclusive environment where every student feels valued and supported.</p>
                        <ul class="content-features">
                            <li><i class="fas fa-check-circle"></i> 24/7 supervision by qualified staff</li>
                            <li><i class="fas fa-check-circle"></i> Academic support and tutoring</li>
                            <li><i class="fas fa-check-circle"></i> Structured study periods</li>
                            <li><i class="fas fa-check-circle"></i> Diverse extracurricular activities</li>
                        </ul>
                        <a href="#" class="btn btn-primary">Learn About Our Approach</a>
                    </div>
                </div>

                <div class="content-section">
                    <div class="content-text">
                        <h3>Life in Our Boarding Houses</h3>
                        <p>Our boarding houses are designed to be comfortable, modern spaces where students can relax, socialize, and study. Each house has common areas, study rooms, and recreational facilities.</p>
                        <ul class="content-features">
                            <li><i class="fas fa-check-circle"></i> Comfortable, well-furnished rooms</li>
                            <li><i class="fas fa-check-circle"></i> Common rooms with entertainment systems</li>
                            <li><i class="fas fa-check-circle"></i> Dedicated study areas</li>
                            <li><i class="fas fa-check-circle"></i> Kitchen facilities for snacks</li>
                        </ul>
                        <a href="#" class="btn btn-primary">View Our Accommodations</a>
                    </div>
                    <div class="content-image">
                        <div style="height: 400px; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="font-size: 3rem; color: var(--color-text-light);"></i>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="section section-alt" id="facilities">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Facilities</h2>
                <p class="section-subtitle">Modern amenities designed to support student wellbeing and academic success.</p>
            </div>

            <div class="facilities-grid">
                <?php if (!empty($facilities)): ?>
                    <?php foreach ($facilities as $facility): ?>
                        <div class="facility-card editable-section" data-id="<?php echo $facility['id']; ?>">
                            <?php if ($is_admin): ?>
                                <div class="edit-indicator" title="Edit Facility" onclick="window.location.href='admin_boarding.php?edit_facility=<?php echo $facility['id']; ?>'">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                            <?php endif; ?>
                            <div class="facility-icon">
                                <i class="<?php echo htmlspecialchars($facility['icon']); ?>"></i>
                            </div>
                            <div class="facility-content">
                                <h3 class="facility-title"><?php echo htmlspecialchars($facility['title']); ?></h3>
                                <p class="facility-description"><?php echo htmlspecialchars($facility['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default facilities if none in database -->
                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="facility-content">
                            <h3 class="facility-title">Comfortable Accommodations</h3>
                            <p class="facility-description">Spacious, well-furnished rooms with study areas, comfortable beds, and storage space for personal belongings.</p>
                        </div>
                    </div>

                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="facility-content">
                            <h3 class="facility-title">Nutritious Dining</h3>
                            <p class="facility-description">Healthy, balanced meals prepared by our professional kitchen staff, with options for special dietary requirements.</p>
                        </div>
                    </div>

                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="facility-content">
                            <h3 class="facility-title">Study Support</h3>
                            <p class="facility-description">Quiet study areas, computer labs, and access to tutoring for academic assistance during evening study periods.</p>
                        </div>
                    </div>

                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="facility-content">
                            <h3 class="facility-title">Recreation Facilities</h3>
                            <p class="facility-description">Access to sports fields, gymnasium, swimming pool, and common areas for relaxation and social activities.</p>
                        </div>
                    </div>

                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="facility-content">
                            <h3 class="facility-title">Health & Wellness</h3>
                            <p class="facility-description">On-site health center with qualified nursing staff, counseling services, and wellness programs.</p>
                        </div>
                    </div>

                    <div class="facility-card">
                        <div class="facility-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <div class="facility-content">
                            <h3 class="facility-title">Technology Access</h3>
                            <p class="facility-description">High-speed internet throughout campus, computer labs, and supervised technology use policies.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Hear From Our Boarders</h2>
                <p class="section-subtitle">Discover what current students and parents say about their boarding experience.</p>
            </div>

            <div class="testimonials-container">
                <div class="testimonial-slider" id="testimonialSlider">
                    <?php if (!empty($testimonials)): ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="testimonial-card editable-section" data-id="<?php echo $testimonial['id']; ?>">
                                <?php if ($is_admin): ?>
                                    <div class="edit-indicator" title="Edit Testimonial" onclick="window.location.href='admin_boarding.php?edit_testimonial=<?php echo $testimonial['id']; ?>'">
                                        <i class="fas fa-pencil-alt"></i>
                                    </div>
                                <?php endif; ?>
                                <p class="testimonial-text"><?php echo htmlspecialchars($testimonial['content']); ?></p>
                                <div class="testimonial-author">
                                    <?php if (!empty($testimonial['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($testimonial['image_url']); ?>" alt="<?php echo htmlspecialchars($testimonial['author']); ?>" class="author-avatar">
                                    <?php else: ?>
                                        <div class="author-avatar" style="background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="author-info">
                                        <h4><?php echo htmlspecialchars($testimonial['author']); ?></h4>
                                        <p><?php echo htmlspecialchars($testimonial['role']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Default testimonials if none in database -->
                        <div class="testimonial-card">
                            <p class="testimonial-text">Boarding at St. Philip Neri has been an incredible experience. The staff are supportive, the facilities are excellent, and I've made friends for life. The structured study periods have really helped improve my grades.</p>
                            <div class="testimonial-author">
                                <div class="author-avatar" style="background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="author-info">
                                    <h4>Sarah Johnson</h4>
                                    <p>Grade 11 Student</p>
                                </div>
                            </div>
                        </div>

                        <div class="testimonial-card">
                            <p class="testimonial-text">As a parent, I appreciate the safe and nurturing environment. The communication from house parents is excellent, and I know my daughter is well cared for. The academic support has made a real difference in her confidence.</p>
                            <div class="testimonial-author">
                                <div class="author-avatar" style="background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="author-info">
                                    <h4>Michael Thompson</h4>
                                    <p>Parent</p>
                                </div>
                            </div>
                        </div>

                        <div class="testimonial-card">
                            <p class="testimonial-text">The boarding community feels like a second family. There's always something to do on weekends, from sports to cultural activities. The food is great, and the facilities are modern and comfortable.</p>
                            <div class="testimonial-author">
                                <div class="author-avatar" style="background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="author-info">
                                    <h4>James Wilson</h4>
                                    <p>Grade 10 Student</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="slider-controls">
                    <button class="slider-btn" id="prevTestimonial">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slider-btn" id="nextTestimonial">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section section-alt">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Life in Pictures</h2>
                <p class="section-subtitle">Take a visual tour of our boarding facilities and community activities.</p>
            </div>

            <div class="gallery-grid">
                <?php if (!empty($gallery)): ?>
                    <?php foreach ($gallery as $image): ?>
                        <div class="gallery-item editable-section" data-id="<?php echo $image['id']; ?>">
                            <?php if ($is_admin): ?>
                                <div class="edit-indicator" title="Edit Image" onclick="window.location.href='admin_boarding.php?edit_gallery=<?php echo $image['id']; ?>'">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($image['caption']); ?>">
                            <div class="gallery-overlay">
                                <button class="gallery-view-btn" onclick="openImageModal('<?php echo htmlspecialchars($image['image_url']); ?>', '<?php echo htmlspecialchars($image['caption']); ?>')">View</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Placeholder gallery items if none in database -->
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="gallery-item">
                            <div style="height: 100%; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="font-size: 2rem; color: var(--color-text-light);"></i>
                            </div>
                            <div class="gallery-overlay">
                                <button class="gallery-view-btn">View</button>
                            </div>
                        </div>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="container">
            <div class="cta-section" id="contact">
                <h2 class="cta-title">Ready to Join Our Boarding Community?</h2>
                <p class="cta-subtitle">Contact us today to learn more about our boarding program, schedule a tour, or begin the application process.</p>
                <div class="hero-actions">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                    <a href="admissions.php" class="btn btn-secondary">
                        <i class="fas fa-file-alt"></i> Apply Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Modal -->
    <div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; align-items: center; justify-content: center;">
        <div style="position: relative; max-width: 90%; max-height: 90%;">
            <img id="modalImage" src="" alt="" style="max-width: 100%; max-height: 80vh; border-radius: 10px;">
            <p id="modalCaption" style="color: white; text-align: center; margin-top: 10px;"></p>
            <button onclick="closeImageModal()" style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 2rem; cursor: pointer;">×</button>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Testimonial slider functionality
            const slider = document.getElementById('testimonialSlider');
            const prevBtn = document.getElementById('prevTestimonial');
            const nextBtn = document.getElementById('nextTestimonial');

            if (slider && prevBtn && nextBtn) {
                let currentIndex = 0;
                const testimonials = slider.children;
                const totalTestimonials = testimonials.length;

                function updateSlider() {
                    slider.scrollTo({
                        left: currentIndex * slider.offsetWidth,
                        behavior: 'smooth'
                    });
                }

                prevBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalTestimonials - 1;
                    updateSlider();
                });

                nextBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex < totalTestimonials - 1) ? currentIndex + 1 : 0;
                    updateSlider();
                });

                // Auto-advance testimonials
                setInterval(function() {
                    currentIndex = (currentIndex < totalTestimonials - 1) ? currentIndex + 1 : 0;
                    updateSlider();
                }, 5000);
            }

            // Gallery view functionality
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach(item => {
                const viewBtn = item.querySelector('.gallery-view-btn');
                if (viewBtn) {
                    viewBtn.addEventListener('click', function() {
                        const img = item.querySelector('img');
                        const caption = item.querySelector('img')?.alt || 'Boarding Facility';
                        openImageModal(img?.src, caption);
                    });
                }
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            // Animate content sections
            document.querySelectorAll('.content-section, .facility-card, .testimonial-card, .gallery-item').forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.6s ease';
                observer.observe(element);
            });
        });

        // Image modal functions
        function openImageModal(src, caption) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');

            modalImage.src = src;
            modalCaption.textContent = caption;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>
