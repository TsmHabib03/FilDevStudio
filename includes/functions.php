<?php
/**
 * Common Functions
 * FilDevStudio Web Services Platform
 * 
 * Contains reusable helper functions
 */

/**
 * Sanitize user input
 * @param string $data Input to sanitize
 * @return string Sanitized input
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to another page
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login - redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please log in to access this page.';
        redirect('../auth/login.php');
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = 'Access denied. Admin privileges required.';
        redirect('../index.php');
    }
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Display alert messages
 * @param string $type Type of alert (success, error, warning, info)
 * @param string $message Message to display
 * @return string HTML alert element
 */
function displayAlert($type, $message) {
    $colors = [
        'success' => 'bg-green-100 border-green-500 text-green-700',
        'error' => 'bg-red-100 border-red-500 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-500 text-blue-700'
    ];
    
    $icons = [
        'success' => 'fa-check-circle',
        'error' => 'fa-exclamation-circle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle'
    ];
    
    $color = $colors[$type] ?? $colors['info'];
    $icon = $icons[$type] ?? $icons['info'];
    
    return "<div class='border-l-4 p-4 mb-4 $color' role='alert'>
                <p class='flex items-center'>
                    <i class='fas $icon mr-2'></i>
                    " . htmlspecialchars($message) . "
                </p>
            </div>";
}

/**
 * Handle file upload
 * @param array $file $_FILES array element
 * @param string $destination Upload destination folder
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array Result with success status and message/path
 */
function handleFileUpload($file, $destination, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], $maxSize = 5242880) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File is too large. Maximum size is ' . ($maxSize / 1048576) . 'MB.'];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type.'];
    }
    
    // Create destination folder if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $destination . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $filepath];
    }
    
    return ['success' => false, 'message' => 'Failed to save file.'];
}

/**
 * Get status badge HTML
 * @param string $status Status value
 * @return string HTML badge element
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'draft' => 'bg-gray-100 text-gray-800',
        'active' => 'bg-green-100 text-green-800',
        'suspended' => 'bg-red-100 text-red-800'
    ];
    
    $class = $badges[$status] ?? 'bg-gray-100 text-gray-800';
    $label = ucfirst(str_replace('_', ' ', $status));
    
    return "<span class='px-2 py-1 text-xs font-semibold rounded-full $class'>$label</span>";
}

/**
 * Log activity
 * @param PDO $pdo Database connection
 * @param int|null $userId User ID
 * @param string $action Action performed
 * @param string $description Description of the action
 */
function logActivity($pdo, $userId, $action, $description) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $action, $description, $ip]);
}

/**
 * Format date for display
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 * @param string $datetime Datetime string
 * @return string Time ago string
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return formatDate($datetime);
}
?>
