-- ============================================================================
-- Fix Notifications and Request Files Tables
-- Run this if you're experiencing issues with notifications or file uploads
-- ============================================================================

-- Drop and recreate admin_notifications table with correct structure
DROP TABLE IF EXISTS admin_notifications;
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

-- Ensure request_files table exists with correct structure
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

-- Ensure request_revisions table exists
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
    INDEX idx_request_revisions (request_id, revision_number)
) ENGINE=InnoDB;

-- Ensure request_messages table exists
CREATE TABLE IF NOT EXISTS request_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_request_messages (request_id, created_at)
) ENGINE=InnoDB;

-- Add a test notification to verify everything works
INSERT INTO admin_notifications (type, title, message, link_url, is_read, created_at)
VALUES ('new_request', 'Test Notification', 'This is a test notification to verify the system is working.', 'requests.php', 0, NOW());

SELECT 'Notification and request tables fixed successfully!' as Status;
