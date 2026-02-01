-- FilDevStudio Template Migration
-- Reduces templates to 5 focused SME templates for Filipino businesses
-- Run this after initial schema.sql

USE fildevstudio_db;

-- First, deactivate all existing templates
UPDATE templates SET is_active = 0;

-- Delete all existing templates and re-insert the 5 SME-focused templates
DELETE FROM templates;

-- Insert 5 SME-focused templates with updated content
INSERT INTO templates (id, name, category, description, preview_image, folder_path, is_active) VALUES 
(1, 'Sari-Sari Store', 'sarisari', 'Colorful, friendly template for neighborhood sari-sari stores. Features tingi prices display, E-Load services, GCash/Maya payments, and Piso WiFi. Perfect for tindahan and convenience stores.', 'assets/images/templates/template-1.svg', 'templates/sari-sari-store', 1),

(2, 'Carinderia & Food Business', 'food', 'Warm, appetizing design for karinderya, catering, food stalls, and small restaurants. Shows menu with prices, operating hours, location, and delivery options. Great for lutong bahay businesses.', 'assets/images/templates/template-2.svg', 'templates/food-business', 1),

(3, 'Local Services', 'services', 'Professional template for laundry shops, repair services, salons, computer shops, and other service businesses. Displays services list, pricing, and contact info with booking features.', 'assets/images/templates/template-3.svg', 'templates/local-services', 1),

(4, 'Small Retail Shop', 'retail', 'Clean, modern design for RTW shops, ukay-ukay, gadget stores, and general merchandise. Product showcase grid, store info, payment methods, and social media integration.', 'assets/images/templates/template-4.svg', 'templates/small-retail', 1),

(5, 'Freelancer Portfolio', 'freelance', 'Minimalist portfolio template for photographers, graphic designers, tutors, and other professionals. Showcases work samples, skills, rates, and contact information.', 'assets/images/templates/template-5.svg', 'templates/freelancer', 1);

-- Note: Template IDs are mapped as follows:
-- Template 1 (Sari-Sari Store) -> Previously template 12
-- Template 2 (Food Business) -> Previously template 2
-- Template 3 (Local Services) -> Previously template 4  
-- Template 4 (Small Retail) -> Previously template 1
-- Template 5 (Freelancer) -> Previously template 3

