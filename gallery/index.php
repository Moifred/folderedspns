<?php
session_start();
require_once '../includes/config.php';

// Get filter and search parameters
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build WHERE conditions
$where_conditions = ["is_active = 1"];
$params = [];

if (!empty($category_filter) && $category_filter !== 'all') {
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
}

if (!empty($search_query)) {
    $where_conditions[] = "(title LIKE ? OR description LIKE ? OR category LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Build ORDER BY clause
$order_clause = "";
switch ($sort_by) {
    case 'oldest':
        $order_clause = "ORDER BY created_at ASC";
        break;
    case 'title':
        $order_clause = "ORDER BY title ASC";
        break;
    case 'category':
        $order_clause = "ORDER BY category ASC, created_at DESC";
        break;
    default:
        $order_clause = "ORDER BY created_at DESC";
        break;
}

try {
    // Get gallery items
    $sql = "SELECT * FROM gallery $where_clause $order_clause";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $gallery_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add media type to each item
    foreach ($gallery_items as &$item) {
        $item['media_type'] = isVideo($item['image_path']) ? 'video' : 'image';
    }
    unset($item); // Break the reference

    // Get all unique categories for filter
    $categories_sql = "SELECT DISTINCT category FROM gallery WHERE is_active = 1 AND category IS NOT NULL AND category != '' ORDER BY category";
    $categories_stmt = $pdo->query($categories_sql);
    $categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $gallery_items = [];
    $categories = [];
}

// Function to check if file is video
function isVideo($file_path) {
    $video_extensions = ['mp4', 'mov', 'avi', 'webm', 'wmv', 'flv', 'mkv'];
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    return in_array($extension, $video_extensions);
}

// Function to generate thumbnail path (assuming thumbnails are stored in a 'thumbs' folder)
function getThumbnailPath($file_path) {
    if (isVideo($file_path)) {
        $filename = pathinfo($file_path, PATHINFO_FILENAME);
        $thumbnail_path = 'thumbs/' . $filename . '.jpg';
        // Check if thumbnail exists, if not return a placeholder
        return file_exists($thumbnail_path) ? $thumbnail_path : null;
    }
    return $file_path; // Images use the original path
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - St. Philip Neri School</title>
    <meta name="description" content="Browse our school gallery featuring photos and videos from events and activities.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #1a365d;
            --color-accent: #e53e3e;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;
            --color-success: #38a169;
            --color-warning: #dd6b20;

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.2);

            --border-radius: 8px;
            --transition: all 0.3s ease;
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

        /* Header Space */
        .header-space {
            height: 80px;
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Page Header */
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--color-text-light);
            font-size: 1.1rem;
        }

        /* Controls Section */
        .controls-section {
            background: var(--color-surface);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 1.5rem;
            align-items: center;
            box-shadow: var(--shadow-sm);
        }

        .search-box {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 0.75rem 0.75rem 2.5rem;
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

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-light);
        }

        .filter-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .select-control {
            padding: 0.75rem;
            border: 2px solid var(--color-border);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            background: var(--color-background);
            cursor: pointer;
            transition: var(--transition);
            min-width: 150px;
        }

        .select-control:focus {
            outline: none;
            border-color: var(--color-accent);
        }

        /* Results Info */
        .results-info {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        .results-count {
            font-weight: 600;
            color: var(--color-accent);
        }

        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .gallery-item {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            cursor: pointer;
            aspect-ratio: 1;
            background: var(--color-primary);
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .gallery-media {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-media {
            transform: scale(1.05);
        }

        .media-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1rem;
            color: white;
            opacity: 0;
            transition: var(--transition);
        }

        .gallery-item:hover .media-overlay {
            opacity: 1;
        }

        .media-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }

        .media-category {
            font-size: 0.85rem;
            opacity: 0.9;
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            margin-top: 0.5rem;
        }

        .media-type-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--color-accent);
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.5rem;
        }

        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .lightbox.active {
            opacity: 1;
        }

        .lightbox-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lightbox-media-container {
            position: relative;
            max-width: 100%;
            max-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-media {
            max-width: 100%;
            max-height: 80vh;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .lightbox-info {
            color: white;
            text-align: center;
            margin-top: 1.5rem;
            max-width: 600px;
        }

        .lightbox-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .lightbox-description {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
        }

        .lightbox-meta {
            display: flex;
            justify-content: center;
            gap: 1rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .lightbox-close {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: var(--transition);
            z-index: 1001;
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: var(--transition);
            z-index: 1001;
        }

        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-prev {
            left: 2rem;
        }

        .lightbox-next {
            right: 2rem;
        }

        .lightbox-loading {
            color: white;
            font-size: 1.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-text-light);
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .controls-section {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .filter-controls {
                justify-content: center;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .lightbox-nav {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .lightbox-prev {
                left: 1rem;
            }

            .lightbox-next {
                right: 1rem;
            }

            .lightbox-close {
                top: 1rem;
                right: 1rem;
            }

            .page-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .select-control {
                min-width: 120px;
            }

            .lightbox-info {
                margin-top: 1rem;
            }

            .lightbox-title {
                font-size: 1.2rem;
            }
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        /* Filter Tags */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
            justify-content: center;
        }

        .filter-tag {
            background: var(--color-primary);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-tag button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Header Space -->
    <div class="header-space"></div>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Page Header -->
        <header class="page-header">
            <h1 class="page-title">School Gallery</h1>
            <p class="page-subtitle">Memories from events, activities, and everyday moments</p>
        </header>

        <!-- Active Filters -->
        <?php if (!empty($search_query) || (!empty($category_filter) && $category_filter !== 'all')): ?>
        <div class="active-filters">
            <?php if (!empty($search_query)): ?>
                <div class="filter-tag">
                    Search: "<?php echo htmlspecialchars($search_query); ?>"
                    <button onclick="clearSearch()"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($category_filter) && $category_filter !== 'all'): ?>
                <div class="filter-tag">
                    Category: <?php echo htmlspecialchars($category_filter); ?>
                    <button onclick="clearCategory()"><i class="fas fa-times"></i></button>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Controls Section -->
        <section class="controls-section">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <form method="GET" id="search-form">
                    <input type="text"
                           name="search"
                           class="search-input"
                           placeholder="Search gallery..."
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_by); ?>">
                </form>
            </div>

            <div class="filter-controls">
                <select class="select-control" name="sort" onchange="updateSort(this.value)">
                    <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="title" <?php echo $sort_by === 'title' ? 'selected' : ''; ?>>Title A-Z</option>
                    <option value="category" <?php echo $sort_by === 'category' ? 'selected' : ''; ?>>By Category</option>
                </select>
            </div>

            <div class="filter-controls">
                <select class="select-control" name="category" onchange="updateCategory(this.value)">
                    <option value="all" <?php echo empty($category_filter) || $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>"
                                <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </section>

        <!-- Results Info -->
        <div class="results-info">
            Showing <span class="results-count"><?php echo count($gallery_items); ?></span> item<?php echo count($gallery_items) !== 1 ? 's' : ''; ?>
            <?php if (!empty($search_query)): ?> matching "<?php echo htmlspecialchars($search_query); ?>"<?php endif; ?>
            <?php if (!empty($category_filter) && $category_filter !== 'all'): ?> in <?php echo htmlspecialchars($category_filter); ?><?php endif; ?>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid">
            <?php if (!empty($gallery_items)): ?>
                <?php foreach ($gallery_items as $index => $item): ?>
                    <div class="gallery-item fade-in" onclick="openLightbox(<?php echo $index; ?>)">
                        <?php if (!empty($item['image_path'])): ?>
                            <?php if ($item['media_type'] === 'video'): ?>
                                <!-- Video thumbnail with play button -->
                                <div class="gallery-media" style="background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-play" style="font-size: 3rem; color: white; opacity: 0.7;"></i>
                                </div>
                                <div class="media-type-badge">
                                    <i class="fas fa-video"></i> VIDEO
                                </div>
                            <?php else: ?>
                                <!-- Image with loading state -->
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                                     class="gallery-media"
                                     loading="lazy"
                                     onload="this.style.opacity = '1'"
                                     style="opacity: 0; transition: opacity 0.3s;">
                                <div class="media-type-badge">
                                    <i class="fas fa-image"></i> IMAGE
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Placeholder for missing media -->
                            <div class="gallery-media" style="background: #f7fafc; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="font-size: 2rem; color: #718096;"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Media overlay with info -->
                        <div class="media-overlay">
                            <div class="media-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <?php if (!empty($item['description'])): ?>
                                <div class="media-description"><?php echo htmlspecialchars($item['description']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item['category'])): ?>
                                <div class="media-category"><?php echo htmlspecialchars($item['category']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-images"></i>
                    <h3>No items found</h3>
                    <p><?php echo !empty($search_query) || !empty($category_filter) ? 'Try adjusting your search or filter criteria.' : 'The gallery is currently empty.'; ?></p>
                    <?php if (!empty($search_query) || !empty($category_filter)): ?>
                        <button onclick="clearAllFilters()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: var(--color-primary); color: white; border: none; border-radius: var(--border-radius); cursor: pointer;">
                            Clear All Filters
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button>

        <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>

        <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div class="lightbox-content">
            <div class="lightbox-media-container">
                <div class="lightbox-loading" id="lightbox-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
                <img id="lightbox-image" class="lightbox-media" src="" alt="" style="display: none;">
                <video id="lightbox-video" class="lightbox-media" controls style="display: none;">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="lightbox-info" id="lightbox-info" style="display: none;">
                <h2 class="lightbox-title" id="lightbox-title"></h2>
                <p class="lightbox-description" id="lightbox-description"></p>
                <div class="lightbox-meta">
                    <span id="lightbox-category"></span>
                    <span id="lightbox-date"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Gallery data for lightbox
        const galleryItems = <?php echo json_encode($gallery_items); ?>;
        let currentLightboxIndex = 0;

        // Filter functions
        function updateSort(sortBy) {
            const url = new URL(window.location);
            url.searchParams.set('sort', sortBy);
            window.location.href = url.toString();
        }

        function updateCategory(category) {
            const url = new URL(window.location);
            if (category === 'all') {
                url.searchParams.delete('category');
            } else {
                url.searchParams.set('category', category);
            }
            window.location.href = url.toString();
        }

        function clearSearch() {
            const url = new URL(window.location);
            url.searchParams.delete('search');
            window.location.href = url.toString();
        }

        function clearCategory() {
            const url = new URL(window.location);
            url.searchParams.delete('category');
            window.location.href = url.toString();
        }

        function clearAllFilters() {
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('category');
            window.location.href = url.toString();
        }

        // Search with debounce
        let searchTimeout;
        document.querySelector('.search-input').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('search-form').submit();
            }, 500);
        });

        // Lightbox functions
        function openLightbox(index) {
            currentLightboxIndex = index;
            const item = galleryItems[index];
            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightbox-image');
            const lightboxVideo = document.getElementById('lightbox-video');
            const lightboxLoading = document.getElementById('lightbox-loading');
            const lightboxInfo = document.getElementById('lightbox-info');

            // Show loading state
            lightboxLoading.style.display = 'block';
            lightboxImage.style.display = 'none';
            lightboxVideo.style.display = 'none';
            lightboxInfo.style.display = 'none';

            // Show lightbox with animation
            lightbox.style.display = 'block';
            setTimeout(() => {
                lightbox.classList.add('active');
            }, 10);

            document.body.style.overflow = 'hidden';

            // Set media based on type
            if (item.media_type === 'video') {
                // For videos, show the video element
                lightboxVideo.style.display = 'block';
                lightboxVideo.src = item.image_path;

                lightboxVideo.onloadeddata = function() {
                    lightboxLoading.style.display = 'none';
                    lightboxVideo.play().catch(e => console.log('Autoplay prevented:', e));
                };

                lightboxVideo.onerror = function() {
                    lightboxLoading.style.display = 'none';
                    lightboxVideo.innerHTML = 'Error loading video.';
                };
            } else {
                // For images, show the image
                lightboxImage.style.display = 'block';
                lightboxImage.src = item.image_path;
                lightboxImage.alt = item.title || '';

                lightboxImage.onload = function() {
                    lightboxLoading.style.display = 'none';
                };

                lightboxImage.onerror = function() {
                    lightboxLoading.style.display = 'none';
                    lightboxImage.alt = 'Error loading image';
                };
            }

            // Set item info
            document.getElementById('lightbox-title').textContent = item.title || 'Untitled';
            document.getElementById('lightbox-description').textContent = item.description || '';
            document.getElementById('lightbox-category').textContent = item.category ? `Category: ${item.category}` : '';
            document.getElementById('lightbox-date').textContent = item.created_at ? `Added: ${formatDate(item.created_at)}` : '';

            // Show info after a brief delay
            setTimeout(() => {
                lightboxInfo.style.display = 'block';
            }, 300);
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            const lightboxVideo = document.getElementById('lightbox-video');

            lightbox.classList.remove('active');

            setTimeout(() => {
                lightbox.style.display = 'none';
                document.body.style.overflow = 'auto';

                if (lightboxVideo.style.display !== 'none') {
                    lightboxVideo.pause();
                    lightboxVideo.currentTime = 0;
                }
            }, 300);
        }

        function navigateLightbox(direction) {
            let newIndex = currentLightboxIndex + direction;

            if (newIndex < 0) {
                newIndex = galleryItems.length - 1;
            } else if (newIndex >= galleryItems.length) {
                newIndex = 0;
            }

            openLightbox(newIndex);
        }

        // Format date for display
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.style.display === 'block') {
                switch(e.key) {
                    case 'Escape':
                        closeLightbox();
                        break;
                    case 'ArrowLeft':
                        navigateLightbox(-1);
                        break;
                    case 'ArrowRight':
                        navigateLightbox(1);
                        break;
                }
            }
        });

        // Close lightbox when clicking outside
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Preload images for better user experience
        window.addEventListener('load', function() {
            galleryItems.forEach(item => {
                if (item.media_type === 'image') {
                    const img = new Image();
                    img.src = item.image_path;
                }
            });
        });
    </script>
</body>
</html>
