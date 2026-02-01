<?php
/**
 * Template Confirmation Page
 * Simplified - just confirm template choice and enter site name
 * All customization happens in edit-site.php
 */
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to access this page.';
    header('Location: ../auth/login.php');
    exit();
}

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];
$error = '';

// Get template info
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM templates WHERE id = ? AND is_active = 1");
    $stmt->execute([$templateId]);
    $template = $stmt->fetch();
    
    if (!$template) {
        $_SESSION['error'] = 'Template not found.';
        header('Location: ../templates.php');
        exit();
    }
    
    // Get business profile for default site name
    $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    
} catch (Exception $e) {
    header('Location: ../templates.php');
    exit();
}

// Template default colors (used when creating the site)
$templateDefaults = [
    1 => ['primary' => '#2C3E50', 'secondary' => '#1A252F', 'accent' => '#E67E22', 'category' => 'Retail'],
    2 => ['primary' => '#8B4513', 'secondary' => '#5D2E0C', 'accent' => '#DAA520', 'category' => 'Food & Restaurant'],
    3 => ['primary' => '#1A1A2E', 'secondary' => '#4ECCA3', 'accent' => '#00D9FF', 'category' => 'Portfolio'],
    4 => ['primary' => '#1E3A5F', 'secondary' => '#0F2744', 'accent' => '#3498DB', 'category' => 'Services'],
    5 => ['primary' => '#3B82F6', 'secondary' => '#1E40AF', 'accent' => '#F59E0B', 'category' => 'General'],
    6 => ['primary' => '#059669', 'secondary' => '#047857', 'accent' => '#FBBF24', 'category' => 'E-Commerce'],
    7 => ['primary' => '#0D0D0D', 'secondary' => '#FF3131', 'accent' => '#FFD700', 'category' => 'Fashion'],
    8 => ['primary' => '#6366F1', 'secondary' => '#4F46E5', 'accent' => '#22D3EE', 'category' => 'Tech/Startup'],
    9 => ['primary' => '#BE185D', 'secondary' => '#9D174D', 'accent' => '#F472B6', 'category' => 'Boutique'],
    10 => ['primary' => '#0F172A', 'secondary' => '#1E293B', 'accent' => '#22D3EE', 'category' => 'Electronics'],
    11 => ['primary' => '#15803D', 'secondary' => '#166534', 'accent' => '#FDE047', 'category' => 'Grocery'],
    12 => ['primary' => '#EA580C', 'secondary' => '#C2410C', 'accent' => '#FACC15', 'category' => 'Sari-Sari'],
    13 => ['primary' => '#0891B2', 'secondary' => '#0E7490', 'accent' => '#F97316', 'category' => 'Sari-Sari Plus']
];

$defaults = $templateDefaults[$templateId] ?? $templateDefaults[5];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name'] ?? '');
    
    if (empty($siteName)) {
        $error = 'Please enter a name for your website.';
    } else {
        try {
            // Create site with template defaults - user will customize in edit-site.php
            $stmt = $pdo->prepare("INSERT INTO client_sites (user_id, template_id, site_name, primary_color, secondary_color, accent_color, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([
                $userId, 
                $templateId, 
                $siteName, 
                $defaults['primary'], 
                $defaults['secondary'], 
                $defaults['accent']
            ]);
            $siteId = $pdo->lastInsertId();
            
            logActivity($pdo, $userId, 'create_site', "Created new site: $siteName");
            
            $_SESSION['success'] = "Great! Now let's customize your website.";
            header("Location: edit-site.php?id=$siteId");
            exit();
            
        } catch (Exception $e) {
            $error = 'Failed to create website. Please try again.';
        }
    }
}

// Check for template thumbnail
$thumbnailFormats = ['jpg', 'jpeg', 'png', 'webp'];
$thumbnailPath = null;
foreach ($thumbnailFormats as $format) {
    $testPath = '../assets/images/templates/template-' . $templateId . '.' . $format;
    if (file_exists($testPath)) {
        $thumbnailPath = $testPath;
        break;
    }
}

$pageTitle = "Create Website - FilDevStudio";
require_once '../includes/header.php';
?>

