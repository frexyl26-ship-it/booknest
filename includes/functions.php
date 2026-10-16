<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/../config/database.php';

$db = (new Database())->getConnection();

/** Escape output (XSS prevention) */
function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

function url($path = '') {
    return BASE_URL . $path;
}

function redirect($path) {
    header("Location: " . url($path));
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        redirect('/auth/login.php');
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('/index.php');
    }
}

/** CSRF protection */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function verify_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Invalid request (CSRF check failed).');
    }
}

/** Flash messages */
function flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $cls = $f['type'] === 'success'
            ? 'bg-green-50 border-green-200 text-green-800'
            : 'bg-red-50 border-red-200 text-red-800';
        echo '<div class="border ' . $cls . ' rounded-lg p-3 mb-4 text-sm">' . e($f['message']) . '</div>';
    }
}
