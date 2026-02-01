<?php
/**
 * Email Test Script
 * Use this to verify email configuration is working
 */
require_once '../includes/functions.php';
require_once '../includes/mail.php';

// Check if form submitted
$result = null;
$testEmail = '';
$diagnostics = [];
$smtpDebug = '';

// Run diagnostics
$diagnostics['php_mail'] = function_exists('mail') ? 'Available' : 'Not available';
$diagnostics['phpmailer'] = class_exists('PHPMailer\PHPMailer\PHPMailer') ? 'Installed' : 'Not installed';
$diagnostics['fsockopen'] = function_exists('fsockopen') ? 'Available' : 'Disabled';
$diagnostics['openssl'] = extension_loaded('openssl') ? 'Loaded' : 'Not loaded';

// Test Gmail SMTP connection with detailed debug
$smtpTest = 'Not tested';
$socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
if ($socket) {
    $smtpTest = 'Connection OK';
    
    // Try to read greeting
    $greeting = fgets($socket, 515);
    $smtpDebug .= "Greeting: " . trim($greeting) . "\n";
    
    // Send EHLO
    fputs($socket, "EHLO localhost\r\n");
    $response = '';
    while ($line = fgets($socket, 515)) {
        $response .= $line;
        if (substr($line, 3, 1) === ' ') break;
    }
    $smtpDebug .= "EHLO response received\n";
    
    // Try STARTTLS
    fputs($socket, "STARTTLS\r\n");
    $tlsResponse = fgets($socket, 515);
    $smtpDebug .= "STARTTLS: " . trim($tlsResponse) . "\n";
    
    if (substr($tlsResponse, 0, 3) === '220') {
        // Try to enable TLS
        $cryptoResult = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if ($cryptoResult) {
            $smtpDebug .= "TLS: Enabled successfully\n";
            
            // EHLO again after TLS
            fputs($socket, "EHLO localhost\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            
            // Try AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            $authResponse = fgets($socket, 515);
            $smtpDebug .= "AUTH LOGIN: " . trim($authResponse) . "\n";
            
            if (substr($authResponse, 0, 3) === '334') {
                $config = getEmailConfig();
                
                // Send username
                fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
                $userResponse = fgets($socket, 515);
                $smtpDebug .= "Username: " . trim($userResponse) . "\n";
                
                if (substr($userResponse, 0, 3) === '334') {
                    // Send password
                    fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
                    $passResponse = fgets($socket, 515);
                    $smtpDebug .= "Password: " . trim($passResponse) . "\n";
                    
                    if (substr($passResponse, 0, 3) === '235') {
                        $diagnostics['gmail_auth'] = 'Authentication OK!';
                    } else {
                        $diagnostics['gmail_auth'] = 'Auth failed: ' . trim($passResponse);
                    }
                } else {
                    $diagnostics['gmail_auth'] = 'Username rejected';
                }
            }
        } else {
            $smtpDebug .= "TLS: Failed to enable\n";
            $diagnostics['tls'] = 'Failed to enable';
        }
    }
    
    fputs($socket, "QUIT\r\n");
    fclose($socket);
} else {
    $smtpTest = "Failed: $errstr ($errno)";
}
$diagnostics['gmail_smtp'] = $smtpTest;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = sanitize($_POST['test_email'] ?? '');
    
    if (!empty($testEmail) && filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        $subject = "FilDevStudio Email Test - " . date('Y-m-d H:i:s');
        $html = "
        <html>
        <body style='font-family: Arial, sans-serif; padding: 20px;'>
            <h2 style='color: #3B82F6;'>Email Test Successful!</h2>
            <p>If you're reading this, your email configuration is working correctly.</p>
            <p><strong>Sent at:</strong> " . date('F j, Y g:i:s A') . "</p>
            <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
            <p style='color: #6b7280; font-size: 12px;'>This is a test email from FilDevStudio.</p>
        </body>
        </html>
        ";
        
        $result = sendEmail($testEmail, $subject, $html);
    } else {
        $result = ['success' => false, 'message' => 'Invalid email address'];
    }
}

