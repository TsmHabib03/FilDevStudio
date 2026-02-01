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

// ============================================================================
// FONT CUSTOMIZATION FUNCTIONS
// ============================================================================

/**
 * Get available fonts for customization
 * All fonts are from Google Fonts (free & web-safe)
 * @return array Font configuration array
 */
function getAvailableFonts() {
    return [
        'Inter' => [
            'label' => 'Inter (Modern & Clean)',
            'category' => 'sans-serif',
            'weights' => '300;400;500;600;700'
        ],
        'Poppins' => [
            'label' => 'Poppins (Friendly & Rounded)',
            'category' => 'sans-serif',
            'weights' => '300;400;500;600;700'
        ],
        'Roboto' => [
            'label' => 'Roboto (Professional)',
            'category' => 'sans-serif',
            'weights' => '300;400;500;700'
        ],
        'Open Sans' => [
            'label' => 'Open Sans (Readable)',
            'category' => 'sans-serif',
            'weights' => '300;400;600;700'
        ],
        'Playfair Display' => [
            'label' => 'Playfair Display (Elegant Serif)',
            'category' => 'serif',
            'weights' => '400;500;600;700'
        ],
        'Merriweather' => [
            'label' => 'Merriweather (Classic Serif)',
            'category' => 'serif',
            'weights' => '300;400;700'
        ],
        'Lora' => [
            'label' => 'Lora (Literary)',
            'category' => 'serif',
            'weights' => '400;500;600;700'
        ],
        'Montserrat' => [
            'label' => 'Montserrat (Bold & Modern)',
            'category' => 'sans-serif',
            'weights' => '300;400;500;600;700'
        ],
        'Oswald' => [
            'label' => 'Oswald (Strong & Condensed)',
            'category' => 'sans-serif',
            'weights' => '300;400;500;600;700'
        ],
        'Raleway' => [
            'label' => 'Raleway (Elegant Sans)',
            'category' => 'sans-serif',
            'weights' => '300;400;500;600;700'
        ],
        'Nunito' => [
            'label' => 'Nunito (Soft & Friendly)',
            'category' => 'sans-serif',
            'weights' => '300;400;600;700'
        ],
        'Source Sans Pro' => [
            'label' => 'Source Sans Pro (Adobe Classic)',
            'category' => 'sans-serif',
            'weights' => '300;400;600;700'
        ],
        'Libre Baskerville' => [
            'label' => 'Libre Baskerville (Traditional)',
            'category' => 'serif',
            'weights' => '400;700'
        ],
        'Dancing Script' => [
            'label' => 'Dancing Script (Handwritten)',
            'category' => 'cursive',
            'weights' => '400;500;600;700'
        ],
        'Bebas Neue' => [
            'label' => 'Bebas Neue (Display Headlines)',
            'category' => 'sans-serif',
            'weights' => '400'
        ],
    ];
}

/**
 * Get font pairing suggestions
 * Returns recommended heading + body font combinations
 * @return array Font pairing suggestions
 */
function getFontPairings() {
    return [
        [
            'heading' => 'Playfair Display',
            'body' => 'Inter',
            'style' => 'Elegant & Modern',
            'best_for' => 'Boutiques, Restaurants, Luxury'
        ],
        [
            'heading' => 'Montserrat',
            'body' => 'Open Sans',
            'style' => 'Bold & Professional',
            'best_for' => 'Tech, Startups, Services'
        ],
        [
            'heading' => 'Oswald',
            'body' => 'Roboto',
            'style' => 'Strong & Clean',
            'best_for' => 'Fitness, Sports, Industrial'
        ],
        [
            'heading' => 'Bebas Neue',
            'body' => 'Poppins',
            'style' => 'Bold Headlines',
            'best_for' => 'Streetwear, Events, Creative'
        ],
        [
            'heading' => 'Lora',
            'body' => 'Nunito',
            'style' => 'Classic & Friendly',
            'best_for' => 'Blogs, Bakeries, Local Business'
        ],
        [
            'heading' => 'Raleway',
            'body' => 'Inter',
            'style' => 'Minimal & Elegant',
            'best_for' => 'Portfolio, Fashion, Design'
        ],
        [
            'heading' => 'Merriweather',
            'body' => 'Source Sans Pro',
            'style' => 'Editorial & Readable',
            'best_for' => 'News, Magazines, Content'
        ],
        [
            'heading' => 'Dancing Script',
            'body' => 'Poppins',
            'style' => 'Playful & Creative',
            'best_for' => 'Cafes, Bakeries, Events'
        ],
    ];
}

