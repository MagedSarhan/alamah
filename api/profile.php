<?php
/**
 * Profile API — Update user profile, password, avatar
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
    exit;
}

$userModel = new User();
$user = $userModel->findById($userId);
if (!$user) {
    echo json_encode(['ok' => false, 'error' => 'user_not_found']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'update_profile') {
    $name = trim($_POST['name'] ?? '');
    if (empty($name)) {
        echo json_encode(['ok' => false, 'error' => 'الاسم مطلوب']);
        exit;
    }
    $db = Database::getInstance();
    $stmt = $db->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt->execute([$name, $userId]);
    $_SESSION['user_name'] = $name;
    echo json_encode(['ok' => true, 'message' => 'تم تحديث الاسم بنجاح']);
    exit;
}

if ($action === 'update_password') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($current) || empty($new)) {
        echo json_encode(['ok' => false, 'error' => 'جميع الحقول مطلوبة']);
        exit;
    }
    if ($new !== $confirm) {
        echo json_encode(['ok' => false, 'error' => 'كلمة المرور الجديدة غير متطابقة']);
        exit;
    }
    if (strlen($new) < 6) {
        echo json_encode(['ok' => false, 'error' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل']);
        exit;
    }
    if (!$userModel->verifyPassword($current, $user['password_hash'])) {
        echo json_encode(['ok' => false, 'error' => 'كلمة المرور الحالية غير صحيحة']);
        exit;
    }
    $userModel->updatePassword($userId, $new);
    echo json_encode(['ok' => true, 'message' => 'تم تغيير كلمة المرور بنجاح']);
    exit;
}

if ($action === 'update_avatar') {
    if (empty($_FILES['avatar']['name'])) {
        echo json_encode(['ok' => false, 'error' => 'لم يتم اختيار صورة']);
        exit;
    }
    $uploaded = upload_image($_FILES['avatar'], __DIR__ . '/../uploads/avatars/');
    if (!$uploaded) {
        echo json_encode(['ok' => false, 'error' => 'فشل رفع الصورة (الحد الأقصى 5MB, JPG/PNG/WebP)']);
        exit;
    }
    $avatarPath = 'uploads/avatars/' . $uploaded;
    $db = Database::getInstance();
    $db->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$avatarPath, $userId]);
    $_SESSION['user_avatar'] = $avatarPath;
    echo json_encode(['ok' => true, 'message' => 'تم تحديث الصورة', 'avatar' => $avatarPath]);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'invalid_action']);
