<?php
/**
 * Template Gallery Page
 */
$pageTitle = "Templates - FilDevStudio";
require_once 'includes/header.php';

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
        ['id' => 1, 'name' => 'Modern Retail', 'category' => 'retail', 'description' => 'Clean template for retail stores and shops', 'preview_image' => 'assets/images/templates/retail.jpg'],
        ['id' => 2, 'name' => 'Restaurant Pro', 'category' => 'food', 'description' => 'Perfect for restaurants, cafes, and food businesses', 'preview_image' => 'assets/images/templates/food.jpg'],
        ['id' => 3, 'name' => 'Freelancer Portfolio', 'category' => 'freelance', 'description' => 'Professional portfolio for freelancers and creatives', 'preview_image' => 'assets/images/templates/freelance.jpg'],
        ['id' => 4, 'name' => 'Service Business', 'category' => 'services', 'description' => 'Template for service-based businesses', 'preview_image' => 'assets/images/templates/services.jpg'],
        ['id' => 5, 'name' => 'General Business', 'category' => 'general', 'description' => 'Versatile template for any business type', 'preview_image' => 'assets/images/templates/general.jpg'],
        ['id' => 6, 'name' => 'E-Commerce Starter', 'category' => 'retail', 'description' => 'Start selling online with this e-commerce template', 'preview_image' => 'assets/images/templates/ecommerce.jpg'],
    ];
}

$categories = [
    'all' => 'All Templates',
    'retail' => 'Retail & Shop',
    'food' => 'Food & Restaurant',
    'freelance' => 'Freelance & Portfolio',
    'services' => 'Service Business',
    'general' => 'General'
];
?>

<!-- Page Header -->
<section class="gradient-bg py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Website Templates</h1>
        <p class="text-blue-100 max-w-2xl mx-auto">Choose a professionally designed template for your business. All templates are fully customizable.</p>
    </div>
</section>

<!-- Filter Section -->
<section class="py-8 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-3">
            <?php foreach ($categories as $key => $label): ?>
                <button class="category-filter px-4 py-2 rounded-full text-sm font-medium transition
                    <?php echo $key === 'all' ? 'gradient-bg text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>"
                    data-category="<?php echo $key; ?>">
                    <?php echo $label; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Templates Grid -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="templates-grid">
            <?php foreach ($templates as $template): ?>
                <div class="template-card bg-white rounded-xl shadow-lg overflow-hidden card-hover" data-category="<?php echo $template['category']; ?>">
                    <!-- Template Preview -->
                    <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
                                <p class="text-gray-400 text-sm">Template Preview</p>
                            </div>
                        </div>
                        <!-- Category Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full capitalize">
                                <?php echo $template['category']; ?>
                            </span>
                        </div>
                    </div>
                    <!-- Template Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($template['name']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($template['description']); ?></p>
                        <div class="flex gap-3">
                            <a href="template-preview.php?id=<?php echo $template['id']; ?>" 
                               class="flex-1 text-center border-2 border-primary text-primary px-4 py-2 rounded-lg font-medium hover:bg-primary hover:text-white transition">
                                <i class="fas fa-eye mr-1"></i> Preview
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <a href="client/select-template.php?id=<?php echo $template['id']; ?>" 
                                   class="flex-1 text-center gradient-bg text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition">
                                    <i class="fas fa-check mr-1"></i> Use This
                                </a>
                            <?php else: ?>
                                <a href="auth/register.php?template=<?php echo $template['id']; ?>" 
                                   class="flex-1 text-center gradient-bg text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition">
                                    <i class="fas fa-check mr-1"></i> Use This
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div id="no-results" class="hidden text-center py-12">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">No templates found in this category.</p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Need a Custom Design?</h2>
        <p class="text-gray-600 mb-6">Our creative team can customize any template to match your brand perfectly.</p>
        <a href="<?php echo isLoggedIn() ? 'client/custom-request.php' : 'auth/register.php'; ?>" 
           class="inline-flex items-center gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
            <i class="fas fa-palette mr-2"></i>Request Customization
        </a>
    </div>
</section>

<script>
// Template filtering
document.querySelectorAll('.category-filter').forEach(button => {
    button.addEventListener('click', function() {
        const category = this.dataset.category;
        
        // Update active button
        document.querySelectorAll('.category-filter').forEach(btn => {
            btn.classList.remove('gradient-bg', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        });
        this.classList.remove('bg-gray-100', 'text-gray-600');
        this.classList.add('gradient-bg', 'text-white');
        
        // Filter templates
        const cards = document.querySelectorAll('.template-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        document.getElementById('no-results').classList.toggle('hidden', visibleCount > 0);
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