/**
 * Generate Google Fonts URL for selected fonts
 * @param string $headingFont Heading font name
 * @param string $bodyFont Body font name
 * @return string Google Fonts URL
 */
function getGoogleFontsUrl($headingFont, $bodyFont) {
    $fonts = getAvailableFonts();
    $fontsToLoad = array_unique([$headingFont, $bodyFont]);
    
    $fontParams = [];
    foreach ($fontsToLoad as $fontName) {
        if (isset($fonts[$fontName])) {
            $weights = $fonts[$fontName]['weights'];
            $encodedName = urlencode($fontName);
            $fontParams[] = "family={$encodedName}:wght@{$weights}";
        }
    }
    
    if (empty($fontParams)) {
        // Fallback to Inter
        return 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap';
    }
    
    return 'https://fonts.googleapis.com/css2?' . implode('&', $fontParams) . '&display=swap';
}

/**
 * Get CSS font-family string with fallbacks
 * @param string $fontName Font name
 * @return string CSS font-family value
 */
function getFontFamily($fontName) {
    $fonts = getAvailableFonts();
    
    if (!isset($fonts[$fontName])) {
        return "'Inter', sans-serif";
    }
    
    $category = $fonts[$fontName]['category'];
    return "'{$fontName}', {$category}";
}

/**
 * Validate font selection
 * @param string $fontName Font name to validate
 * @return bool True if font is valid
 */
function isValidFont($fontName) {
    $fonts = getAvailableFonts();
    return isset($fonts[$fontName]);
}

/**
 * Get default fonts
 * @return array Default heading and body fonts
 */
