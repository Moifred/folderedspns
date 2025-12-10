<?php
session_start();
require_once '../includes/config.php';

// Pagination settings
$posts_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$tag_filter = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$author_filter = isset($_GET['author']) ? trim($_GET['author']) : '';

// Build WHERE conditions
$where_conditions = ["is_published = 1"];
$params = [];
$param_types = [];

if (!empty($search_query)) {
    $where_conditions[] = "(title LIKE ? OR excerpt LIKE ? OR content LIKE ? OR tags LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types = array_merge($param_types, ['s', 's', 's', 's']);
}

if (!empty($tag_filter)) {
    $where_conditions[] = "tags LIKE ?";
    $params[] = "%$tag_filter%";
    $param_types[] = 's';
}

if (!empty($author_filter)) {
    $where_conditions[] = "author = ?";
    $params[] = $author_filter;
    $param_types[] = 's';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM blogs $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_posts = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_posts / $posts_per_page);

    // Get blogs with pagination - FIXED: Use integers for LIMIT and OFFSET
    $sql = "SELECT * FROM blogs $where_clause ORDER BY published_at DESC, created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);

    // Bind all search parameters if they exist
    $param_index = 1;
    foreach ($params as $key => $value) {
        $stmt->bindValue($param_index, $value, $param_types[$key] ?? PDO::PARAM_STR);
        $param_index++;
    }

    // Bind LIMIT and OFFSET as integers
    $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all unique tags for filter
    $tags_sql = "SELECT DISTINCT tags FROM blogs WHERE is_published = 1 AND tags IS NOT NULL AND tags != ''";
    $tags_stmt = $pdo->query($tags_sql);
    $all_tags = [];
    while ($row = $tags_stmt->fetch(PDO::FETCH_ASSOC)) {
        $post_tags = explode(',', $row['tags']);
        foreach ($post_tags as $tag) {
            $clean_tag = trim($tag);
            if (!empty($clean_tag) && !in_array($clean_tag, $all_tags)) {
                $all_tags[] = $clean_tag;
            }
        }
    }
    sort($all_tags);

    // Get all authors for filter
    $authors_sql = "SELECT DISTINCT author FROM blogs WHERE is_published = 1 ORDER BY author";
    $authors_stmt = $pdo->query($authors_sql);
    $all_authors = $authors_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get popular tags (most frequent)
    $popular_tags = array_slice($all_tags, 0, 10);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $blogs = [];
    $all_tags = [];
    $all_authors = [];
    $total_posts = 0;
    $total_pages = 1;
}

