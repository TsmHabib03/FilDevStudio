-- FilDevStudio Web Services Platform
-- Database Schema
-- Run this file in phpMyAdmin or MySQL CLI to create the database

-- Create Database
CREATE DATABASE IF NOT EXISTS fildevstudio_db;
USE fildevstudio_db;

-- Users Table
-- Stores both clients and admin users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Business Profiles Table
-- Stores client business information
CREATE TABLE IF NOT EXISTS business_profiles (
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
-- Stores available website templates
CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('retail', 'food', 'freelance', 'services', 'general') NOT NULL,
    description TEXT,
    preview_image VARCHAR(255) NOT NULL,
    folder_path VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Client Sites Table
-- Stores client website selections and configurations
CREATE TABLE IF NOT EXISTS client_sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_id INT NOT NULL,
    site_name VARCHAR(200),
    primary_color VARCHAR(7) DEFAULT '#3B82F6',
    secondary_color VARCHAR(7) DEFAULT '#1E40AF',
    accent_color VARCHAR(7) DEFAULT '#F59E0B',
    hero_title VARCHAR(255),
    hero_subtitle TEXT,
    about_content TEXT,
    services_content TEXT,
    contact_info TEXT,
    status ENUM('draft', 'active', 'suspended') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Customization Requests Table
-- Stores client requests for design changes
CREATE TABLE IF NOT EXISTS custom_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    site_id INT,
    request_type ENUM('color', 'layout', 'logo', 'content', 'other') NOT NULL,
    request_details TEXT NOT NULL,
    attachment_path VARCHAR(255),
    status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (site_id) REFERENCES client_sites(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Site Images Table
-- Stores uploaded images for client sites
CREATE TABLE IF NOT EXISTS site_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('logo', 'hero', 'gallery', 'product', 'other') NOT NULL,
    alt_text VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES client_sites(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Activity Log Table
-- Tracks important system activities
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insert Default Admin User
-- Password: admin123 (hashed)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@fildevstudio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Templates
INSERT INTO templates (name, category, description, preview_image, folder_path) VALUES 
('Modern Retail', 'retail', 'Clean and modern template perfect for retail stores and shops', 'assets/templates/retail-modern.jpg', 'templates/retail-modern'),
('Food & Restaurant', 'food', 'Appetizing design for restaurants, cafes, and food businesses', 'assets/templates/food-restaurant.jpg', 'templates/food-restaurant'),
('Freelancer Portfolio', 'freelance', 'Professional portfolio template for freelancers and creatives', 'assets/templates/freelancer-portfolio.jpg', 'templates/freelancer-portfolio'),
('Service Business', 'services', 'Professional template for service-based businesses', 'assets/templates/service-business.jpg', 'templates/service-business'),
('General Business', 'general', 'Versatile template suitable for any business type', 'assets/templates/general-business.jpg', 'templates/general-business');
