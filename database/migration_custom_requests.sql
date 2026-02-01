-- FilDevStudio Custom Request System Enhancement
-- Migration: Add columns and tables for enhanced custom request workflow
-- Run this migration in phpMyAdmin or MySQL CLI
-- NOTE: MySQL doesn't support IF NOT EXISTS for ADD COLUMN, so errors for existing columns are expected

USE fildevstudio_db;

-- ============================================================================
-- STEP 1: Add new columns to custom_requests table
-- Run each ALTER separately. If column already exists, it will error (that's OK)
-- ============================================================================

-- Add reference number for easy tracking
ALTER TABLE custom_requests ADD COLUMN reference_number VARCHAR(20) UNIQUE AFTER id;

-- Add project details
ALTER TABLE custom_requests ADD COLUMN project_title VARCHAR(255) AFTER site_id;
ALTER TABLE custom_requests ADD COLUMN business_type ENUM('sarisari', 'food', 'services', 'retail', 'freelance', 'other') DEFAULT 'other' AFTER project_title;

-- Add preview and revision workflow
ALTER TABLE custom_requests ADD COLUMN preview_url VARCHAR(500) AFTER attachment_path;
ALTER TABLE custom_requests ADD COLUMN revision_count INT DEFAULT 0 AFTER preview_url;
ALTER TABLE custom_requests ADD COLUMN revision_notes TEXT AFTER revision_count;

-- Add budget and timeline
ALTER TABLE custom_requests ADD COLUMN budget_range VARCHAR(50) AFTER revision_notes;
ALTER TABLE custom_requests ADD COLUMN timeline VARCHAR(100) AFTER budget_range;

-- Add color preferences
ALTER TABLE custom_requests ADD COLUMN preferred_colors VARCHAR(100) AFTER timeline;

-- Add approval tracking
ALTER TABLE custom_requests ADD COLUMN approved_at DATETIME AFTER assigned_to;
ALTER TABLE custom_requests ADD COLUMN completed_at DATETIME AFTER approved_at;

-- Update status ENUM to include new workflow states
ALTER TABLE custom_requests MODIFY COLUMN status ENUM('pending', 'in_progress', 'preview_ready', 'revision_requested', 'completed', 'approved', 'rejected') DEFAULT 'pending';

-- ============================================================================
-- STEP 2: Create request_files table for multiple file uploads
-- ============================================================================

CREATE TABLE IF NOT EXISTS request_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    file_type ENUM('logo', 'product', 'reference', 'asset', 'other') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255),
    file_size INT,
    mime_type VARCHAR(100),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES custom_requests(id) ON DELETE CASCADE,
    INDEX idx_request_files (request_id, file_type)
) ENGINE=InnoDB;

-- ============================================================================
-- STEP 3: Create request_revisions table for revision history
-- ============================================================================

CREATE TABLE IF NOT EXISTS request_revisions (
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

-- ============================================================================
-- STEP 4: Create request_messages table for communication
-- ============================================================================

CREATE TABLE IF NOT EXISTS request_messages (
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
-- STEP 5: Add email notification settings table
-- ============================================================================

CREATE TABLE IF NOT EXISTS email_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default email settings
INSERT INTO email_settings (setting_key, setting_value) VALUES 
('admin_email', 'admin@fildevstudio.com'),
('notification_new_request', '1'),
('notification_revision_request', '1'),
('notification_approval', '1'),
('email_from_name', 'FilDevStudio'),
('email_from_address', 'noreply@fildevstudio.com')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- ============================================================================
-- STEP 6: Create function to generate reference number (if supported)
-- Note: Run this separately if needed
-- ============================================================================

-- Reference numbers format: FDS-YYMMDD-XXXX (e.g., FDS-260201-0001)

-- ============================================================================
-- VERIFICATION: Check the updated structure
-- ============================================================================

-- Run these to verify:
-- DESCRIBE custom_requests;
-- DESCRIBE request_files;
-- DESCRIBE request_revisions;
-- DESCRIBE request_messages;
-- SELECT * FROM email_settings;

