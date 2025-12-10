<?php
session_start();
require_once 'config.php';

// Pagination settings
$events_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $events_per_page;

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? trim($_GET['type']) : '';
$venue_filter = isset($_GET['venue']) ? trim($_GET['venue']) : '';

// Build WHERE conditions
$where_conditions = ["is_active = 1"];
$params = [];
$param_types = [];

if (!empty($search_query)) {
    $where_conditions[] = "(title LIKE ? OR description LIKE ? OR venue LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types = array_merge($param_types, ['s', 's', 's']);
}

if (!empty($type_filter)) {
    $where_conditions[] = "event_type = ?";
    $params[] = $type_filter;
    $param_types[] = 's';
}

if (!empty($venue_filter)) {
    $where_conditions[] = "venue LIKE ?";
    $params[] = "%$venue_filter%";
    $param_types[] = 's';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM events $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_events = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_events / $events_per_page);

    // Get events with pagination
    $sql = "SELECT * FROM events $where_clause ORDER BY event_date ASC, created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);

    // Bind all search parameters if they exist
    $param_index = 1;
    foreach ($params as $key => $value) {
        $stmt->bindValue($param_index, $value, $param_types[$key] ?? PDO::PARAM_STR);
        $param_index++;
    }

    // Bind LIMIT and OFFSET as integers
    $stmt->bindValue(':limit', $events_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all unique event types for filter
    $types_sql = "SELECT DISTINCT event_type FROM events WHERE is_active = 1 AND event_type IS NOT NULL AND event_type != '' ORDER BY event_type";
    $types_stmt = $pdo->query($types_sql);
    $all_types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get all unique venues for filter
    $venues_sql = "SELECT DISTINCT venue FROM events WHERE is_active = 1 AND venue IS NOT NULL AND venue != '' ORDER BY venue";
    $venues_stmt = $pdo->query($venues_sql);
    $all_venues = $venues_stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $events = [];
    $all_types = [];
    $all_venues = [];
    $total_events = 0;
    $total_pages = 1;
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
    <title>Events - St. Philip Neri School</title>
    <meta name="description" content="Discover upcoming events, activities, and gatherings at St. Philip Neri School.">
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
            --color-upcoming: #38a169;
            --color-past: #718096;

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
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 3rem;
        }

        /* Search and Filters */
        .search-section {
            background: var(--color-surface);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input, .filter-select {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--color-border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.1);
        }

        .search-btn {
            background: var(--color-accent);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .search-btn:hover {
            background: #2f855a;
            transform: translateY(-2px);
        }

        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .filter-tag {
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            text-decoration: none;
            color: var(--color-text);
            border: 2px solid var(--color-border);
            transition: var(--transition);
        }

        .filter-tag:hover,
        .filter-tag.active {
            background: var(--color-accent);
            color: white;
            border-color: var(--color-accent);
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .event-card {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border-left: 4px solid var(--color-accent);
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .event-card.upcoming {
            border-left-color: var(--color-upcoming);
        }

        .event-card.past {
            border-left-color: var(--color-past);
            opacity: 0.8;
        }

        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .event-title a {
            color: var(--color-primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .event-title a:hover {
            color: var(--color-accent);
        }

        .event-description {
            color: var(--color-text-light);
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--color-text-light);
        }

        .event-meta-item i {
            width: 16px;
            color: var(--color-accent);
        }

        .event-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-upcoming {
            background: var(--color-accent-light);
            color: var(--color-upcoming);
        }

        .status-past {
            background: #edf2f7;
            color: var(--color-past);
        }

        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .read-more:hover {
            gap: 0.75rem;
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

        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tag-cloud .filter-tag {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }

        .types-list, .venues-list {
            list-style: none;
        }

        .types-list li, .venues-list li {
            margin-bottom: 0.75rem;
        }

        .types-list a, .venues-list a {
            color: var(--color-text);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .types-list a:hover, .venues-list a:hover {
            color: var(--color-accent);
            transform: translateX(5px);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 3rem 0;
        }

        .pagination a,
        .pagination span {
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
        }

        .pagination a {
            background: var(--color-surface);
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }

        .pagination a:hover {
            background: var(--color-accent);
            color: white;
            border-color: var(--color-accent);
        }

        .pagination .current {
            background: var(--color-accent);
            color: white;
            border-color: var(--color-accent);
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Results Info */
        .results-info {
            text-align: center;
            color: var(--color-text-light);
            margin-bottom: 2rem;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-text-light);
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Active Filters */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }

        .active-filter {
            background: var(--color-accent);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .clear-filters {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
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

            .events-grid {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .hero-section {
                padding: 100px 1rem 60px;
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
            <h1 class="hero-title">School Events</h1>
            <p class="hero-subtitle">Discover upcoming events, activities, and gatherings at St. Philip Neri School</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Search Section -->
            <section class="search-section">
                <form method="GET" class="search-form">
                    <input type="text"
                           name="search"
                           class="search-input"
                           placeholder="Search events..."
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>

                <!-- Active Filters -->
                <?php if (!empty($search_query) || !empty($type_filter) || !empty($venue_filter)): ?>
                <div class="active-filters">
                    <strong>Active Filters:</strong>
                    <?php if (!empty($search_query)): ?>
                        <span class="active-filter">
                            Search: "<?php echo htmlspecialchars($search_query); ?>"
                            <a href="?<?php echo http_build_query(array_diff_key($_GET, ['search' => ''])); ?>" class="clear-filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($type_filter)): ?>
                        <span class="active-filter">
                            Type: <?php echo htmlspecialchars($type_filter); ?>
                            <a href="?<?php echo http_build_query(array_diff_key($_GET, ['type' => ''])); ?>" class="clear-filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($venue_filter)): ?>
                        <span class="active-filter">
                            Venue: <?php echo htmlspecialchars($venue_filter); ?>
                            <a href="?<?php echo http_build_query(array_diff_key($_GET, ['venue' => ''])); ?>" class="clear-filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <a href="all_events.php" class="clear-filters">Clear All Filters</a>
                </div>
                <?php endif; ?>

                <!-- Quick Filters -->
                <div class="filter-tags">
                    <strong>Event Types:</strong>
                    <?php foreach ($all_types as $type): ?>
                        <a href="?type=<?php echo urlencode($type); ?>"
                           class="filter-tag <?php echo $type_filter === $type ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($type); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Results Info -->
            <div class="results-info">
                <?php if ($total_events > 0): ?>
                    <p>Showing <?php echo count($events); ?> of <?php echo $total_events; ?> event<?php echo $total_events !== 1 ? 's' : ''; ?>
                    <?php if (!empty($search_query)): ?>matching "<?php echo htmlspecialchars($search_query); ?>"<?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Events Grid -->
            <?php if (!empty($events)): ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <?php
                        $event_class = isUpcoming($event['event_date']) ? 'upcoming' : 'past';
                        $status_text = isUpcoming($event['event_date']) ? 'Upcoming' : 'Past';
                        $status_class = isUpcoming($event['event_date']) ? 'status-upcoming' : 'status-past';
                        ?>
                        <article class="event-card <?php echo $event_class; ?>">
                            <div class="event-content">
                                <span class="event-status <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>

                                <h2 class="event-title">
                                    <a href="single_event.php?id=<?php echo $event['id']; ?>">
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </a>
                                </h2>

                                <?php if (!empty($event['description'])): ?>
                                    <p class="event-description">
                                        <?php
                                        $description = strip_tags($event['description']);
                                        echo strlen($description) > 120 ? substr($description, 0, 120) . '...' : $description;
                                        ?>
                                    </p>
                                <?php endif; ?>

                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><?php echo formatEventDate($event['event_date']); ?></span>
                                    </div>

                                    <?php if (!empty($event['event_type'])): ?>
                                    <div class="event-meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo htmlspecialchars($event['event_type']); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if (!empty($event['venue'])): ?>
                                    <div class="event-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($event['venue']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <a href="single_event.php?id=<?php echo $event['id']; ?>" class="read-more">
                                    View Details <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php else: ?>
                            <span class="disabled"><i class="fas fa-angle-double-left"></i></span>
                            <span class="disabled"><i class="fas fa-angle-left"></i></span>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                               class="<?php echo $i == $current_page ? 'current' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="disabled"><i class="fas fa-angle-right"></i></span>
                            <span class="disabled"><i class="fas fa-angle-double-right"></i></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Results -->
                <div class="no-results">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No events found</h3>
                    <p><?php echo !empty($search_query) ? 'Try adjusting your search terms or filters.' : 'No events are currently scheduled.'; ?></p>
                    <?php if (!empty($search_query) || !empty($type_filter) || !empty($venue_filter)): ?>
                        <a href="all_events.php" class="search-btn" style="display: inline-block; margin-top: 1rem;">
                            Clear Filters
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Event Types Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Event Types</h3>
                <ul class="types-list">
                    <?php foreach ($all_types as $type): ?>
                        <li>
                            <a href="?type=<?php echo urlencode($type); ?>"
                               class="<?php echo $type_filter === $type ? 'active' : ''; ?>">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($type); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Venues Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Venues</h3>
                <ul class="venues-list">
                    <?php foreach ($all_venues as $venue): ?>
                        <li>
                            <a href="?venue=<?php echo urlencode($venue); ?>"
                               class="<?php echo $venue_filter === $venue ? 'active' : ''; ?>">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($venue); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Upcoming Events Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Upcoming Events</h3>
                <ul class="types-list">
                    <?php
                    $upcoming_sql = "SELECT id, title, event_date FROM events WHERE is_active = 1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
                    $upcoming_stmt = $pdo->query($upcoming_sql);
                    $upcoming_events = $upcoming_stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($upcoming_events as $upcoming):
                    ?>
                        <li>
                            <a href="single_event.php?id=<?php echo $upcoming['id']; ?>">
                                <i class="fas fa-calendar-check"></i>
                                <?php echo htmlspecialchars($upcoming['title']); ?>
                                <br>
                                <small style="color: var(--color-text-light); margin-left: 24px;">
                                    <?php echo formatEventDate($upcoming['event_date']); ?>
                                </small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Add loading state to search form
            const searchForm = document.querySelector('.search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function() {
                    const button = this.querySelector('button[type="submit"]');
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                    button.disabled = true;
                });
            }

            // Add animation to event cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.event-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
