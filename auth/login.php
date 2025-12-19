<?php
/**
 * Login Page - FilDevStudio Web Services Platform
 * Enhanced UI/UX with Modern Design
 */
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(isAdmin() ? '../admin/dashboard.php' : '../client/dashboard.php');
}

$error = '';
$email = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                logActivity($pdo, $user['id'], 'login', 'User logged in');
                
                redirect($user['role'] === 'admin' ? '../admin/dashboard.php' : '../client/dashboard.php');
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}

$pageTitle = "Login - FilDevStudio";
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
                    Welcome Back to<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary-400 to-accent-400">Your Dashboard</span>
                </h1>
                <p class="text-xl text-primary-200 mb-8 max-w-md">
                    Sign in to manage your websites, track requests, and grow your online presence.
                </p>
                
                <!-- Features List -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 text-primary-100">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-globe text-secondary-400"></i>
                        </div>
                        <span>Manage your websites easily</span>
                    </div>
                    <div class="flex items-center space-x-3 text-primary-100">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-palette text-accent-400"></i>
                        </div>
                        <span>Request custom designs</span>
                    </div>
                    <div class="flex items-center space-x-3 text-primary-100">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-secondary-400"></i>
                        </div>
                        <span>Track your progress</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
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
                
                <!-- Login Card -->
                <div class="glass rounded-3xl shadow-xl p-8 sm:p-10 border border-gray-100">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-dark mb-2">Welcome Back</h2>
                        <p class="text-gray-600">Sign in to your account to continue</p>
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
                    
                    <!-- Success Message -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-xl">
                            <p class="flex items-center text-green-700 text-sm">
                                <i class="fas fa-check-circle mr-2"></i>
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="space-y-5">
                        <!-- Email Field -->
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
                        
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" id="password" name="password" 
                                       class="w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 placeholder-gray-400"
                                       placeholder="••••••••" required>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="w-full gradient-bg text-white py-4 rounded-xl font-semibold hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </form>
                    
                    <!-- Register Link -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            Don't have an account? 
                            <a href="register.php" class="text-primary-600 font-semibold hover:text-primary-700 transition-colors">Create one</a>
                        </p>
                    </div>
                </div>
                
                <!-- Demo Credentials -->
                <div class="mt-6 p-4 bg-primary-50 rounded-xl border border-primary-100">
                    <p class="text-sm text-primary-800 text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Demo Admin:</strong> admin@fildevstudio.com / admin123
                    </p>
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