<!-- Minimal Header -->
<section class="gradient-bg py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="../templates.php" class="inline-flex items-center text-blue-200 hover:text-white transition mb-2">
            <i class="fas fa-arrow-left mr-2"></i>Back to Templates
        </a>
        <h1 class="text-2xl font-bold text-white">Create Your Website</h1>
    </div>
</section>

<section class="py-8 lg:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if ($error): ?>
            <?php echo displayAlert('error', $error); ?>
        <?php endif; ?>
        
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="grid lg:grid-cols-5">
                
                <!-- Template Preview (3 columns) -->
                <div class="lg:col-span-3 bg-gray-900 p-6 lg:p-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="ml-auto text-gray-400 text-sm"><?php echo htmlspecialchars($template['name']); ?></span>
                    </div>
                    
                    <!-- Template Screenshot -->
                    <div class="rounded-lg overflow-hidden bg-white shadow-2xl">
                        <?php if ($thumbnailPath): ?>
                            <img src="<?php echo $thumbnailPath; ?>" 
                                 alt="<?php echo htmlspecialchars($template['name']); ?>" 
                                 class="w-full h-auto">
                        <?php else: ?>
                            <!-- Fallback Preview -->
                            <div class="aspect-video flex items-center justify-center" style="background: linear-gradient(135deg, <?php echo $defaults['primary']; ?>, <?php echo $defaults['secondary']; ?>);">
                                <div class="text-center text-white p-8">
                                    <i class="fas fa-laptop-code text-5xl mb-4 opacity-50"></i>
                                    <p class="text-xl font-bold"><?php echo htmlspecialchars($template['name']); ?></p>
                                    <p class="text-sm opacity-75 mt-2"><?php echo $defaults['category']; ?> Template</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Template Info -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-sm">Theme Colors:</span>
                            <span class="w-4 h-4 rounded-full border border-white/30" style="background: <?php echo $defaults['primary']; ?>;"></span>
                            <span class="w-4 h-4 rounded-full border border-white/30" style="background: <?php echo $defaults['secondary']; ?>;"></span>
                            <span class="w-4 h-4 rounded-full border border-white/30" style="background: <?php echo $defaults['accent']; ?>;"></span>
                        </div>
                        <a href="../template-preview.php?id=<?php echo $templateId; ?>" target="_blank" 
                           class="text-sm text-blue-400 hover:text-blue-300 transition">
                            <i class="fas fa-external-link-alt mr-1"></i>Full Preview
                        </a>
                    </div>
                </div>
                
                <!-- Create Form (2 columns) -->
                <div class="lg:col-span-2 p-6 lg:p-8 flex flex-col">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-full gradient-bg flex items-center justify-center">
                                <i class="fas fa-rocket text-white text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Ready to Start!</h2>
                                <p class="text-gray-500 text-sm">Just name your site to begin</p>
                            </div>
                        </div>
                        
                        <form method="POST" action="">
                            <div class="mb-6">
                                <label for="site_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Website Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="site_name" 
                                       id="site_name"
                                       value="<?php echo htmlspecialchars($profile['business_name'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition text-lg"
                                       placeholder="e.g., My Awesome Store"
                                       required
                                       autofocus>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    You can change this anytime
                                </p>
                            </div>
                            
                            <!-- What's Next Info -->
                            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                                <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                                    <i class="fas fa-tasks mr-2"></i>What's Next?
                                </h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-blue-500"></i>
                                        Add your business details
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-blue-500"></i>
                                        Upload logo & images
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-blue-500"></i>
                                        Customize colors & content
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="fas fa-check text-blue-500"></i>
                                        Preview & publish!
                                    </li>
                                </ul>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full gradient-bg text-white py-4 rounded-xl font-bold text-lg hover:opacity-90 transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <i class="fas fa-magic"></i>
                                Start Building
                            </button>
                        </form>
                    </div>
                    
                    <!-- Bottom Note -->
                    <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-400">
                            <i class="fas fa-lock mr-1"></i>
                            Your site will be saved as a draft
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="mt-8 grid md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Quick Setup</h4>
                    <p class="text-sm text-gray-500">Takes about 5-10 minutes</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-undo text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Change Anytime</h4>
                    <p class="text-sm text-gray-500">Switch templates later</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-eye text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Preview First</h4>
                    <p class="text-sm text-gray-500">Review before publishing</p>
                </div>
            </div>
        </div>
        
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
