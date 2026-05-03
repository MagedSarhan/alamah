<?php
/**
 * علامة | ALAMAH — Product Model
 */

require_once __DIR__ . '/Database.php';

class Product {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(bool $activeOnly = true): array {
        $sql = "SELECT p.*, c.key_name as category_key, c.label as cat_label 
                FROM products p 
                JOIN categories c ON p.category_id = c.id";
        if ($activeOnly) $sql .= " WHERE p.is_active = 1";
        $sql .= " ORDER BY p.sort_order ASC";
        $products = $this->db->query($sql)->fetchAll();

        foreach ($products as &$p) {
            $p['customFields'] = $this->getCustomFields($p['id']);
        }
        return $products;
    }

    public function getByCategory(string $categoryKey): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.key_name as category_key, c.label as cat_label 
             FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE c.key_name = ? AND p.is_active = 1 
             ORDER BY p.sort_order ASC"
        );
        $stmt->execute([$categoryKey]);
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            $p['customFields'] = $this->getCustomFields($p['id']);
        }
        return $products;
    }

    public function getBestsellers(int $limit = 4): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.key_name as category_key, c.label as cat_label 
             FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE p.is_active = 1 AND p.is_bestseller = 1 
             ORDER BY p.sort_order ASC LIMIT ?"
        );
        $stmt->execute([$limit]);
        $products = $stmt->fetchAll();
        foreach ($products as &$p) {
            $p['customFields'] = $this->getCustomFields($p['id']);
        }
        return $products;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.key_name as category_key, c.label as cat_label 
             FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            $product['customFields'] = $this->getCustomFields($product['id']);
        }
        return $product ?: null;
    }

    public function getRelated(int $productId, int $categoryId, int $limit = 4): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.key_name as category_key, c.label as cat_label 
             FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
             ORDER BY RAND() LIMIT ?"
        );
        $stmt->execute([$categoryId, $productId, $limit]);
        $products = $stmt->fetchAll();
        if (count($products) < $limit) {
            $ids = array_merge([$productId], array_column($products, 'id'));
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $remaining = $limit - count($products);
            $stmt2 = $this->db->prepare(
                "SELECT p.*, c.key_name as category_key, c.label as cat_label 
                 FROM products p 
                 JOIN categories c ON p.category_id = c.id 
                 WHERE p.id NOT IN ($placeholders) AND p.is_active = 1 
                 ORDER BY RAND() LIMIT ?"
            );
            $stmt2->execute(array_merge($ids, [$remaining]));
            $products = array_merge($products, $stmt2->fetchAll());
        }
        foreach ($products as &$p) {
            $p['customFields'] = $this->getCustomFields($p['id']);
        }
        return $products;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO products (name, description, price, image, category_id, badge, badge_color, `time`, is_active, is_bestseller, sort_order) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'], $data['description'] ?? null, $data['price'], $data['image'] ?? null,
            $data['category_id'], $data['badge'] ?? null, $data['badge_color'] ?? null,
            $data['time'] ?? null, $data['is_active'] ?? 1, $data['is_bestseller'] ?? 0,
            $data['sort_order'] ?? 0
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE products SET name=?, description=?, price=?, image=?, category_id=?, badge=?, badge_color=?, `time`=?, is_active=?, is_bestseller=?, sort_order=? WHERE id=?"
        );
        return $stmt->execute([
            $data['name'], $data['description'] ?? null, $data['price'], $data['image'] ?? null,
            $data['category_id'], $data['badge'] ?? null, $data['badge_color'] ?? null,
            $data['time'] ?? null, $data['is_active'] ?? 1, $data['is_bestseller'] ?? 0,
            $data['sort_order'] ?? 0, $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    // ── Custom Fields ──

    public function getCustomFields(int $productId): array {
        $stmt = $this->db->prepare("SELECT * FROM product_custom_fields WHERE product_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function saveCustomFields(int $productId, array $fields): void {
        $this->db->prepare("DELETE FROM product_custom_fields WHERE product_id = ?")->execute([$productId]);
        $stmt = $this->db->prepare("INSERT INTO product_custom_fields (product_id, label, type, is_required, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($fields as $i => $f) {
            if (!empty($f['label'])) {
                $stmt->execute([$productId, $f['label'], $f['type'] ?? 'text', $f['is_required'] ?? 0, $i + 1]);
            }
        }
    }

    /**
     * Format product for JSON API (matching the JS format)
     */
    public function toApiFormat(array $product): array {
        return [
            'id'           => (int)$product['id'],
            'name'         => $product['name'],
            'description'  => $product['description'] ?? '',
            'price'        => (float)$product['price'],
            'image'        => $product['image'],
            'category'     => $product['category_key'],
            'catLabel'     => $product['cat_label'],
            'badge'        => $product['badge'] ?? '',
            'badgeColor'   => $product['badge_color'] ?? '',
            'time'         => $product['time'] ?? '',
            'customFields' => array_map(fn($f) => [
                'label'    => $f['label'],
                'type'     => $f['type'],
                'required' => (bool)$f['is_required']
            ], $product['customFields'] ?? [])
        ];
    }
}
