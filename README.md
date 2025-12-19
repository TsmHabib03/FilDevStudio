# FilDevStudio Web Services Platform

A template-based website builder system for small businesses built with PHP, MySQL, HTML, CSS (Tailwind CSS), and JavaScript.

## 📋 Project Overview

**Company:** FilDevStudio: Code & Creative Solutions  
**System:** Web-based service platform for SMEs  
**Purpose:** Student Project - For Educational Purposes Only

## 🚀 Features

### For Clients (Business Owners)
- User registration and authentication
- Choose from pre-built website templates by business type
- Request custom design updates
- Manage website content (text, images, colors)
- No coding required

### For Admins (FilDevStudio Team)
- Manage website templates (Add/Edit/Delete)
- Review customization requests
- Update client websites
- Manage users and services

## 📁 Project Structure

```
fildevstudio/
├── admin/                  # Admin panel pages
│   ├── dashboard.php       # Admin dashboard
│   ├── templates.php       # Manage templates
│   ├── requests.php        # Manage customization requests
│   ├── users.php           # Manage users
│   └── sites.php           # Manage client sites
├── auth/                   # Authentication pages
│   ├── login.php           # User login
│   ├── register.php        # User registration
│   └── logout.php          # Logout handler
├── client/                 # Client dashboard pages
│   ├── dashboard.php       # Client dashboard
│   ├── select-template.php # Template selection
│   ├── edit-site.php       # Content management
│   ├── custom-request.php  # Request customization
│   ├── preview-site.php    # Preview website
│   └── profile.php         # User profile
├── config/                 # Configuration files
│   └── database.php        # Database connection
├── database/               # Database files
│   └── schema.sql          # Database schema
├── includes/               # Shared components
│   ├── header.php          # Common header/navigation
│   ├── footer.php          # Common footer
│   └── functions.php       # Helper functions
├── assets/                 # Static assets
│   └── images/             # Image files
├── uploads/                # User uploads
├── index.php               # Landing page
├── templates.php           # Template gallery
├── template-preview.php    # Template preview
└── README.md               # This file
```

## 🛠️ Installation

### Requirements
- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser

### Setup Steps

1. **Copy Project Files**
   - Copy the `fildevstudio` folder to `C:\xampp\htdocs\`

2. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL

3. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database named `fildevstudio_db`
   - Import `database/schema.sql` or run the SQL manually

4. **Configure Database** (if needed)
   - Edit `config/database.php`
   - Update credentials if different from default

5. **Access the Platform**
   - Open browser: http://localhost/fildevstudio

## 🔑 Default Login Credentials

### Admin Account
- **Email:** admin@fildevstudio.com
- **Password:** admin123

## 📊 Database Tables

| Table | Description |
|-------|-------------|
| users | User accounts (clients & admins) |
| business_profiles | Client business information |
| templates | Website templates |
| client_sites | Client website configurations |
| custom_requests | Customization requests |
| site_images | Uploaded images |
| activity_log | System activity logs |

## 🎨 Technology Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **CSS Framework:** Tailwind CSS (CDN)
- **Icons:** Font Awesome 6

## 📝 Business Logic

The system reflects FilDevStudio's "whole-team" approach:
- **Technical Team:** Handles system logic and backend
- **Creative Team:** Handles UI, branding, and customization
- **Operations Team:** Manages communication and onboarding

## ⚠️ Notes

- This is a student project for educational purposes
- Default admin password should be changed in production
- File upload limits may need server configuration
- Enable error reporting during development

## 📧 Support

For questions or issues, contact: hello@fildevstudio.com

---

**FilDevStudio: Code & Creative Solutions**  
*Integrated Web & Brand Identity Packages for Local Businesses*
