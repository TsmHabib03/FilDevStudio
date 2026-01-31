<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - FilDevStudio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#F59E0B'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center shadow-xl">
                <i class="fas fa-exclamation-triangle text-white text-5xl"></i>
            </div>
        </div>
        
        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
        
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Sorry, the site you're looking for doesn't exist or hasn't been published yet.
        </p>
        
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/fildevstudio/" class="inline-flex items-center px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-blue-600 transition shadow-lg">
                <i class="fas fa-home mr-2"></i>Go to Homepage
            </a>
            <a href="/fildevstudio/templates.php" class="inline-flex items-center px-6 py-3 border-2 border-primary text-primary font-medium rounded-lg hover:bg-primary hover:text-white transition">
                <i class="fas fa-rocket mr-2"></i>Create Your Site
            </a>
        </div>
        
        <div class="mt-12 text-sm text-gray-500">
            <p>Powered by <a href="/fildevstudio/" class="text-primary hover:underline">FilDevStudio</a></p>
        </div>
    </div>
</body>
</html>
