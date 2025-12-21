<?php
/**
 * Template Gallery Page - FilDevStudio Web Services Platform
 * Enhanced UI/UX with Modern Design
 */
$pageTitle = "Templates - FilDevStudio";
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Get templates from database
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM templates WHERE is_active = 1 ORDER BY category, name");
    $templates = $stmt->fetchAll();
} catch (Exception $e) {
    $templates = [];
}

// If no database, use sample data
if (empty($templates)) {
    $templates = [
        // Retail Templates
        ['id' => 1, 'name' => 'Modern Retail', 'category' => 'retail', 'description' => 'Elegant, minimal design for luxury retail stores and boutiques', 'preview_image' => 'assets/images/templates/retail.jpg', 'features' => ['Product Grid', 'Cart Ready', 'Mobile First']],
        ['id' => 6, 'name' => 'E-Commerce Starter', 'category' => 'retail', 'description' => 'Start selling online with this conversion-optimized template', 'preview_image' => 'assets/images/templates/ecommerce.jpg', 'features' => ['Product Pages', 'Checkout Flow', 'Inventory']],
        ['id' => 7, 'name' => 'Urban Streetwear', 'category' => 'retail', 'description' => 'Edgy and bold design for fashion and streetwear brands', 'preview_image' => 'assets/images/templates/streetwear.jpg', 'features' => ['Lookbook Style', 'Instagram Feed', 'Dark Theme']],
        ['id' => 9, 'name' => 'Boutique Shop', 'category' => 'retail', 'description' => 'Feminine and elegant design for boutiques and gift shops', 'preview_image' => 'assets/images/templates/boutique.jpg', 'features' => ['Soft Aesthetics', 'Product Showcase', 'Newsletter']],
        ['id' => 10, 'name' => 'Electronics Store', 'category' => 'retail', 'description' => 'Modern tech-focused design for gadget and electronics stores', 'preview_image' => 'assets/images/templates/electronics.jpg', 'features' => ['Spec Display', 'Compare Products', 'Tech Style']],
        ['id' => 11, 'name' => 'Grocery & Supermarket', 'category' => 'retail', 'description' => 'Clean organized layout for grocery stores and supermarkets', 'preview_image' => 'assets/images/templates/grocery.jpg', 'features' => ['Category Grid', 'Weekly Deals', 'Store Locator']],
        
        // Sari-Sari Store (Filipino convenience store)
        ['id' => 12, 'name' => 'Sari-Sari Store', 'category' => 'sarisari', 'description' => 'Colorful, friendly template for neighborhood sari-sari stores', 'preview_image' => 'assets/images/templates/sarisari.jpg', 'features' => ['Tingi Prices', 'Load & Bills', 'Promo Display']],
        ['id' => 13, 'name' => 'Sari-Sari Plus', 'category' => 'sarisari', 'description' => 'Modern sari-sari store with online ordering features', 'preview_image' => 'assets/images/templates/sarisari2.jpg', 'features' => ['Order Online', 'Delivery Info', 'GCash Ready']],
        
        // Food Templates
        ['id' => 2, 'name' => 'Restaurant Pro', 'category' => 'food', 'description' => 'Warm, appetizing design for restaurants and cafes', 'preview_image' => 'assets/images/templates/food.jpg', 'features' => ['Menu Display', 'Reservations', 'Gallery']],
        
        // Freelance Templates
        ['id' => 3, 'name' => 'Freelancer Portfolio', 'category' => 'freelance', 'description' => 'Creative portfolio for designers and developers', 'preview_image' => 'assets/images/templates/freelance.jpg', 'features' => ['Project Gallery', 'Skills Section', 'Contact Form']],
        
        // Service Templates
        ['id' => 4, 'name' => 'Service Business', 'category' => 'services', 'description' => 'Professional design for service-based businesses', 'preview_image' => 'assets/images/templates/services.jpg', 'features' => ['Service List', 'Testimonials', 'Booking']],
        
        // General Templates
        ['id' => 5, 'name' => 'General Business', 'category' => 'general', 'description' => 'Versatile template for any business type', 'preview_image' => 'assets/images/templates/general.jpg', 'features' => ['Flexible Sections', 'Multi-purpose', 'SEO Ready']],
        ['id' => 8, 'name' => 'Tech Startup', 'category' => 'general', 'description' => 'Clean, modern SaaS landing page design', 'preview_image' => 'assets/images/templates/tech.jpg', 'features' => ['SaaS Features', 'Pricing Tables', 'Integration Logos']],
    ];
}

