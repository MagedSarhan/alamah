<?php
/**
 * علامة | ALAMAH — Setting Model (v2)
 */
require_once __DIR__ . '/Database.php';

class Setting {
    private PDO $db;
    private static array $cache = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function get(string $key, ?string $default = null): ?string {
        if (isset(self::$cache[$key])) return self::$cache[$key];
        $stmt = $this->db->prepare("SELECT `value` FROM settings WHERE key_name = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        $result = $val !== false ? $val : $default;
        self::$cache[$key] = $result;
        return $result;
    }

    public function set(string $key, ?string $value): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO settings (key_name, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = :v2"
        );
        self::$cache[$key] = $value;
        return $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
    }

    public function getAll(): array {
        $rows = $this->db->query("SELECT key_name, `value` FROM settings ORDER BY key_name")->fetchAll();
        $result = [];
        foreach ($rows as $r) {
            $result[$r['key_name']] = $r['value'];
            self::$cache[$r['key_name']] = $r['value'];
        }
        return $result;
    }

    public function getMultiple(array $keys): array {
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $stmt = $this->db->prepare("SELECT key_name, `value` FROM settings WHERE key_name IN ($placeholders)");
        $stmt->execute($keys);
        $result = [];
        foreach ($stmt->fetchAll() as $r) {
            $result[$r['key_name']] = $r['value'];
        }
        return $result;
    }

    // ── Hero Slides ──
    public function getHeroSlides(bool $activeOnly = true): array {
        $sql = "SELECT * FROM hero_slides";
        if ($activeOnly) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY sort_order ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function createSlide(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO hero_slides (image, alt_text, link, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['image'], $data['alt_text'] ?? null, $data['link'] ?? null, $data['sort_order'] ?? 0, $data['is_active'] ?? 1]);
        return (int) $this->db->lastInsertId();
    }

    public function deleteSlide(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM hero_slides WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleSlide(int $id): bool {
        $stmt = $this->db->prepare("UPDATE hero_slides SET is_active = NOT is_active WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ── Social Links ──
    public function getSocialLinks(bool $activeOnly = true): array {
        $sql = "SELECT * FROM social_links";
        if ($activeOnly) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY sort_order ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function createSocialLink(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO social_links (platform, icon_class, url, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['platform'], $data['icon_class'], $data['url'], $data['sort_order'] ?? 0, $data['is_active'] ?? 1]);
        return (int) $this->db->lastInsertId();
    }

    public function updateSocialLink(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE social_links SET platform=?, icon_class=?, url=?, sort_order=?, is_active=? WHERE id=?");
        return $stmt->execute([$data['platform'], $data['icon_class'], $data['url'], $data['sort_order'] ?? 0, $data['is_active'] ?? 1, $id]);
    }

    public function deleteSocialLink(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM social_links WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ── Abandoned Carts ──
    public function getAbandonedCarts(int $limit = 50): array {
        $stmt = $this->db->prepare(
            "SELECT ac.*, u.name as user_name, u.email as user_email 
             FROM abandoned_carts ac 
             LEFT JOIN users u ON ac.user_id = u.id 
             ORDER BY ac.updated_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        $carts = $stmt->fetchAll();
        foreach ($carts as &$c) {
            $c['items'] = json_decode($c['cart_data'] ?? '[]', true) ?: [];
        }
        return $carts;
    }

    public function countAbandonedCarts(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM abandoned_carts")->fetchColumn();
    }

    public function deleteAbandonedCart(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM abandoned_carts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ── Wishlists ──
    public function getWishlistStats(int $limit = 20): array {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.name, p.image, COUNT(w.id) as wish_count 
             FROM wishlists w 
             JOIN products p ON w.product_id = p.id 
             GROUP BY p.id 
             ORDER BY wish_count DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function countWishlists(): int {
        return (int) $this->db->query("SELECT COUNT(DISTINCT product_id) FROM wishlists")->fetchColumn();
    }
}
