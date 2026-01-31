-- FilDevStudio Migration: Add Subdomain and Publishing Columns
-- Run this migration to add publishing functionality
-- Date: 2026-01-31

-- Add subdomain column (unique, URL-safe identifier for public access)
ALTER TABLE client_sites ADD COLUMN subdomain VARCHAR(50) UNIQUE AFTER status;

-- Add published_at timestamp (tracks when site was published)
ALTER TABLE client_sites ADD COLUMN published_at TIMESTAMP NULL AFTER subdomain;

-- Create index for faster subdomain lookups
CREATE INDEX idx_client_sites_subdomain ON client_sites(subdomain);

-- Create index for public site queries (active + subdomain)
CREATE INDEX idx_client_sites_public ON client_sites(status, subdomain);
