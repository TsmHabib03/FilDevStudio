-- FilDevStudio Migration: Add Font Customization Columns
-- Run this migration to add font customization support
-- Date: 2026-01-31

-- Add heading font column (defaults to Inter)
ALTER TABLE client_sites ADD COLUMN font_heading VARCHAR(50) DEFAULT 'Inter' AFTER accent_color;

-- Add body font column (defaults to Inter)
ALTER TABLE client_sites ADD COLUMN font_body VARCHAR(50) DEFAULT 'Inter' AFTER font_heading;
