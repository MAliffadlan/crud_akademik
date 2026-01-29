<?php
// config/functions.php

session_start();

require_once __DIR__ . '/routes.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has required role, otherwise redirect appropriately
 */
function requireRole($allowed_roles) {
    if (!isLoggedIn()) {
        header('Location: /crud_akademik/index.php?error=Silakan login terlebih dahulu');
        exit;
    }
    
    // If string, convert to array
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    $userRole = $_SESSION['role'];
    
    if (!in_array($userRole, $allowed_roles)) {
        // Redirect to user's own dashboard instead of showing "Access Denied"
        $dashboardUrl = getDashboardUrl($userRole);
        header("Location: $dashboardUrl?error=Anda tidak memiliki akses ke halaman tersebut");
        exit;
    }
}

/**
 * Redirect to path with base URL
 */
function redirect($path) {
    header("Location: /crud_akademik/" . $path);
    exit;
}

/**
 * Redirect user to their dashboard based on role
 */
function redirectToDashboard() {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
    
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin': redirect('src/admin/dashboard.php'); break;
        case 'marketing': redirect('src/marketing/dashboard_staff.php'); break;
        case 'manager': redirect('src/marketing/dashboard_manager.php'); break;
        case 'academic': redirect('src/academic/dashboard.php'); break;
        case 'student': redirect('src/student/dashboard.php'); break;
        default: redirect('index.php');
    }
}

/**
 * Sanitize user input
 */
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

/**
 * Check if user is already logged in, redirect to dashboard if so
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        redirectToDashboard();
    }
}

/**
 * Get current user's role
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? 'guest';
}

/**
 * Get current user's name
 */
function getCurrentUserName() {
    return $_SESSION['full_name'] ?? 'Guest';
}
?>