// Function to estimate reading time
function estimateReadingTime($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    return max(1, $reading_time);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - St. Philip Neri School</title>
    <meta name="description" content="Explore our latest blog posts, articles, and insights from St. Philip Neri School community.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1a365d;
            --color-secondary: #2d3748;
            --color-accent: #e53e3e;
            --color-accent-light: #fed7d7;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;

            --gradient-hero: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .search-input {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--color-border);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
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
            background: #c53030;
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

        /* Blog Grid */
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .blog-card {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .blog-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .blog-content {
            padding: 1.5rem;
        }

        .blog-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .blog-title a {
            color: var(--color-primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .blog-title a:hover {
            color: var(--color-accent);
        }

        .blog-excerpt {
            color: var(--color-text-light);
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .blog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--color-text-light);
            margin-bottom: 1rem;
        }

        .blog-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .blog-tag {
            background: var(--color-surface);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            color: var(--color-text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .blog-tag:hover {
            background: var(--color-accent);
            color: white;
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

        .authors-list {
            list-style: none;
        }

        .authors-list li {
            margin-bottom: 0.75rem;
        }

        .authors-list a {
            color: var(--color-text);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .authors-list a:hover {
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

            .blog-grid {
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
            <h1 class="hero-title">School Blog</h1>
            <p class="hero-subtitle">Discover insights, stories, and updates from St. Philip Neri School community</p>
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
                           placeholder="Search blog posts..."
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>

                <!-- Active Filters -->
                <?php if (!empty($search_query) || !empty($tag_filter) || !empty($author_filter)): ?>
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
                    <?php if (!empty($tag_filter)): ?>
                        <span class="active-filter">
                            Tag: <?php echo htmlspecialchars($tag_filter); ?>
                            <a href="?<?php echo http_build_query(array_diff_key($_GET, ['tag' => ''])); ?>" class="clear-filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($author_filter)): ?>
                        <span class="active-filter">
                            Author: <?php echo htmlspecialchars($author_filter); ?>
                            <a href="?<?php echo http_build_query(array_diff_key($_GET, ['author' => ''])); ?>" class="clear-filters">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <a href="all_blogs.php" class="clear-filters">Clear All Filters</a>
                </div>
                <?php endif; ?>

                <!-- Popular Tags -->
                <div class="filter-tags">
                    <strong>Popular Tags:</strong>
                    <?php foreach ($popular_tags as $tag): ?>
                        <a href="?tag=<?php echo urlencode($tag); ?>"
                           class="filter-tag <?php echo $tag_filter === $tag ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Results Info -->
            <div class="results-info">
                <?php if ($total_posts > 0): ?>
                    <p>Showing <?php echo count($blogs); ?> of <?php echo $total_posts; ?> post<?php echo $total_posts !== 1 ? 's' : ''; ?>
                    <?php if (!empty($search_query)): ?>matching "<?php echo htmlspecialchars($search_query); ?>"<?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Blog Grid -->
            <?php if (!empty($blogs)): ?>
                <div class="blog-grid">
                    <?php foreach ($blogs as $blog): ?>
                        <article class="blog-card">
                            <?php if (!empty($blog['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>"
                                     alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                     class="blog-image">
                            <?php else: ?>
                                <div style="height: 200px; background: var(--gradient-subtle); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-newspaper" style="font-size: 3rem; color: var(--color-text-light);"></i>
                                </div>
                            <?php endif; ?>

                            <div class="blog-content">
                                <h2 class="blog-title">
                                    <a href="blog_single.php?id=<?php echo $blog['id']; ?>">
                                        <?php echo htmlspecialchars($blog['title']); ?>
                                    </a>
                                </h2>

                                <?php if (!empty($blog['excerpt'])): ?>
                                    <p class="blog-excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                                <?php endif; ?>

                                <div class="blog-meta">
                                    <span>By <?php echo htmlspecialchars($blog['author']); ?></span>
                                    <span><?php echo date('M j, Y', strtotime($blog['published_at'] ?? $blog['created_at'])); ?></span>
                                    <span><?php echo estimateReadingTime($blog['content']); ?> min read</span>
                                </div>

                                <?php if (!empty($blog['tags'])): ?>
                                    <div class="blog-tags">
                                        <?php
                                        $tags = explode(',', $blog['tags']);
                                        foreach ($tags as $tag):
                                            if (!empty(trim($tag))):
                                        ?>
                                            <a href="?tag=<?php echo urlencode(trim($tag)); ?>" class="blog-tag">
                                                <?php echo htmlspecialchars(trim($tag)); ?>
                                            </a>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <a href="blog_single.php?id=<?php echo $blog['id']; ?>" class="read-more">
                                    Read More <i class="fas fa-arrow-right"></i>
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
                    <i class="fas fa-search"></i>
                    <h3>No blog posts found</h3>
                    <p><?php echo !empty($search_query) ? 'Try adjusting your search terms or filters.' : 'No blog posts have been published yet.'; ?></p>
                    <?php if (!empty($search_query) || !empty($tag_filter) || !empty($author_filter)): ?>
                        <a href="all_blogs.php" class="search-btn" style="display: inline-block; margin-top: 1rem;">
                            Clear Filters
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Categories/Tags Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Browse Tags</h3>
                <div class="tag-cloud">
                    <?php foreach ($all_tags as $tag): ?>
                        <a href="?tag=<?php echo urlencode($tag); ?>"
                           class="filter-tag <?php echo $tag_filter === $tag ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($tag); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Authors Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Authors</h3>
                <ul class="authors-list">
                    <?php foreach ($all_authors as $author): ?>
                        <li>
                            <a href="?author=<?php echo urlencode($author); ?>"
                               class="<?php echo $author_filter === $author ? 'active' : ''; ?>">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($author); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Recent Posts Widget -->
            <div class="sidebar-widget">
                <h3 class="widget-title">Recent Posts</h3>
                <ul class="authors-list">
                    <?php
                    $recent_sql = "SELECT id, title FROM blogs WHERE is_published = 1 ORDER BY created_at DESC LIMIT 5";
                    $recent_stmt = $pdo->query($recent_sql);
                    $recent_posts = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($recent_posts as $recent):
                    ?>
                        <li>
                            <a href="blog_single.php?id=<?php echo $recent['id']; ?>">
                                <i class="fas fa-file-alt"></i>
                                <?php echo htmlspecialchars($recent['title']); ?>
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

            // Add animation to blog cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.blog-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
