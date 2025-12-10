<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'edit') {
            // Create or update testimonial
            $name = trim($_POST['name']);
            $role = trim($_POST['role']);
            $content = trim($_POST['content']);
            $rating = intval($_POST['rating']);
            $is_approved = isset($_POST['is_approved']) ? 1 : 0;

            // Handle image upload
            $image_path = '';
            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/testimonials/';

                // Create uploads directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_extension = pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    // Generate unique filename
                    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_FILES['image_path']['name']);
                    $file_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['image_path']['tmp_name'], $file_path)) {
                        $image_path = $file_path;
                    } else {
                        $error = 'Failed to upload image';
                    }
                } else {
                    $error = 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.';
                }
            }

            if (empty($name) || empty($content)) {
                $error = 'Name and content are required';
            } else if (empty($error)) {
                if ($action === 'create') {
                    $sql = "INSERT INTO testimonials (name, role, content, image_path, rating, is_approved)
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$name, $role, $content, $image_path, $rating, $is_approved]);

                    $testimonial_id = $pdo->lastInsertId();
                    logAction('testimonial_create', "Created testimonial for: $name", $testimonial_id);
                    $success = 'Testimonial created successfully!';

                } elseif ($action === 'edit') {
                    $testimonial_id = $_POST['testimonial_id'];

                    // Get current data to preserve existing image if not updated
                    $current_sql = "SELECT image_path FROM testimonials WHERE id = ?";
                    $current_stmt = $pdo->prepare($current_sql);
                    $current_stmt->execute([$testimonial_id]);
                    $current = $current_stmt->fetch(PDO::FETCH_ASSOC);

                    // If no new image uploaded, keep the existing one
                    if (empty($image_path) && !empty($current['image_path'])) {
                        $image_path = $current['image_path'];
                    }

                    $sql = "UPDATE testimonials SET name = ?, role = ?, content = ?, image_path = ?, rating = ?, is_approved = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$name, $role, $content, $image_path, $rating, $is_approved, $testimonial_id]);

                    logAction('testimonial_edit', "Updated testimonial for: $name", $testimonial_id);
                    $success = 'Testimonial updated successfully!';
                }
            }

        } elseif ($action === 'delete') {
            // Delete testimonial
            $testimonial_id = $_POST['testimonial_id'];

            // Get testimonial data to delete image
            $check_sql = "SELECT name, image_path FROM testimonials WHERE id = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$testimonial_id]);
            $testimonial = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if ($testimonial) {
                // Delete image file if exists
                if (!empty($testimonial['image_path']) && file_exists($testimonial['image_path'])) {
                    unlink($testimonial['image_path']);
                }

                $sql = "DELETE FROM testimonials WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$testimonial_id]);

                logAction('testimonial_delete', "Deleted testimonial for: " . $testimonial['name'], $testimonial_id);
                $success = 'Testimonial deleted successfully!';
            } else {
                $error = 'Testimonial not found';
            }
        }

    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Handle toggle approval status
