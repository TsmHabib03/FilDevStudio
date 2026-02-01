<?php
/**
 * FilDevStudio Email & Notification System
 * Uses PHPMailer with fallback to native SMTP or PHP mail()
 * 
 * SETUP: Download PHPMailer to includes/PHPMailer/ or use Composer
 *        Alternatively, this will use native PHP sockets for SMTP
 */

// Try to load PHPMailer
$phpmailerPath = __DIR__ . '/PHPMailer/';
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists($phpmailerPath . 'PHPMailer.php')) {
    require_once $phpmailerPath . 'PHPMailer.php';
    require_once $phpmailerPath . 'SMTP.php';
    require_once $phpmailerPath . 'Exception.php';
}

/**
 * Email Configuration - UPDATE THESE VALUES
 */
function getEmailConfig() {
    return [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_secure' => 'tls',
        'smtp_auth' => true,
        'smtp_username' => 'jaudianhabib879@gmail.com',   // Your Gmail address
        'smtp_password' => 'gxjkdamkdpgxrdiw',             // Gmail App Password (16 chars, no spaces)
        'from_email' => 'jaudianhabib879@gmail.com',      // Should match smtp_username for Gmail
        'from_name' => 'FilDevStudio',
        'admin_email' => 'jaudianhabib879@gmail.com',     // Fallback admin email
        'use_smtp' => true,                               // Set to true to use SMTP
        'base_url' => 'http://localhost/fildevstudio'
    ];
}

/**
 * Send email using PHPMailer, native SMTP, or fallback to PHP mail()
 * On localhost without proper mail config, emails are logged instead
 */
function sendEmail($to, $subject, $htmlBody, $plainBody = '') {
    $config = getEmailConfig();
    $lastError = '';
    
    // Try PHPMailer if available and SMTP is configured
    if (class_exists('PHPMailer\PHPMailer\PHPMailer') && $config['use_smtp']) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = $config['smtp_auth'];
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainBody ?: strip_tags($htmlBody);
            $mail->send();
            return ['success' => true, 'message' => 'Email sent via PHPMailer SMTP'];
        } catch (Exception $e) {
            $lastError = "PHPMailer: " . $e->getMessage();
            error_log("PHPMailer Error: " . $e->getMessage());
        }
    }
    
    // Try native SMTP (for Gmail) if configured
    if ($config['use_smtp']) {
        $result = sendEmailViaNativeSMTP($to, $subject, $htmlBody, $config);
        if ($result['success']) {
            return $result;
        }
        $lastError = "Native SMTP: " . $result['message'];
        error_log("Native SMTP failed: " . $result['message']);
    }
    
    // Try PHP mail() as fallback
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
        'Reply-To: ' . $config['admin_email'],
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $mailResult = @mail($to, $subject, $htmlBody, implode("\r\n", $headers));
    
    if ($mailResult) {
        return ['success' => true, 'message' => 'Email sent via PHP mail()'];
    }
    
    $lastError = $lastError ?: 'PHP mail() failed - no mail server configured';
    
    // All methods failed - return the actual error
    return [
        'success' => false,
        'message' => $lastError
    ];
}

/**
 * Native SMTP email sending (works without PHPMailer)
 * Supports Gmail with TLS/STARTTLS
 */
