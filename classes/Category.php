<?php
/**
 * علامة | ALAMAH — Category Model (v2)
 */
require_once __DIR__ . '/Database.php';

class Category {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(bool $activeOnly = true): array {
        $sql = "SELECT * FROM categories";
        if ($activeOnly) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY sort_order ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByKey(string $key): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE key_name = ?");
        $stmt->execute([$key]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO categories (key_name, label, description, image, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['key_name'], $data['label'], $data['description'] ?? null,
            $data['image'] ?? null, $data['sort_order'] ?? 0, $data['is_active'] ?? 1
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE categories SET key_name=?, label=?, description=?, image=?, sort_order=?, is_active=? WHERE id=?");
        return $stmt->execute([
            $data['key_name'], $data['label'], $data['description'] ?? null,
            $data['image'] ?? null, $data['sort_order'] ?? 0, $data['is_active'] ?? 1, $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    }

    public function toApiFormat(array $cat): array {
        return ['key' => $cat['key_name'], 'label' => $cat['label']];
    }
}
