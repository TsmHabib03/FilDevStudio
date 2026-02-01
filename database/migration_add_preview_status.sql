-- Migration: Add preview_ready status and reference_number to custom_requests
-- Date: 2024
-- Description: Adds preview_ready status for developer workflow and reference tracking

-- Add preview_ready to status enum
ALTER TABLE custom_requests 
MODIFY COLUMN status ENUM('pending', 'in_progress', 'preview_ready', 'completed', 'rejected') DEFAULT 'pending';

-- Add reference_number column if not exists (for tracking)
ALTER TABLE custom_requests ADD COLUMN IF NOT EXISTS reference_number VARCHAR(20) DEFAULT NULL;

-- Update existing rows to have reference numbers
UPDATE custom_requests SET reference_number = CONCAT('REQ-', LPAD(id, 4, '0')) WHERE reference_number IS NULL;
