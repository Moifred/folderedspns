<?php
session_start();
require_once 'config.php';

// Fetch transport facilities
try {
    $facilities_sql = "SELECT * FROM transport_facilities WHERE is_active = 1 ORDER BY display_order ASC";
    $facilities_stmt = $pdo->query($facilities_sql);
    $facilities = $facilities_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $facilities = [];
}

// Fetch transport routes
try {
    $routes_sql = "SELECT * FROM transport_routes WHERE is_active = 1 ORDER BY route_name ASC";
    $routes_stmt = $pdo->query($routes_sql);
    $routes = $routes_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $routes = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Facilities - St. Philip Neri School</title>
    <meta name="description" content="Discover our safe and reliable school transport services at St. Philip Neri School.">
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

        /* Routes Section */
        .routes-section {
            background: var(--color-surface);
            padding: 3rem 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 4rem;
        }

        .routes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .routes-table th {
            background: var(--color-primary);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .routes-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--color-border);
        }

        .routes-table tr:nth-child(even) {
            background: rgba(0,0,0,0.02);
        }

        .routes-table tr:hover {
            background: rgba(45, 90, 39, 0.05);
        }

        /* Safety Section */
        .safety-section {
            margin-bottom: 4rem;
        }

        .safety-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .safety-item {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            text-align: center;
            transition: var(--transition);
        }

        .safety-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .safety-icon {
            font-size: 2.5rem;
            color: var(--color-accent);
            margin-bottom: 1rem;
        }

        .safety-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-secondary);
        }

        .safety-description {
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
        }

        .cta-button:hover {
            background: #c53030;
            transform: translateY(-2px);
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

            .routes-table {
                display: block;
                overflow-x: auto;
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
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">School Transport</h1>
            <p class="hero-subtitle">Safe, reliable, and convenient transportation services for all students</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Transport Features -->
        <section class="features-section">
            <div class="section-header">
                <h2 class="section-title">Our Transport Facilities</h2>
                <p class="section-subtitle">We prioritize safety and convenience with our modern fleet and professional staff</p>
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
                                    <i class="fas fa-bus" style="font-size: 3rem; color: var(--color-text-light);"></i>
                                </div>
                            <?php endif; ?>

                            <div class="feature-content">
                                <h3 class="feature-title"><?php echo htmlspecialchars($facility['title']); ?></h3>
                                <p class="feature-description"><?php echo htmlspecialchars($facility['description']); ?></p>

                                <div class="feature-details">
                                    <?php if (!empty($facility['coverage_areas'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Coverage:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['coverage_areas']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($facility['safety_features'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Safety:</span>
                                            <span class="detail-value"><?php echo htmlspecialchars($facility['safety_features']); ?></span>
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
                    <i class="fas fa-bus" style="font-size: 4rem; color: var(--color-text-light); margin-bottom: 1rem;"></i>
                    <h3>Transport Information Coming Soon</h3>
                    <p>We're currently updating our transport facilities information. Please check back later.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Transport Routes -->
        <section class="routes-section">
            <div class="section-header">
                <h2 class="section-title">Transport Routes & Timings</h2>
                <p class="section-subtitle">Our comprehensive route network ensures convenient pick-up and drop-off for all students</p>
            </div>

            <?php if (!empty($routes)): ?>
                <div class="table-container">
                    <table class="routes-table">
                        <thead>
                            <tr>
                                <th>Route Name</th>
                                <th>Pickup Points</th>
                                <th>Morning Timing</th>
                                <th>Evening Timing</th>
                                <th>Vehicle Type</th>
                                <th>Driver</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($routes as $route): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($route['route_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($route['pickup_points']); ?></td>
                                    <td><?php echo htmlspecialchars($route['timing_morning']); ?></td>
                                    <td><?php echo htmlspecialchars($route['timing_evening']); ?></td>
                                    <td><?php echo htmlspecialchars($route['vehicle_type']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($route['driver_name']); ?>
                                        <?php if (!empty($route['driver_contact'])): ?>
                                            <br><small><?php echo htmlspecialchars($route['driver_contact']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem;">
                    <p>Route information is currently being updated. Please contact the transport office for details.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Safety Features -->
        <section class="safety-section">
            <div class="section-header">
                <h2 class="section-title">Safety & Security</h2>
                <p class="section-subtitle">Your child's safety is our top priority</p>
            </div>

            <div class="safety-grid">
                <div class="safety-item">
                    <div class="safety-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="safety-title">Trained Drivers</h3>
                    <p class="safety-description">All our drivers are licensed, experienced, and trained in student safety protocols.</p>
                </div>

                <div class="safety-item">
                    <div class="safety-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="safety-title">GPS Tracking</h3>
                    <p class="safety-description">Real-time tracking of all vehicles for monitoring and safety assurance.</p>
                </div>

                <div class="safety-item">
                    <div class="safety-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="safety-title">Female Attendants</h3>
                    <p class="safety-description">Female attendants on all routes for additional supervision and assistance.</p>
                </div>

                <div class="safety-item">
                    <div class="safety-icon">
                        <i class="fas fa-first-aid"></i>
                    </div>
                    <h3 class="safety-title">First Aid Kits</h3>
                    <p class="safety-description">All vehicles equipped with comprehensive first aid kits for emergencies.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h2 class="cta-title">Need More Information?</h2>
            <p class="cta-description">Contact our transport department for route inquiries, registration, or any questions about our services.</p>
            <a href="contact.php" class="cta-button">Contact Transport Office</a>
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

            document.querySelectorAll('.feature-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Add animation to safety items
            document.querySelectorAll('.safety-item').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(item);
            });
        });
    </script>
</body>
</html>
