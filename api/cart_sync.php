<?php
/**
 * Cart Sync API — Save & Load cart from DB
 * GET  → Load user's cart from DB
 * POST → Save cart to DB
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance();
$userId = $_SESSION['user_id'] ?? null;
$sessionId = session_id();
$method = $_SERVER['REQUEST_METHOD'];

// ── GET: Load cart from DB ──
if ($method === 'GET') {
    if (!$userId) {
        echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
        exit;
    }
    $stmt = $db->prepare("SELECT cart_data FROM abandoned_carts WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    $items = $row ? (json_decode($row['cart_data'], true) ?: []) : [];
    echo json_encode(['ok' => true, 'items' => $items]);
    exit;
}

// ── POST: Save cart to DB ──
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $items = $input['items'] ?? [];
    $total = (float)($input['total'] ?? 0);

    if (empty($items)) {
        if ($userId) {
            $db->prepare("DELETE FROM abandoned_carts WHERE user_id = ?")->execute([$userId]);
        } else {
            $db->prepare("DELETE FROM abandoned_carts WHERE session_id = ? AND user_id IS NULL")->execute([$sessionId]);
        }
        echo json_encode(['ok' => true, 'cleared' => true]);
        exit;
    }

    $cartDataJson = json_encode($items, JSON_UNESCAPED_UNICODE);

    if ($userId) {
        $stmt = $db->prepare("SELECT id FROM abandoned_carts WHERE user_id = ?");
        $stmt->execute([$userId]);
        if ($stmt->fetch()) {
            $db->prepare("UPDATE abandoned_carts SET cart_data = ?, total = ?, updated_at = NOW() WHERE user_id = ?")
               ->execute([$cartDataJson, $total, $userId]);
        } else {
            $db->prepare("INSERT INTO abandoned_carts (user_id, session_id, cart_data, total) VALUES (?, ?, ?, ?)")
               ->execute([$userId, $sessionId, $cartDataJson, $total]);
        }
    } else {
        $stmt = $db->prepare("SELECT id FROM abandoned_carts WHERE session_id = ? AND user_id IS NULL");
        $stmt->execute([$sessionId]);
        if ($stmt->fetch()) {
            $db->prepare("UPDATE abandoned_carts SET cart_data = ?, total = ?, updated_at = NOW() WHERE session_id = ? AND user_id IS NULL")
               ->execute([$cartDataJson, $total, $sessionId]);
        } else {
            $db->prepare("INSERT INTO abandoned_carts (session_id, cart_data, total) VALUES (?, ?, ?)")
               ->execute([$sessionId, $cartDataJson, $total]);
        }
    }

    echo json_encode(['ok' => true, 'synced' => true]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'invalid_method']);
