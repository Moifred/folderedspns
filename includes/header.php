<?php
// Check if current page is homepage for carousel size
$is_homepage = basename($_SERVER['PHP_SELF']) == 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>St. Philip Neri School</title>
    <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : 'St. Philip Neri School - Together we Achieve the extraordinary'; ?>">
    <meta name="keywords" content="<?php echo isset($meta_keywords) ? $meta_keywords : 'school, education, learning, St. Philip Neri School, St. Philip Neri School Joska, St. Philip Neri School, St. Philip Neri Joska, Philip Neri School, SPN Joska, Private schools in Joska, Schools in Joska Kenya, Best schools Kenya, Best schools Joska, Schools off Kangundo Road, Day and boarding school Joska, Schools near Joska, Education in Joska, CBC school Kenya, Competency Based Curriculum school, CBE curriculum Kenya, CBC hub, Junior Secondary School (JSS) Joska, Senior School, 21st century learning, Future-ready curriculum, Kenyan curriculum school, Modern private school Kenya, State of the art facilities school, Modern learning tools, Technology in classroom Kenya, STEM school Kenya, STEM facilities Kenya, ICT integration school, Digital learning Kenya, Smart classrooms Kenya, Modern labs school, Holistic education Kenya, Child-centered learning, Best primary school Joska, Best private primary school, Best day school Joska, Best boarding school Joska, School fees structure Joska, Admissions Joska, School with good facilities, Safe school environment, School for all-round development, Christian values school, Catholic private school Kenya, Talent nurturing school Kenya, Co-curricular activities school, Music school Kenya, Football academy Joska, Sports academy Kenya, Netball team Kenya, Performing arts school, Kenya Music Festivals champions, Drama and festivals school, Art and talent development'; ?>">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* ============================================
           RESET AND BASE STYLES
        ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-red: <?php echo COLOR_RED; ?>;
            --color-green: <?php echo COLOR_GREEN; ?>;
            --color-black: <?php echo COLOR_BLACK; ?>;
            --color-white: #ffffff;
            --color-gray-light: #f3f4f6;
            --color-gray: #6b7280;
            --color-gray-dark: #374151;
            --social-facebook: #1877F2;
            --social-instagram: #E4405F;
            --social-twitter: #1DA1F2;
            --social-tiktok: #000000;
            --social-youtube: #FF0000;
            --social-linkedin: #0A66C2;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow: 0 4px 20px rgba(0,0,0,0.1);
            --radius: 12px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--color-gray-dark);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ============================================
           DESKTOP HEADER STYLES (Default)
        ============================================ */
        .header {
            background: var(--color-white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }

        .header.scrolled {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        /* Desktop Header Top */
        .header-top {
            background: linear-gradient(135deg, var(--color-black) 0%, #2a2a2a 100%);
            color: var(--color-white);
            padding: 12px 0;
            position: relative;
            overflow: hidden;
        }

        .header-top::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--color-red), var(--color-green));
            z-index: 1;
        }

        .header-top .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        /* Logo Section */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 0 1 auto;
            min-width: 200px;
        }

        .logo {
            height: 60px;
            width: auto;
            transition: var(--transition);
            border-radius: 50%;
            padding: 4px;
            background: var(--color-white);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .logo:hover {
            transform: scale(1.05) rotate(5deg);
        }

        .school-name {
            flex: 1;
        }

        .school-name h1 {
            color: var(--color-red);
            font-size: 1.5rem;
            margin-bottom: 3px;
            transition: var(--transition);
            font-weight: 700;
            letter-spacing: -0.3px;
            line-height: 1.2;
            white-space: nowrap;
        }

        .logo-section:hover .school-name h1 {
            color: var(--color-green);
        }

        .school-name .motto {
            color: var(--color-green);
            font-size: 0.8rem;
            font-style: italic;
            transition: var(--transition);
            font-weight: 500;
            line-height: 1.2;
            white-space: nowrap;
        }

        .logo-section:hover .school-name .motto {
            color: var(--color-red);
        }

        /* Social Media Icons - Desktop */
        .social-media {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: nowrap;
            flex-shrink: 0;
        }

        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            color: var(--color-white);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            font-size: 0.9rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            flex-shrink: 0;
        }

        .social-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .social-icon:hover::before {
            left: 100%;
        }

        .social-icon:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .social-icon.facebook {
            background-color: var(--social-facebook);
        }

        .social-icon.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        }

        .social-icon.twitter {
            background-color: var(--social-twitter);
        }

        .social-icon.tiktok {
            background-color: var(--social-tiktok);
        }

        .social-icon.youtube {
            background-color: var(--social-youtube);
        }

        .social-icon.linkedin {
            background-color: var(--social-linkedin);
        }

        /* Desktop Navigation */
        .navbar {
            background: var(--color-white);
            border-top: 2px solid var(--color-green);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 16px 18px;
            color: var(--color-black);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            position: relative;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--color-red);
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .nav-link:hover {
            color: var(--color-red);
            background-color: rgba(0,0,0,0.02);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--color-white);
            min-width: 240px;
            box-shadow: var(--shadow);
            opacity: 0;
            visibility: hidden;
            transform: translateY(15px);
            transition: var(--transition);
            z-index: 1000;
            border-radius: 0 0 var(--radius) var(--radius);
            overflow: hidden;
            border-top: 3px solid var(--color-green);
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu .dropdown-menu {
            top: 0;
            left: 100%;
            border-radius: var(--radius);
            transform: translateX(15px);
        }

        .dropdown:hover .dropdown-menu .dropdown-menu {
            transform: translateX(0);
        }

        .dropdown-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px;
            border-bottom: 1px solid var(--color-gray-light);
            color: var(--color-gray-dark);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dropdown-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: var(--color-green);
            transition: width 0.3s ease;
            z-index: -1;
        }

        .dropdown-link:hover::before {
            width: 4px;
        }

        .dropdown-link:hover {
            background: rgba(0,0,0,0.03);
            color: var(--color-green);
            padding-left: 25px;
        }

        /* ============================================
           MOBILE ELEMENTS (Hidden by Default)
        ============================================ */
        .mobile-header-container,
        .mobile-menu-overlay,
        .mobile-nav-menu,
        .menu-toggle,
        .mobile-only {
            display: none;
        }

        /* ============================================
           TABLET ADJUSTMENTS (769px - 1024px)
        ============================================ */
        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                padding: 0 15px;
            }

            .nav-link {
                padding: 14px 12px;
                font-size: 0.9rem;
            }

            .school-name h1 {
                font-size: 1.3rem;
            }

            .social-icon {
                width: 32px;
                height: 32px;
                font-size: 0.85rem;
            }

            .social-media {
                gap: 6px;
            }
        }

        /* ============================================
           MOBILE STYLES (≤768px)
        ============================================ */
        @media (max-width: 768px) {
            /* Hide desktop elements on mobile */
            .desktop-only {
                display: none !important;
            }

            /* Show mobile elements */
            .mobile-only {
                display: block !important;
            }

            .mobile-header-container {
                display: block;
                background: linear-gradient(135deg, var(--color-black) 0%, #2a2a2a 100%);
                padding: 8px 0;
            }

            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }

            .mobile-logo-section {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .mobile-logo {
                height: 45px;
                width: auto;
                border-radius: 50%;
                padding: 3px;
                background: var(--color-white);
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            /* Mobile Menu Toggle */
            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--color-red), #c82333);
                border: none;
                font-size: 1.3rem;
                cursor: pointer;
                color: var(--color-white);
                transition: var(--transition);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                position: relative;
                z-index: 1001;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }

            .menu-toggle:hover {
                background: linear-gradient(135deg, var(--color-green), #1e7e34);
                transform: scale(1.05);
            }

            /* Mobile Menu Overlay */
            .mobile-menu-overlay {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }

            .mobile-menu-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            /* Mobile Navigation Menu */
            .mobile-nav-menu {
                position: fixed;
                top: 0;
                left: -100%;
                width: 85%;
                max-width: 320px;
                height: 100%;
                background: linear-gradient(135deg, var(--color-white) 0%, var(--color-gray-light) 100%);
                box-shadow: 5px 0 30px rgba(0,0,0,0.2);
                z-index: 1000;
                overflow-y: auto;
                transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
            }

            .mobile-nav-menu.active {
                left: 0;
            }

            .mobile-menu-header {
                background: linear-gradient(135deg, var(--color-black) 0%, var(--color-gray-dark) 100%);
                padding: 15px;
                display: flex;
                align-items: center;
                gap: 12px;
                border-bottom: 3px solid var(--color-green);
            }

            .mobile-menu-logo {
                height: 45px;
                width: auto;
                border-radius: 50%;
                padding: 3px;
                background: var(--color-white);
            }

            .mobile-school-info h3 {
                color: var(--color-red);
                font-size: 1.1rem;
                margin-bottom: 2px;
            }

            .mobile-school-info .motto {
                color: var(--color-green);
                font-size: 0.75rem;
                font-style: italic;
            }

            .mobile-menu-close {
                position: absolute;
                top: 15px;
                right: 15px;
                background: var(--color-red);
                color: white;
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                font-size: 1rem;
                cursor: pointer;
                transition: var(--transition);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mobile-menu-close:hover {
                background: var(--color-green);
                transform: rotate(90deg);
            }

            .mobile-nav-items {
                list-style: none;
                padding: 0;
                margin: 0;
                flex: 1;
                overflow-y: auto;
            }

            .mobile-nav-item {
                border-bottom: 1px solid rgba(0,0,0,0.08);
            }

            .mobile-nav-link {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                color: var(--color-gray-dark);
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                transition: var(--transition);
            }

            .mobile-nav-link:hover {
                background: var(--color-red);
                color: var(--color-white);
            }

            .mobile-dropdown-arrow {
                transition: transform 0.3s ease;
                font-size: 0.8rem;
            }

            .mobile-nav-item.active > .mobile-nav-link .mobile-dropdown-arrow {
                transform: rotate(180deg);
            }

            /* Mobile dropdown menus */
            .mobile-dropdown-menu {
                display: none;
                background: rgba(255,255,255,0.95);
                padding: 0;
                margin: 0;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease;
            }

            .mobile-nav-item.active > .mobile-dropdown-menu {
                display: block;
                max-height: 1000px;
            }

            .mobile-dropdown-item {
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }

            .mobile-dropdown-link {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 18px 14px 32px;
                color: var(--color-gray-dark);
                text-decoration: none;
                font-weight: 500;
                font-size: 0.95rem;
                transition: var(--transition);
                border-left: 3px solid transparent;
            }

            .mobile-dropdown-link:hover {
                background: var(--color-green);
                color: var(--color-white);
                border-left-color: var(--color-red);
            }

            /* Nested dropdowns */
            .mobile-nested-dropdown-menu {
                display: none;
                background: rgba(255,255,255,0.9);
            }

            .mobile-dropdown-item.active > .mobile-nested-dropdown-menu {
                display: block;
            }

            .mobile-nested-dropdown-link {
                padding: 12px 18px 12px 48px;
                font-size: 0.9rem;
            }

            /* Social media in mobile menu */
            .mobile-social-media {
                padding: 15px;
                background: linear-gradient(135deg, var(--color-black) 0%, #2a2a2a 100%);
                display: flex;
                justify-content: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .mobile-social-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                color: var(--color-white);
                text-decoration: none;
                transition: var(--transition);
                font-size: 0.9rem;
            }

            .mobile-social-icon:hover {
                transform: translateY(-3px);
            }

            .mobile-social-icon.facebook {
                background-color: var(--social-facebook);
            }

            .mobile-social-icon.instagram {
                background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            }

            .mobile-social-icon.twitter {
                background-color: var(--social-twitter);
            }

            .mobile-social-icon.tiktok {
                background-color: var(--social-tiktok);
            }

            .mobile-social-icon.youtube {
                background-color: var(--social-youtube);
            }
        }

        /* ============================================
           SMALL MOBILE ADJUSTMENTS (≤480px)
        ============================================ */
        @media (max-width: 480px) {
            .container {
                padding: 0 12px;
            }

            .mobile-logo {
                height: 40px;
            }

            .mobile-nav-link {
                padding: 14px 15px;
                font-size: 0.95rem;
            }

            .mobile-dropdown-link {
                padding: 12px 15px 12px 28px;
                font-size: 0.9rem;
            }

            .mobile-nested-dropdown-link {
                padding: 10px 15px 10px 40px;
                font-size: 0.85rem;
            }

            .menu-toggle {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
            }
        }

        /* ============================================
           DESKTOP SCROLL EFFECTS
        ============================================ */
        @media (min-width: 769px) {
            .header.scrolled .header-top {
                padding: 8px 0;
            }

            .header.scrolled .logo {
                height: 50px;
            }

            .header.scrolled .school-name h1 {
                font-size: 1.3rem;
            }

            .header.scrolled .nav-link {
                padding: 12px 16px;
            }
        }

        /* ============================================
           WHATSAPP FLOAT BUTTON
        ============================================ */
        .whatsapp-float {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #25D366;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            z-index: 1000;
            transition: var(--transition);
            overflow: hidden;
        }

        .whatsapp-float::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transform: scale(0);
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .whatsapp-float:hover::before {
            transform: scale(1);
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.35);
        }

        .whatsapp-icon {
            font-size: 2rem;
            position: relative;
            z-index: 1;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        .whatsapp-float {
            animation: pulse 2s infinite;
        }

        @media (max-width: 768px) {
            .whatsapp-float {
                width: 55px;
                height: 55px;
                bottom: 20px;
                right: 20px;
            }

            .whatsapp-icon {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/+254719221401?text=Hi,%20I'm%20interested%20in%20St.%20Philip%20Neri%20School" class="whatsapp-float" target="_blank" aria-label="Contact us on WhatsApp">
        <i class="fab fa-whatsapp whatsapp-icon"></i>
    </a>

    <!-- Header Section -->
    <header class="header" id="mainHeader">
        <!-- Desktop Header -->
        <div class="header-top desktop-only">
            <div class="container">
                <div class="logo-section">
                    <img src="images/logo.jpg" alt="St. Philip Neri School Logo" class="logo" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjMTZhMzRhIi8+Cjx0ZXh0IHg9IjMwIiB5PSIzNSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+U1BOUzwvdGV4dD4KPC9zdmc+'">
                    <div class="school-name">
                        <h1>St. Philip Neri School</h1>
                        <div class="motto">Together we Achieve the extraordinary</div>
                    </div>
                </div>
                <div class="social-media desktop-only">
                    <a href="https://www.facebook.com/profile.php?id=100057603453033" class="social-icon facebook" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/philip.neri.school/" class="social-icon instagram" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://x.com/PhilipNeriSch" class="social-icon twitter" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.tiktok.com/@st.philip.neri.sc" class="social-icon tiktok" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UC4suquLZlGd0DZaLMsgUo9w/" class="social-icon youtube" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="mobile-header-container mobile-only">
            <div class="container">
                <div class="mobile-header">
                    <div class="mobile-logo-section">
                        <img src="images/logo.jpg" alt="St. Philip Neri School Logo" class="mobile-logo" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjMTZhMzRhIi8+Cjx0ZXh0IHg9IjMwIiB5PSIzNSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+U1BOUzwvdGV4dD4KPC9zdmc+'">
                    </div>
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Desktop Navigation -->
        <nav class="navbar desktop-only">
            <div class="container">
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item"><a href="/index.php" class="nav-link">Home</a></li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            About Us
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown">
                                <a href="../about-us" class="dropdown-link">
                                    Our Story
                                    <i class="fas fa-chevron-right dropdown-arrow"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="../welcome" class="dropdown-link">Welcome Message</a></li>
                                    <li><a href="../mission-vision" class="dropdown-link">Mission & Vision</a></li>
                                    <li><a href="../our-aim" class="dropdown-link">Our Aim</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-link">
                                    Leadership
                                    <i class="fas fa-chevron-right dropdown-arrow"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="../board" class="dropdown-link">Board of Directors</a></li>
                                    <li><a href="../management" class="dropdown-link">Management</a></li>
                                    <li><a href="../leaders" class="dropdown-link">Leaders</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            Our School
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="../sections" class="dropdown-link">Classes/Sections</a></li>
                            <li><a href="../co-curricular" class="dropdown-link">Co - Curricular</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            School Life
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="../gallery" class="dropdown-link">Gallery</a></li>
                            <li><a href="../boarding" class="dropdown-link">Boarding</a></li>
                            <li><a href="../transport" class="dropdown-link">Transport</a></li>
                            <li><a href="../sports-complex" class="dropdown-link">Sports Complex</a></li>
                            <li><a href="../science-complex" class="dropdown-link">Science Complex</a></li>
                            <li><a href="../arts-complex" class="dropdown-link">Arts Complex</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            Apply Online
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="../enquiry" class="dropdown-link">Make Enquiry</a></li>
                            <li><a href="#" class="dropdown-link">Make Application</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="../resources" class="nav-link">
                            Resources
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="../resources" class="dropdown-link">Fee Structure</a></li>
                            <li><a href="../resources" class="dropdown-link">Newsletter</a></li>
                            <li><a href="../resources" class="dropdown-link">Magazine</a></li>
                            <li><a href="../faqs" class="dropdown-link">FAQs</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link">
                            News
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="../blogs" class="dropdown-link">Blogs</a></li>
                            <li><a href="../events" class="dropdown-link">Events</a></li>
                        </ul>
                    </li>

                    <li class="nav-item"><a href="../contact" class="nav-link">Contact Us</a></li>
                    <li class="nav-item"><a href="../admin" class="nav-link">Login</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-nav-menu" id="mobileNavMenu">
        <div class="mobile-menu-header">
            <img src="../images/logo.jpg" alt="St. Philip Neri School Logo" class="mobile-menu-logo" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjMTZhMzRhIi8+Cjx0ZXh0IHg9IjMwIiB5PSIzNSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+U1BOUzwvdGV4dD4KPC9zdmc+'">
            <div class="mobile-school-info">
                <h3>St. Philip Neri School</h3>
                <div class="motto">Together we Achieve the extraordinary</div>
            </div>
        </div>

        <button class="mobile-menu-close" id="mobileMenuClose">
            <i class="fas fa-times"></i>
        </button>

        <ul class="mobile-nav-items">
            <li class="mobile-nav-item">
                <a href="/index.php" class="mobile-nav-link">Home</a>
            </li>

            <li class="mobile-nav-item">
                <a href="#" class="mobile-nav-link">
                    About Us
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li class="mobile-dropdown-item">
                        <a href="../about-us" class="mobile-dropdown-link">
                            Our Story
                            <i class="fas fa-chevron-right mobile-dropdown-arrow"></i>
                        </a>
                        <ul class="mobile-nested-dropdown-menu">
                            <li><a href="../welcome" class="mobile-nested-dropdown-link">Welcome Message</a></li>
                            <li><a href="../mission-vision" class="mobile-nested-dropdown-link">Mission & Vision</a></li>
                            <li><a href="../our-aim" class="mobile-nested-dropdown-link">Our Aim</a></li>
                        </ul>
                    </li>
                    <li class="mobile-dropdown-item">
                        <a href="#" class="mobile-dropdown-link">
                            Leadership
                            <i class="fas fa-chevron-right mobile-dropdown-arrow"></i>
                        </a>
                        <ul class="mobile-nested-dropdown-menu">
                            <li><a href="../board" class="mobile-nested-dropdown-link">Board of Directors</a></li>
                            <li><a href="../management" class="mobile-nested-dropdown-link">Management</a></li>
                            <li><a href="../leaders" class="mobile-nested-dropdown-link">Leaders</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="mobile-nav-item">
                <a href="#" class="mobile-nav-link">
                    Our School
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li><a href="../sections" class="mobile-dropdown-link">Classes/Sections</a></li>
                    <li><a href="../co-curricular" class="mobile-dropdown-link">Co - Curricular</a></li>
                </ul>
            </li>

            <li class="mobile-nav-item">
                <a href="#" class="mobile-nav-link">
                    School Life
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li><a href="../gallery" class="mobile-dropdown-link">Gallery</a></li>
                    <li><a href="../boarding" class="mobile-dropdown-link">Boarding</a></li>
                    <li><a href="../transport" class="mobile-dropdown-link">Transport</a></li>
                    <li><a href="../sports-complex" class="mobile-dropdown-link">Sports Complex</a></li>
                    <li><a href="../science-complex" class="mobile-dropdown-link">Science Complex</a></li>
                    <li><a href="../arts-complex" class="mobile-dropdown-link">Arts Complex</a></li>
                </ul>
            </li>

            <li class="mobile-nav-item">
                <a href="#" class="mobile-nav-link">
                    Apply Online
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li><a href="../enquiry" class="mobile-dropdown-link">Make Enquiry</a></li>
                    <li><a href="#" class="mobile-dropdown-link">Make Application</a></li>
                </ul>
            </li>

            <li class="mobile-nav-item">
                <a href="#" class="mobile-nav-link">
                    Resources
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li><a href="../resources" class="mobile-dropdown-link">Fee Structure</a></li>
                    <li><a href="../resources" class="mobile-dropdown-link">Newsletter</a></li>
                    <li><a href="../resources" class="mobile-dropdown-link">Magazine</a></li>
                    <li><a href="../faqs" class="mobile-dropdown-link">FAQs</a></li>
                </ul>
            </li>

            <li class="mobile-nav-item">
                <a href="#" class="mobile-nav-link">
                    News
                    <i class="fas fa-chevron-down mobile-dropdown-arrow"></i>
                </a>
                <ul class="mobile-dropdown-menu">
                    <li><a href="../blogs" class="mobile-dropdown-link">Blogs</a></li>
                    <li><a href="../events" class="mobile-dropdown-link">Events</a></li>
                </ul>
            </li>

            <li class="mobile-nav-item">
                <a href="../contact" class="mobile-nav-link">Contact Us</a>
            </li>

            <li class="mobile-nav-item">
                <a href="../admin" class="mobile-nav-link">Login</a>
            </li>
        </ul>

        <div class="mobile-social-media">
            <a href="https://www.facebook.com/profile.php?id=100057603453033" class="mobile-social-icon facebook" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://www.instagram.com/philip.neri.school/" class="mobile-social-icon instagram" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://x.com/PhilipNeriSch" class="mobile-social-icon twitter" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.tiktok.com/@st.philip.neri.sc" class="mobile-social-icon tiktok" aria-label="TikTok">
                <i class="fab fa-tiktok"></i>
            </a>
            <a href="https://www.youtube.com/channel/UC4suquLZlGd0DZaLMsgUo9w/" class="mobile-social-icon youtube" aria-label="YouTube">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu elements
            const menuToggle = document.getElementById('menuToggle');
            const mobileMenuClose = document.getElementById('mobileMenuClose');
            const mobileNavMenu = document.getElementById('mobileNavMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

            // Desktop header scroll effect
            const header = document.getElementById('mainHeader');

            // Check if we're on mobile
            function isMobile() {
                return window.innerWidth <= 768;
            }

            // Toggle mobile menu
            function toggleMobileMenu() {
                if (!isMobile()) return;

                mobileNavMenu.classList.toggle('active');
                mobileMenuOverlay.classList.toggle('active');

                // Toggle hamburger/close icon
                const icon = menuToggle.querySelector('i');
                if (mobileNavMenu.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                    document.body.style.overflow = 'hidden';
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                    document.body.style.overflow = '';
                    // Close all dropdowns
                    mobileNavItems.forEach(item => {
                        item.classList.remove('active');
                    });
                }
            }

            // Toggle mobile dropdowns
            function toggleMobileDropdown(event) {
                if (!isMobile()) return;

                const link = event.currentTarget;
                const navItem = link.parentElement;

                // Only toggle if it's a dropdown link
                if (link.querySelector('.mobile-dropdown-arrow')) {
                    event.preventDefault();
                    event.stopPropagation();

                    // Toggle active class
                    navItem.classList.toggle('active');

                    // Close other dropdowns
                    mobileNavItems.forEach(otherItem => {
                        if (otherItem !== navItem && !navItem.contains(otherItem) && !otherItem.contains(navItem)) {
                            otherItem.classList.remove('active');
                        }
                    });
                }
            }

            // Handle mobile link clicks
            function handleMobileLinkClick(event) {
                if (!isMobile()) return;

                const link = event.currentTarget;

                // Close menu if it's a direct link (not a dropdown toggle)
                if (!link.querySelector('.mobile-dropdown-arrow') && link.getAttribute('href') !== '#') {
                    toggleMobileMenu();
                }
            }

            // Event listeners
            mobileMenuOverlay.addEventListener('click', toggleMobileMenu);
            menuToggle.addEventListener('click', toggleMobileMenu);
            mobileMenuClose.addEventListener('click', toggleMobileMenu);

            // Add click listeners to mobile nav links
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link, .mobile-dropdown-link');
            mobileNavLinks.forEach(link => {
                link.addEventListener('click', toggleMobileDropdown);
                link.addEventListener('click', handleMobileLinkClick);
            });

            // Header scroll effect for desktop
            function handleScroll() {
                if (window.innerWidth > 768) {
                    if (window.scrollY > 100) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                }
            }

            window.addEventListener('scroll', handleScroll);

            // Handle window resize
            function handleResize() {
                if (window.innerWidth > 768) {
                    // Close mobile menu if open
                    if (mobileNavMenu.classList.contains('active')) {
                        toggleMobileMenu();
                    }
                }
                // Re-check scroll state
                handleScroll();
            }

            window.addEventListener('resize', handleResize);

            // Keyboard navigation support
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && mobileNavMenu.classList.contains('active')) {
                    toggleMobileMenu();
                }
            });

            // Initialize
            handleScroll();
        });
    </script>
</body>
</html>
