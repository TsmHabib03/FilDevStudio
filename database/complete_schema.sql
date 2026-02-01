-- ============================================================================
-- FilDevStudio Complete Database Schema
-- Version: 2.0 (Consolidated)
-- Date: February 2026
-- ============================================================================
-- INSTRUCTIONS:
-- 1. Open phpMyAdmin
-- 2. Drop the existing 'fildevstudio_db' database (if exists)
-- 3. Run this entire file to create fresh database
-- ============================================================================

-- Create Database
DROP DATABASE IF EXISTS fildevstudio_db;
CREATE DATABASE fildevstudio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fildevstudio_db;

-- ============================================================================
-- CORE TABLES
-- ============================================================================

-- Users Table (clients and admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Business Profiles Table
CREATE TABLE business_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_type ENUM('retail', 'food', 'freelance', 'services', 'other') NOT NULL,
    business_name VARCHAR(200) NOT NULL,
    business_description TEXT,
    contact_phone VARCHAR(20),
    contact_email VARCHAR(150),
    address TEXT,
    logo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Templates Table
CREATE TABLE templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('retail', 'food', 'freelance', 'services', 'general', 'sarisari') NOT NULL,
    description TEXT,
    preview_image VARCHAR(255) NOT NULL,
    folder_path VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Client Sites Table (with all columns including social media)
CREATE TABLE client_sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_id INT NOT NULL,
    site_name VARCHAR(200),
    -- Colors
    primary_color VARCHAR(7) DEFAULT '#3B82F6',
    secondary_color VARCHAR(7) DEFAULT '#1E40AF',
    accent_color VARCHAR(7) DEFAULT '#F59E0B',
    -- Typography
    font_heading VARCHAR(50) DEFAULT 'Inter',
    font_body VARCHAR(50) DEFAULT 'Inter',
    -- Content
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    about_content TEXT,
    services_content TEXT,
    contact_info TEXT,
    -- Social Media Links
    social_facebook VARCHAR(255) DEFAULT NULL,
    social_instagram VARCHAR(255) DEFAULT NULL,
    social_tiktok VARCHAR(255) DEFAULT NULL,
    social_twitter VARCHAR(255) DEFAULT NULL,
    social_youtube VARCHAR(255) DEFAULT NULL,
    social_linkedin VARCHAR(255) DEFAULT NULL,
    social_whatsapp VARCHAR(255) DEFAULT NULL,
    social_messenger VARCHAR(255) DEFAULT NULL,
    -- Status & Publishing
    status ENUM('draft', 'active', 'suspended') DEFAULT 'draft',
    subdomain VARCHAR(50) UNIQUE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE RESTRICT,
    INDEX idx_subdomain (subdomain),
    INDEX idx_status_subdomain (status, subdomain)
) ENGINE=InnoDB;

-- ============================================================================
-- CUSTOM REQUESTS SYSTEM (Design Request Workflow)
-- ============================================================================

-- Custom Requests Table (fully updated)
CREATE TABLE custom_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(50) UNIQUE,
    user_id INT NOT NULL,
    site_id INT,
    project_title VARCHAR(255),
    business_type ENUM('sarisari', 'food', 'services', 'retail', 'freelance', 'other') DEFAULT 'other',
    -- Request Details
    request_type VARCHAR(50) NOT NULL DEFAULT 'full_design',
    description TEXT,
    request_details TEXT,
    priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
    -- Files & Preview
    attachment_path VARCHAR(255),
    preview_url VARCHAR(500),
    -- Revision Tracking
    revision_count INT DEFAULT 0,
    revision_notes TEXT,
    -- Budget & Timeline
    budget_range VARCHAR(50),
    timeline VARCHAR(100),
    preferred_colors VARCHAR(100),
    -- Status & Assignment
    status ENUM('pending', 'in_progress', 'preview_ready', 'revision_requested', 'completed', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    assigned_to INT,
    -- Timestamps
    approved_at DATETIME,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (site_id) REFERENCES client_sites(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reference (reference_number),
    INDEX idx_status (status),
    INDEX idx_user_status (user_id, status)
) ENGINE=InnoDB;

-- Request Files Table (for multiple file uploads)
CREATE TABLE request_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100),
    file_size INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES custom_requests(id) ON DELETE CASCADE,
    INDEX idx_request_files (request_id)
) ENGINE=InnoDB;