function getDefaultFonts() {
    return [
        'heading' => 'Inter',
        'body' => 'Inter'
    ];
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

/**
 * Get template placeholder configuration
 * Returns icon, color scheme, and gradient classes for template placeholders
 * 
 * @param int $templateId Template ID
 * @param string $category Template category (retail, sarisari, food, freelance, services, general)
 * @return array Placeholder configuration with icon, color, gradient, and hex values
 */
function getTemplatePlaceholder($templateId, $category) {
    // Category-specific configurations
    $categoryConfig = [
        'retail' => [
            'icon' => 'fas fa-shopping-bag',
            'color' => 'blue',
            'hex' => '#3B82F6',
            'gradient' => 'from-blue-500 to-blue-600',
            'gradientHover' => 'from-blue-600 to-blue-700',
            'bgLight' => 'bg-blue-50',
            'textColor' => 'text-blue-600',
            'borderColor' => 'border-blue-200'
        ],
        'sarisari' => [
            'icon' => 'fas fa-store-alt',
            'color' => 'orange',
            'hex' => '#F97316',
            'gradient' => 'from-orange-500 to-orange-600',
            'gradientHover' => 'from-orange-600 to-orange-700',
            'bgLight' => 'bg-orange-50',
            'textColor' => 'text-orange-600',
            'borderColor' => 'border-orange-200'
        ],
        'food' => [
            'icon' => 'fas fa-utensils',
            'color' => 'red',
            'hex' => '#EF4444',
            'gradient' => 'from-red-500 to-red-600',
            'gradientHover' => 'from-red-600 to-red-700',
            'bgLight' => 'bg-red-50',
            'textColor' => 'text-red-600',
            'borderColor' => 'border-red-200'
        ],
        'freelance' => [
            'icon' => 'fas fa-briefcase',
            'color' => 'purple',
            'hex' => '#8B5CF6',
            'gradient' => 'from-purple-500 to-purple-600',
            'gradientHover' => 'from-purple-600 to-purple-700',
            'bgLight' => 'bg-purple-50',
            'textColor' => 'text-purple-600',
            'borderColor' => 'border-purple-200'
        ],
        'services' => [
            'icon' => 'fas fa-cogs',
            'color' => 'teal',
            'hex' => '#14B8A6',
            'gradient' => 'from-teal-500 to-teal-600',
            'gradientHover' => 'from-teal-600 to-teal-700',
            'bgLight' => 'bg-teal-50',
            'textColor' => 'text-teal-600',
            'borderColor' => 'border-teal-200'
        ],
        'general' => [
            'icon' => 'fas fa-globe',
            'color' => 'gray',
            'hex' => '#6B7280',
            'gradient' => 'from-gray-500 to-gray-600',
            'gradientHover' => 'from-gray-600 to-gray-700',
            'bgLight' => 'bg-gray-50',
            'textColor' => 'text-gray-600',
            'borderColor' => 'border-gray-200'
        ]
    ];
    
    // Get config for category or default to general
    $config = $categoryConfig[$category] ?? $categoryConfig['general'];
    $config['templateId'] = $templateId;
    $config['category'] = $category;
    
    return $config;
}

/**
 * Render template placeholder HTML
 * Generates a responsive placeholder card with gradient background and icon
 * 
 * @param int $templateId Template ID
 * @param string $category Template category
 * @param string $size Size variant: 'sm', 'md', 'lg' (default: 'md')
 * @return string HTML for the placeholder
 */
function renderTemplatePlaceholder($templateId, $category, $size = 'md') {
    $config = getTemplatePlaceholder($templateId, $category);
    
    // Size-specific classes
    $sizeClasses = [
        'sm' => ['height' => 'h-32', 'icon' => 'text-4xl', 'pattern' => 'w-16 h-16'],
        'md' => ['height' => 'h-48', 'icon' => 'text-6xl', 'pattern' => 'w-24 h-24'],
        'lg' => ['height' => 'h-64', 'icon' => 'text-8xl', 'pattern' => 'w-32 h-32']
    ];
    
    $sizes = $sizeClasses[$size] ?? $sizeClasses['md'];
    
    $html = <<<HTML
<div class="relative {$sizes['height']} bg-gradient-to-br {$config['gradient']} overflow-hidden rounded-t-xl">
    <!-- Decorative Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-4 left-4 {$sizes['pattern']} border-2 border-white rounded-lg"></div>
        <div class="absolute bottom-4 right-4 {$sizes['pattern']} border-2 border-white rounded-full"></div>
    </div>
    <!-- Center Icon -->
    <div class="absolute inset-0 flex items-center justify-center">
        <i class="{$config['icon']} {$sizes['icon']} text-white/30"></i>
    </div>
</div>
HTML;
    
    return $html;
}

/**
 * Generate SVG placeholder for template preview
 * Creates an inline SVG with gradient and icon representation
 * 
 * @param string $category Template category
 * @param int $width Width of the SVG
 * @param int $height Height of the SVG
 * @return string SVG markup as data URI
 */
function getTemplatePlaceholderSVG($category, $width = 400, $height = 300) {
    $config = getTemplatePlaceholder(0, $category);
    $hex = $config['hex'];
    
    // Generate a slightly darker shade for gradient
    $hexDark = adjustBrightness($hex, -30);
    
    // Category icon paths (simplified Font Awesome paths)
    $iconPaths = [
        'retail' => 'M320 448v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24V120c0-13.255 10.745-24 24-24h72v296c0 30.879 25.121 56 56 56h168zm0-344V0H152c-13.255 0-24 10.745-24 24v368c0 13.255 10.745 24 24 24h272c13.255 0 24-10.745 24-24V128H344c-13.2 0-24-10.8-24-24z',
        'sarisari' => 'M602 118.6L537.1 15C531.3 5.7 521 0 510 0H106C95 0 84.7 5.7 78.9 15L14 118.6c-33.5 53.5-3.8 127.9 58.8 136.4 4.5.6 9.1.9 13.7.9 29.6 0 55.8-13 73.8-33.1 18 20.1 44.3 33.1 73.8 33.1 29.6 0 55.8-13 73.8-33.1 18 20.1 44.3 33.1 73.8 33.1 29.6 0 55.8-13 73.8-33.1 18.1 20.1 44.3 33.1 73.8 33.1 4.7 0 9.2-.3 13.7-.9 62.8-8.4 92.6-82.8 59-136.4z',
        'food' => 'M207.9 15.2c.8 4.7 16.1 94.5 16.1 128.8 0 52.3-27.8 89.6-68.9 104.6L168 480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32l12.9-231.4C59.8 233.6 32 196.3 32 144 32 109.7 47.3 19.9 48.1 15.2 51.4-6.7 76.9-6.7 80.2 15.2 83.5 37.1 96 128 96 128h16s12.5-90.9 15.8-112.8c3.3-21.9 28.8-21.9 32.1 0C163.5 37.1 176 128 176 128h16s12.5-90.9 15.8-112.8c3.3-21.9 28.8-21.9 32.1 0z',
        'freelance' => 'M320 336c0 8.84-7.16 16-16 16h-96c-8.84 0-16-7.16-16-16v-48H0v144c0 25.6 22.4 48 48 48h416c25.6 0 48-22.4 48-48V288H320v48zm144-208h-80V80c0-25.6-22.4-48-48-48H176c-25.6 0-48 22.4-48 48v48H48c-25.6 0-48 22.4-48 48v80h512v-80c0-25.6-22.4-48-48-48zm-144 0H192V96h128v32z',
        'services' => 'M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z',
        'general' => 'M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm164.952 181.976l-60.952 60.952L412.948 303.876l-74.984 74.984-52.948-52.948-60.952 60.952L172.116 334.916 233.068 273.964l-52.948-52.948 74.984-74.984 52.948 52.948 60.952-60.952 51.948 51.948z'
    ];
    
    $iconPath = $iconPaths[$category] ?? $iconPaths['general'];
    
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:{$hex};stop-opacity:1" />
            <stop offset="100%" style="stop-color:{$hexDark};stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="100%" height="100%" fill="url(#grad)"/>
    <rect x="20" y="20" width="60" height="60" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2" rx="8"/>
    <circle cx="{$width}" cy="{$height}" r="50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="2" transform="translate(-70,-70)"/>
    <g transform="translate({($width/2 - 30)},{($height/2 - 30)}) scale(0.12)" fill="rgba(255,255,255,0.2)">
        <path d="{$iconPath}"/>
    </g>
</svg>
SVG;
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/**
 * Adjust hex color brightness
 * 
 * @param string $hex Hex color code
 * @param int $steps Steps to adjust (-255 to 255)
 * @return string Adjusted hex color
 */
function adjustBrightness($hex, $steps) {
    // Remove # if present
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust brightness
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

/**
 * Get site images by type
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param string|null $imageType Optional image type filter (logo, hero, gallery, product, other)
 * @return array Array of image records
 */
function getSiteImages($pdo, $siteId, $imageType = null) {
    if ($imageType) {
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE site_id = ? AND image_type = ? ORDER BY display_order ASC, created_at DESC");
        $stmt->execute([$siteId, $imageType]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE site_id = ? ORDER BY image_type, display_order ASC, created_at DESC");
        $stmt->execute([$siteId]);
    }
    return $stmt->fetchAll();
}

/**
 * Get single site image by type (useful for logo and hero)
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param string $imageType Image type (logo, hero)
 * @return array|null Image record or null
 */
function getSiteImage($pdo, $siteId, $imageType) {
    $stmt = $pdo->prepare("SELECT * FROM site_images WHERE site_id = ? AND image_type = ? ORDER BY display_order ASC LIMIT 1");
    $stmt->execute([$siteId, $imageType]);
    return $stmt->fetch() ?: null;
}

/**
 * Upload site image
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param array $file $_FILES array element
 * @param string $imageType Image type (logo, hero, gallery, product, other)
 * @param string $altText Alternative text for the image
 * @param int $displayOrder Display order for gallery images
 * @return array Result with success status and message/data
 */
function uploadSiteImage($pdo, $siteId, $file, $imageType, $altText = '', $displayOrder = 0) {
    // Allowed types include WebP
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5242880; // 5MB
    
    // Create destination folder
    $destination = dirname(__DIR__) . '/uploads/sites/' . $siteId;
    
    // Handle the upload
    $result = handleFileUpload($file, $destination, $allowedTypes, $maxSize);
    
    if (!$result['success']) {
        return $result;
    }
    
    // For logo and hero, delete existing image first
    if (in_array($imageType, ['logo', 'hero'])) {
        $existingImage = getSiteImage($pdo, $siteId, $imageType);
        if ($existingImage) {
            deleteSiteImage($pdo, $existingImage['id'], $siteId);
        }
    }
    
    // Store relative path for database
    $relativePath = 'uploads/sites/' . $siteId . '/' . basename($result['path']);
    
    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO site_images (site_id, image_path, image_type, alt_text, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$siteId, $relativePath, $imageType, $altText, $displayOrder]);
        
        return [
            'success' => true,
            'message' => 'Image uploaded successfully!',
            'image_id' => $pdo->lastInsertId(),
            'path' => $relativePath
        ];
    } catch (Exception $e) {
        // Delete the uploaded file if database insert fails
        @unlink($result['path']);
        return ['success' => false, 'message' => 'Failed to save image record.'];
    }
}

/**
 * Delete site image
 * @param PDO $pdo Database connection
 * @param int $imageId Image ID
 * @param int $siteId Site ID (for verification)
 * @return array Result with success status and message
 */
function deleteSiteImage($pdo, $imageId, $siteId) {
    try {
        // Get image record first
        $stmt = $pdo->prepare("SELECT * FROM site_images WHERE id = ? AND site_id = ?");
        $stmt->execute([$imageId, $siteId]);
        $image = $stmt->fetch();
        
        if (!$image) {
            return ['success' => false, 'message' => 'Image not found.'];
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM site_images WHERE id = ? AND site_id = ?");
        $stmt->execute([$imageId, $siteId]);
        
        // Delete physical file
        $filePath = dirname(__DIR__) . '/' . $image['image_path'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        
        return ['success' => true, 'message' => 'Image deleted successfully!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to delete image.'];
    }
}

/**
 * Update gallery image order
 * @param PDO $pdo Database connection
 * @param int $imageId Image ID
 * @param int $siteId Site ID (for verification)
 * @param int $newOrder New display order
 * @return bool Success status
 */
function updateImageOrder($pdo, $imageId, $siteId, $newOrder) {
    try {
        $stmt = $pdo->prepare("UPDATE site_images SET display_order = ? WHERE id = ? AND site_id = ?");
        $stmt->execute([$newOrder, $imageId, $siteId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Verify user owns the site
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param int $userId User ID
 * @return bool True if user owns the site
 */
function userOwnsSite($pdo, $siteId, $userId) {
    $stmt = $pdo->prepare("SELECT id FROM client_sites WHERE id = ? AND user_id = ?");
    $stmt->execute([$siteId, $userId]);
    return $stmt->fetch() !== false;
}

// ============================================================================
// SITE PUBLISHING FUNCTIONS
// ============================================================================

/**
 * Generate a URL-safe subdomain from site name
 * @param string $siteName The site name to convert
 * @return string URL-safe subdomain
 */
function generateSubdomain($siteName) {
    // Convert to lowercase
    $subdomain = strtolower($siteName);
    // Replace spaces and underscores with hyphens
    $subdomain = preg_replace('/[\s_]+/', '-', $subdomain);
    // Remove non-alphanumeric characters except hyphens
    $subdomain = preg_replace('/[^a-z0-9-]/', '', $subdomain);
    // Remove consecutive hyphens
    $subdomain = preg_replace('/-+/', '-', $subdomain);
    // Trim hyphens from start and end
    $subdomain = trim($subdomain, '-');
    // Ensure minimum length
    if (strlen($subdomain) < 3) {
        $subdomain .= '-site';
    }
    // Truncate to max length
    if (strlen($subdomain) > 50) {
        $subdomain = substr($subdomain, 0, 50);
        $subdomain = rtrim($subdomain, '-');
    }
    return $subdomain;
}

/**
 * Validate subdomain format
 * @param string $subdomain Subdomain to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateSubdomain($subdomain) {
    // Check length
    if (strlen($subdomain) < 3) {
        return ['valid' => false, 'message' => 'Subdomain must be at least 3 characters long.'];
    }
    if (strlen($subdomain) > 50) {
        return ['valid' => false, 'message' => 'Subdomain must be 50 characters or less.'];
    }
    // Check format: lowercase, alphanumeric, hyphens only
    if (!preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]$|^[a-z0-9]$/', $subdomain)) {
        return ['valid' => false, 'message' => 'Subdomain must be lowercase, alphanumeric, and hyphens only. Cannot start or end with a hyphen.'];
    }
    // Check for consecutive hyphens
    if (strpos($subdomain, '--') !== false) {
        return ['valid' => false, 'message' => 'Subdomain cannot contain consecutive hyphens.'];
    }
    return ['valid' => true, 'message' => 'Valid subdomain.'];
}

/**
 * Check if subdomain is available
 * @param PDO $pdo Database connection
 * @param string $subdomain Subdomain to check
 * @param int|null $excludeSiteId Exclude this site ID from check (for updates)
 * @return bool True if subdomain is available
 */
function isSubdomainAvailable($pdo, $subdomain, $excludeSiteId = null) {
    if ($excludeSiteId) {
        $stmt = $pdo->prepare("SELECT id FROM client_sites WHERE subdomain = ? AND id != ?");
        $stmt->execute([$subdomain, $excludeSiteId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM client_sites WHERE subdomain = ?");
        $stmt->execute([$subdomain]);
    }
    return $stmt->fetch() === false;
}

/**
 * Generate unique subdomain (appends number if taken)
 * @param PDO $pdo Database connection
 * @param string $baseSubdomain Base subdomain to try
 * @param int|null $excludeSiteId Exclude this site ID from check
 * @return string Unique subdomain
 */
function generateUniqueSubdomain($pdo, $baseSubdomain, $excludeSiteId = null) {
    $subdomain = $baseSubdomain;
    $counter = 1;
    
    while (!isSubdomainAvailable($pdo, $subdomain, $excludeSiteId)) {
        $suffix = '-' . $counter;
        $maxLength = 50 - strlen($suffix);
        $subdomain = substr($baseSubdomain, 0, $maxLength) . $suffix;
        $counter++;
        
        // Safety limit
        if ($counter > 100) {
            $subdomain = $baseSubdomain . '-' . uniqid();
            break;
        }
    }
    
    return $subdomain;
}

/**
 * Publish a site (set status to active and record timestamp)
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param int $userId User ID (for verification)
 * @param string $subdomain Subdomain to use
 * @return array ['success' => bool, 'message' => string]
 */
function publishSite($pdo, $siteId, $userId, $subdomain) {
    try {
        // Verify ownership
        if (!userOwnsSite($pdo, $siteId, $userId)) {
            return ['success' => false, 'message' => 'You do not have permission to publish this site.'];
        }
        
        // Validate subdomain
        $validation = validateSubdomain($subdomain);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Check availability
        if (!isSubdomainAvailable($pdo, $subdomain, $siteId)) {
            return ['success' => false, 'message' => 'This subdomain is already taken. Please choose another.'];
        }
        
        // Update site
        $stmt = $pdo->prepare("UPDATE client_sites SET status = 'active', subdomain = ?, published_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$subdomain, $siteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Site published successfully!'];
        }
        
        return ['success' => false, 'message' => 'Failed to publish site.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'An error occurred while publishing the site.'];
    }
}

/**
 * Unpublish a site (revert to draft status)
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param int $userId User ID (for verification)
 * @return array ['success' => bool, 'message' => string]
 */
function unpublishSite($pdo, $siteId, $userId) {
    try {
        // Verify ownership
        if (!userOwnsSite($pdo, $siteId, $userId)) {
            return ['success' => false, 'message' => 'You do not have permission to unpublish this site.'];
        }
        
        // Update site - keep subdomain for re-publishing later
        $stmt = $pdo->prepare("UPDATE client_sites SET status = 'draft', published_at = NULL WHERE id = ? AND user_id = ?");
        $stmt->execute([$siteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Site unpublished. It is now a draft.'];
        }
        
        return ['success' => false, 'message' => 'Failed to unpublish site.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'An error occurred while unpublishing the site.'];
    }
}

/**
 * Update site subdomain
 * @param PDO $pdo Database connection
 * @param int $siteId Site ID
 * @param int $userId User ID (for verification)
 * @param string $subdomain New subdomain
 * @return array ['success' => bool, 'message' => string]
 */
function updateSubdomain($pdo, $siteId, $userId, $subdomain) {
    try {
        // Verify ownership
        if (!userOwnsSite($pdo, $siteId, $userId)) {
            return ['success' => false, 'message' => 'You do not have permission to update this site.'];
        }
        
        // Validate subdomain
        $validation = validateSubdomain($subdomain);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Check availability
        if (!isSubdomainAvailable($pdo, $subdomain, $siteId)) {
            return ['success' => false, 'message' => 'This subdomain is already taken. Please choose another.'];
        }
        
        // Update subdomain only
        $stmt = $pdo->prepare("UPDATE client_sites SET subdomain = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$subdomain, $siteId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Subdomain updated successfully!'];
        }
        
        return ['success' => false, 'message' => 'No changes made.'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'An error occurred while updating the subdomain.'];
    }
}

/**
 * Get site by subdomain (for public access)
 * @param PDO $pdo Database connection
 * @param string $subdomain Subdomain to look up
 * @param bool $requireActive Only return active sites
 * @return array|false Site data or false if not found
 */
function getSiteBySubdomain($pdo, $subdomain, $requireActive = true) {
    try {
        $sql = "SELECT cs.*, bp.business_name, bp.contact_phone, bp.contact_email, bp.address, bp.business_type,
                       t.name as template_name, t.category as template_category
                FROM client_sites cs 
                LEFT JOIN business_profiles bp ON cs.user_id = bp.user_id 
                LEFT JOIN templates t ON cs.template_id = t.id
                WHERE cs.subdomain = ?";
        
        if ($requireActive) {
            $sql .= " AND cs.status = 'active'";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subdomain]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get public URL for a site
 * @param string|null $subdomain Site subdomain
 * @return string|null Full public URL or null if no subdomain
 */
function getPublicSiteUrl($subdomain) {
    if (empty($subdomain)) {
        return null;
    }
    // Get base URL dynamically
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
    $basePath = rtrim($basePath, '/');
    
    return "{$protocol}://{$host}{$basePath}/public/site.php?s=" . urlencode($subdomain);
}

/**
 * Get friendly public URL (using rewrite rule)
 * @param string|null $subdomain Site subdomain
 * @return string|null Friendly URL or null if no subdomain
 */
function getFriendlyPublicUrl($subdomain) {
    if (empty($subdomain)) {
        return null;
    }
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
    $basePath = rtrim($basePath, '/');
    
    return "{$protocol}://{$host}{$basePath}/site/" . urlencode($subdomain);
}

// ============================================================================
// SOCIAL MEDIA FUNCTIONS
// ============================================================================

/**
 * Get social media platforms configuration
 * @return array Social media platforms with icons and labels
 */
function getSocialPlatforms() {
    return [
        'facebook' => [
            'label' => 'Facebook',
            'icon' => 'fab fa-facebook-f',
            'placeholder' => 'https://facebook.com/yourpage',
            'color' => '#1877F2'
        ],
        'instagram' => [
            'label' => 'Instagram',
            'icon' => 'fab fa-instagram',
            'placeholder' => 'https://instagram.com/yourprofile or @username',
            'color' => '#E4405F'
        ],
        'tiktok' => [
            'label' => 'TikTok',
            'icon' => 'fab fa-tiktok',
            'placeholder' => 'https://tiktok.com/@username or @username',
            'color' => '#000000'
        ],
        'twitter' => [
            'label' => 'Twitter / X',
            'icon' => 'fab fa-x-twitter',
            'placeholder' => 'https://x.com/username or @username',
            'color' => '#000000'
        ],
        'youtube' => [
            'label' => 'YouTube',
            'icon' => 'fab fa-youtube',
            'placeholder' => 'https://youtube.com/@channel',
            'color' => '#FF0000'
        ],
        'linkedin' => [
            'label' => 'LinkedIn',
            'icon' => 'fab fa-linkedin-in',
            'placeholder' => 'https://linkedin.com/company/yourcompany',
            'color' => '#0A66C2'
        ],
        'whatsapp' => [
            'label' => 'WhatsApp',
            'icon' => 'fab fa-whatsapp',
            'placeholder' => '09171234567 (Philippine mobile number)',
            'color' => '#25D366'
        ],
        'messenger' => [
            'label' => 'Messenger',
            'icon' => 'fab fa-facebook-messenger',
            'placeholder' => 'https://m.me/username or username',
            'color' => '#0084FF'
        ]
    ];
}

/**
 * Format raw social media input into proper URL
 * @param string $platform Platform key (facebook, instagram, etc.)
 * @param string $value Raw input value
 * @return string|null Formatted URL or null if empty
 */
function formatSocialUrl($platform, $value) {
    $value = trim($value);
    if (empty($value)) {
        return null;
    }
    
    switch ($platform) {
        case 'facebook':
            // If it's already a URL, return as-is (ensure https)
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            // If it's a username/page name
            return 'https://facebook.com/' . ltrim($value, '@/');
            
        case 'instagram':
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            return 'https://instagram.com/' . ltrim($value, '@/');
            
        case 'tiktok':
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            $username = ltrim($value, '@/');
            return 'https://tiktok.com/@' . $username;
            
        case 'twitter':
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            return 'https://x.com/' . ltrim($value, '@/');
            
        case 'youtube':
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            // If it starts with @, it's a handle
            if (strpos($value, '@') === 0) {
                return 'https://youtube.com/' . $value;
            }
            return 'https://youtube.com/@' . $value;
            
        case 'linkedin':
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            return 'https://linkedin.com/company/' . ltrim($value, '/');
            
        case 'whatsapp':
            // Format Philippine numbers: remove leading 0, add +63
            $number = preg_replace('/[^0-9]/', '', $value);
            if (strlen($number) === 11 && substr($number, 0, 1) === '0') {
                // Philippine mobile: 09XX → 63 9XX
                $number = '63' . substr($number, 1);
            } elseif (strlen($number) === 10 && substr($number, 0, 1) === '9') {
                // Already without leading 0: 9XX → 63 9XX
                $number = '63' . $number;
            }
            // If already has country code, use as-is
            return 'https://wa.me/' . $number;
            
        case 'messenger':
            if (preg_match('/^https?:\/\//i', $value)) {
                return preg_replace('/^http:\/\//i', 'https://', $value);
            }
            return 'https://m.me/' . ltrim($value, '@/');
            
        default:
            return $value;
    }
}

/**
 * Get active social links for a site
 * @param array $site Site data array containing social_* fields
 * @return array Array of active social links with icon, url, label, and color
 */
function getSocialLinks($site) {
    $platforms = getSocialPlatforms();
    $socialLinks = [];
    
    foreach ($platforms as $key => $config) {
        $fieldName = 'social_' . $key;
        $rawValue = $site[$fieldName] ?? '';
        
        if (!empty($rawValue)) {
            $url = formatSocialUrl($key, $rawValue);
            if ($url) {
                $socialLinks[$key] = [
                    'platform' => $key,
                    'label' => $config['label'],
                    'icon' => $config['icon'],
                    'url' => $url,
                    'color' => $config['color'],
                    'raw' => $rawValue
                ];
            }
        }
    }
    
    return $socialLinks;
}

/**
 * Render social media icons for header (small, icon-only)
 * @param array $socialLinks Array from getSocialLinks()
 * @param string $primaryColor Site's primary color for hover effect
 * @return string HTML string of social icons
 */
function renderSocialIconsHeader($socialLinks, $primaryColor = '#3B82F6') {
    if (empty($socialLinks)) {
        return '';
    }
    
    $html = '<div class="flex items-center space-x-3">';
    foreach ($socialLinks as $link) {
        $html .= sprintf(
            '<a href="%s" target="_blank" rel="noopener noreferrer" title="%s" class="text-gray-500 hover:text-primary transition-colors duration-200 transform hover:scale-110" style="--tw-text-opacity: 1;">
                <i class="%s text-lg"></i>
            </a>',
            htmlspecialchars($link['url']),
            htmlspecialchars($link['label']),
            htmlspecialchars($link['icon'])
        );
    }
    $html .= '</div>';
    
    return $html;
}

/**
 * Render social media icons for footer (larger, with hover effects)
 * @param array $socialLinks Array from getSocialLinks()
 * @param string $primaryColor Site's primary color
 * @param bool $showLabels Whether to show text labels
 * @return string HTML string of social icons
 */
function renderSocialIconsFooter($socialLinks, $primaryColor = '#3B82F6', $showLabels = false) {
    if (empty($socialLinks)) {
        return '';
    }
    
    $html = '<div class="flex flex-wrap items-center justify-center gap-4">';
    foreach ($socialLinks as $link) {
        if ($showLabels) {
            $html .= sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" 
                    class="flex items-center space-x-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 transform hover:scale-105 hover:-translate-y-0.5">
                    <i class="%s text-xl"></i>
                    <span class="text-sm">%s</span>
                </a>',
                htmlspecialchars($link['url']),
                htmlspecialchars($link['icon']),
                htmlspecialchars($link['label'])
            );
        } else {
            $html .= sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" title="%s"
                    class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 transform hover:scale-110 hover:-translate-y-0.5">
                    <i class="%s text-xl"></i>
                </a>',
                htmlspecialchars($link['url']),
                htmlspecialchars($link['label']),
                htmlspecialchars($link['icon'])
            );
        }
    }
    $html .= '</div>';
    
    return $html;
}

/**
 * Sanitize social media input before saving
 * @param string $value Raw input value
 * @return string Sanitized value
 */
function sanitizeSocialInput($value) {
    $value = trim($value);
    // Remove potentially dangerous characters but allow URLs
    $value = strip_tags($value);
    // Limit length
    if (strlen($value) > 255) {
        $value = substr($value, 0, 255);
    }
    return $value;
}
?>