// Get current config (without showing password)
$config = getEmailConfig();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - FilDevStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                <i class="fas fa-envelope-open-text text-blue-500 mr-2"></i>
                Email Configuration Test
            </h1>
            <p class="text-gray-500 mb-6">Test if emails are being sent correctly from FilDevStudio</p>
            
            <!-- Current Config -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-cog mr-2"></i>Current Configuration</h3>
                <table class="w-full text-sm">
                    <tr><td class="text-gray-500 py-1">SMTP Host:</td><td class="font-mono"><?php echo htmlspecialchars($config['smtp_host']); ?></td></tr>
                    <tr><td class="text-gray-500 py-1">SMTP Port:</td><td class="font-mono"><?php echo $config['smtp_port']; ?></td></tr>
                    <tr><td class="text-gray-500 py-1">SMTP Secure:</td><td class="font-mono"><?php echo $config['smtp_secure']; ?></td></tr>
                    <tr><td class="text-gray-500 py-1">Username:</td><td class="font-mono"><?php echo htmlspecialchars($config['smtp_username']); ?></td></tr>
                    <tr><td class="text-gray-500 py-1">Password:</td><td class="font-mono text-gray-400">••••••••••••••••</td></tr>
                    <tr><td class="text-gray-500 py-1">From Email:</td><td class="font-mono"><?php echo htmlspecialchars($config['from_email']); ?></td></tr>
                    <tr><td class="text-gray-500 py-1">From Name:</td><td class="font-mono"><?php echo htmlspecialchars($config['from_name']); ?></td></tr>
                    <tr><td class="text-gray-500 py-1">Use SMTP:</td><td class="font-mono"><?php echo $config['use_smtp'] ? '<span class="text-green-600">Yes</span>' : '<span class="text-red-600">No</span>'; ?></td></tr>
                </table>
            </div>
            
            <!-- System Diagnostics -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                <h3 class="font-semibold text-blue-800 mb-3"><i class="fas fa-stethoscope mr-2"></i>System Diagnostics</h3>
                <table class="w-full text-sm">
                    <?php foreach ($diagnostics as $key => $value): ?>
                    <tr>
                        <td class="text-blue-700 py-1"><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</td>
                        <td class="font-mono <?php echo (strpos($value, 'OK') !== false || strpos($value, 'Available') !== false || strpos($value, 'Loaded') !== false || strpos($value, 'Installed') !== false) ? 'text-green-600' : 'text-orange-600'; ?>">
                            <?php echo htmlspecialchars($value); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <?php if (!empty($smtpDebug)): ?>
            <!-- SMTP Debug Log -->
            <div class="bg-gray-800 rounded-lg p-4 mb-6 border border-gray-700">
                <h3 class="font-semibold text-green-400 mb-3"><i class="fas fa-terminal mr-2"></i>SMTP Debug Log</h3>
                <pre class="text-xs text-green-300 font-mono whitespace-pre-wrap"><?php echo htmlspecialchars($smtpDebug); ?></pre>
            </div>
            <?php endif; ?>
            
            <?php if ($result): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $result['success'] ? 'bg-green-100 border border-green-300' : 'bg-red-100 border border-red-300'; ?>">
                <?php if ($result['success']): ?>
                    <p class="text-green-700"><i class="fas fa-check-circle mr-2"></i><strong>Success!</strong> Email sent to <?php echo htmlspecialchars($testEmail); ?></p>
                    <p class="text-green-600 text-sm mt-1"><?php echo htmlspecialchars($result['message']); ?></p>
                <?php else: ?>
                    <p class="text-red-700"><i class="fas fa-times-circle mr-2"></i><strong>Failed!</strong></p>
                    <p class="text-red-600 text-sm mt-1"><?php echo htmlspecialchars($result['message']); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Test Form -->
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Send test email to:</label>
                    <input type="email" name="test_email" value="<?php echo htmlspecialchars($testEmail ?: $config['admin_email']); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="your@email.com" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i>Send Test Email
                </button>
            </form>
            
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h4 class="font-semibold text-yellow-800 mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Troubleshooting Tips</h4>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Make sure the Gmail App Password is correct (16 characters, no spaces)</li>
                    <li>• Check that "Less secure app access" is NOT needed with App Passwords</li>
                    <li>• Verify 2-Factor Authentication is enabled on Gmail</li>
                    <li>• Check <code class="bg-yellow-100 px-1">includes/mail.php</code> for configuration</li>
                    <li>• Review PHP error log for detailed error messages</li>
                </ul>
            </div>
            
            <div class="mt-4 text-center">
                <a href="requests.php" class="text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Requests
                </a>
            </div>
        </div>
    </div>
</body>
</html>
