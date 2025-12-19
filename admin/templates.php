<?php
/**
 * Admin - Manage Templates
 */
$pageTitle = "Manage Templates - FilDevStudio Admin";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireAdmin();

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getConnection();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = sanitize($_POST['name']);
                $category = sanitize($_POST['category']);
                $description = sanitize($_POST['description']);
                $previewImage = sanitize($_POST['preview_image']);
                $folderPath = sanitize($_POST['folder_path']);
                
                if (!empty($name) && !empty($category)) {
                    $stmt = $pdo->prepare("INSERT INTO templates (name, category, description, preview_image, folder_path) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $category, $description, $previewImage, $folderPath]);
                    $success = 'Template added successfully!';
                } else {
                    $error = 'Name and category are required.';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['template_id'];
                $stmt = $pdo->prepare("UPDATE templates SET is_active = 0 WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Template deactivated.';
                break;
                
            case 'activate':
                $id = (int)$_POST['template_id'];
                $stmt = $pdo->prepare("UPDATE templates SET is_active = 1 WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Template activated.';
                break;
        }
    }
}

// Get templates
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM templates ORDER BY category, name");
    $templates = $stmt->fetchAll();
} catch (Exception $e) {
    $templates = [];
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
                <h1 class="text-2xl md:text-3xl font-bold text-white">Manage Templates</h1>
                <p class="text-blue-100">Add, edit, and manage website templates</p>
            </div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                    class="bg-white text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition">
                <i class="fas fa-plus mr-2"></i>Add Template
            </button>
        </div>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <!-- Templates Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Template</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Description</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($templates as $template): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-code text-gray-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($template['name']); ?></p>
                                            <p class="text-sm text-gray-500">ID: <?php echo $template['id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm capitalize">
                                        <?php echo $template['category']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                                    <?php echo htmlspecialchars($template['description'] ?? '-'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($template['is_active']): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="../template-preview.php?id=<?php echo $template['id']; ?>" 
                                           class="text-blue-500 hover:text-blue-700" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($template['is_active']): ?>
                                            <form method="POST" class="inline" onsubmit="return confirm('Deactivate this template?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Deactivate">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="activate">
                                                <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                                                <button type="submit" class="text-green-500 hover:text-green-700" title="Activate">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($templates)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No templates found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Add Template Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Add New Template</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template Name *</label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="retail">Retail</option>
                        <option value="food">Food</option>
                        <option value="freelance">Freelance</option>
                        <option value="services">Services</option>
                        <option value="general">General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Image Path</label>
                    <input type="text" name="preview_image" placeholder="assets/images/templates/name.jpg"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Folder Path</label>
                    <input type="text" name="folder_path" placeholder="templates/name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="flex-1 gradient-bg text-white py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Add Template
                </button>
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