$categories = [
    'all' => ['label' => 'All Templates', 'icon' => 'fas fa-th-large', 'count' => count($templates)],
    'retail' => ['label' => 'Retail & Shop', 'icon' => 'fas fa-shopping-bag', 'count' => 0],
    'sarisari' => ['label' => 'Sari-Sari Store', 'icon' => 'fas fa-store-alt', 'count' => 0],
    'food' => ['label' => 'Food & Restaurant', 'icon' => 'fas fa-utensils', 'count' => 0],
    'freelance' => ['label' => 'Freelance & Portfolio', 'icon' => 'fas fa-briefcase', 'count' => 0],
    'services' => ['label' => 'Service Business', 'icon' => 'fas fa-cogs', 'count' => 0],
    'general' => ['label' => 'General', 'icon' => 'fas fa-globe', 'count' => 0]
];

// Count templates per category
foreach ($templates as $t) {
    if (isset($categories[$t['category']])) {
        $categories[$t['category']]['count']++;
    }
}

$categoryIcons = [
    'retail' => 'fas fa-shopping-bag',
    'sarisari' => 'fas fa-store-alt',
    'food' => 'fas fa-utensils',
    'freelance' => 'fas fa-briefcase',
    'services' => 'fas fa-cogs',
    'general' => 'fas fa-globe'
];

$categoryColors = [
    'retail' => 'from-primary-500 to-primary-600',
    'sarisari' => 'from-yellow-500 to-orange-500',
    'food' => 'from-orange-500 to-red-500',
    'freelance' => 'from-accent-500 to-purple-600',
    'services' => 'from-secondary-500 to-teal-600',
    'general' => 'from-gray-500 to-gray-600'
];
?>

<!-- Hero Header Section -->
<section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-16 lg:py-24">
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary-400/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border border-primary-400/10 rounded-full"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full mb-6">
                <i class="fas fa-sparkles text-secondary-400 mr-2"></i>
                <span class="text-white text-sm font-medium">Premium Quality Templates</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                Website Templates
            </h1>
            <p class="text-xl text-primary-100 max-w-3xl mx-auto mb-10">
                Choose from our collection of professionally designed, fully customizable templates. 
                Each template is crafted to help your business shine online.
            </p>
            
            <!-- Stats Row -->
            <div class="flex flex-wrap justify-center gap-8 md:gap-16">
                <div class="text-center">
                    <p class="text-4xl font-bold text-white"><?php echo count($templates); ?>+</p>
                    <p class="text-primary-200 text-sm">Templates</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">100%</p>
                    <p class="text-primary-200 text-sm">Customizable</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold text-white">24/7</p>
                    <p class="text-primary-200 text-sm">Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter Section -->
