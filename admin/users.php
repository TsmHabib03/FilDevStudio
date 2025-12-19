<?php
/**
 * Admin - Manage Users
 */
$pageTitle = "Manage Users - FilDevStudio Admin";
require_once '../includes/header.php';
require_once '../includes/functions.php';
requireAdmin();

// Get users
try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT u.*, bp.business_name, bp.business_type,
                         (SELECT COUNT(*) FROM client_sites WHERE user_id = u.id) as site_count,
                         (SELECT COUNT(*) FROM custom_requests WHERE user_id = u.id) as request_count
                         FROM users u 
                         LEFT JOIN business_profiles bp ON u.id = bp.user_id 
                         ORDER BY u.created_at DESC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
}
?>

<!-- Page Header -->
<section class="gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="dashboard.php" class="text-blue-200 hover:text-white mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <h1 class="text-2xl md:text-3xl font-bold text-white">Manage Users</h1>
        <p class="text-blue-100">View and manage registered users</p>
    </div>
</section>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Business</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Role</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Sites</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Requests</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name']); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($user['business_name']): ?>
                                        <p class="text-gray-800"><?php echo htmlspecialchars($user['business_name']); ?></p>
                                        <p class="text-sm text-gray-500 capitalize"><?php echo $user['business_type']; ?></p>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">Admin</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Client</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-medium"><?php echo $user['site_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-medium"><?php echo $user['request_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    <?php echo formatDate($user['created_at']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
