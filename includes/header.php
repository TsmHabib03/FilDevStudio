<?php
/**
 * Header Include
 * Contains navigation and common header elements
 */

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userName = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'FilDevStudio Web Services'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#F59E0B',
                        dark: '#1F2937',
                        light: '#F9FAFB'
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">F</span>
                        </div>
                        <span class="text-xl font-bold text-gray-800">FilDevStudio</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-primary transition">Home</a>
                    <a href="templates.php" class="text-gray-600 hover:text-primary transition">Templates</a>
                    <?php if ($isLoggedIn): ?>
                        <?php if ($isAdmin): ?>
                            <a href="admin/dashboard.php" class="text-gray-600 hover:text-primary transition">Admin Panel</a>
                        <?php else: ?>
                            <a href="client/dashboard.php" class="text-gray-600 hover:text-primary transition">My Dashboard</a>
                        <?php endif; ?>
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-500">Hi, <?php echo htmlspecialchars($userName); ?></span>
                            <a href="auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                                Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="auth/login.php" class="text-gray-600 hover:text-primary transition">Login</a>
                        <a href="auth/register.php" class="gradient-bg text-white px-6 py-2 rounded-lg hover:opacity-90 transition">
                            Get Started
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-primary">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-3 space-y-3">
                <a href="index.php" class="block text-gray-600 hover:text-primary">Home</a>
                <a href="templates.php" class="block text-gray-600 hover:text-primary">Templates</a>
                <?php if ($isLoggedIn): ?>
                    <?php if ($isAdmin): ?>
                        <a href="admin/dashboard.php" class="block text-gray-600 hover:text-primary">Admin Panel</a>
                    <?php else: ?>
                        <a href="client/dashboard.php" class="block text-gray-600 hover:text-primary">My Dashboard</a>
                    <?php endif; ?>
                    <a href="auth/logout.php" class="block text-red-500 hover:text-red-600">Logout</a>
                <?php else: ?>
                    <a href="auth/login.php" class="block text-gray-600 hover:text-primary">Login</a>
                    <a href="auth/register.php" class="block gradient-bg text-white px-4 py-2 rounded-lg text-center">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-grow">
