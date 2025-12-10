<?php
require_once 'config.php';

// Set page meta for SEO
$page_title = "Academic Sections - St. Philip Neri School";
$meta_description = "Explore our comprehensive academic sections from Kindergarten to Junior School. Each section offers specialized programs tailored to different age groups.";
$meta_keywords = "kindergarten, primary school, junior school, academic sections, St. Philip Neri";

// Get school sections data
$sections = [];
try {
    $stmt = $pdo->prepare("
        SELECT ss.*,
               GROUP_CONCAT(sf.feature_title ORDER BY sf.display_order) as feature_titles,
               GROUP_CONCAT(sf.feature_description ORDER BY sf.display_order) as feature_descriptions,
               GROUP_CONCAT(sf.feature_icon ORDER BY sf.display_order) as feature_icons
        FROM school_sections ss
        LEFT JOIN section_features sf ON ss.id = sf.section_id
        WHERE ss.is_active = 1
        GROUP BY ss.id
        ORDER BY ss.display_order
    ");
    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process features for each section
    foreach ($sections as &$section) {
        if ($section['feature_titles']) {
            $titles = explode(',', $section['feature_titles']);
            $descriptions = explode(',', $section['feature_descriptions']);
            $icons = explode(',', $section['feature_icons']);

            $section['features'] = [];
            for ($i = 0; $i < count($titles); $i++) {
                $section['features'][] = [
                    'title' => $titles[$i],
                    'description' => $descriptions[$i],
                    'icon' => $icons[$i]
                ];
            }
        } else {
            $section['features'] = [];
        }

        // Process classes
        $section['class_list'] = explode(',', $section['classes']);
    }
    unset($section);

} catch (PDOException $e) {
    error_log("Error fetching school sections: " . $e->getMessage());
}

// Get section statistics
$section_stats = [];
try {
    $stmt = $pdo->prepare("
        SELECT section_id, students_count, teacher_count, avg_class_size
        FROM section_statistics
        ORDER BY section_id
    ");
    $stmt->execute();
    $section_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Use default stats if table doesn't exist
    $section_stats = [
        ['section_id' => 1, 'students_count' => 120, 'teacher_count' => 8, 'avg_class_size' => 15],
        ['section_id' => 2, 'students_count' => 180, 'teacher_count' => 12, 'avg_class_size' => 20],
        ['section_id' => 3, 'students_count' => 200, 'teacher_count' => 14, 'avg_class_size' => 22],
        ['section_id' => 4, 'students_count' => 160, 'teacher_count' => 11, 'avg_class_size' => 20]
    ];
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
        /* Extended CSS with school section specific styles */
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
            --gradient-kindergarten: linear-gradient(135deg, #ec4899, #8b5cf6);
            --gradient-lower-primary: linear-gradient(135deg, #3b82f6, #0ea5e9);
            --gradient-upper-primary: linear-gradient(135deg, #f59e0b, #84cc16);
            --gradient-junior-school: linear-gradient(135deg, #dc2626, #7c2d12);
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

        /* Hero Section for School Page */
        .school-hero {
            background: var(--gradient-primary);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .school-hero::before {
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

        .school-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .school-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            margin-bottom: 24px;
            font-weight: 800;
            line-height: 1.1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .school-hero p {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.6;
        }

        /* Section Navigation */
        .section-nav {
            background: var(--color-white);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .section-nav-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .section-nav-btn {
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

        .section-nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .section-nav-btn.active,
        .section-nav-btn:hover {
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .section-nav-btn.active::before,
        .section-nav-btn:hover::before {
            left: 0;
        }

        /* School Section Styles */
        .school-section {
            padding: 120px 0;
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .section-kindergarten {
            background: linear-gradient(135deg, #fdf2ff 0%, #f0f9ff 100%);
        }

        .section-lower-primary {
            background: linear-gradient(135deg, #f0f9ff 0%, #f0fdf4 100%);
        }

        .section-upper-primary {
            background: linear-gradient(135deg, #fefce8 0%, #f0fdf4 100%);
        }

        .section-junior-school {
            background: linear-gradient(135deg, #fef2f2 0%, #fefce8 100%);
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-badge {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .section-badge:hover {
            transform: translateY(-2px);
        }

        .badge-kindergarten {
            background: var(--gradient-kindergarten);
            color: white;
        }

        .badge-lower-primary {
            background: var(--gradient-lower-primary);
            color: white;
        }

        .badge-upper-primary {
            background: var(--gradient-upper-primary);
            color: white;
        }

        .badge-junior-school {
            background: var(--gradient-junior-school);
            color: white;
        }

        .section-title {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            margin-bottom: 24px;
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

        /* Section Content Layout */
        .section-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: start;
        }

        .head-profile {
            text-align: center;
            position: relative;
        }

        .head-image-container {
            position: relative;
            width: 320px;
            height: 320px;
            margin: 0 auto 40px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }

        .head-image-container:hover {
            transform: rotate(-2deg) scale(1.02);
        }

        .head-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .head-image-container:hover .head-image {
            transform: scale(1.1);
        }

        .head-info h3 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            font-weight: 700;
            color: var(--color-black);
        }

        .head-title {
            color: var(--color-red);
            font-weight: 600;
            margin-bottom: 30px;
            font-size: 1.2rem;
        }

        .head-message {
            background: var(--color-white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            position: relative;
            margin-top: 30px;
            border-left: 6px solid var(--color-red);
        }

        .head-message::before {
            content: '"';
            font-size: 5rem;
            color: var(--color-red);
            position: absolute;
            top: -20px;
            left: 30px;
            line-height: 1;
            opacity: 0.3;
        }

        .head-message p {
            font-style: italic;
            color: var(--color-gray-dark);
            line-height: 1.8;
            font-size: 1.1rem;
            position: relative;
            z-index: 2;
        }

        /* Section Statistics */
        .section-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 40px 0;
        }

        .stat-item {
            background: var(--color-white);
            padding: 25px 20px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border-top: 4px solid transparent;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-kindergarten { border-top-color: #ec4899; }
        .stat-lower-primary { border-top-color: #3b82f6; }
        .stat-upper-primary { border-top-color: #f59e0b; }
        .stat-junior-school { border-top-color: #dc2626; }

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

        /* Classes and Features */
        .section-details {
            display: grid;
            gap: 50px;
        }

        .classes-container {
            background: var(--color-white);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .classes-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--gradient-primary);
        }

        .classes-title {
            font-size: 1.5rem;
            margin-bottom: 30px;
            color: var(--color-black);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 20px;
        }

        .class-item {
            background: var(--color-gray-light);
            padding: 25px 20px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .class-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            transition: left 0.4s ease;
            opacity: 0.1;
        }

        .class-item:hover::before {
            left: 0;
        }

        .class-item:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-md);
        }

        .class-kindergarten { border-left-color: #ec4899; }
        .class-lower-primary { border-left-color: #3b82f6; }
        .class-upper-primary { border-left-color: #f59e0b; }
        .class-junior-school { border-left-color: #dc2626; }

        .class-name {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--color-black);
            font-size: 1.1rem;
            position: relative;
            z-index: 2;
        }

        .class-age {
            font-size: 0.9rem;
            color: var(--color-gray);
            position: relative;
            z-index: 2;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            gap: 25px;
        }

        .feature-card {
            background: var(--color-white);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: flex-start;
            gap: 25px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            opacity: 0.05;
            transition: left 0.4s ease;
        }

        .feature-card:hover::before {
            left: 0;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
            transition: var(--transition);
            position: relative;
            z-index: 2;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .icon-kindergarten { background: var(--gradient-kindergarten); color: white; }
        .icon-lower-primary { background: var(--gradient-lower-primary); color: white; }
        .icon-upper-primary { background: var(--gradient-upper-primary); color: white; }
        .icon-junior-school { background: var(--gradient-junior-school); color: white; }

        .feature-content {
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .feature-content h4 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            color: var(--color-black);
            font-weight: 700;
        }

        .feature-content p {
            color: var(--color-gray);
            line-height: 1.7;
            font-size: 1rem;
        }

        /* Achievements Section */
        .achievements-section {
            background: var(--gradient-secondary);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .achievements-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,0 1000,100 0,100"/></svg>');
            background-size: cover;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 35px;
            margin-top: 60px;
            position: relative;
            z-index: 2;
        }

        .achievement-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .achievement-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .achievement-card:hover::before {
            left: 100%;
        }

        .achievement-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.2);
        }

        .achievement-icon {
            font-size: 3rem;
            margin-bottom: 25px;
            display: block;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .achievement-text {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .achievement-date {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 600;
        }

        /* CTA Section */
        .school-cta {
            background: var(--color-gray-dark);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
        }

        .school-cta::before {
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

        /* Section Titles */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
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

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .section-content {
                grid-template-columns: 1fr;
                gap: 60px;
            }

            .head-image-container {
                width: 280px;
                height: 280px;
            }

            .section-stats {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }

            .school-hero {
                padding: 100px 0 60px;
                min-height: 70vh;
            }

            .section-nav-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .section-nav-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .school-section {
                padding: 80px 0;
                min-height: auto;
            }

            .classes-grid {
                grid-template-columns: 1fr;
            }

            .section-stats {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .feature-card {
                flex-direction: column;
                text-align: center;
                padding: 25px;
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

            .head-message {
                padding: 30px 25px;
            }

            .classes-container {
                padding: 30px 25px;
            }
        }

        @media (max-width: 480px) {
            .school-hero h1 {
                font-size: 2.2rem;
            }

            .head-image-container {
                width: 240px;
                height: 240px;
            }

            .head-message {
                padding: 25px 20px;
            }

            .classes-container,
            .feature-card {
                padding: 25px 20px;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .achievement-card {
                padding: 30px 20px;
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
    </style>
</head>
<body>
    <!-- Scroll Progress Indicator -->
    <div class="scroll-progress"></div>

    <?php include '../includes/header.php'; ?>

    <!-- School Hero Section -->
    <section class="school-hero">
        <div class="container">
            <div class="school-hero-content">
                <h1 class="fade-in">Our Academic Journey</h1>
                <p class="fade-in">From early childhood exploration to junior high excellence, discover how each stage of our educational pathway nurtures young minds for lifelong success.</p>
                <div class="cta-buttons fade-in">
                    <a href="#kindergarten" class="btn btn-secondary">
                        <i class="fas fa-arrow-down"></i>
                        Explore Sections
                    </a>
                    <a href="apply.php" class="btn btn-primary">
                        <i class="fas fa-user-graduate"></i>
                        Begin Journey
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Navigation -->
    <nav class="section-nav">
        <div class="container">
            <div class="section-nav-container">
                <button class="section-nav-btn active" data-section="kindergarten">
                    <i class="fas fa-child"></i>
                    Kindergarten
                </button>
                <button class="section-nav-btn" data-section="lower-primary">
                    <i class="fas fa-pencil-alt"></i>
                    Lower Primary
                </button>
                <button class="section-nav-btn" data-section="upper-primary">
                    <i class="fas fa-book-reader"></i>
                    Upper Primary
                </button>
                <button class="section-nav-btn" data-section="junior-school">
                    <i class="fas fa-graduation-cap"></i>
                    Junior School
                </button>
            </div>
        </div>
    </nav>

    <!-- School Sections -->
    <main>
        <?php if (!empty($sections)): ?>
            <?php foreach ($sections as $index => $section): ?>
                <?php
                $section_stats_data = array_filter($section_stats, function($stat) use ($section) {
                    return $stat['section_id'] == $section['id'];
                });
                $current_stats = !empty($section_stats_data) ? array_values($section_stats_data)[0] : null;
                ?>

                <section id="<?php echo $section['section_slug']; ?>"
                         class="school-section section-<?php echo $section['section_slug']; ?>">
                    <div class="container">
                        <div class="section-header fade-in">
                            <span class="section-badge badge-<?php echo $section['section_slug']; ?> floating">
                                <?php echo $section['section_name']; ?>
                            </span>
                            <h2 class="section-title">Building Foundations for <?php echo $section['section_name']; ?></h2>
                            <p class="section-subtitle">Led by <?php echo $section['head_name']; ?> - <?php echo $section['head_title']; ?></p>
                        </div>

                        <div class="section-content">
                            <!-- Head Profile -->
                            <div class="head-profile slide-in-left">
                                <div class="head-image-container">
                                    <img src="<?php echo $section['head_image']; ?>"
                                         alt="<?php echo $section['head_name']; ?>"
                                         class="head-image"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDMwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjZDNkM2QzIi8+CjxjaXJjbGUgY3g9IjE1MCIgY3k9IjEyMCIgcj0iNDAiIGZpbGw9IiM2YjcyODAiLz4KPHBhdGggZD0iTTE1MCAyMDBDMTAwIDIwMCA4MCAyNTAgODAgMjUwSDIyMEMyMjAgMjUwIDIwMCAyMDAgMTUwIDIwMFoiIGZpbGw9IiM2YjcyODAiLz4KPC9zdmc+Cg=='">
                                </div>
                                <div class="head-info">
                                    <h3><?php echo $section['head_name']; ?></h3>
                                    <div class="head-title"><?php echo $section['head_title']; ?></div>

                                    <?php if ($current_stats): ?>
                                    <div class="section-stats">
                                        <div class="stat-item stat-<?php echo $section['section_slug']; ?>">
                                            <div class="stat-number"><?php echo $current_stats['students_count']; ?></div>
                                            <div class="stat-label">Students</div>
                                        </div>
                                        <div class="stat-item stat-<?php echo $section['section_slug']; ?>">
                                            <div class="stat-number"><?php echo $current_stats['teacher_count']; ?></div>
                                            <div class="stat-label">Teachers</div>
                                        </div>
                                        <div class="stat-item stat-<?php echo $section['section_slug']; ?>">
                                            <div class="stat-number"><?php echo $current_stats['avg_class_size']; ?></div>
                                            <div class="stat-label">Class Size</div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="head-message">
                                        <p><?php echo $section['head_message']; ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Details -->
                            <div class="section-details slide-in-right">
                                <!-- Classes -->
                                <div class="classes-container">
                                    <h3 class="classes-title">
                                        <i class="fas fa-door-open"></i>
                                        Classes in This Section
                                    </h3>
                                    <div class="classes-grid">
                                        <?php foreach ($section['class_list'] as $class): ?>
                                            <div class="class-item class-<?php echo $section['section_slug']; ?>">
                                                <div class="class-name"><?php echo trim($class); ?></div>
                                                <div class="class-age">CBE Curriculum</div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Features -->
                                <div class="features-container">
                                    <h3 class="classes-title">
                                        <i class="fas fa-star"></i>
                                        Key Features & Programs
                                    </h3>
                                    <div class="features-grid">
                                        <?php foreach ($section['features'] as $feature): ?>
                                            <div class="feature-card">
                                                <div class="feature-icon icon-<?php echo $section['section_slug']; ?>">
                                                    <i class="<?php echo $feature['icon']; ?>"></i>
                                                </div>
                                                <div class="feature-content">
                                                    <h4><?php echo $feature['title']; ?></h4>
                                                    <p><?php echo $feature['description']; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback content if no sections are found -->
            <section class="school-section" style="padding: 100px 0; text-align: center;">
                <div class="container">
                    <h2>Academic Sections Coming Soon</h2>
                    <p>We're currently updating our academic section information. Please check back later.</p>
                </div>
            </section>
        <?php endif; ?>

        <!-- Achievements Section -->
        <section class="achievements-section">
            <div class="container">
                <div class="section-title section-title-dark">
                    <h2>Celebrating Excellence</h2>
                    <p>Recent achievements and milestones across all our academic sections</p>
                </div>

                <div class="achievements-grid">
                    <div class="achievement-card fade-in">
                        <i class="fas fa-trophy achievement-icon"></i>
                        <p class="achievement-text">Kindergarten students won 1st place in the Inter-School Art Competition with their collaborative mural project</p>
                        <div class="achievement-date">March 2024</div>
                    </div>

                    <div class="achievement-card fade-in">
                        <i class="fas fa-medal achievement-icon"></i>
                        <p class="achievement-text">Lower Primary mathletes ranked top 3 in National Mathematics Olympiad, showcasing exceptional problem-solving skills</p>
                        <div class="achievement-date">February 2024</div>
                    </div>

                    <div class="achievement-card fade-in">
                        <i class="fas fa-award achievement-icon"></i>
                        <p class="achievement-text">Upper Primary science project on renewable energy selected for State Level Innovation Exhibition</p>
                        <div class="achievement-date">January 2024</div>
                    </div>

                    <div class="achievement-card fade-in">
                        <i class="fas fa-star achievement-icon"></i>
                        <p class="achievement-text">Junior School debate team champions in Regional Youth Parliament, demonstrating outstanding critical thinking</p>
                        <div class="achievement-date">December 2023</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="school-cta">
            <div class="container">
                <div class="section-title section-title-dark">
                    <h2>Begin Your Educational Journey</h2>
                    <p>Join our community of learners and discover the difference that sets St. Philip Neri apart</p>
                </div>
                <div class="cta-buttons">
                    <a href="apply.php?type=enquiry" class="btn btn-secondary">
                        <i class="fas fa-info-circle"></i>
                        Request Information
                    </a>
                    <a href="tour.php" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i>
                        Schedule a Tour
                    </a>
                    <a href="apply.php" class="btn btn-secondary">
                        <i class="fas fa-edit"></i>
                        Start Application
                    </a>
                </div>
            </div>
        </section>
    </main>

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

            // Section Navigation
            const navButtons = document.querySelectorAll('.section-nav-btn');
            const sections = document.querySelectorAll('.school-section');

            navButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetSection = button.getAttribute('data-section');

                    // Update active button
                    navButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Scroll to section
                    document.getElementById(targetSection).scrollIntoView({
                        behavior: 'smooth'
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

            const slideLeftObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    slideLeftObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            const slideRightObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        slideRightObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            // Observe elements
            document.querySelectorAll('.fade-in').forEach(el => fadeObserver.observe(el));
            document.querySelectorAll('.slide-in-left').forEach(el => slideLeftObserver.observe(el));
            document.querySelectorAll('.slide-in-right').forEach(el => slideRightObserver.observe(el));

            // Update active nav button based on scroll position
            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const sectionId = entry.target.id;
                        navButtons.forEach(btn => {
                            btn.classList.remove('active');
                            if (btn.getAttribute('data-section') === sectionId) {
                                btn.classList.add('active');
                            }
                        });
                    }
                });
            }, {
                threshold: 0.5
            });

            sections.forEach(section => {
                sectionObserver.observe(section);
            });

            // Enhanced hover effects
            const classItems = document.querySelectorAll('.class-item');
            classItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.05)';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Parallax effect for hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const hero = document.querySelector('.school-hero');
                if (hero) {
                    hero.style.transform = `translateY(${scrolled * 0.5}px)`;
                }
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
        });

        // Add some interactive elements on load
        window.addEventListener('load', function() {
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
