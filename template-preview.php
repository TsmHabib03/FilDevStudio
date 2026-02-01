<?php
/**
 * Template Preview Page - FilDevStudio
 * Uses the same modular templates from public/templates/ for consistency
 */
$pageTitle = "Template Preview - FilDevStudio";
require_once 'includes/header.php';
require_once 'includes/functions.php';

$templateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Template configurations (must match actual templates)
$templates = [
    1 => [
        'name' => 'Sari-Sari Store', 
        'category' => 'sarisari', 
        'description' => 'Colorful, friendly template for neighborhood sari-sari stores',
        'primary' => '#EF4444',
        'secondary' => '#F97316',
        'accent' => '#FCD34D'
    ],
    2 => [
        'name' => 'Carinderia & Food Business', 
        'category' => 'food', 
        'description' => 'Warm, appetizing design for restaurants and cafes',
        'primary' => '#78350F',
        'secondary' => '#FFFBEB',
        'accent' => '#D97706'
    ],
    3 => [
        'name' => 'Local Services', 
        'category' => 'services', 
        'description' => 'Professional, trustworthy design for service companies',
        'primary' => '#0D9488',
        'secondary' => '#F0FDFA',
        'accent' => '#14B8A6'
    ],
    4 => [
        'name' => 'Small Retail Shop', 
        'category' => 'retail', 
        'description' => 'Clean, modern design for retail stores and shops',
        'primary' => '#1F2937',
        'secondary' => '#F9FAFB',
        'accent' => '#3B82F6'
    ],
    5 => [
        'name' => 'Freelancer Portfolio', 
        'category' => 'freelance', 
        'description' => 'Creative, bold portfolio for designers and creatives',
        'primary' => '#0F172A',
        'secondary' => '#8B5CF6',
        'accent' => '#EC4899'
    ],
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

<!-- Color Customizer Preview -->
<section class="py-4 bg-gray-50 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-6">
            <span class="text-sm font-medium text-gray-600">Template Colors:</span>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-white shadow" style="background-color: <?php echo $template['primary']; ?>"></div>
                <span class="text-xs text-gray-500">Primary</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow" style="background-color: <?php echo $template['secondary']; ?>"></div>
                <span class="text-xs text-gray-500">Secondary</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-white shadow" style="background-color: <?php echo $template['accent']; ?>"></div>
                <span class="text-xs text-gray-500">Accent</span>
            </div>
            <span class="text-xs text-gray-400 italic">* Colors are fully customizable</span>
        </div>
    </div>
</section>

<!-- Template Preview Frame -->
<section class="py-8 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Device Toggle -->
        <div class="flex justify-center gap-4 mb-6">
            <button class="device-toggle active px-4 py-2 bg-white rounded-lg shadow hover:shadow-md transition gradient-bg text-white" data-device="desktop">
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
                        www.yourbusiness.fildevstudio.com
                    </div>
                </div>
                <a href="public/template-demo.php?id=<?php echo $templateId; ?>" target="_blank" 
                   class="text-gray-500 hover:text-primary transition" title="Open in new tab">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
            
            <!-- Preview Content - Using iframe for actual template -->
            <iframe id="template-iframe" 
                    src="public/template-demo.php?id=<?php echo $templateId; ?>" 
                    class="w-full border-0"
                    style="height: 700px;"
                    loading="lazy"></iframe>
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
                    <p class="text-gray-600 text-sm">Looks great on all devices - desktop, tablet, and mobile</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Easy Customization</h3>
                    <p class="text-gray-600 text-sm">Change colors, fonts, and content without coding</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">SEO Optimized</h3>
                    <p class="text-gray-600 text-sm">Built for search engines to help customers find you</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Fast Loading</h3>
                    <p class="text-gray-600 text-sm">Optimized for performance on any connection</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Social Media Ready</h3>
                    <p class="text-gray-600 text-sm">Connect your Facebook, Instagram, and more</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Contact Integration</h3>
                    <p class="text-gray-600 text-sm">Built-in contact forms and WhatsApp/Messenger links</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 gradient-bg">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">Ready to get started?</h2>
        <p class="text-blue-100 mb-8">Create your professional website in minutes with this template</p>
        <div class="flex flex-wrap justify-center gap-4">
            <?php if (isLoggedIn()): ?>
                <a href="client/select-template.php?id=<?php echo $templateId; ?>" 
                   class="px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>Start Building Now
                </a>
            <?php else: ?>
                <a href="auth/register.php?template=<?php echo $templateId; ?>" 
                   class="px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>Start Building Now
                </a>
                <a href="auth/login.php" 
                   class="px-8 py-4 border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Continue
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Device toggle functionality
document.querySelectorAll('.device-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const device = this.dataset.device;
        const container = document.getElementById('preview-container');
        const iframe = document.getElementById('template-iframe');
        
        // Update active button
        document.querySelectorAll('.device-toggle').forEach(btn => {
            btn.classList.remove('active', 'gradient-bg', 'text-white');
        });
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

