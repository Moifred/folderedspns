<?php
session_start();
require_once 'config.php';

// Fetch terms of service content
try {
    $terms_sql = "SELECT * FROM terms_of_service WHERE is_active = 1 ORDER BY updated_at DESC LIMIT 1";
    $terms_stmt = $pdo->query($terms_sql);
    $terms_content = $terms_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $terms_content = [];
}

// Fetch terms documents
try {
    $documents_sql = "SELECT * FROM terms_documents WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC";
    $documents_stmt = $pdo->query($documents_sql);
    $documents = $documents_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $documents = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - St. Philip Neri School</title>
    <meta name="description" content="Read our terms of service and download related documents.">
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

        /* Content Section */
        .content-section {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            padding: 3rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow-md);
        }

        .content-body {
            line-height: 1.8;
            color: var(--color-text);
        }

        .content-body h2 {
            font-family: 'Playfair Display', serif;
            color: var(--color-primary);
            margin: 2rem 0 1rem;
            font-size: 1.8rem;
        }

        .content-body h3 {
            color: var(--color-secondary);
            margin: 1.5rem 0 0.75rem;
            font-size: 1.3rem;
        }

        .content-body p {
            margin-bottom: 1rem;
        }

        .content-body ul, .content-body ol {
            margin: 1rem 0 1rem 2rem;
        }

        .content-body li {
            margin-bottom: 0.5rem;
        }

        .last-updated {
            text-align: right;
            color: var(--color-text-light);
            font-style: italic;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--color-border);
        }

        /* Documents Section */
        .documents-section {
            background: var(--color-surface);
            padding: 3rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 3rem;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .document-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .document-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .document-icon {
            width: 50px;
            height: 50px;
            background: var(--color-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .document-icon i {
            color: white;
            font-size: 1.25rem;
        }

        .document-content {
            flex-grow: 1;
        }

        .document-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .document-description {
            color: var(--color-text-light);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .document-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--color-text-light);
        }

        .document-download {
            background: var(--color-primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .document-download:hover {
            background: var(--color-accent);
            transform: translateY(-2px);
        }

        /* Contact Section */
        .contact-section {
            background: var(--gradient-hero);
            color: white;
            padding: 3rem;
            border-radius: var(--border-radius-lg);
            text-align: center;
        }

        .contact-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .contact-description {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }

        .contact-button {
            display: inline-block;
            background: var(--color-accent);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .contact-button:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .content-section,
            .documents-section {
                padding: 2rem 1.5rem;
            }

            .documents-grid {
                grid-template-columns: 1fr;
            }

            .document-card {
                flex-direction: column;
                text-align: center;
            }

            .document-meta {
                flex-direction: column;
                gap: 1rem;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .hero-section {
                padding: 100px 1rem 60px;
            }

            .content-section,
            .documents-section,
            .contact-section {
                padding: 1.5rem 1rem;
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
            <h1 class="hero-title">Terms of Service</h1>
            <p class="hero-subtitle">Understanding our policies, procedures, and community guidelines</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Terms of Service Content -->
        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">Terms & Conditions</h2>
                <p class="section-subtitle">Policies governing the use of our services and facilities</p>
            </div>

            <div class="content-body">
                <?php if (!empty($terms_content)): ?>
                    <?php echo $terms_content['content']; ?>

                    <?php if (!empty($terms_content['updated_at'])): ?>
                        <div class="last-updated">
                            Last updated: <?php echo date('F j, Y', strtotime($terms_content['updated_at'])); ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-file-contract" style="font-size: 4rem; color: var(--color-text-light); margin-bottom: 1rem;"></i>
                        <h3>Terms of Service Coming Soon</h3>
                        <p>We're currently updating our terms of service. Please check back later or contact us for more information.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Documents Section -->
        <section class="documents-section">
            <div class="section-header">
                <h2 class="section-title">Policy Documents</h2>
                <p class="section-subtitle">Download our official policy documents and forms</p>
            </div>

            <?php if (!empty($documents)): ?>
                <div class="documents-grid">
                    <?php foreach ($documents as $document): ?>
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="document-content">
                                <h3 class="document-title"><?php echo htmlspecialchars($document['title']); ?></h3>
                                <?php if (!empty($document['description'])): ?>
                                    <p class="document-description"><?php echo htmlspecialchars($document['description']); ?></p>
                                <?php endif; ?>
                                <div class="document-meta">
                                    <span>File size: <?php echo htmlspecialchars($document['file_size']); ?></span>
                                    <a href="<?php echo htmlspecialchars($document['file_path']); ?>"
                                       class="document-download"
                                       download>
                                        <i class="fas fa-download"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem;">
                    <p>No documents available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Contact Section -->
        <section class="contact-section">
            <h2 class="contact-title">Questions About Our Terms?</h2>
            <p class="contact-description">If you have any questions about our terms of service or school policies, please don't hesitate to contact us.</p>
            <a href="contact.php" class="contact-button">Contact Us</a>
        </section>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to document cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            // Animate document cards
            document.querySelectorAll('.document-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
