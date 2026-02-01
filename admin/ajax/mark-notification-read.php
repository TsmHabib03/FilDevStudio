<?php
/**
 * AJAX endpoint to mark admin notification as read
 */
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/mail.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;

if ($notificationId > 0) {
    try {
        $pdo = getConnection();
        $result = markNotificationRead($pdo, $notificationId);
        echo json_encode(['success' => $result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid notification ID']);
}
