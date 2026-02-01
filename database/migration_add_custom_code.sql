-- Migration: Add custom code columns to client_sites
-- Date: 2024
-- Description: Allows developers to add custom CSS, JS, and HTML to client sites

-- Add custom_css column if not exists
ALTER TABLE client_sites ADD COLUMN IF NOT EXISTS custom_css TEXT DEFAULT NULL;

-- Add custom_js column if not exists  
ALTER TABLE client_sites ADD COLUMN IF NOT EXISTS custom_js TEXT DEFAULT NULL;

-- Add custom_html column if not exists
ALTER TABLE client_sites ADD COLUMN IF NOT EXISTS custom_html TEXT DEFAULT NULL;

-- Add preview_token column for secure client preview access
ALTER TABLE client_sites ADD COLUMN IF NOT EXISTS preview_token VARCHAR(64) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN IF NOT EXISTS preview_expires DATETIME DEFAULT NULL;