-- Request Revisions Table (revision history)
CREATE TABLE request_revisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    revision_number INT NOT NULL,
    feedback TEXT NOT NULL,
    submitted_by INT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    admin_response TEXT,
    responded_at DATETIME,
    responded_by INT,
    status ENUM('pending', 'addressed') DEFAULT 'pending',
    FOREIGN KEY (request_id) REFERENCES custom_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (responded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_request_revisions (request_id, revision_number)
) ENGINE=InnoDB;

-- Request Messages Table (client-admin communication)
CREATE TABLE request_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES custom_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_request_messages (request_id, created_at)
) ENGINE=InnoDB;

-- ============================================================================
-- SITE IMAGES & MEDIA
-- ============================================================================

-- Site Images Table
CREATE TABLE site_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('logo', 'hero', 'gallery', 'product', 'other') NOT NULL,
    alt_text VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES client_sites(id) ON DELETE CASCADE,
    INDEX idx_site_images (site_id, image_type)
) ENGINE=InnoDB;

-- ============================================================================
-- NOTIFICATIONS & ACTIVITY
-- ============================================================================

-- Admin Notifications Table
CREATE TABLE admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    link_url VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_notif (is_read, created_at)
) ENGINE=InnoDB;

-- Activity Log Table
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_activity (user_id, created_at)
) ENGINE=InnoDB;

-- ============================================================================
-- SETTINGS
-- ============================================================================

-- Email Settings Table
CREATE TABLE email_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================================
-- DEFAULT DATA
-- ============================================================================

-- Default Admin User (Password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@fildevstudio.com', '$2y$10$sBJd0ElPwHJwFT.2Vm7Qmuxi7gDLtHaqUq.ZukHw5OWZ7toc0wVaK', 'admin');

-- 5 SME-Focused Templates
INSERT INTO templates (id, name, category, description, preview_image, folder_path, is_active) VALUES 
(1, 'Sari-Sari Store', 'sarisari', 'Colorful Filipino neighborhood store template with tingi prices, GCash/Maya payment icons, and WhatsApp ordering', 'assets/images/templates/sari-sari.jpg', 'public/templates/template-1.php', 1),
(2, 'Carinderia & Food', 'food', 'Warm and appetizing template for carinderias, eateries, karinderya, and small food businesses', 'assets/images/templates/carinderia.jpg', 'public/templates/template-2.php', 1),
(3, 'Local Services', 'services', 'Professional template for repair shops, salons, laundry services, and local service providers', 'assets/images/templates/services.jpg', 'public/templates/template-3.php', 1),
(4, 'Small Retail Shop', 'retail', 'Modern retail template for ukay-ukay, RTW shops, gadget stores, and small retail businesses', 'assets/images/templates/retail.jpg', 'public/templates/template-4.php', 1),
(5, 'Freelancer Portfolio', 'freelance', 'Creative dark-themed portfolio for freelancers, designers, developers, and creative professionals', 'assets/images/templates/freelancer.jpg', 'public/templates/template-5.php', 1);

-- Default Email Settings
INSERT INTO email_settings (setting_key, setting_value) VALUES 
('admin_email', 'admin@fildevstudio.com'),
('notification_new_request', '1'),
('notification_revision_request', '1'),
('notification_approval', '1'),
('email_from_name', 'FilDevStudio'),
('email_from_address', 'noreply@fildevstudio.com');

-- ============================================================================
-- VERIFICATION QUERIES (Run these to check structure)
-- ============================================================================
-- SHOW TABLES;
-- DESCRIBE users;
-- DESCRIBE client_sites;
-- DESCRIBE custom_requests;
-- SELECT * FROM templates;
-- SELECT * FROM users;

-- ============================================================================
-- SCHEMA COMPLETE!
-- Default login: admin@fildevstudio.com / admin123
-- ============================================================================
