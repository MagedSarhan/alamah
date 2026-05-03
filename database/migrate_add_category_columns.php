<?php
/**
 * Migration: Add description and image columns to categories table
 * Run once: php database/migrate_add_category_columns.php
 */
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance();

try {
    // Check if columns exist
    $cols = $db->query("SHOW COLUMNS FROM categories")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('description', $cols)) {
        $db->exec("ALTER TABLE categories ADD COLUMN `description` VARCHAR(255) DEFAULT NULL AFTER `label`");
        echo "✓ Added 'description' column\n";
    } else {
        echo "• 'description' column already exists\n";
    }
    
    if (!in_array('image', $cols)) {
        $db->exec("ALTER TABLE categories ADD COLUMN `image` VARCHAR(255) DEFAULT NULL AFTER `description`");
        echo "✓ Added 'image' column\n";
    } else {
        echo "• 'image' column already exists\n";
    }
    
    echo "\nDone!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