<section class="sticky top-16 z-40 bg-white/95 backdrop-blur-lg border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-4">
            <!-- Desktop Filter -->
            <div class="hidden md:flex flex-wrap justify-center gap-3">
                <?php foreach ($categories as $key => $cat): ?>
                    <button class="category-filter group inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                        <?php echo $key === 'all' ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/25' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>"
                        data-category="<?php echo $key; ?>">
                        <i class="<?php echo $cat['icon']; ?> mr-2 text-xs"></i>
                        <?php echo $cat['label']; ?>
                        <span class="ml-2 px-2 py-0.5 text-xs rounded-full 
                            <?php echo $key === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-500 group-hover:bg-gray-300'; ?>">
                            <?php echo $cat['count']; ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile Filter Dropdown -->
            <div class="md:hidden">
                <select id="mobile-category-filter" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <?php foreach ($categories as $key => $cat): ?>
                        <option value="<?php echo $key; ?>"><?php echo $cat['label']; ?> (<?php echo $cat['count']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Templates Grid -->
<section class="py-12 lg:py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Results Count -->
        <div class="flex items-center justify-between mb-8">
            <p class="text-gray-600">
                Showing <span id="results-count" class="font-semibold text-dark"><?php echo count($templates); ?></span> templates
            </p>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <i class="fas fa-info-circle"></i>
                <span>Click preview to see full template</span>
            </div>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="templates-grid">
            <?php foreach ($templates as $template): 
                $catColor = $categoryColors[$template['category']] ?? 'from-gray-500 to-gray-600';
                $catIcon = $categoryIcons[$template['category']] ?? 'fas fa-globe';
                $features = $template['features'] ?? ['Responsive', 'Customizable', 'SEO Ready'];
            ?>
                <div class="template-card group bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-2xl hover:border-primary-100 transition-all duration-300 hover:-translate-y-1" data-category="<?php echo $template['category']; ?>">
                    <!-- Template Preview -->
                    <div class="relative h-56 bg-gradient-to-br <?php echo $catColor; ?> overflow-hidden">
                        <!-- Decorative Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-4 left-4 w-32 h-32 border-2 border-white rounded-lg"></div>
                            <div class="absolute bottom-4 right-4 w-24 h-24 border-2 border-white rounded-full"></div>
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                <i class="<?php echo $catIcon; ?> text-8xl text-white"></i>
                            </div>
                        </div>
                        
                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <a href="template-preview.php?id=<?php echo $template['id']; ?>" 
                               class="inline-flex items-center px-6 py-3 bg-white text-dark font-semibold rounded-xl hover:bg-gray-100 transition transform translate-y-4 group-hover:translate-y-0 duration-300">
                                <i class="fas fa-eye mr-2"></i>Live Preview
                            </a>
                        </div>
                        
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4 z-10">
                            <span class="inline-flex items-center px-3 py-1.5 bg-white/90 backdrop-blur-sm text-dark text-xs font-semibold rounded-lg shadow-sm capitalize">
                                <i class="<?php echo $catIcon; ?> mr-1.5 text-primary-500"></i>
                                <?php echo $template['category']; ?>
                            </span>
                        </div>
                        
                        <!-- Popular Badge (for first 2 templates) -->
                        <?php if ($template['id'] <= 2): ?>
                        <div class="absolute top-4 right-4 z-10">
                            <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-secondary-500 to-secondary-600 text-white text-xs font-semibold rounded-lg shadow-lg">
                                <i class="fas fa-fire mr-1.5"></i>Popular
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Template Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-dark mb-2 group-hover:text-primary-600 transition-colors">
                            <?php echo htmlspecialchars($template['name']); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($template['description']); ?>
                        </p>
                        
                        <!-- Features Tags -->
                        <div class="flex flex-wrap gap-2 mb-5">
                            <?php foreach ($features as $feature): ?>
                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-md">
                                    <i class="fas fa-check text-secondary-500 mr-1.5 text-[10px]"></i>
                                    <?php echo $feature; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <a href="template-preview.php?id=<?php echo $template['id']; ?>" 
                               class="flex-1 text-center border-2 border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-medium hover:border-primary-500 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200">
                                <i class="fas fa-eye mr-1.5"></i>Preview
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <a href="client/select-template.php?id=<?php echo $template['id']; ?>" 
                                   class="flex-1 text-center bg-gradient-to-r from-primary-500 to-primary-600 text-white px-4 py-2.5 rounded-xl font-medium hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all duration-200">
                                    <i class="fas fa-rocket mr-1.5"></i>Use This
                                </a>
                            <?php else: ?>
                                <a href="auth/register.php?template=<?php echo $template['id']; ?>" 
                                   class="flex-1 text-center bg-gradient-to-r from-primary-500 to-primary-600 text-white px-4 py-2.5 rounded-xl font-medium hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all duration-200">
                                    <i class="fas fa-rocket mr-1.5"></i>Use This
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div id="no-results" class="hidden text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-dark mb-2">No templates found</h3>
            <p class="text-gray-500 mb-6">No templates available in this category yet.</p>
            <button class="category-filter inline-flex items-center px-6 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition" data-category="all">
                <i class="fas fa-th-large mr-2"></i>View All Templates
            </button>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 lg:py-20 bg-gradient-to-br from-primary-900 via-primary-800 to-accent-900 relative overflow-hidden">
    <!-- Decorative -->
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-secondary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full mb-6">
            <i class="fas fa-magic text-secondary-400 mr-2"></i>
            <span class="text-white text-sm font-medium">Custom Design Service</span>
        </div>
        
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Need Something Unique?
        </h2>
        <p class="text-xl text-primary-200 mb-8 max-w-2xl mx-auto">
            Our creative team can design a custom website tailored specifically to your brand and business needs.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo isLoggedIn() ? 'client/custom-request.php' : 'auth/register.php'; ?>" 
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-200 shadow-xl">
                <i class="fas fa-palette mr-2"></i>Request Custom Design
            </a>
            <a href="#" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white border border-white/20 rounded-xl font-semibold hover:bg-white/20 transition-all duration-200">
                <i class="fas fa-comments mr-2"></i>Talk to an Expert
            </a>
        </div>
        
        <!-- Trust Indicators -->
        <div class="mt-12 flex flex-wrap justify-center gap-6 text-primary-200 text-sm">
            <span class="flex items-center"><i class="fas fa-check-circle text-secondary-400 mr-2"></i>No upfront payment</span>
            <span class="flex items-center"><i class="fas fa-check-circle text-secondary-400 mr-2"></i>Unlimited revisions</span>
            <span class="flex items-center"><i class="fas fa-check-circle text-secondary-400 mr-2"></i>Fast turnaround</span>
        </div>
    </div>
</section>

<script>
// Template filtering functionality
function filterTemplates(category) {
    const cards = document.querySelectorAll('.template-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease-out';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update results count
    document.getElementById('results-count').textContent = visibleCount;
    
    // Show/hide no results message
    document.getElementById('no-results').classList.toggle('hidden', visibleCount > 0);
}

// Desktop filter buttons
document.querySelectorAll('.category-filter').forEach(button => {
    button.addEventListener('click', function() {
        const category = this.dataset.category;
        
        // Update active button styling
        document.querySelectorAll('.category-filter').forEach(btn => {
            btn.classList.remove('bg-primary-500', 'text-white', 'shadow-lg', 'shadow-primary-500/25');
            btn.classList.add('bg-gray-100', 'text-gray-600');
            // Update count badge
            const badge = btn.querySelector('span:last-child');
            if (badge) {
                badge.classList.remove('bg-white/20', 'text-white');
                badge.classList.add('bg-gray-200', 'text-gray-500');
            }
        });
        this.classList.remove('bg-gray-100', 'text-gray-600');
        this.classList.add('bg-primary-500', 'text-white', 'shadow-lg', 'shadow-primary-500/25');
        // Update count badge for active
        const activeBadge = this.querySelector('span:last-child');
        if (activeBadge) {
            activeBadge.classList.remove('bg-gray-200', 'text-gray-500');
            activeBadge.classList.add('bg-white/20', 'text-white');
        }
        
        // Update mobile dropdown
        document.getElementById('mobile-category-filter').value = category;
        
        filterTemplates(category);
    });
});

// Mobile dropdown filter
document.getElementById('mobile-category-filter').addEventListener('change', function() {
    filterTemplates(this.value);
});

// Add fade-in animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
</script>

<?php require_once 'includes/footer.php'; ?>
