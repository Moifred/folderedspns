<?php
session_start();
require_once 'config.php';

// Fetch science facilities
try {
    $facilities_sql = "SELECT * FROM science_facilities WHERE is_active = 1 ORDER BY display_order ASC";
    $facilities_stmt = $pdo->query($facilities_sql);
    $facilities = $facilities_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $facilities = [];
}

// Fetch science departments
try {
    $departments_sql = "SELECT * FROM science_departments WHERE is_active = 1 ORDER BY department_name ASC";
    $departments_stmt = $pdo->query($departments_sql);
    $departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $departments = [];
}

// Fetch science events (upcoming events only)
try {
    $events_sql = "SELECT * FROM science_events WHERE is_active = 1 AND event_date >= CURDATE() ORDER BY event_date ASC, event_time ASC";
    $events_stmt = $pdo->query($events_sql);
    $events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $events = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Science Complex - St. Philip Neri School</title>
    <meta name="description" content="Explore our state-of-the-art science facilities and innovative academic programs at St. Philip Neri School.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #2d5a27; /* Dark Green */
            --color-secondary: #000000; /* Black */
            --color-accent: #e53e3e; /* Red */
            --color-accent-light: #fed7d7;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;

            --gradient-hero: linear-gradient(135deg, #2d5a27 0%, #1a365d 100%);
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
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-hero);
            color: white;
            padding: 120px 2rem 80px;
            text-align: center;
            margin-top: 80px;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--color-accent);
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--color-text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Facilities Grid */
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .facility-card {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .facility-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .facility-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .facility-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .facility-description {
            color: var(--color-text-light);
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .facility-details {
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .detail-label {
            font-weight: 600;
            min-width: 120px;
            color: var(--color-secondary);
        }

        .detail-value {
            color: var(--color-text);
        }

        .contact-info {
            background: var(--color-surface);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: auto;
        }

        .contact-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-secondary);
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        /* Departments Section */
        .departments-section {
            background: var(--color-surface);
            padding: 3rem 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 4rem;
        }

        .departments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .department-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .department-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .department-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .department-icon {
            width: 60px;
            height: 60px;
            background: var(--color-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .department-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .department-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--color-primary);
        }

        .department-head {
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        .department-details {
            margin-bottom: 1rem;
        }

        .department-detail {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }

        .department-detail i {
            color: var(--color-accent);
            margin-right: 0.5rem;
            margin-top: 0.2rem;
        }

        .achievements {
            background: var(--color-surface);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }

        .achievements-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-secondary);
        }

        /* Events Section */
        .events-section {
            margin-bottom: 4rem;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .event-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .event-date {
            background: var(--color-accent);
            color: white;
            padding: 1rem;
            text-align: center;
        }

        .event-month {
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .event-day {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--color-primary);
        }

        .event-time {
            color: var(--color-accent);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .event-venue {
            color: var(--color-text-light);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .event-description {
            color: var(--color-text);
            font-size: 0.9rem;
        }

        /* Stats Section */
        .stats-section {
            background: var(--gradient-hero);
            color: white;
            padding: 4rem 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 4rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item {
            padding: 1.5rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--color-accent-light);
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Research Highlights */
        .research-section {
            margin-bottom: 4rem;
        }

        .research-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .research-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--color-accent);
            transition: var(--transition);
        }

        .research-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .research-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .research-description {
            color: var(--color-text);
            margin-bottom: 1rem;
        }

        .research-meta {
            display: flex;
            justify-content: space-between;
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-hero);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: var(--border-radius-lg);
            margin-bottom: 4rem;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            background: var(--color-accent);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            margin: 0 0.5rem;
        }

        .cta-button:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        .cta-button.outline {
            background: transparent;
            border: 2px solid white;
        }

        .cta-button.outline:hover {
            background: white;
            color: var(--color-primary);
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .facilities-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .departments-grid,
            .events-grid,
            .research-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .hero-section {
                padding: 100px 1rem 60px;
            }

            .facility-card {
                margin-bottom: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .cta-button {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Science Complex</h1>
            <p class="hero-subtitle">Innovative laboratories and research facilities fostering scientific curiosity and discovery</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Science Facilities -->
        <section class="facilities-section">
            <div class="section-header">
                <h2 class="section-title">Our Science Facilities</h2>
                <p class="section-subtitle">State-of-the-art laboratories and equipment designed to support cutting-edge scientific education and research</p>
            </div>

            <?php if (!empty($facilities)): ?>
                <div class="facilities-grid">
                    <?php foreach ($facilities as $facility): ?>
                        <div class="facility-card">
                            <?php if (!empty($facility['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($facility['image_url']); ?>"
                                     alt="<?php echo htmlspecialchars($facility['title']); ?>"
                                     class="facility-image">
                            <?php else: ?>
                                <div style="height: 200px; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-flask" style="font-size: 3rem; color: var(--color-text-light);"></i>
                                </div>
                            <?php endif; ?>

                            <div class="facility-content">
                                <h3 class="facility-title"><?php echo htmlspecialchars($facility['title']); ?></h3>
                                <p class="facility-description"><?php echo htmlspecialchars($facility['description']); ?></p>

                                <div class="facility-details">
                                    <?php if (!empty($facility['facility_type'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Type:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['facility_type']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($facility['equipment'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Equipment:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['equipment']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($facility['capacity'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Capacity:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['capacity']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($facility['timing'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Hours:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['timing']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="contact-info">
                                    <div class="contact-title">Contact Information</div>
                                    <div class="contact-details">
                                        <?php if (!empty($facility['contact_person'])): ?>
                                            <span><strong>Person:</strong> <?php echo htmlspecialchars($facility['contact_person']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($facility['contact_email'])): ?>
                                            <span><strong>Email:</strong> <?php echo htmlspecialchars($facility['contact_email']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($facility['contact_phone'])): ?>
                                            <span><strong>Phone:</strong> <?php echo htmlspecialchars($facility['contact_phone']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; background: var(--color-surface); border-radius: var(--border-radius);">
                    <i class="fas fa-flask" style="font-size: 4rem; color: var(--color-text-light); margin-bottom: 1rem;"></i>
                    <h3>Science Facilities Information Coming Soon</h3>
                    <p>We're currently updating our science facilities information. Please check back later.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="section-header" style="color: white;">
                <h2 class="section-title" style="color: white;">Scientific Excellence</h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.9);">Our commitment to advancing scientific knowledge and innovation</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Research Labs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">40+</div>
                    <div class="stat-label">Faculty Members</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Annual Publications</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Research Projects</div>
                </div>
            </div>
        </section>

        <!-- Science Departments -->
        <section class="departments-section">
            <div class="section-header">
                <h2 class="section-title">Science Departments</h2>
                <p class="section-subtitle">Specialized academic departments offering comprehensive science education and research opportunities</p>
            </div>

            <?php if (!empty($departments)): ?>
                <div class="departments-grid">
                    <?php foreach ($departments as $department): ?>
                        <div class="department-card">
                            <div class="department-header">
                                <div class="department-icon">
                                    <?php
                                    // Map department names to appropriate icons
                                    $department_icons = [
                                        'Physics' => 'fas fa-atom',
                                        'Chemistry' => 'fas fa-flask',
                                        'Biology' => 'fas fa-dna',
                                        'Mathematics' => 'fas fa-square-root-alt',
                                        'Computer Science' => 'fas fa-laptop-code',
                                        'Environmental Science' => 'fas fa-leaf'
                                    ];
                                    $icon = $department_icons[$department['department_name']] ?? 'fas fa-microscope';
                                    ?>
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <div class="department-name"><?php echo htmlspecialchars($department['department_name']); ?></div>
                                    <div class="department-head">Head: <?php echo htmlspecialchars($department['head_of_department']); ?></div>
                                </div>
                            </div>

                            <div class="department-details">
                                <p><?php echo htmlspecialchars($department['description']); ?></p>

                                <?php if (!empty($department['faculty_count'])): ?>
                                    <div class="department-detail">
                                        <i class="fas fa-users"></i>
                                        <span><strong>Faculty:</strong> <?php echo $department['faculty_count']; ?> members</span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($department['courses_offered'])): ?>
                                    <div class="department-detail">
                                        <i class="fas fa-book"></i>
                                        <span><strong>Courses:</strong> <?php echo htmlspecialchars($department['courses_offered']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($department['achievements'])): ?>
                                <div class="achievements">
                                    <div class="achievements-title">Notable Achievements</div>
                                    <p><?php echo htmlspecialchars($department['achievements']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem;">
                    <p>Department information is currently being updated. Please contact the science department for details.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Research Highlights -->
        <section class="research-section">
            <div class="section-header">
                <h2 class="section-title">Research Highlights</h2>
                <p class="section-subtitle">Innovative research projects and scientific discoveries from our students and faculty</p>
            </div>

            <div class="research-grid">
                <div class="research-card">
                    <h3 class="research-title">Renewable Energy Solutions</h3>
                    <p class="research-description">Students developed a novel solar cell design that increases energy conversion efficiency by 25% using nanotechnology approaches.</p>
                    <div class="research-meta">
                        <span>Physics Department</span>
                        <span>2023</span>
                    </div>
                </div>

                <div class="research-card">
                    <h3 class="research-title">Environmental Bioremediation</h3>
                    <p class="research-description">Research team discovered bacterial strains capable of breaking down plastic waste in marine environments, offering sustainable pollution solutions.</p>
                    <div class="research-meta">
                        <span>Biology Department</span>
                        <span>2024</span>
                    </div>
                </div>

                <div class="research-card">
                    <h3 class="research-title">Advanced Materials Synthesis</h3>
                    <p class="research-description">Development of self-healing polymers with applications in aerospace and medical device manufacturing through innovative chemical processes.</p>
                    <div class="research-meta">
                        <span>Chemistry Department</span>
                        <span>2023</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Science Events -->
        <section class="events-section">
            <div class="section-header">
                <h2 class="section-title">Upcoming Science Events</h2>
                <p class="section-subtitle">Join us for exciting scientific demonstrations, competitions, and educational workshops</p>
            </div>

            <?php if (!empty($events)): ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <div class="event-date">
                                <?php
                                $event_date = new DateTime($event['event_date']);
                                $month = $event_date->format('M');
                                $day = $event_date->format('d');
                                ?>
                                <div class="event-month"><?php echo $month; ?></div>
                                <div class="event-day"><?php echo $day; ?></div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <?php if (!empty($event['event_time'])): ?>
                                    <div class="event-time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($event['venue'])): ?>
                                    <div class="event-venue">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($event['venue']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($event['description'])): ?>
                                    <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem;">
                    <p>No upcoming events scheduled at the moment. Please check back later for updates.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h2 class="cta-title">Explore the World of Science</h2>
            <p class="cta-description">Join our community of curious minds and innovative thinkers. Discover your passion for science through hands-on learning and cutting-edge research.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="cta-button">Contact Science Department</a>
                <a href="academics.php" class="cta-button outline">View Academic Programs</a>
            </div>
        </section>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to facility cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            // Animate facility cards
            document.querySelectorAll('.facility-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Animate department cards
            document.querySelectorAll('.department-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Animate event cards
            document.querySelectorAll('.event-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Animate research cards
            document.querySelectorAll('.research-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Animate stats
            document.querySelectorAll('.stat-item').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(item);
            });
        });
    </script>
</body>
</html>
