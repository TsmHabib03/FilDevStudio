<?php
/**
 * Registration Page
 */
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('../client/dashboard.php');
}

$error = '';
$success = '';
$name = '';
$email = '';
$businessType = '';
$businessName = '';
$templateId = isset($_GET['template']) ? (int)$_GET['template'] : 0;

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $businessType = sanitize($_POST['business_type'] ?? '');
    $businessName = sanitize($_POST['business_name'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($businessType) || empty($businessName)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo = getConnection();
            
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered. Please login instead.';
            } else {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')");
                $stmt->execute([$name, $email, $hashedPassword]);
                $userId = $pdo->lastInsertId();
                
                // Create business profile
                $stmt = $pdo->prepare("INSERT INTO business_profiles (user_id, business_type, business_name) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $businessType, $businessName]);
                
                logActivity($pdo, $userId, 'register', 'New user registered');
                
                $_SESSION['success'] = 'Registration successful! Please login.';
                redirect('login.php');
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}

$pageTitle = "Register - FilDevStudio";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#F59E0B',
                        dark: '#1F2937'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="../index.php" class="inline-flex items-center space-x-2">
                <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-2xl">F</span>
                </div>
                <span class="text-2xl font-bold text-gray-800">FilDevStudio</span>
            </a>
        </div>
        
        <!-- Registration Card -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-2">Create Account</h2>
            <p class="text-gray-600 text-center mb-6">Start building your website today</p>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?></p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php if ($templateId): ?>
                    <input type="hidden" name="template_id" value="<?php echo $templateId; ?>">
                <?php endif; ?>
                
                <!-- Personal Info -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Juan Dela Cruz" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="you@example.com" required>
                    </div>
                </div>
                
                <!-- Business Info -->
                <div class="mb-4">
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">Business Type *</label>
                    <select id="business_type" name="business_type" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="">Select your business type</option>
                        <option value="retail" <?php echo $businessType === 'retail' ? 'selected' : ''; ?>>Retail / Shop</option>
                        <option value="food" <?php echo $businessType === 'food' ? 'selected' : ''; ?>>Food / Restaurant</option>
                        <option value="freelance" <?php echo $businessType === 'freelance' ? 'selected' : ''; ?>>Freelance / Portfolio</option>
                        <option value="services" <?php echo $businessType === 'services' ? 'selected' : ''; ?>>Service Business</option>
                        <option value="other" <?php echo $businessType === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-store"></i>
                        </span>
                        <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($businessName); ?>" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Your Business Name" required>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Min. 6 characters" required minlength="6">
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <button type="submit" class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-primary font-semibold hover:underline">Sign in</a>
                </p>
            </div>
        </div>
        
        <!-- Back Link -->
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-gray-500 hover:text-primary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</body>
</html>
