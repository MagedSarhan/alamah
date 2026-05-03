<?php
/**
 * علامة | ALAMAH — Contact Model
 */

require_once __DIR__ . '/Database.php';

class Contact {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO contacts (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['phone'] ?? null, $data['email'] ?? null, $data['subject'] ?? null, $data['message']]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("SELECT * FROM contacts ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function markRead(int $id): bool {
        $stmt = $this->db->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    }

    public function countUnread(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();
    }
}
