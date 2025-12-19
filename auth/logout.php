<?php
/**
 * Logout Handler
 */
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getConnection();
        logActivity($pdo, $_SESSION['user_id'], 'logout', 'User logged out');
    } catch (Exception $e) {
        // Silent fail for logging
    }
}

// Destroy session
session_unset();
session_destroy();

// Redirect to home
header("Location: ../index.php");
exit();
