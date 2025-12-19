<?php
/**
 * Template Preview Page
 */
$pageTitle = "Template Preview - FilDevStudio";
require_once 'includes/header.php';
require_once 'includes/functions.php';

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Sample template data
$templates = [
    1 => ['name' => 'Modern Retail', 'category' => 'retail', 'description' => 'Clean and modern template perfect for retail stores and shops'],
    2 => ['name' => 'Restaurant Pro', 'category' => 'food', 'description' => 'Appetizing design for restaurants, cafes, and food businesses'],
    3 => ['name' => 'Freelancer Portfolio', 'category' => 'freelance', 'description' => 'Professional portfolio template for freelancers and creatives'],
    4 => ['name' => 'Service Business', 'category' => 'services', 'description' => 'Professional template for service-based businesses'],
    5 => ['name' => 'General Business', 'category' => 'general', 'description' => 'Versatile template suitable for any business type'],
];

$template = $templates[$templateId] ?? null;

if (!$template) {
    redirect('templates.php');
}
?>

<!-- Preview Header -->
<section class="bg-white border-b py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <a href="templates.php" class="text-gray-500 hover:text-primary mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Templates
                </a>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($template['name']); ?></h1>
                <p class="text-gray-600"><?php echo htmlspecialchars($template['description']); ?></p>
            </div>
            <div class="flex gap-3">
                <?php if (isLoggedIn()): ?>
                    <a href="client/select-template.php?id=<?php echo $templateId; ?>" 
                       class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="fas fa-check mr-2"></i>Use This Template
                    </a>
                <?php else: ?>
                    <a href="auth/register.php?template=<?php echo $templateId; ?>" 
                       class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="fas fa-check mr-2"></i>Use This Template
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Template Preview Frame -->
<section class="py-8 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Device Toggle -->
        <div class="flex justify-center gap-4 mb-6">
            <button class="device-toggle active px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition" data-device="desktop">
                <i class="fas fa-desktop mr-2"></i>Desktop
            </button>
            <button class="device-toggle px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition" data-device="tablet">
                <i class="fas fa-tablet-alt mr-2"></i>Tablet
            </button>
            <button class="device-toggle px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition" data-device="mobile">
                <i class="fas fa-mobile-alt mr-2"></i>Mobile
            </button>
        </div>
        
        <!-- Preview Container -->
        <div id="preview-container" class="bg-white rounded-xl shadow-xl overflow-hidden mx-auto transition-all duration-300" style="max-width: 100%;">
            <!-- Browser Chrome -->
            <div class="bg-gray-200 px-4 py-3 flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <div class="flex-1 ml-4">
                    <div class="bg-white rounded-full px-4 py-1 text-sm text-gray-500">
                        www.yourbusiness.com
                    </div>
                </div>
            </div>
            
            <!-- Preview Content -->
            <div class="h-[600px] overflow-y-auto">
                <!-- Sample Template Content -->
                <div class="gradient-bg text-white py-16 px-8">
                    <div class="max-w-3xl mx-auto text-center">
                        <h1 class="text-4xl font-bold mb-4">Your Business Name</h1>
                        <p class="text-xl text-blue-100 mb-8">Your tagline or value proposition goes here</p>
                        <button class="bg-white text-primary px-8 py-3 rounded-lg font-semibold">Get Started</button>
                    </div>
                </div>
                
                <div class="py-12 px-8">
                    <div class="max-w-3xl mx-auto">
                        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Our Services</h2>
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="text-center p-6 bg-gray-50 rounded-xl">
                                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                                <h3 class="font-semibold mb-2">Service One</h3>
                                <p class="text-gray-600 text-sm">Description of your first service</p>
                            </div>
                            <div class="text-center p-6 bg-gray-50 rounded-xl">
                                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-heart text-white"></i>
                                </div>
                                <h3 class="font-semibold mb-2">Service Two</h3>
                                <p class="text-gray-600 text-sm">Description of your second service</p>
                            </div>
                            <div class="text-center p-6 bg-gray-50 rounded-xl">
                                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-bolt text-white"></i>
                                </div>
                                <h3 class="font-semibold mb-2">Service Three</h3>
                                <p class="text-gray-600 text-sm">Description of your third service</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 text-white py-8 px-8 text-center">
                    <p class="text-gray-400">&copy; 2024 Your Business Name. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Template Features -->
<section class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Template Features</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Fully Responsive</h3>
                    <p class="text-gray-600 text-sm">Looks great on all devices</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Easy Customization</h3>
                    <p class="text-gray-600 text-sm">Change colors, fonts, and content</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">SEO Optimized</h3>
                    <p class="text-gray-600 text-sm">Built for search engines</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Fast Loading</h3>
                    <p class="text-gray-600 text-sm">Optimized for performance</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Device toggle functionality
document.querySelectorAll('.device-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const device = this.dataset.device;
        const container = document.getElementById('preview-container');
        
        // Update active button
        document.querySelectorAll('.device-toggle').forEach(btn => btn.classList.remove('active', 'gradient-bg', 'text-white'));
        this.classList.add('active', 'gradient-bg', 'text-white');
        
        // Update preview size
        switch(device) {
            case 'mobile':
                container.style.maxWidth = '375px';
                break;
            case 'tablet':
                container.style.maxWidth = '768px';
                break;
            default:
                container.style.maxWidth = '100%';
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
