SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

CREATE DATABASE IF NOT EXISTS `alamah_db`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `alamah_db`;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `is_verified` TINYINT(1) DEFAULT 0,
  `is_admin` TINYINT(1) DEFAULT 0,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active','banned') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `verification_codes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `code` VARCHAR(10) NOT NULL,
  `type` ENUM('email_verify','password_reset') DEFAULT 'email_verify',
  `expires_at` DATETIME NOT NULL,
  `is_used` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(50) NOT NULL UNIQUE,
  `label` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `badge` VARCHAR(50) DEFAULT NULL,
  `badge_color` VARCHAR(20) DEFAULT NULL,
  `time` VARCHAR(50) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `is_bestseller` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_custom_fields` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(200) NOT NULL,
  `type` ENUM('text','textarea') DEFAULT 'text',
  `is_required` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `customer_email` VARCHAR(150) DEFAULT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `status` ENUM('new','processing','completed','cancelled') DEFAULT 'new',
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `qty` INT UNSIGNED NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `custom_data` JSON DEFAULT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(100) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `image` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(200) DEFAULT NULL,
  `link` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(100) DEFAULT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_product` (`user_id`, `product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `abandoned_carts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(100) DEFAULT NULL,
  `items` JSON NOT NULL,
  `total` DECIMAL(10,2) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `social_links` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `platform` VARCHAR(50) NOT NULL,
  `icon_class` VARCHAR(100) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

INSERT IGNORE INTO `users`
(`id`, `name`, `email`, `password_hash`, `is_verified`, `is_admin`)
VALUES
(1, 'مدير الموقع', 'admin@alamah.sa', '$2y$10$Ys25ckvR4G4r/.jzl0AR.eSbyGNFk5nW5SdPu4a0RkNFE5ugVPTVm', 1, 1);

INSERT IGNORE INTO `categories`
(`id`, `key_name`, `label`, `sort_order`, `is_active`)
VALUES
(1, 'giveaways', 'هدايا دعائية', 1, 1),
(2, 'car-hangers', 'معلقات سيارات', 2, 1),
(3, 'stickers', 'ستيكرات أجهزة', 3, 1),
(4, 'laser', 'حفر بالليزر', 4, 1),
(5, 'occasions', 'مناسبات', 5, 1),
(6, 'custom', 'طلبات خاصة', 6, 1);

INSERT IGNORE INTO `products`
(`id`, `name`, `price`, `image`, `category_id`, `badge`, `badge_color`, `time`, `is_active`, `is_bestseller`, `sort_order`)
VALUES
(1, 'طقم هدايا دعائية فاخر', 45.00, 'image/cat-giveaways.png', 1, 'الأكثر مبيعاً', NULL, '٢٤ ساعة', 1, 1, 1),
(2, 'معلقة سيارة محفورة بالاسم', 35.00, 'image/cat-car-hangers.png', 2, NULL, NULL, '٤٨ ساعة', 1, 1, 2),
(3, 'ستيكر جوال بتصميم خاص', 25.00, 'image/hero-1.png', 3, 'جديد', '#C9A96E', '٢٤ ساعة', 1, 1, 3),
(4, 'لوحة خشبية محفورة بالليزر', 65.00, 'image/hero-3.png', 4, NULL, NULL, '٣ أيام', 1, 1, 4),
(5, 'بوكس هدية مناسبات', 85.00, 'image/hero-2.png', 5, 'مميز', '#C9A96E', '٣ أيام', 1, 0, 5),
(6, 'طقم أقلام محفورة', 55.00, 'image/cat-giveaways.png', 1, NULL, NULL, '٤٨ ساعة', 1, 0, 6),
(7, 'معلقة سيارة كريستال', 50.00, 'image/cat-car-hangers.png', 2, NULL, NULL, '٣ أيام', 1, 0, 7),
(8, 'تصميم خاص حسب الطلب', 120.00, 'image/custom-order-banner.png', 6, 'حسب الطلب', '#1B2A5B', 'يُحدد لاحقاً', 1, 0, 8),
(9, 'ميدالية مفاتيح محفورة', 20.00, 'image/hero-1.png', 4, NULL, NULL, '٢٤ ساعة', 1, 0, 9),
(10, 'ستيكر لابتوب مخصص', 30.00, 'image/hero-2.png', 3, NULL, NULL, '٢٤ ساعة', 1, 0, 10),
(11, 'بوكس تخرج فاخر', 95.00, 'image/hero-3.png', 5, NULL, NULL, '٣ أيام', 1, 0, 11),
(12, 'طقم هدايا شركات', 150.00, 'image/cat-giveaways.png', 1, 'للشركات', '#1B2A5B', '٥ أيام', 1, 0, 12);

INSERT IGNORE INTO `product_custom_fields`
(`product_id`, `label`, `type`, `is_required`, `sort_order`)
VALUES
(2, 'الاسم المراد حفره', 'text', 1, 1),
(3, 'وصف التصميم المطلوب', 'textarea', 1, 1),
(4, 'النص المراد حفره', 'text', 1, 1),
(4, 'ملاحظات إضافية', 'textarea', 0, 2),
(5, 'اسم المهدى إليه', 'text', 0, 1),
(6, 'الاسم أو الشعار المراد حفره', 'text', 1, 1),
(7, 'الاسم المراد حفره', 'text', 1, 1),
(8, 'وصف الطلب بالتفصيل', 'textarea', 1, 1),
(8, 'الكمية المطلوبة', 'text', 1, 2),
(9, 'الاسم المراد حفره', 'text', 1, 1),
(10, 'وصف التصميم', 'textarea', 0, 1),
(11, 'اسم الخريج/ة', 'text', 1, 1),
(12, 'اسم الشركة', 'text', 1, 1),
(12, 'الكمية', 'text', 1, 2);

INSERT IGNORE INTO `hero_slides`
(`id`, `image`, `alt_text`, `sort_order`, `is_active`)
VALUES
(1, 'image/hero-1.png', 'حملة علامة الرئيسية', 1, 1),
(2, 'image/hero-2.png', 'هدايا مميزة من علامة', 2, 1),
(3, 'image/hero-3.png', 'حفر بالليزر من علامة', 3, 1);

INSERT IGNORE INTO `settings`
(`key_name`, `value`)
VALUES
('site_name', 'علامة | ALAMAH'),
('site_description', 'منتجات مخصصة فاخرة، حفر بالليزر، هدايا دعائية، ومعلقات سيارات'),
('whatsapp_number', '967784449090'),
('phone', '967784449090'),
('email', 'info@alamah.sa'),
('address', 'الرياض - حي الملقا - طريق الامير محمد بن سعد'),
('instagram', '#'),
('snapchat', '#'),
('facebook', '#'),
('youtube', '#'),
('stats_orders', '+5000'),
('stats_designs', '+200'),
('stats_satisfaction', '98%'),
('stats_categories', '+15'),
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14486.0!2d46.63!3d24.80');

INSERT IGNORE INTO `social_links`
(`id`, `platform`, `icon_class`, `url`, `sort_order`, `is_active`)
VALUES
(1, 'Instagram', 'fa-brands fa-instagram', '#', 1, 1),
(2, 'Snapchat', 'fa-brands fa-snapchat', '#', 2, 1),
(3, 'Facebook', 'fa-brands fa-facebook-f', '#', 3, 1),
(4, 'YouTube', 'fa-brands fa-youtube', '#', 4, 1);