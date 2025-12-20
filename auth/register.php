<?php
/**
 * Registration Page - FilDevStudio Web Services Platform
 * Enhanced UI/UX with Modern Design
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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/logo.svg">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#EEF2FF', 100: '#E0E7FF', 200: '#C7D2FE', 300: '#A5B4FC',
                            400: '#818CF8', 500: '#4F46E5', 600: '#4338CA', 700: '#3730A3',
                            800: '#312E81', 900: '#1E1B4B',
                        },
                        secondary: {
                            50: '#F0FDFA', 100: '#CCFBF1', 200: '#99F6E4', 300: '#5EEAD4',
                            400: '#2DD4BF', 500: '#14B8A6', 600: '#0D9488', 700: '#0F766E',
                        },
                        accent: {
                            400: '#C084FC', 500: '#A855F7', 600: '#9333EA', 700: '#7C3AED',
                        },
                        dark: '#0F172A',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%); }
        .gradient-hero { background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4F46E5 100%); }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Full Page Layout with Side Branding -->
    <div class="min-h-screen flex">
        <!-- Left Side - Branding Panel (hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 gradient-hero relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-64 h-64 bg-primary-400/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-80 h-80 bg-accent-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            </div>
            
            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20">
                <!-- Logo -->
                <a href="../index.php" class="flex items-center space-x-3 mb-12">
                    <img src="../assets/images/logo.png" alt="FilDevStudio Logo" class="w-14 h-14 rounded-xl shadow-lg">
                    <div>
                        <span class="text-2xl font-bold text-white">FilDev<span class="text-primary-300">Studio</span></span>
                        <span class="block text-sm text-primary-200">Code & Creative Solutions</span>
                    </div>
                </a>
                
                <h1 class="text-4xl xl:text-5xl font-bold text-white mb-6 leading-tight">
                    Start Building Your<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary-400 to-accent-400">Online Presence</span>
                </h1>
                <p class="text-xl text-primary-200 mb-8 max-w-md">
                    Create your free account and get access to professional website templates designed for your business.
                </p>
                
                <!-- Benefits List -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 text-primary-100">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check text-secondary-400"></i>
                        </div>
                        <span>Free to start — no credit card required</span>
                    </div>
                    <div class="flex items-center space-x-3 text-primary-100">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-palette text-accent-400"></i>
                        </div>
                        <span>Choose from professionally designed templates</span>
                    </div>
                    <div class="flex items-center space-x-3 text-primary-100">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-headset text-secondary-400"></i>
                        </div>
                        <span>Expert support every step of the way</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gray-50 overflow-y-auto">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="../index.php" class="inline-flex items-center space-x-3">
                        <img src="../assets/images/logo.svg" alt="FilDevStudio Logo" class="w-12 h-12 rounded-xl shadow-lg">
                        <div class="text-left">
                            <span class="text-xl font-bold text-dark">FilDev<span class="text-primary-500">Studio</span></span>
                            <span class="block text-xs text-gray-500">Code & Creative Solutions</span>
                        </div>
                    </a>
                </div>
                
                <!-- Registration Card -->
                <div class="glass rounded-3xl shadow-xl p-8 sm:p-10 border border-gray-100">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-dark mb-2">Create Account</h2>
                        <p class="text-gray-600">Start building your website today</p>
                    </div>
                    
                    <!-- Error Message -->
                    <?php if ($error): ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl">
                            <p class="flex items-center text-red-700 text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?php echo $error; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="space-y-4">
                        <?php if ($templateId): ?>
                            <input type="hidden" name="template_id" value="<?php echo $templateId; ?>">
                        <?php endif; ?>
                        
                        <!-- Personal Info Section -->
                        <div class="pb-4 mb-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                                <i class="fas fa-user mr-2"></i>Personal Information
                            </h3>
                            
                            <!-- Full Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" 
                                           class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                           placeholder="Juan Dela Cruz" required>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                                           class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                           placeholder="you@example.com" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Business Info Section -->
                        <div class="pb-4 mb-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                                <i class="fas fa-store mr-2"></i>Business Information
                            </h3>
                            
                            <!-- Business Type -->
                            <div class="mb-4">
                                <label for="business_type" class="block text-sm font-semibold text-gray-700 mb-2">Business Type</label>
                                <div class="relative">
                                    <select id="business_type" name="business_type" 
                                            class="w-full px-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 appearance-none cursor-pointer" required>
                                        <option value="">Select your business type</option>
                                        <option value="retail" <?php echo $businessType === 'retail' ? 'selected' : ''; ?>>🛒 Retail / Shop</option>
                                        <option value="food" <?php echo $businessType === 'food' ? 'selected' : ''; ?>>🍽️ Food / Restaurant</option>
                                        <option value="freelance" <?php echo $businessType === 'freelance' ? 'selected' : ''; ?>>💼 Freelance / Portfolio</option>
                                        <option value="services" <?php echo $businessType === 'services' ? 'selected' : ''; ?>>🔧 Service Business</option>
                                        <option value="other" <?php echo $businessType === 'other' ? 'selected' : ''; ?>>📦 Other</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Business Name -->
                            <div>
                                <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-2">Business Name</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($businessName); ?>" 
                                           class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                           placeholder="Your Business Name" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password Section -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                                <i class="fas fa-lock mr-2"></i>Security
                            </h3>
                            
                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" id="password" name="password" 
                                           class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                           placeholder="Min. 6 characters" required minlength="6">
                                </div>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" id="confirm_password" name="confirm_password" 
                                           class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                           placeholder="Confirm your password" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="w-full gradient-bg text-white py-4 rounded-xl font-semibold hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center mt-6">
                            <i class="fas fa-user-plus mr-2"></i>Create Account
                        </button>
                    </form>
                    
                    <!-- Login Link -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            Already have an account? 
                            <a href="login.php" class="text-primary-600 font-semibold hover:text-primary-700 transition-colors">Sign in</a>
                        </p>
                    </div>
                </div>
                
                <!-- Back to Home -->
                <div class="mt-6 text-center">
                    <a href="../index.php" class="inline-flex items-center text-gray-500 hover:text-primary-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
