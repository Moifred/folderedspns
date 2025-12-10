<?php
session_start();
require_once '../includes/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: all_blogs.php");
    exit();
}

try {
    // Fetch the single blog
    $sql = "SELECT * FROM blogs WHERE id = ? AND is_published = 1 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        header("Location: all_blogs.php");
        exit();
    }

    // Fetch recent posts
    $recent_sql = "SELECT id, title FROM blogs WHERE is_published = 1 AND id != ? ORDER BY published_at DESC, created_at DESC LIMIT 5";
    $recent_stmt = $pdo->prepare($recent_sql);
    $recent_stmt->execute([$id]);
    $recent_posts = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all tags for sidebar
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

    // Fetch related posts based on shared tags
    $related_posts = [];
    if (!empty($blog['tags'])) {
        $tag_array = array_map('trim', explode(',', $blog['tags']));
        $tag_placeholders = implode(',', array_fill(0, count($tag_array), '?'));
        $related_sql = "SELECT id, title, featured_image FROM blogs
                        WHERE is_published = 1 AND id != ?
                        AND (" . implode(' OR ', array_map(fn($t) => "tags LIKE ?", $tag_array)) . ")
                        ORDER BY published_at DESC LIMIT 3";
        $related_stmt = $pdo->prepare($related_sql);
        $params = array_merge([$id], array_map(fn($t) => "%$t%", $tag_array));
        $related_stmt->execute($params);
        $related_posts = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: all_blogs.php");
    exit();
}

// Function for reading time
function estimateReadingTime($content) {
    $word_count = str_word_count(strip_tags($content));
    return max(1, ceil($word_count / 200));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - St. Philip Neri School Blog</title>
    <meta name="description" content="<?php echo htmlspecialchars($blog['excerpt'] ?? 'Read this insightful post from St. Philip Neri School'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #1a365d;
            --color-accent: #e53e3e;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-surface: #f7fafc;
            --border-radius: 12px;
            --shadow-md: 0 8px 25px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--color-text);
            background: white;
            margin: 0;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 2rem 80px;
            text-align: center;
            margin-top: 80px;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            margin-bottom: 0.5rem;
        }

        .hero-meta {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.9);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 3rem;
        }

        .blog-content {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }

        .featured-image {
            width: 100%;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            object-fit: cover;
        }

        .blog-body {
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .blog-body p {
            margin-bottom: 1.2rem;
        }

        .blog-tags {
            margin-top: 2rem;
        }

        .blog-tag {
            background: var(--color-surface);
            color: var(--color-text);
            text-decoration: none;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            margin-right: 0.5rem;
            transition: 0.3s;
        }

        .blog-tag:hover {
            background: var(--color-accent);
            color: white;
        }

        .related-posts {
            margin-top: 4rem;
        }

        .related-posts h3 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .related-card {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            background: white;
            text-decoration: none;
            color: var(--color-text);
            transition: 0.3s;
        }

        .related-card:hover {
            transform: translateY(-5px);
        }

        .related-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .related-card h4 {
            padding: 1rem;
            font-size: 1rem;
            font-family: 'Playfair Display', serif;
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
            border-radius: var(--border-radius);
        }

        .widget-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--color-primary);
        }

        .tag-cloud a {
            display: inline-block;
            background: white;
            color: var(--color-text);
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.85rem;
            text-decoration: none;
            margin: 0.2rem;
            transition: 0.3s;
        }

        .tag-cloud a:hover {
            background: var(--color-accent);
            color: white;
        }

        .recent-posts a {
            display: block;
            text-decoration: none;
            color: var(--color-text);
            padding: 0.4rem 0;
            transition: 0.3s;
        }

        .recent-posts a:hover {
            color: var(--color-accent);
        }

        @media(max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<section class="hero-section">
    <h1 class="hero-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
    <div class="hero-meta">
        By <?php echo htmlspecialchars($blog['author']); ?> |
        <?php echo date('M j, Y', strtotime($blog['published_at'] ?? $blog['created_at'])); ?> |
        <?php echo estimateReadingTime($blog['content']); ?> min read
    </div>
</section>

<div class="container">
    <article class="blog-content">
        <?php if (!empty($blog['featured_image'])): ?>
            <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" class="featured-image" alt="Featured Image">
        <?php endif; ?>

        <div class="blog-body">
            <?php echo nl2br($blog['content']); ?>
        </div>

        <?php if (!empty($blog['tags'])): ?>
            <div class="blog-tags">
                <strong>Tags:</strong>
                <?php foreach (explode(',', $blog['tags']) as $tag): ?>
                    <a href="all_blogs.php?tag=<?php echo urlencode(trim($tag)); ?>" class="blog-tag">
                        <?php echo htmlspecialchars(trim($tag)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Related Posts -->
        <?php if (!empty($related_posts)): ?>
        <div class="related-posts">
            <h3>Related Posts</h3>
            <div class="related-grid">
                <?php foreach ($related_posts as $post): ?>
                    <a href="blog_single.php?id=<?php echo $post['id']; ?>" class="related-card">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="">
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($post['title']); ?></h4>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </article>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-widget">
            <h3 class="widget-title">Recent Posts</h3>
            <div class="recent-posts">
                <?php foreach ($recent_posts as $recent): ?>
                    <a href="blog_single.php?id=<?php echo $recent['id']; ?>">
                        <i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($recent['title']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sidebar-widget">
            <h3 class="widget-title">Tags</h3>
            <div class="tag-cloud">
                <?php foreach ($all_tags as $tag): ?>
                    <a href="all_blogs.php?tag=<?php echo urlencode($tag); ?>"><?php echo htmlspecialchars($tag); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
