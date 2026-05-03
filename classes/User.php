<?php
/**
 * علامة | ALAMAH — User Model
 */

require_once __DIR__ . '/Database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(string $name, string $email, string $password): int {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);
        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function verifyEmail(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$hash, $userId]);
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function getAll(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("SELECT id, name, email, is_verified, is_admin, status, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    public function toggleAdmin(int $id): bool {
        $stmt = $this->db->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleStatus(int $id): bool {
        $stmt = $this->db->prepare("UPDATE users SET status = IF(status='active','banned','active') WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
        return $stmt->execute([$id]);
    }

    // ── Verification Codes ──

    public function createVerificationCode(int $userId, string $type = 'email_verify'): string {
        // Invalidate old codes
        $stmt = $this->db->prepare("UPDATE verification_codes SET is_used = 1 WHERE user_id = ? AND type = ? AND is_used = 0");
        $stmt->execute([$userId, $type]);

        $code = str_pad(random_int(0, 999999), VERIFICATION_CODE_LENGTH, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_CODE_EXPIRY . ' minutes'));

        $stmt = $this->db->prepare("INSERT INTO verification_codes (user_id, code, type, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $code, $type, $expiresAt]);

        return $code;
    }

    public function verifyCode(int $userId, string $code, string $type = 'email_verify'): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM verification_codes WHERE user_id = ? AND code = ? AND type = ? AND is_used = 0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$userId, $code, $type]);
        $row = $stmt->fetch();

        if ($row) {
            // Mark as used
            $upd = $this->db->prepare("UPDATE verification_codes SET is_used = 1 WHERE id = ?");
            $upd->execute([$row['id']]);
            return true;
        }
        return false;
    }
}
