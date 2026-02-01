<?php
/**
 * Template Thumbnail Generator
 * Generates SVG placeholder images for template previews
 * Access: /assets/images/templates/generate.php?id=1
 */

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Template configurations
$templates = [
    1 => [
        'name' => 'Sari-Sari Store',
        'tagline' => 'Tindahan Template',
        'icon' => '🏪',
        'gradient' => ['#F97316', '#FB923C'],
        'features' => ['E-Load', 'GCash', 'Tingi']
    ],
    2 => [
        'name' => 'Food Business',
        'tagline' => 'Karinderya Template',
        'icon' => '🍜',
        'gradient' => ['#EF4444', '#F97316'],
        'features' => ['Menu', 'Hours', 'Delivery']
    ],
    3 => [
        'name' => 'Local Services',
        'tagline' => 'Services Template',
        'icon' => '🔧',
        'gradient' => ['#14B8A6', '#06B6D4'],
        'features' => ['Services', 'Pricing', 'Contact']
    ],
    4 => [
        'name' => 'Small Retail',
        'tagline' => 'Shop Template',
        'icon' => '🛍️',
        'gradient' => ['#3B82F6', '#6366F1'],
        'features' => ['Products', 'Store', 'Payment']
    ],
    5 => [
        'name' => 'Freelancer',
        'tagline' => 'Portfolio Template',
        'icon' => '💼',
        'gradient' => ['#8B5CF6', '#EC4899'],
        'features' => ['Portfolio', 'Skills', 'Rates']
    ]
];

$template = $templates[$templateId] ?? $templates[1];

// Set header to SVG
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=86400');

// Generate SVG
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600">
    <defs>
        <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:<?php echo $template['gradient'][0]; ?>"/>
            <stop offset="100%" style="stop-color:<?php echo $template['gradient'][1]; ?>"/>
        </linearGradient>
        <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="4" stdDeviation="8" flood-color="#000000" flood-opacity="0.15"/>
        </filter>
    </defs>
    
    <!-- Background -->
    <rect width="800" height="600" fill="url(#bgGradient)"/>
    
    <!-- Decorative circles -->
    <circle cx="650" cy="100" r="120" fill="rgba(255,255,255,0.1)"/>
    <circle cx="100" cy="500" r="80" fill="rgba(255,255,255,0.1)"/>
    
    <!-- Browser mockup -->
    <g filter="url(#shadow)">
        <rect x="100" y="80" width="600" height="440" rx="12" fill="white"/>
        
        <!-- Browser header -->
        <rect x="100" y="80" width="600" height="36" rx="12" fill="#F3F4F6"/>
        <rect x="100" y="104" width="600" height="12" fill="#F3F4F6"/>
        <circle cx="124" cy="98" r="6" fill="#EF4444"/>
        <circle cx="144" cy="98" r="6" fill="#F59E0B"/>
        <circle cx="164" cy="98" r="6" fill="#22C55E"/>
        
        <!-- URL bar -->
        <rect x="200" y="88" width="400" height="20" rx="4" fill="#E5E7EB"/>
        <text x="400" y="102" font-family="Arial" font-size="10" fill="#9CA3AF" text-anchor="middle">mystore.fildevstudio.com</text>
    </g>
    
    <!-- Content area -->
    <g transform="translate(120, 140)">
        <!-- Nav bar mock -->
        <rect x="0" y="0" width="560" height="40" fill="#FFFFFF"/>
        <rect x="10" y="10" width="80" height="20" rx="4" fill="<?php echo $template['gradient'][0]; ?>" opacity="0.2"/>
        <circle cx="520" cy="20" r="10" fill="#E5E7EB"/>
        <circle cx="545" cy="20" r="10" fill="#E5E7EB"/>
        
        <!-- Hero section -->
        <rect x="0" y="50" width="560" height="180" fill="<?php echo $template['gradient'][0]; ?>" opacity="0.1"/>
        <text x="280" y="120" font-family="Arial, sans-serif" font-size="48" fill="<?php echo $template['gradient'][0]; ?>" text-anchor="middle"><?php echo $template['icon']; ?></text>
        <text x="280" y="160" font-family="Arial, sans-serif" font-size="20" font-weight="bold" fill="#1F2937" text-anchor="middle"><?php echo htmlspecialchars($template['name']); ?></text>
        <text x="280" y="185" font-family="Arial, sans-serif" font-size="12" fill="#6B7280" text-anchor="middle"><?php echo htmlspecialchars($template['tagline']); ?></text>
        
        <!-- Feature boxes -->
        <g transform="translate(90, 250)">
            <?php foreach ($template['features'] as $i => $feature): ?>
            <g transform="translate(<?php echo $i * 140; ?>, 0)">
                <rect x="0" y="0" width="120" height="70" rx="8" fill="<?php echo $template['gradient'][0]; ?>" opacity="0.1"/>
                <text x="60" y="45" font-family="Arial" font-size="12" fill="#374151" text-anchor="middle" font-weight="bold"><?php echo $feature; ?></text>
            </g>
            <?php endforeach; ?>
        </g>
    </g>
    
    <!-- Template label -->
    <rect x="30" y="540" width="200" height="40" rx="8" fill="white" opacity="0.95"/>
    <text x="130" y="565" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="<?php echo $template['gradient'][0]; ?>" text-anchor="middle"><?php echo htmlspecialchars($template['name']); ?></text>
</svg>
