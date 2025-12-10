<?php
session_start();
require_once '../includes/config.php';

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Get event details
    $sql = "SELECT * FROM events WHERE id = ? AND is_active = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header("HTTP/1.0 404 Not Found");
        $error_message = "Event not found or no longer available.";
    }

    // Get related events (same event type, excluding current event)
    $related_sql = "SELECT id, title, event_date, venue FROM events
                   WHERE event_type = ? AND id != ? AND is_active = 1 AND event_date >= CURDATE()
                   ORDER BY event_date ASC LIMIT 3";
    $related_stmt = $pdo->prepare($related_sql);
    $related_stmt->execute([$event['event_type'], $event_id]);
    $related_events = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $event = null;
    $related_events = [];
    $error_message = "Unable to load event details.";
}

// Function to format event date
function formatEventDate($event_date) {
    return date('F j, Y', strtotime($event_date));
}

// Function to check if event is upcoming
function isUpcoming($event_date) {
    return strtotime($event_date) >= strtotime('today');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($event['title']) ? htmlspecialchars($event['title']) : 'Event Not Found'; ?> - St. Philip Neri School</title>
    <meta name="description" content="<?php echo isset($event['description']) ? htmlspecialchars(strip_tags($event['description'])) : 'Event details'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1a365d;
            --color-secondary: #2d3748;
            --color-accent: #38a169;
            --color-accent-light: #c6f6d5;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;

            --gradient-hero: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #38a169, #2f855a);
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
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
        }

        /* Event Details */
        .event-details {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
        }

        .event-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            background: var(--color-surface);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .meta-icon {
            width: 50px;
            height: 50px;
            background: var(--color-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .meta-content h3 {
            font-size: 0.9rem;
            color: var(--color-text-light);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-content p {
            font-weight: 600;
            color: var(--color-primary);
        }

        .event-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--color-text);
        }

        .event-description h2,
        .event-description h3,
        .event-description h4 {
            font-family: 'Playfair Display', serif;
            margin: 2rem 0 1rem;
            color: var(--color-primary);
        }

        .event-description p {
            margin-bottom: 1rem;
        }

        .event-description ul,
        .event-description ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }

        .event-description li {
            margin-bottom: 0.5rem;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .sidebar-widget {
            background: var(--color-surface);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
        }

        .widget-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--color-primary);
            position: relative;
        }

        .widget-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--color-accent);
        }

        .related-events-list {
            list-style: none;
        }

        .related-events-list li {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--color-border);
        }

        .related-events-list li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .related-event {
            color: var(--color-text);
            text-decoration: none;
            transition: var(--transition);
            display: block;
        }

        .related-event:hover {
            color: var(--color-accent);
        }

        .related-event-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .related-event-meta {
            font-size: 0.9rem;
            color: var(--color-text-light);
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .status-upcoming {
            background: var(--color-accent-light);
            color: var(--color-accent);
        }

        .status-past {
            background: #edf2f7;
            color: var(--color-text-light);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--color-accent);
            color: white;
        }

        .btn-primary:hover {
            background: #2f855a;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--color-surface);
            color: var(--color-text);
            border: 2px solid var(--color-border);
        }

        .btn-secondary:hover {
            background: var(--color-border);
            transform: translateY(-2px);
        }

        /* Error State */
        .error-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-text-light);
        }

        .error-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .main-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .event-meta-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .hero-section {
                padding: 100px 1rem 60px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <?php if (isset($error_message)): ?>
        <!-- Hero Section for Error -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">Event Not Found</h1>
                <p class="hero-subtitle">The event you're looking for doesn't exist or is no longer available.</p>
            </div>
        </section>

        <!-- Error Message -->
        <div class="main-container">
            <div class="error-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3><?php echo $error_message; ?></h3>
                <p>Please check the event URL or return to the events listing.</p>
                <a href="all_events.php" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <span class="status-badge <?php echo isUpcoming($event['event_date']) ? 'status-upcoming' : 'status-past'; ?>">
                    <?php echo isUpcoming($event['event_date']) ? 'Upcoming Event' : 'Past Event'; ?>
                </span>
                <h1 class="hero-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                <p class="hero-subtitle">
                    <?php echo formatEventDate($event['event_date']); ?>
                    <?php if (!empty($event['venue'])): ?>
                         • <?php echo htmlspecialchars($event['venue']); ?>
                    <?php endif; ?>
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <div class="main-container">
            <!-- Event Details -->
            <div class="event-details">
                <!-- Event Meta Information -->
                <div class="event-meta-grid">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="meta-content">
                            <h3>Date</h3>
                            <p><?php echo formatEventDate($event['event_date']); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($event['event_type'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="meta-content">
                            <h3>Event Type</h3>
                            <p><?php echo htmlspecialchars($event['event_type']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($event['venue'])): ?>
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="meta-content">
                            <h3>Venue</h3>
                            <p><?php echo htmlspecialchars($event['venue']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="meta-content">
                            <h3>Status</h3>
                            <p><?php echo isUpcoming($event['event_date']) ? 'Upcoming' : 'Completed'; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Event Description -->
                <div class="event-description">
                    <?php
                    // Display the description with basic HTML formatting
                    echo nl2br(htmlspecialchars($event['description']));
                    ?>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="all_events.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Events
                    </a>
                    <?php if (isUpcoming($event['event_date'])): ?>
                        <button class="btn btn-primary" onclick="alert('Registration functionality would go here!')">
                            <i class="fas fa-calendar-plus"></i> Register for Event
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Related Events -->
                <?php if (!empty($related_events)): ?>
                <div class="sidebar-widget">
                    <h3 class="widget-title">Related Events</h3>
                    <ul class="related-events-list">
                        <?php foreach ($related_events as $related): ?>
                            <li>
                                <a href="single_event.php?id=<?php echo $related['id']; ?>" class="related-event">
                                    <div class="related-event-title">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </div>
                                    <div class="related-event-meta">
                                        <?php echo formatEventDate($related['event_date']); ?>
                                        <?php if (!empty($related['venue'])): ?>
                                            • <?php echo htmlspecialchars($related['venue']); ?>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Quick Links -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Quick Links</h3>
                    <ul class="related-events-list">
                        <li>
                            <a href="all_events.php" class="related-event">
                                <i class="fas fa-calendar-alt"></i> All Events
                            </a>
                        </li>
                        <li>
                            <a href="all_events.php?type=<?php echo urlencode($event['event_type']); ?>" class="related-event">
                                <i class="fas fa-tag"></i> More <?php echo htmlspecialchars($event['event_type']); ?> Events
                            </a>
                        </li>
                        <li>
                            <a href="index.php" class="related-event">
                                <i class="fas fa-home"></i> Back to Home
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Share Event -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Share Event</h3>
                    <div class="action-buttons">
                        <button class="btn btn-secondary" onclick="shareEvent('facebook')" style="flex: 1;">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="btn btn-secondary" onclick="shareEvent('twitter')" style="flex: 1;">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-secondary" onclick="shareEvent('linkedin')" style="flex: 1;">
                            <i class="fab fa-linkedin-in"></i>
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        function shareEvent(platform) {
            const title = "<?php echo isset($event['title']) ? addslashes($event['title']) : ''; ?>";
            const url = window.location.href;
            let shareUrl = '';

            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                    break;
            }

            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        }

        // Add animation to meta items
        document.addEventListener('DOMContentLoaded', function() {
            const metaItems = document.querySelectorAll('.meta-item');
            metaItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = `all 0.6s ease ${index * 0.1}s`;

                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 100);
            });
        });
    </script>
</body>
</html>
