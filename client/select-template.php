<?php
/**
 * Template Selection Page
 */
$pageTitle = "Select Template - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireLogin();

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get template info
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM templates WHERE id = ? AND is_active = 1");
    $stmt->execute([$templateId]);
    $template = $stmt->fetch();
    
    if (!$template) {
        redirect('../templates.php');
    }
    
    // Get business profile
    $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    
} catch (Exception $e) {
    redirect('../templates.php');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name'] ?? '');
    $heroTitle = sanitize($_POST['hero_title'] ?? '');
    $heroSubtitle = sanitize($_POST['hero_subtitle'] ?? '');
    $primaryColor = sanitize($_POST['primary_color'] ?? '#3B82F6');
    
    if (empty($siteName)) {
        $error = 'Please enter a site name.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO client_sites (user_id, template_id, site_name, hero_title, hero_subtitle, primary_color, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, 'draft')");
            $stmt->execute([$userId, $templateId, $siteName, $heroTitle, $heroSubtitle, $primaryColor]);
            $siteId = $pdo->lastInsertId();
            
            logActivity($pdo, $userId, 'create_site', "Created new site: $siteName");
            
            $_SESSION['success'] = 'Website created successfully!';
            redirect("edit-site.php?id=$siteId");
            
        } catch (Exception $e) {
            $error = 'Failed to create website. Please try again.';
        }
    }
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="../templates.php" class="text-blue-200 hover:text-white mb-4 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Templates
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Set Up Your Website</h1>
        <p class="text-blue-100">Template: <?php echo htmlspecialchars($template['name']); ?></p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): ?>
            <?php echo displayAlert('error', $error); ?>
        <?php endif; ?>
        
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2">
                <!-- Template Preview -->
                <div class="bg-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Template Preview</h3>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                            <div class="text-center text-white">
                                <h4 class="text-xl font-bold mb-2">Your Business Name</h4>
                                <p class="text-blue-100">Your tagline here</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="h-3 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            You can fully customize colors, content, and images after setup.
                        </p>
                    </div>
                </div>
                
                <!-- Setup Form -->
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Basic Information</h3>
                    <form method="POST" action="">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Site Name *</label>
                                <input type="text" name="site_name" 
                                       value="<?php echo htmlspecialchars($profile['business_name'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="My Business Website" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                <input type="text" name="hero_title" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="Welcome to My Business">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                <textarea name="hero_subtitle" rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="A short description of your business..."></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="primary_color" value="#3B82F6"
                                           class="w-12 h-12 rounded cursor-pointer">
                                    <span class="text-sm text-gray-500">Choose your brand color</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex gap-3">
                            <button type="submit" class="flex-1 gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                                <i class="fas fa-check mr-2"></i>Create Website
                            </button>
                            <a href="../templates.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