if (isset($_GET['toggle_approval']) && isset($_GET['id'])) {
    $testimonial_id = $_GET['id'];

    try {
        $sql = "UPDATE testimonials SET is_approved = NOT is_approved WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$testimonial_id]);

        // Get name for logging
        $name_sql = "SELECT name FROM testimonials WHERE id = ?";
        $name_stmt = $pdo->prepare($name_sql);
        $name_stmt->execute([$testimonial_id]);
        $testimonial = $name_stmt->fetch(PDO::FETCH_ASSOC);

        logAction('testimonial_toggle', "Toggled approval status for: " . $testimonial['name'], $testimonial_id);
        $success = 'Testimonial approval status updated!';

    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Handle image deletion
if (isset($_GET['delete_image']) && isset($_GET['id'])) {
    $testimonial_id = $_GET['id'];

    try {
        // Get current image path
        $current_sql = "SELECT image_path FROM testimonials WHERE id = ?";
        $current_stmt = $pdo->prepare($current_sql);
        $current_stmt->execute([$testimonial_id]);
        $current = $current_stmt->fetch(PDO::FETCH_ASSOC);

        if ($current && !empty($current['image_path'])) {
            // Delete image file
            if (file_exists($current['image_path'])) {
                unlink($current['image_path']);
            }

            // Update database
            $sql = "UPDATE testimonials SET image_path = '' WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$testimonial_id]);

            logAction('testimonial_image_delete', "Deleted image for testimonial ID: $testimonial_id", $testimonial_id);
            $success = 'Image deleted successfully!';
        }

    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get all testimonials
try {
    $sql = "SELECT * FROM testimonials ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get statistics
    $total_testimonials = count($testimonials);
    $approved_testimonials = array_filter($testimonials, function($testimonial) {
        return $testimonial['is_approved'];
    });
    $pending_testimonials = $total_testimonials - count($approved_testimonials);

} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    $testimonials = [];
    $total_testimonials = 0;
    $approved_testimonials = 0;
    $pending_testimonials = 0;
}

// Get testimonial data for editing
$edit_testimonial = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $testimonial_id = $_GET['edit'];

    try {
        $sql = "SELECT * FROM testimonials WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$testimonial_id]);
        $edit_testimonial = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - St. Philip Neri School</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-red: #dc2626;
            --color-green: #16a34a;
            --color-blue: #3b82f6;
            --color-purple: #8b5cf6;
            --color-yellow: #f59e0b;
            --color-orange: #ea580c;
            --color-black: #000000;
            --color-white: #ffffff;
            --color-gray-light: #f8fafc;
            --color-gray: #64748b;
            --color-gray-dark: #1e293b;

            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-blue: linear-gradient(135deg, var(--color-blue), #6366f1);
            --gradient-purple: linear-gradient(135deg, var(--color-purple), #a855f7);

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.2);

            --border-radius: 8px;
            --border-radius-lg: 12px;

            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: var(--color-gray-dark);
            line-height: 1.6;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: var(--color-white);
            box-shadow: var(--shadow-md);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--gradient-primary)' : 'var(--gradient-blue)'; ?>;
            color: var(--color-white);
        }

        .sidebar-header h1 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .menu-section-title {
            padding: 0 20px 10px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--color-gray-dark);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background: #f8fafc;
            color: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--color-red)' : 'var(--color-blue)'; ?>;
            border-left-color: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--color-red)' : 'var(--color-blue)'; ?>;
        }

        .menu-item.active {
            background: <?php echo $_SESSION['role'] === 'superadmin' ? '#fef2f2' : '#eff6ff'; ?>;
            color: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--color-red)' : 'var(--color-blue)'; ?>;
            border-left-color: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--color-red)' : 'var(--color-blue)'; ?>;
            font-weight: 600;
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
        }

        /* Top Navigation */
        .top-nav {
            background: var(--color-white);
            padding: 15px 25px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-gray-dark);
        }

        .page-title p {
            color: var(--color-gray);
            font-size: 0.9rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--color-gray-dark);
        }

        .user-role {
            font-size: 0.8rem;
            color: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--color-red)' : 'var(--color-blue)'; ?>;
            text-transform: capitalize;
            font-weight: 600;
        }

        .logout-btn {
            background: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--gradient-primary)' : 'var(--gradient-blue)'; ?>;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .logout-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--color-white);
            padding: 20px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            text-align: center;
            border-left: 4px solid;
        }

        .stat-card.total { border-left-color: var(--color-blue); }
        .stat-card.approved { border-left-color: var(--color-green); }
        .stat-card.pending { border-left-color: var(--color-orange); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--color-gray);
            font-size: 0.9rem;
        }

        /* Content Area */
        .content-area {
            display: grid;
            grid-template-columns: <?php echo $edit_testimonial ? '1fr 1fr' : '1fr'; ?>;
            gap: 25px;
        }

        .card {
            background: var(--color-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--color-gray-dark);
        }

        .card-body {
            padding: 25px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--color-gray-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            transition: var(--transition);
            background: var(--color-white);
            font-family: inherit;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--color-red)' : 'var(--color-blue)'; ?>;
            box-shadow: 0 0 0 3px <?php echo $_SESSION['role'] === 'superadmin' ? 'rgba(220, 38, 38, 0.1)' : 'rgba(59, 130, 246, 0.1)'; ?>;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--gradient-primary)' : 'var(--gradient-blue)'; ?>;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-success {
            background: var(--color-green);
            color: white;
        }

        .btn-danger {
            background: var(--color-red);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #e5e7eb;
            color: var(--color-gray-dark);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 0.8rem;
        }

        /* Testimonials Grid */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .testimonial-card {
            background: var(--color-white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border-left: 4px solid var(--color-blue);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .testimonial-header {
            padding: 1.5rem;
            background: var(--color-surface);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--color-white);
            box-shadow: var(--shadow-sm);
        }

        .testimonial-info {
            flex: 1;
        }

        .testimonial-name {
            font-weight: 700;
            color: var(--color-gray-dark);
            margin-bottom: 0.25rem;
        }

        .testimonial-role {
            color: var(--color-gray);
            font-size: 0.9rem;
        }

        .testimonial-rating {
            color: var(--color-yellow);
            font-size: 0.9rem;
        }

        .testimonial-content {
            padding: 1.5rem;
            color: var(--color-text);
            font-style: italic;
            line-height: 1.6;
        }

        .testimonial-meta {
            padding: 1rem 1.5rem;
            background: var(--color-surface);
            border-top: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--color-gray);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-approved { background: #dcfce7; color: #16a34a; }
        .status-pending { background: #fef3c7; color: #d97706; }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        /* Messages */
        .alert {
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* File Upload Styles */
        .file-upload-container {
            margin-bottom: 20px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-btn {
            background: var(--color-purple);
            color: white;
            padding: 12px 15px;
            border-radius: var(--border-radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
            justify-content: center;
            border: 2px dashed var(--color-purple);
        }

        .upload-btn:hover {
            background: #7c3aed;
        }

        .file-preview {
            margin-top: 10px;
            display: <?php echo ($edit_testimonial && !empty($edit_testimonial['image_path'])) ? 'block' : 'none'; ?>;
        }

        .preview-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .preview-item {
            position: relative;
            border: 2px solid var(--color-border);
            border-radius: var(--border-radius);
            padding: 10px;
            background: var(--color-surface);
        }

        .preview-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }

        .preview-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }

        .preview-actions .btn-sm {
            padding: 4px 8px;
        }

        .current-file {
            font-size: 0.85rem;
            color: var(--color-gray);
            margin-top: 5px;
        }

        /* Rating Stars */
        .rating-stars {
            display: flex;
            gap: 5px;
            margin: 10px 0;
        }

        .rating-star {
            color: #e5e7eb;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.5rem;
        }

        .rating-star:hover,
        .rating-star.active {
            color: var(--color-yellow);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .content-area {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar-header h1,
            .sidebar-header p,
            .menu-section-title,
            .menu-item span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .top-nav {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .user-info {
                text-align: center;
            }

            .form-actions {
                flex-direction: column;
            }

            .testimonial-header {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: var(--color-green);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .toggle-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1><?php echo $_SESSION['role'] === 'superadmin' ? 'Super Admin' : 'Admin'; ?> Panel</h1>
                <p>St. Philip Neri School</p>
            </div>

            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Main</div>
                    <a href="<?php echo $_SESSION['role'] === 'superadmin' ? 'superadmin_dashboard.php' : 'admin_dashboard.php'; ?>" class="menu-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Content Management</div>
                    <a href="blogs.php" class="menu-item">
                        <i class="fas fa-blog"></i>
                        <span>Blogs</span>
                    </a>
                    <a href="carousel.php" class="menu-item">
                        <i class="fas fa-images"></i>
                        <span>Carousel</span>
                    </a>
                    <a href="events.php" class="menu-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                    <a href="testimonials.php" class="menu-item active">
                        <i class="fas fa-comments"></i>
                        <span>Testimonials</span>
                    </a>
                    <a href="gallery.php" class="menu-item">
                        <i class="fas fa-images"></i>
                        <span>Gallery</span>
                    </a>
                </div>

                <?php if ($_SESSION['role'] === 'superadmin'): ?>
                <div class="menu-section">
                    <div class="menu-section-title">System</div>
                    <a href="users.php" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                    <a href="settings.php" class="menu-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="logs.php" class="menu-item">
                        <i class="fas fa-clipboard-list"></i>
                        <span>System Logs</span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <div class="top-nav">
                <div class="page-title">
                    <h1>Manage Testimonials</h1>
                    <p>Create and manage student and parent testimonials</p>
                </div>

                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <div class="user-role"><?php echo htmlspecialchars($_SESSION['role']); ?></div>
                    </div>
                    <form method="POST" action="logout.php" style="display: inline;">
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-number"><?php echo $total_testimonials; ?></div>
                    <div class="stat-label">Total Testimonials</div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-number"><?php echo count($approved_testimonials); ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-number"><?php echo $pending_testimonials; ?></div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Testimonials Grid -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Testimonials</div>
                        <a href="testimonials.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Testimonial
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($testimonials)): ?>
                            <div class="testimonials-grid">
                                <?php foreach ($testimonials as $testimonial): ?>
                                    <div class="testimonial-card">
                                        <div class="testimonial-header">
                                            <?php if (!empty($testimonial['image_path'])): ?>
                                                <img src="<?php echo htmlspecialchars($testimonial['image_path']); ?>"
                                                     alt="<?php echo htmlspecialchars($testimonial['name']); ?>"
                                                     class="testimonial-avatar">
                                            <?php else: ?>
                                                <div class="testimonial-avatar" style="background: var(--gradient-blue); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                    <?php echo strtoupper(substr($testimonial['name'], 0, 2)); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="testimonial-info">
                                                <div class="testimonial-name"><?php echo htmlspecialchars($testimonial['name']); ?></div>
                                                <div class="testimonial-role"><?php echo htmlspecialchars($testimonial['role']); ?></div>
                                                <div class="testimonial-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star<?php echo $i <= $testimonial['rating'] ? '' : '-empty'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="testimonial-content">
                                            "<?php echo htmlspecialchars($testimonial['content']); ?>"
                                        </div>
                                        <div class="testimonial-meta">
                                            <span class="status-badge status-<?php echo $testimonial['is_approved'] ? 'approved' : 'pending'; ?>">
                                                <?php echo $testimonial['is_approved'] ? 'Approved' : 'Pending'; ?>
                                            </span>
                                            <span><?php echo date('M j, Y', strtotime($testimonial['created_at'])); ?></span>
                                        </div>
                                        <div class="action-buttons" style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border);">
                                            <a href="testimonials.php?edit=<?php echo $testimonial['id']; ?>" class="btn btn-outline btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="testimonials.php?toggle_approval=1&id=<?php echo $testimonial['id']; ?>" class="btn btn-<?php echo $testimonial['is_approved'] ? 'secondary' : 'success'; ?> btn-sm">
                                                <i class="fas fa-<?php echo $testimonial['is_approved'] ? 'times' : 'check'; ?>"></i>
                                                <?php echo $testimonial['is_approved'] ? 'Unapprove' : 'Approve'; ?>
                                            </a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: var(--color-gray);">
                                <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                                <p>No testimonials found. Create your first testimonial!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Testimonial Form -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><?php echo $edit_testimonial ? 'Edit Testimonial' : 'Create New Testimonial'; ?></div>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?php echo $edit_testimonial ? 'edit' : 'create'; ?>">
                            <?php if ($edit_testimonial): ?>
                                <input type="hidden" name="testimonial_id" value="<?php echo $edit_testimonial['id']; ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label" for="name">Name *</label>
                                <input type="text" id="name" name="name" class="form-input" required
                                       value="<?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['name']) : ''; ?>"
                                       placeholder="Enter person's name">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="role">Role/Position</label>
                                <input type="text" id="role" name="role" class="form-input"
                                       value="<?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['role']) : ''; ?>"
                                       placeholder="e.g., Student, Parent, Alumni">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="content">Testimonial Content *</label>
                                <textarea id="content" name="content" class="form-textarea" required
                                          placeholder="What did they say about our school?"><?php echo $edit_testimonial ? htmlspecialchars($edit_testimonial['content']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Rating</label>
                                <div class="rating-stars" id="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star rating-star <?php echo ($edit_testimonial && $i <= $edit_testimonial['rating']) ? 'active' : ''; ?>"
                                           data-rating="<?php echo $i; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" id="rating" name="rating" value="<?php echo $edit_testimonial ? $edit_testimonial['rating'] : 5; ?>">
                            </div>

                            <!-- Image Upload -->
                            <div class="form-group">
                                <label class="form-label">Profile Image</label>
                                <div class="file-upload-container">
                                    <?php if ($edit_testimonial && !empty($edit_testimonial['image_path'])): ?>
                                        <div class="current-file">
                                            <strong>Current Image:</strong>
                                            <?php echo basename($edit_testimonial['image_path']); ?>
                                        </div>
                                        <div class="file-preview">
                                            <div class="preview-container">
                                                <div class="preview-item">
                                                    <img src="<?php echo htmlspecialchars($edit_testimonial['image_path']); ?>" alt="Image Preview">
                                                    <div class="preview-actions">
                                                        <a href="testimonials.php?delete_image=1&id=<?php echo $edit_testimonial['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="file-input-wrapper">
                                        <div class="upload-btn">
                                            <i class="fas fa-upload"></i>
                                            Choose Profile Image
                                        </div>
                                        <input type="file" id="image_path" name="image_path" accept="image/*" onchange="previewImage(this, 'image-preview')">
                                    </div>
                                    <div class="file-preview" id="image-preview-container" style="display: none;">
                                        <div class="preview-container">
                                            <div class="preview-item">
                                                <img id="image-preview" src="" alt="Image Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                            </div>
                                        </div>
                                    </div>
                                    <small style="display: block; margin-top: 5px; color: var(--color-gray);">
                                        Supported formats: JPG, PNG, GIF, WebP. Max file size: 5MB.
                                    </small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="toggle-label">
                                    <input type="checkbox" name="is_approved" value="1"
                                           <?php echo ($edit_testimonial && $edit_testimonial['is_approved']) || !$edit_testimonial ? 'checked' : ''; ?>>
                                    <span class="toggle-switch">
                                        <span class="toggle-slider"></span>
                                    </span>
                                    Approved for Display
                                </label>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <?php echo $edit_testimonial ? 'Update Testimonial' : 'Create Testimonial'; ?>
                                </button>
                                <?php if ($edit_testimonial): ?>
                                    <a href="testimonials.php" class="btn btn-outline">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Rating stars functionality
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-star');
            const ratingInput = document.getElementById('rating');

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    ratingInput.value = rating;

                    // Update star display
                    stars.forEach(s => {
                        if (s.getAttribute('data-rating') <= rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });

                star.addEventListener('mouseover', function() {
                    const rating = this.getAttribute('data-rating');

                    stars.forEach(s => {
                        if (s.getAttribute('data-rating') <= rating) {
                            s.style.color = 'var(--color-yellow)';
                        } else {
                            s.style.color = '#e5e7eb';
                        }
                    });
                });

                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value;

                    stars.forEach(s => {
                        if (s.getAttribute('data-rating') <= currentRating) {
                            s.style.color = 'var(--color-yellow)';
                        } else {
                            s.style.color = '#e5e7eb';
                        }
                    });
                });
            });
        });

        // Image preview functionality
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById('image-preview-container');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);

                // Validate file
                validateImageFile(input.files[0]);
            } else {
                preview.src = '';
                previewContainer.style.display = 'none';
            }
        }

        // File validation
        function validateImageFile(file) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            const fileSize = file.size / 1024 / 1024; // MB

            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF, or WebP).');
                document.getElementById('image_path').value = '';
                return;
            }

            if (fileSize > 5) {
                alert('File size must be less than 5MB.');
                document.getElementById('image_path').value = '';
                return;
            }
        }

        // Add event listener for file input
        document.getElementById('image_path').addEventListener('change', function() {
            previewImage(this, 'image-preview');
        });

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('button');
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.cssText = `
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: <?php echo $_SESSION['role'] === 'superadmin' ? 'var(--gradient-primary)' : 'var(--gradient-blue)'; ?>;
                color: white;
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 8px;
                font-size: 1.2rem;
                cursor: pointer;
                display: none;
            `;

            document.body.appendChild(menuToggle);

            // Show/hide based on screen size
            function checkScreenSize() {
                if (window.innerWidth <= 480) {
                    menuToggle.style.display = 'flex';
                    menuToggle.style.alignItems = 'center';
                    menuToggle.style.justifyContent = 'center';
                } else {
                    menuToggle.style.display = 'none';
                }
            }

            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);

            // Toggle sidebar
            menuToggle.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        });
    </script>
</body>
</html>
