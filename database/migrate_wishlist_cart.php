<?php
/**
 * Migration: Create wishlists and abandoned_carts tables
 * Run once: php database/migrate_wishlist_cart.php
 */
require_once __DIR__ . '/../classes/Database.php';
$db = Database::getInstance();

try {
    $db->exec("CREATE TABLE IF NOT EXISTS `wishlists` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `product_id` INT UNSIGNED NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `user_product` (`user_id`, `product_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ wishlists table created\n";

    $db->exec("CREATE TABLE IF NOT EXISTS `abandoned_carts` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED DEFAULT NULL,
        `session_id` VARCHAR(100) DEFAULT NULL,
        `items` JSON NOT NULL,
        `total` DECIMAL(10,2) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ abandoned_carts table created\n";

    // Ensure avatar column exists
    $cols = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('avatar', $cols)) {
        $db->exec("ALTER TABLE users ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL AFTER `status`");
        echo "✓ avatar column added\n";
    } else {
        echo "• avatar column exists\n";
    }

    echo "\nDone!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
