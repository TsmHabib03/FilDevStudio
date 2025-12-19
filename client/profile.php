<?php
/**
 * Client Profile Page
 */
$pageTitle = "My Profile - FilDevStudio";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireLogin();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user and profile data
try {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    
} catch (Exception $e) {
    redirect('dashboard.php');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $businessName = sanitize($_POST['business_name'] ?? '');
    $businessType = sanitize($_POST['business_type'] ?? '');
    $businessDesc = sanitize($_POST['business_description'] ?? '');
    $phone = sanitize($_POST['contact_phone'] ?? '');
    $contactEmail = sanitize($_POST['contact_email'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    if (empty($name) || empty($businessName)) {
        $error = 'Name and Business Name are required.';
    } else {
        try {
            // Update user
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->execute([$name, $userId]);
            $_SESSION['user_name'] = $name;
            
            // Update or insert profile
            if ($profile) {
                $stmt = $pdo->prepare("UPDATE business_profiles SET 
                                       business_name = ?, business_type = ?, business_description = ?,
                                       contact_phone = ?, contact_email = ?, address = ?
                                       WHERE user_id = ?");
                $stmt->execute([$businessName, $businessType, $businessDesc, $phone, $contactEmail, $address, $userId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO business_profiles (user_id, business_name, business_type, business_description, contact_phone, contact_email, address) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $businessName, $businessType, $businessDesc, $phone, $contactEmail, $address]);
            }
            
            $success = 'Profile updated successfully!';
            
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM business_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch();
            
        } catch (Exception $e) {
            $error = 'Failed to update profile. Please try again.';
        }
    }
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">My Profile</h1>
        <p class="text-blue-100">Manage your account and business information</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($error): echo displayAlert('error', $error); endif; ?>
        <?php if ($success): echo displayAlert('success', $success); endif; ?>
        
        <form method="POST" action="">
            <!-- Account Info -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user text-primary mr-2"></i>Account Information
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                        <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                    </div>
                </div>
            </div>
            
            <!-- Business Info -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-store text-primary mr-2"></i>Business Information
                </h2>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                            <input type="text" name="business_name" value="<?php echo htmlspecialchars($profile['business_name'] ?? ''); ?>" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                            <select name="business_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Select type</option>
                                <option value="retail" <?php echo ($profile['business_type'] ?? '') === 'retail' ? 'selected' : ''; ?>>Retail / Shop</option>
                                <option value="food" <?php echo ($profile['business_type'] ?? '') === 'food' ? 'selected' : ''; ?>>Food / Restaurant</option>
                                <option value="freelance" <?php echo ($profile['business_type'] ?? '') === 'freelance' ? 'selected' : ''; ?>>Freelance / Portfolio</option>
                                <option value="services" <?php echo ($profile['business_type'] ?? '') === 'services' ? 'selected' : ''; ?>>Service Business</option>
                                <option value="other" <?php echo ($profile['business_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                        <textarea name="business_description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Brief description of your business..."><?php echo htmlspecialchars($profile['business_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-address-book text-primary mr-2"></i>Contact Information
                </h2>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="contact_phone" value="<?php echo htmlspecialchars($profile['contact_phone'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="+63 XXX XXX XXXX">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Email</label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars($profile['contact_email'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="business@example.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
                        <textarea name="address" rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                  placeholder="Your business address..."><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
                <a href="dashboard.php" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
