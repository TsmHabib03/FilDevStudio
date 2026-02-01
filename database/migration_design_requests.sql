-- FilDevStudio Database Migration
-- Adds tables and columns for the design request system
-- Run this migration after the initial schema.sql
-- Compatible with MySQL 5.7+ and MariaDB

USE fildevstudio_db;

-- =====================================================
-- SOCIAL MEDIA COLUMNS FOR client_sites
-- Run each one separately. If a column already exists,
-- you'll get "Duplicate column name" error - just skip it.
-- =====================================================

-- Check if columns exist before adding (run one at a time)
ALTER TABLE client_sites ADD COLUMN social_facebook VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_instagram VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_tiktok VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_twitter VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_youtube VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_linkedin VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_whatsapp VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_messenger VARCHAR(255) DEFAULT NULL;

-- =====================================================
-- MODIFY custom_requests TABLE
-- =====================================================

-- Change request_type to VARCHAR to allow new values
ALTER TABLE custom_requests MODIFY COLUMN request_type VARCHAR(50) NOT NULL DEFAULT 'full_design';

-- Add new columns (run one at a time, skip if error "Duplicate column")
ALTER TABLE custom_requests ADD COLUMN description TEXT;
ALTER TABLE custom_requests ADD COLUMN priority ENUM('low', 'normal', 'high') DEFAULT 'normal';
ALTER TABLE custom_requests ADD COLUMN reference_number VARCHAR(50);

-- Add unique index to reference_number (ignore error if already exists)
ALTER TABLE custom_requests ADD UNIQUE INDEX idx_reference_number (reference_number);

-- =====================================================
-- CREATE NEW TABLES (safe to run multiple times)
-- =====================================================

-- Request Files Table
CREATE TABLE IF NOT EXISTS request_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100),
    file_size INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_request_files (request_id)
) ENGINE=InnoDB;

-- Admin Notifications Table
CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    link_url VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_notif (is_read, created_at)
) ENGINE=InnoDB;
