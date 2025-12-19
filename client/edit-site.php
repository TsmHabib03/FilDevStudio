<?php
/**
 * Edit Site Page - Content Management
 */
$pageTitle = "Edit Website - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireLogin();

$siteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get site data
try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name FROM client_sites cs 
                           LEFT JOIN templates t ON cs.template_id = t.id 
                           WHERE cs.id = ? AND cs.user_id = ?");
    $stmt->execute([$siteId, $userId]);
    $site = $stmt->fetch();
    
    if (!$site) {
        redirect('dashboard.php');
    }
    
} catch (Exception $e) {
    redirect('dashboard.php');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name'] ?? '');
    $heroTitle = sanitize($_POST['hero_title'] ?? '');
    $heroSubtitle = sanitize($_POST['hero_subtitle'] ?? '');
    $aboutContent = sanitize($_POST['about_content'] ?? '');
    $servicesContent = sanitize($_POST['services_content'] ?? '');
    $contactInfo = sanitize($_POST['contact_info'] ?? '');
    $primaryColor = sanitize($_POST['primary_color'] ?? '#3B82F6');
    $secondaryColor = sanitize($_POST['secondary_color'] ?? '#1E40AF');
    $accentColor = sanitize($_POST['accent_color'] ?? '#F59E0B');
    
    try {
        $stmt = $pdo->prepare("UPDATE client_sites SET 
                               site_name = ?, hero_title = ?, hero_subtitle = ?, 
                               about_content = ?, services_content = ?, contact_info = ?,
                               primary_color = ?, secondary_color = ?, accent_color = ?,
                               updated_at = NOW()
                               WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $siteName, $heroTitle, $heroSubtitle,
            $aboutContent, $servicesContent, $contactInfo,
            $primaryColor, $secondaryColor, $accentColor,
            $siteId, $userId
        ]);
        
        logActivity($pdo, $userId, 'update_site', "Updated site: $siteName");
        $success = 'Website updated successfully!';
        
        // Refresh site data
        $stmt = $pdo->prepare("SELECT cs.*, t.name as template_name FROM client_sites cs 
                               LEFT JOIN templates t ON cs.template_id = t.id 
                               WHERE cs.id = ?");
        $stmt->execute([$siteId]);
        $site = $stmt->fetch();
        
    } catch (Exception $e) {
        $error = 'Failed to update. Please try again.';
    }
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-white"><?php echo htmlspecialchars($site['site_name'] ?? 'Edit Website'); ?></h1>
                <p class="text-blue-100">Template: <?php echo htmlspecialchars($site['template_name']); ?> • <?php echo getStatusBadge($site['status']); ?></p>
            </div>
            <div class="flex gap-3">
                <a href="custom-request.php?site_id=<?php echo $siteId; ?>" class="bg-white text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                    <i class="fas fa-palette mr-2"></i>Request Customization
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <form method="POST" action="">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content Editor -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-info-circle text-primary mr-2"></i>Basic Information
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                <input type="text" name="site_name" value="<?php echo htmlspecialchars($site['site_name'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-home text-primary mr-2"></i>Hero Section
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                                <input type="text" name="hero_title" value="<?php echo htmlspecialchars($site['hero_title'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="Welcome to Our Business">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                                <textarea name="hero_subtitle" rows="2"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                          placeholder="A short tagline or description..."><?php echo htmlspecialchars($site['hero_subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- About Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-building text-primary mr-2"></i>About Section
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">About Your Business</label>
                            <textarea name="about_content" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Tell visitors about your business..."><?php echo htmlspecialchars($site['about_content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Services Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-concierge-bell text-primary mr-2"></i>Services / Products
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">List Your Services</label>
                            <textarea name="services_content" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Describe your services or products..."><?php echo htmlspecialchars($site['services_content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Contact Section -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-address-card text-primary mr-2"></i>Contact Information
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Details</label>
                            <textarea name="contact_info" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="Phone, email, address..."><?php echo htmlspecialchars($site['contact_info'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Colors -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-palette text-primary mr-2"></i>Colors
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="primary_color" value="<?php echo $site['primary_color'] ?? '#3B82F6'; ?>"
                                           class="w-12 h-10 rounded cursor-pointer border">
                                    <input type="text" value="<?php echo $site['primary_color'] ?? '#3B82F6'; ?>"
                                           class="flex-1 px-3 py-2 border rounded text-sm" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="secondary_color" value="<?php echo $site['secondary_color'] ?? '#1E40AF'; ?>"
                                           class="w-12 h-10 rounded cursor-pointer border">
                                    <input type="text" value="<?php echo $site['secondary_color'] ?? '#1E40AF'; ?>"
                                           class="flex-1 px-3 py-2 border rounded text-sm" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="accent_color" value="<?php echo $site['accent_color'] ?? '#F59E0B'; ?>"
                                           class="w-12 h-10 rounded cursor-pointer border">
                                    <input type="text" value="<?php echo $site['accent_color'] ?? '#F59E0B'; ?>"
                                           class="flex-1 px-3 py-2 border rounded text-sm" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="bg-white rounded-xl shadow p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Actions</h2>
                        <div class="space-y-3">
                            <button type="submit" class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                            <a href="preview-site.php?id=<?php echo $siteId; ?>" target="_blank"
                               class="block w-full text-center border-2 border-primary text-primary py-3 rounded-lg font-semibold hover:bg-primary hover:text-white transition">
                                <i class="fas fa-eye mr-2"></i>Preview Site
                            </a>
                        </div>
                    </div>
                    
                    <!-- Help -->
                    <div class="bg-blue-50 rounded-xl p-6">
                        <h3 class="font-semibold text-blue-800 mb-2">
                            <i class="fas fa-lightbulb mr-2"></i>Tips
                        </h3>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• Keep your hero title short and impactful</li>
                            <li>• Use bullet points in your services section</li>
                            <li>• Include all contact methods customers might use</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
