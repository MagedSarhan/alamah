<?php
/**
 * Wishlist API — Add/Remove/Get wishlist items
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->prepare("SELECT product_id FROM wishlists WHERE user_id = ?");
    $stmt->execute([$userId]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['ok' => true, 'product_ids' => array_map('intval', $ids)]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $productId = (int)($input['product_id'] ?? 0);

    if (!$productId) {
        echo json_encode(['ok' => false, 'error' => 'missing_product_id']);
        exit;
    }

    if ($action === 'add') {
        $stmt = $db->prepare("INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$userId, $productId]);
        echo json_encode(['ok' => true, 'added' => true]);
    } elseif ($action === 'remove') {
        $stmt = $db->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        echo json_encode(['ok' => true, 'removed' => true]);
    } elseif ($action === 'toggle') {
        $stmt = $db->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        if ($stmt->fetch()) {
            $db->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?")->execute([$userId, $productId]);
            echo json_encode(['ok' => true, 'wishlisted' => false]);
        } else {
            $db->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)")->execute([$userId, $productId]);
            echo json_encode(['ok' => true, 'wishlisted' => true]);
        }
    } else {
        echo json_encode(['ok' => false, 'error' => 'invalid_action']);
    }
    exit;
}

echo json_encode(['ok' => false, 'error' => 'invalid_method']);
