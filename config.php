<?php
// config.php
// Start session for flash messages & auth
session_start();

// Simple flash helper
function flash_set($key, $msg) {
    $_SESSION['flash'][$key] = $msg;
}
function flash_get($key) {
    if (!empty($_SESSION['flash'][$key])) {
        $m = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    return null;
}

// DB connection (PDO)
$host = 'localhost';
$db   = 'blood_donation';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}

// Helper: require login / role
function require_role($role = 'user') {
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: login.php');
        exit;
    }
}
function require_logged_in() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
