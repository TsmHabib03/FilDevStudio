# FilDevStudio Copilot Instructions

## Project Overview
Template-based website builder for SMEs built with **PHP 7.4+**, **MySQL**, **Tailwind CSS (CDN)**, and **vanilla JavaScript**. Runs on XAMPP (Apache) at `http://localhost/fildevstudio`.

## Architecture

### Directory Structure
- `admin/` - Admin panel (requires `requireAdmin()` check)
- `client/` - Client dashboard (requires `requireLogin()` check)
- `auth/` - Login/register/logout (standalone pages, don't use header.php)
- `includes/` - Shared components: `header.php`, `footer.php`, `functions.php`
- `config/database.php` - PDO connection via `getConnection()`
- `uploads/` - User file uploads (logo, images)

### Database Schema (MySQL)
Key tables: `users`, `business_profiles`, `templates`, `client_sites`, `custom_requests`, `site_images`, `activity_log`
- Users have roles: `'client'` or `'admin'`
- Business types: `'retail'`, `'food'`, `'freelance'`, `'services'`, `'other'`
- Request statuses: `'pending'`, `'in_progress'`, `'completed'`, `'rejected'`

## Code Patterns

### Page Structure
```php
<?php
$pageTitle = "Page Title - FilDevStudio";
require_once 'includes/header.php';   // Or '../includes/header.php' from subdirs
require_once 'includes/functions.php';
requireLogin(); // or requireAdmin() for admin pages
?>
<!-- Page content here -->
<?php require_once 'includes/footer.php'; ?>
```

### Database Access
```php
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(); // Returns associative array
```

### Session Variables
- `$_SESSION['user_id']`, `$_SESSION['user_name']`, `$_SESSION['user_email']`, `$_SESSION['role']`
- Use `isLoggedIn()` and `isAdmin()` helper functions

### Input Handling
- Always use `sanitize($input)` from functions.php for user input
- Use `htmlspecialchars()` when outputting to HTML
- CSRF: `generateCSRFToken()` and `verifyCSRFToken($token)`

### File Uploads
Use `handleFileUpload($file, $destination, $allowedTypes, $maxSize)` - returns `['success' => bool, 'message' => string]`

## Styling Conventions

### Tailwind CSS (CDN)
Custom colors defined in header.php's tailwind.config:
- `primary` (#3B82F6), `secondary` (#1E40AF), `accent` (#F59E0B), `dark` (#1F2937), `light` (#F9FAFB)

### Common CSS Classes
- `.gradient-bg` - Blue gradient background for headers/buttons
- `.card-hover` - Cards with hover animation
- Alert styles via `displayAlert($type, $message)` - types: success, error, warning, info

### Icons
Font Awesome 6.4.0 via CDN: `<i class="fas fa-icon-name"></i>`

## Development Setup
1. Place in `C:\xampp\htdocs\fildevstudio`
2. Start Apache + MySQL in XAMPP
3. Import `database/schema.sql` into `fildevstudio_db`
4. Access at `http://localhost/fildevstudio`

**Default Admin:** admin@fildevstudio.com / admin123

## Important Notes
- Auth pages (`auth/*.php`) have their own HTML structure - don't include header.php
- Relative paths differ between root files and subdirectory files (e.g., `../includes/` vs `includes/`)
- Logo is at `assets/images/fildevstudio logo.ico` or `assets/images/logo.svg`
- Activity logging: `logActivity($pdo, $userId, $action, $description)`