function sendEmailViaNativeSMTP($to, $subject, $htmlBody, $config) {
    $host = $config['smtp_host'];
    $port = $config['smtp_port'];
    $username = $config['smtp_username'];
    $password = $config['smtp_password'];
    $from = $config['from_email'];
    $fromName = $config['from_name'];
    
    // Build the email message
    $boundary = md5(uniqid(time()));
    $message = "MIME-Version: 1.0\r\n";
    $message .= "From: {$fromName} <{$from}>\r\n";
    $message .= "To: {$to}\r\n";
    $message .= "Subject: {$subject}\r\n";
    $message .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
    $message .= "\r\n";
    $message .= "--{$boundary}\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $message .= strip_tags($htmlBody) . "\r\n";
    $message .= "--{$boundary}\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    $message .= $htmlBody . "\r\n";
    $message .= "--{$boundary}--\r\n";
    
    try {
        // Connect to SMTP server
        $socket = @fsockopen($host, $port, $errno, $errstr, 30);
        if (!$socket) {
            return ['success' => false, 'message' => "Connection failed: {$errstr} ({$errno})"];
        }
        
        // Read greeting
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '220') {
            fclose($socket);
            return ['success' => false, 'message' => "Invalid greeting: {$response}"];
        }
        
        // EHLO
        fputs($socket, "EHLO localhost\r\n");
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        
        // STARTTLS for port 587
        if ($port == 587) {
            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) !== '220') {
                fclose($socket);
                return ['success' => false, 'message' => "STARTTLS failed: {$response}"];
            }
            
            // Upgrade to TLS
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                return ['success' => false, 'message' => "TLS encryption failed"];
            }
            
            // EHLO again after TLS
            fputs($socket, "EHLO localhost\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
        }
        
        // AUTH LOGIN
        fputs($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '334') {
            fclose($socket);
            return ['success' => false, 'message' => "AUTH LOGIN failed: {$response}"];
        }
        
        // Send username
        fputs($socket, base64_encode($username) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '334') {
            fclose($socket);
            return ['success' => false, 'message' => "Username rejected: {$response}"];
        }
        
        // Send password
        fputs($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '235') {
            fclose($socket);
            return ['success' => false, 'message' => "Authentication failed - check app password"];
        }
        
        // MAIL FROM
        fputs($socket, "MAIL FROM:<{$from}>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '250') {
            fclose($socket);
            return ['success' => false, 'message' => "MAIL FROM rejected: {$response}"];
        }
        
        // RCPT TO
        fputs($socket, "RCPT TO:<{$to}>\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '250') {
            fclose($socket);
            return ['success' => false, 'message' => "RCPT TO rejected: {$response}"];
        }
        
        // DATA
        fputs($socket, "DATA\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '354') {
            fclose($socket);
            return ['success' => false, 'message' => "DATA command rejected: {$response}"];
        }
        
        // Send message
        fputs($socket, $message . "\r\n.\r\n");
        $response = fgets($socket, 515);
        if (substr($response, 0, 3) !== '250') {
            fclose($socket);
            return ['success' => false, 'message' => "Message not accepted: {$response}"];
        }
        
        // QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        return ['success' => true, 'message' => 'Email sent via native SMTP'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'SMTP Error: ' . $e->getMessage()];
    }
}

/**
 * Notify admin about new design request
 */
function notifyAdminNewRequest($pdo, $requestData) {
    $config = getEmailConfig();
    
    // Get admin email(s) from database - more reliable than hardcoded config
    $adminEmails = [];
    try {
        $stmt = $pdo->query("SELECT email FROM users WHERE role = 'admin' LIMIT 5");
        while ($row = $stmt->fetch()) {
            $adminEmails[] = $row['email'];
        }
    } catch (Exception $e) {
        error_log("Failed to get admin emails: " . $e->getMessage());
    }
    
    // Fallback to config email if no admins found
    if (empty($adminEmails)) {
        $adminEmails = [$config['admin_email']];
    }
    
    $subject = "🎨 New Design Request: " . ($requestData['project_title'] ?? $requestData['site_name'] ?? 'New Project');
    $html = buildEmailTemplate('new_request', $requestData);
    
    // Send email to all admins
    $emailResult = ['success' => false, 'message' => 'No admins to notify'];
    foreach ($adminEmails as $adminEmail) {
        $result = sendEmail($adminEmail, $subject, $html);
        if ($result['success']) {
            $emailResult = $result;
        }
        error_log("Email to admin $adminEmail: " . ($result['success'] ? 'sent' : 'failed') . " - " . $result['message']);
    }
    
    // Store notification in database (this is critical - always do this even if email fails)
    $notifMessage = ($requestData['client_name'] ?? 'Client') . ': ';
    $notifMessage .= substr($requestData['description'] ?? $requestData['request_details'] ?? 'No description', 0, 100);
    if (strlen($requestData['description'] ?? $requestData['request_details'] ?? '') > 100) {
        $notifMessage .= '...';
    }
    
    $notifStored = storeAdminNotification($pdo, 
        'new_request',
        'New Request: ' . ($requestData['reference_number'] ?? 'N/A'),
        $notifMessage,
        'requests.php?id=' . ($requestData['request_id'] ?? $requestData['id'] ?? '')
    );
    
    error_log("Admin notification stored: " . ($notifStored ? 'yes' : 'no'));
    
    return $emailResult;
}

