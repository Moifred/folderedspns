<?php
session_start();
require_once '../includes/config.php';

// Fetch sports facilities
try {
    $facilities_sql = "SELECT * FROM sports_facilities WHERE is_active = 1 ORDER BY display_order ASC";
    $facilities_stmt = $pdo->query($facilities_sql);
    $facilities = $facilities_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $facilities = [];
}

// Fetch sports teams
try {
    $teams_sql = "SELECT * FROM sports_teams WHERE is_active = 1 ORDER BY team_name ASC";
    $teams_stmt = $pdo->query($teams_sql);
    $teams = $teams_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $teams = [];
}

// Fetch sports events (upcoming events only)
try {
    $events_sql = "SELECT * FROM sports_events WHERE is_active = 1 AND event_date >= CURDATE() ORDER BY event_date ASC, event_time ASC";
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
    <title>Sports Complex - St. Philip Neri School</title>
    <meta name="description" content="Explore our state-of-the-art sports facilities and athletic programs at St. Philip Neri School.">
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

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .feature-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .feature-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .feature-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--color-text-light);
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .feature-details {
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

        /* Teams Section */
        .teams-section {
            background: var(--color-surface);
            padding: 3rem 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 4rem;
        }

        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .team-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .team-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .team-icon {
            width: 60px;
            height: 60px;
            background: var(--color-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .team-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .team-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--color-primary);
        }

        .team-sport {
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        .team-details {
            margin-bottom: 1rem;
        }

        .team-detail {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }

        .team-detail i {
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
            .features-grid {
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

            .teams-grid,
            .events-grid {
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

            .feature-card {
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
            <h1 class="hero-title">Sports Complex</h1>
            <p class="hero-subtitle">State-of-the-art athletic facilities and programs for students to excel in sports and physical education</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Sports Facilities -->
        <section class="facilities-section">
            <div class="section-header">
                <h2 class="section-title">Our Sports Facilities</h2>
                <p class="section-subtitle">World-class facilities designed to nurture athletic talent and promote physical fitness</p>
            </div>

            <?php if (!empty($facilities)): ?>
                <div class="features-grid">
                    <?php foreach ($facilities as $facility): ?>
                        <div class="feature-card">
                            <?php if (!empty($facility['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($facility['image_url']); ?>"
                                     alt="<?php echo htmlspecialchars($facility['title']); ?>"
                                     class="feature-image">
                            <?php else: ?>
                                <div style="height: 200px; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-running" style="font-size: 3rem; color: var(--color-text-light);"></i>
                                </div>
                            <?php endif; ?>

                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo htmlspecialchars($facility['title']); ?></h3>
                                <p class="feature-description"><?php echo htmlspecialchars($facility['description']); ?></p>

                                <div class="feature-details">
                                    <?php if (!empty($facility['facility_type'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Type:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['facility_type']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($facility['features'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Features:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['features']); ?></span>
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
                    <i class="fas fa-running" style="font-size: 4rem; color: var(--color-text-light); margin-bottom: 1rem;"></i>
                    <h3>Sports Facilities Information Coming Soon</h3>
                    <p>We're currently updating our sports facilities information. Please check back later.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Sports Teams -->
        <section class="teams-section">
            <div class="section-header">
                <h2 class="section-title">Our Sports Teams</h2>
                <p class="section-subtitle">Competitive teams that represent our school in various sports disciplines</p>
            </div>

            <?php if (!empty($teams)): ?>
                <div class="teams-grid">
                    <?php foreach ($teams as $team): ?>
                        <div class="team-card">
                            <div class="team-header">
                                <div class="team-icon">
                                    <?php
                                    // Map sport types to appropriate icons
                                    $sport_icons = [
                                        'Basketball' => 'fas fa-basketball-ball',
                                        'Football' => 'fas fa-football-ball',
                                        'Swimming' => 'fas fa-swimmer',
                                        'Tennis' => 'fas fa-table-tennis',
                                        'Volleyball' => 'fas fa-volleyball-ball',
                                        'Soccer' => 'fas fa-futbol',
                                        'Track' => 'fas fa-running',
                                        'Baseball' => 'fas fa-baseball-ball'
                                    ];
                                    $icon = $sport_icons[$team['sport_type']] ?? 'fas fa-running';
                                    ?>
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <div class="team-name"><?php echo htmlspecialchars($team['team_name']); ?></div>
                                    <div class="team-sport"><?php echo htmlspecialchars($team['sport_type']); ?></div>
                                </div>
                            </div>

                            <div class="team-details">
                                <?php if (!empty($team['coach_name'])): ?>
                                    <div class="team-detail">
                                        <i class="fas fa-user-tie"></i>
                                        <span><strong>Coach:</strong> <?php echo htmlspecialchars($team['coach_name']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($team['coach_contact'])): ?>
                                    <div class="team-detail">
                                        <i class="fas fa-phone"></i>
                                        <span><strong>Coach Contact:</strong> <?php echo htmlspecialchars($team['coach_contact']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($team['practice_schedule'])): ?>
                                    <div class="team-detail">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><strong>Practice:</strong> <?php echo htmlspecialchars($team['practice_schedule']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($team['achievements'])): ?>
                                <div class="achievements">
                                    <div class="achievements-title">Achievements</div>
                                    <p><?php echo htmlspecialchars($team['achievements']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem;">
                    <p>Team information is currently being updated. Please contact the sports department for details.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="section-header" style="color: white;">
                <h2 class="section-title" style="color: white;">Sports Excellence</h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.9);">Our commitment to athletic development and achievement</p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Sports Disciplines</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">25+</div>
                    <div class="stat-label">School Teams</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Trained Coaches</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Annual Events</div>
                </div>
            </div>
        </section>

        <!-- Sports Events -->
        <section class="events-section">
            <div class="section-header">
                <h2 class="section-title">Upcoming Sports Events</h2>
                <p class="section-subtitle">Join us for exciting competitions and sporting events throughout the year</p>
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
            <h2 class="cta-title">Join Our Sports Community</h2>
            <p class="cta-description">Whether you're a student athlete or a sports enthusiast, there's a place for you in our vibrant sports community.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="cta-button">Contact Sports Department</a>
                <a href="admissions.php" class="cta-button outline">Learn About Admissions</a>
            </div>
        </section>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to feature cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            // Animate feature cards
            document.querySelectorAll('.feature-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Animate team cards
            document.querySelectorAll('.team-card').forEach((card, index) => {
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
