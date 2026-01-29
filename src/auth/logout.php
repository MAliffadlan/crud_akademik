<?php
// src/auth/logout.php
require_once '../../config/database.php';
require_once '../../config/activity_log.php';

session_start();

if (isset($_SESSION['user_id'])) {
    logActivity('logout', 'User logged out', 'user', $_SESSION['user_id']);
}

session_destroy();
header("Location: /crud_akademik/login.php");
exit;
?>
