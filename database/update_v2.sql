USE alamah_db;

-- Add image and description columns to categories
ALTER TABLE categories ADD COLUMN description VARCHAR(200) DEFAULT NULL AFTER label;
ALTER TABLE categories ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER description;

-- Social links table
CREATE TABLE IF NOT EXISTS social_links (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  platform VARCHAR(50) NOT NULL,
  icon_class VARCHAR(100) NOT NULL,
  url VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Abandoned carts
CREATE TABLE IF NOT EXISTS abandoned_carts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(100) DEFAULT NULL,
  user_id INT UNSIGNED DEFAULT NULL,
  items JSON NOT NULL,
  total DECIMAL(10,2) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wishlists
CREATE TABLE IF NOT EXISTS wishlists (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  session_id VARCHAR(100) DEFAULT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default social links
INSERT INTO social_links (platform, icon_class, url, sort_order) VALUES
('Instagram', 'fa-brands fa-instagram', '#', 1),
('Snapchat', 'fa-brands fa-snapchat', '#', 2),
('Facebook', 'fa-brands fa-facebook-f', '#', 3),
('YouTube', 'fa-brands fa-youtube', '#', 4);

-- Map embed url setting
INSERT IGNORE INTO settings (key_name, `value`) VALUES
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14486.0!2d46.63!3d24.80');
