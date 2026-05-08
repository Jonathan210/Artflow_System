<?php
// config/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define BASE_URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/ArtfFlow_System');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function currentUserRole() {
    return $_SESSION['user_role'] ?? 'user';
}

function isAdmin() {
    return currentUserRole() === 'admin';
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}

function avatarUrl($filename) {
    if (!$filename || $filename === 'default-avatar.png') {
        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='gray'><circle cx='50' cy='50' r='50'/><text x='50' y='60' font-size='30' fill='white' text-anchor='middle'>A</text></svg>";
    }
    return BASE_URL . '/assets/uploads/' . $filename;
}

function postImageUrl($filename) {
    return BASE_URL . '/assets/uploads/' . $filename;
}

function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $time);
}
function truncate($text, $limit = 100) {
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit) . '...';
}
