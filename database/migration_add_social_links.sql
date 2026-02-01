-- FilDevStudio Migration: Add Social Media Links to client_sites
-- Run this file in phpMyAdmin or MySQL CLI

USE fildevstudio_db;

-- Add social media columns to client_sites table
ALTER TABLE client_sites ADD COLUMN social_facebook VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_instagram VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_tiktok VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_twitter VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_youtube VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_linkedin VARCHAR(255) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_whatsapp VARCHAR(20) DEFAULT NULL;
ALTER TABLE client_sites ADD COLUMN social_messenger VARCHAR(255) DEFAULT NULL;

-- Verify columns were added
DESCRIBE client_sites;