/**
 * Notify client about status update
 */
function notifyClientStatusUpdate($pdo, $clientEmail, $requestData, $newStatus) {
    $statusMessages = [
        'in_progress' => ['🚀 Your design is being worked on!', 'We have started working on your website design.'],
        'preview_ready' => ['👀 Your preview is ready!', 'Your website preview is ready for review.'],
        'completed' => ['✅ Your design is complete!', 'Your website design has been completed.'],
        'approved' => ['🎉 Your website is now live!', 'Congratulations! Your website has been published.'],
        'rejected' => ['❌ Request Update', 'There was an issue with your request.'],
    ];
    
    $info = $statusMessages[$newStatus] ?? ['📋 Request Updated', 'Your request status has been updated.'];
    
    $subject = $info[0] . ' - ' . ($requestData['reference_number'] ?? '');
    $requestData['status_message'] = $info[1];
    $requestData['new_status'] = $newStatus;
    
    $html = buildEmailTemplate('status_update', $requestData);
    
    return sendEmail($clientEmail, $subject, $html);
}

/**
 * Store notification for admin dashboard
 */
function storeAdminNotification($pdo, $type, $title, $message, $linkUrl = null) {
    try {
        // Create table if not exists (using TINYINT for MySQL compatibility)
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT,
            link_url VARCHAR(500),
            is_read TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_admin_notif (is_read, created_at)
        ) ENGINE=InnoDB");
        
        $stmt = $pdo->prepare("INSERT INTO admin_notifications (type, title, message, link_url, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
        $stmt->execute([$type, $title, $message, $linkUrl]);
        
        error_log("Notification stored successfully: $title");
        return true;
    } catch (Exception $e) {
        error_log("Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get admin notifications
 */
function getAdminNotifications($pdo, $limit = 10, $unreadOnly = false) {
    try {
        // Ensure table exists first
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT,
            link_url VARCHAR(500),
            is_read TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_admin_notif (is_read, created_at)
        ) ENGINE=InnoDB");
        
        $sql = "SELECT * FROM admin_notifications";
        if ($unreadOnly) {
            $sql .= " WHERE is_read = 0";
        }
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit;
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("getAdminNotifications error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get unread notification count
 */
function getUnreadNotificationCount($pdo) {
    try {
        // Ensure table exists first
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT,
            link_url VARCHAR(500),
            is_read TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_admin_notif (is_read, created_at)
        ) ENGINE=InnoDB");
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Mark notification as read
 */
function markNotificationRead($pdo, $notificationId) {
    try {
        $stmt = $pdo->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$notificationId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Mark all notifications as read
 */
function markAllNotificationsRead($pdo) {
    try {
        $pdo->exec("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Build HTML email template
 */
function buildEmailTemplate($type, $data) {
    $config = getEmailConfig();
    $baseUrl = $config['base_url'];
    
    // Email wrapper start
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilDevStudio</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, sans-serif; background-color: #f4f4f5;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">FilDevStudio</h1>
            <p style="color: #BFDBFE; margin: 8px 0 0 0; font-size: 14px;">Website Solutions for Filipino SMEs</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px;">';
    
    // Content based on type
    switch ($type) {
        case 'new_request':
            $html .= '
            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 22px;">🎨 New Design Request!</h2>
            
            <div style="background-color: #EFF6FF; border-left: 4px solid #3B82F6; padding: 15px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">
                <p style="margin: 0; color: #1E40AF; font-weight: bold; font-size: 16px;">
                    Reference: ' . htmlspecialchars($data['reference_number'] ?? 'N/A') . '
                </p>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280; width: 120px;">Client</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-weight: 500;">' . htmlspecialchars($data['client_name'] ?? 'N/A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280;">Email</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937;">' . htmlspecialchars($data['client_email'] ?? 'N/A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280;">Site Name</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937;">' . htmlspecialchars($data['site_name'] ?? 'N/A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #6b7280;">Template</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb; color: #1f2937;">' . htmlspecialchars($data['template_name'] ?? 'N/A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; color: #6b7280;">Files</td>
                    <td style="padding: 12px 0; color: #1f2937;">' . ($data['file_count'] ?? 0) . ' files uploaded</td>
                </tr>
            </table>
            
            ' . (!empty($data['description']) ? '
            <div style="background-color: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px; font-weight: 500;">Client Request Details:</p>
                <p style="margin: 0; color: #1f2937; line-height: 1.6;">' . nl2br(htmlspecialchars($data['description'])) . '</p>
            </div>' : '') . '
            
            ' . (!empty($data['priority']) ? '
            <div style="margin-bottom: 20px;">
                <span style="display: inline-block; padding: 6px 12px; background-color: ' . ($data['priority'] === 'urgent' ? '#FEE2E2' : ($data['priority'] === 'high' ? '#FEF3C7' : '#E0E7FF')) . '; color: ' . ($data['priority'] === 'urgent' ? '#DC2626' : ($data['priority'] === 'high' ? '#D97706' : '#4338CA')) . '; border-radius: 6px; font-size: 14px; font-weight: 500;">Priority: ' . ucfirst(htmlspecialchars($data['priority'])) . '</span>
            </div>' : '') . '
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="' . $baseUrl . '/admin/requests.php?id=' . ($data['request_id'] ?? $data['id'] ?? '') . '" 
                   style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                    View Request in Dashboard
                </a>
            </div>';
            break;
            
        case 'status_update':
            $statusColors = [
                'in_progress' => ['#DBEAFE', '#3B82F6', '#1E40AF'],
                'preview_ready' => ['#F3E8FF', '#8B5CF6', '#6D28D9'],
                'completed' => ['#D1FAE5', '#10B981', '#047857'],
                'approved' => ['#D1FAE5', '#10B981', '#047857'],
                'rejected' => ['#FEE2E2', '#EF4444', '#DC2626'],
            ];
            $colors = $statusColors[$data['new_status']] ?? $statusColors['in_progress'];
            
            $html .= '
            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 22px;">Request Status Update</h2>
            
            <div style="background-color: ' . $colors[0] . '; border-left: 4px solid ' . $colors[1] . '; padding: 15px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">
                <p style="margin: 0; color: ' . $colors[2] . '; font-weight: bold; font-size: 16px;">
                    Status: ' . ucwords(str_replace('_', ' ', $data['new_status'])) . '
                </p>
            </div>
            
            <p style="color: #4b5563; line-height: 1.8; font-size: 16px;">
                Hi ' . htmlspecialchars($data['client_name'] ?? 'there') . ',<br><br>
                ' . ($data['status_message'] ?? 'Your request has been updated.') . '
            </p>
            
            <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
                Reference: <strong>' . htmlspecialchars($data['reference_number'] ?? 'N/A') . '</strong>
            </p>';
            
            // Add preview button if preview is ready
            if ($data['new_status'] === 'preview_ready' && !empty($data['preview_url'])) {
                $html .= '
                <div style="text-align: center; margin-top: 30px;">
                    <a href="' . htmlspecialchars($data['preview_url']) . '" 
                       style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                        View Your Preview
                    </a>
                </div>';
            }
            
            $html .= '
            <div style="text-align: center; margin-top: 20px;">
                <a href="' . $baseUrl . '/client/my-requests.php" 
                   style="display: inline-block; padding: 12px 28px; background-color: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 14px;">
                    View in Dashboard
                </a>
            </div>';
            break;
            
        default:
            $html .= '<p style="color: #1f2937;">No content available.</p>';
    }
    
    // Email wrapper end
    $html .= '
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 25px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 13px; margin: 0 0 10px 0;">
                © ' . date('Y') . ' FilDevStudio. All rights reserved.
            </p>
            <p style="margin: 0;">
                <a href="' . $baseUrl . '" style="color: #3B82F6; text-decoration: none; font-size: 13px;">Visit Website</a>
            </p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}

/**
 * Generate unique reference number
 */
function generateReferenceNumber($prefix = 'FDS') {
    return $prefix . '-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
}
