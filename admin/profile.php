<?php
/** Admin — Profile Page */
$adminPageTitle = 'الملف الشخصي';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/User.php';
$userModel = new User();
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if (!$userId) { header('Location: ../auth/login.php'); exit; }
$user = $userModel->findById($userId);
if (!$user) { header('Location: ../auth/login.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    // Update name/email
    if ($name && $email) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user['id']]);
        $_SESSION['user_name'] = $name;
    }

    // Update password if provided
    if ($newPass) {
        if (!$userModel->verifyPassword($currentPass, $user['password_hash'])) {
            set_flash('error', 'كلمة المرور الحالية غير صحيحة');
            header('Location: profile.php'); exit;
        }
        if (strlen($newPass) < 6) {
            set_flash('error', 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل');
            header('Location: profile.php'); exit;
        }
        if ($newPass !== $confirmPass) {
            set_flash('error', 'كلمة المرور الجديدة وتأكيدها غير متطابقتين');
            header('Location: profile.php'); exit;
        }
        $userModel->updatePassword($user['id'], $newPass);
    }

    set_flash('success', 'تم تحديث الملف الشخصي بنجاح');
    header('Location: profile.php'); exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar"><div><h1><i class="fa-solid fa-user-pen" style="margin-left:0.4rem;color:var(--admin-gold);"></i> الملف الشخصي</h1></div></div>

<div style="max-width:600px;">
  <form method="POST" class="admin-form">
    <div class="admin-card">
      <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-id-card" style="margin-left:0.3rem;color:var(--admin-gold);"></i> المعلومات الأساسية</h2>
      <label>الاسم</label><input type="text" name="name" value="<?= clean($user['name']) ?>" required>
      <label>البريد الإلكتروني</label><input type="email" name="email" value="<?= clean($user['email']) ?>" dir="ltr" required>
    </div>

    <div class="admin-card">
      <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-lock" style="margin-left:0.3rem;color:var(--admin-gold);"></i> تغيير كلمة المرور</h2>
      <p style="font-size:0.82rem;color:var(--admin-gray);margin-bottom:1rem;">اتركها فارغة إذا لم ترد تغييرها</p>
      <label>كلمة المرور الحالية</label><input type="password" name="current_password" placeholder="••••••••">
      <label>كلمة المرور الجديدة</label><input type="password" name="new_password" placeholder="6 أحرف على الأقل">
      <label>تأكيد كلمة المرور الجديدة</label><input type="password" name="confirm_password" placeholder="أعد كتابة كلمة المرور">
    </div>

    <button type="submit" class="btn-admin btn-admin--primary" style="padding:0.75rem 3rem;"><i class="fa-solid fa-floppy-disk" style="margin-left:0.3rem;"></i> حفظ التغييرات</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
