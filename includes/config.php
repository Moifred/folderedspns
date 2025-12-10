<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'fytfkmpe_spnweb2');
define('DB_PASSWORD', 'Fredoh-333');
define('DB_NAME', 'fytfkmpe_spnweb2');

// Website configuration
define('SITE_NAME', 'St. Philip Neri School');
define('SITE_MOTTO', 'Together we Achieve the extraordinary');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// Color theme
define('COLOR_RED', '#dc2626');
define('COLOR_GREEN', '#16a34a');
define('COLOR_BLACK', '#000000');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
session_start();

// Function to log system actions
function logAction($action, $details = '') {
    global $pdo;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $sql = "INSERT INTO system_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $action, $details, $ip_address, $user_agent]);
}

// Function to get testimonials
function getTestimonials($limit = 3) {
    global $pdo;
    $sql = "SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get carousel items
function getCarouselItems($page_type = 'homepage') {
    global $pdo;
    $sql = "SELECT * FROM carousel WHERE is_active = 1 AND page_type = ? ORDER BY display_order ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$page_type]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get recent blogs
function getRecentBlogs($limit = 6) {
    global $pdo;
    $sql = "SELECT * FROM blogs WHERE is_published = 1 ORDER BY published_at DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get recent events
function getRecentEvents($limit = 4) {
    global $pdo;
    $sql = "SELECT * FROM events WHERE is_active = 1 ORDER BY event_date ASC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Kindergarten specific functions
function getKindergartenHeadMessage() {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT message FROM kindergarten_head_message WHERE active = 1 ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['message'] : '';
    } catch (Exception $e) {
        error_log("Error getting kindergarten head message: " . $e->getMessage());
        return '';
    }
}

function getKindergartenGallery($limit = 8) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT image_path as path, caption FROM kindergarten_gallery WHERE active = 1 ORDER BY display_order LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error getting kindergarten gallery: " . $e->getMessage());
        return [];
    }
}
// Get all co-curricular programs
function getCoCurricularItems() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM cocurricular_programs WHERE is_active = TRUE ORDER BY title");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get co-curricular categories
function getCoCurricularCategories() {
    return ['sports', 'arts', 'academic', 'leadership', 'technology'];
}
?>
