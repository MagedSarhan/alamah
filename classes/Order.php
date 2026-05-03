<?php
/**
 * علامة | ALAMAH — Order Model
 */

require_once __DIR__ . '/Database.php';

class Order {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(array $data, array $items): int {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id, customer_name, customer_phone, customer_email, total, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['user_id'] ?? null, $data['customer_name'], $data['customer_phone'],
                $data['customer_email'] ?? null, $data['total'], 'new', $data['notes'] ?? null
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                "INSERT INTO order_items (order_id, product_id, product_name, qty, price, custom_data) VALUES (?, ?, ?, ?, ?, ?)"
            );
            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId, $item['product_id'] ?? null, $item['product_name'],
                    $item['qty'], $item['price'],
                    !empty($item['custom_data']) ? json_encode($item['custom_data'], JSON_UNESCAPED_UNICODE) : null
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAll(int $limit = 50, int $offset = 0, ?string $status = null): array {
        $sql = "SELECT * FROM orders";
        $params = [];
        if ($status) { $sql .= " WHERE status = ?"; $params[] = $status; }
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order) {
            $order['items'] = $this->getItems($id);
        }
        return $order ?: null;
    }

    public function getItems(int $orderId): array {
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();
        foreach ($items as &$item) {
            if ($item['custom_data']) {
                $item['custom_data'] = json_decode($item['custom_data'], true);
            }
        }
        return $items;
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(?string $status = null): int {
        $sql = "SELECT COUNT(*) FROM orders";
        if ($status) {
            $stmt = $this->db->prepare($sql . " WHERE status = ?");
            $stmt->execute([$status]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function getByUserId(int $userId, int $limit = 20): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function getRecent(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getTotalRevenue(): float {
        return (float) $this->db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    }
}
