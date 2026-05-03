<?php
/** Admin — Users Management (v2) */
$adminPageTitle = 'إدارة المستخدمين';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/User.php';
$userModel = new User();

if (isset($_GET['toggle_admin'])) { $userModel->toggleAdmin((int)$_GET['toggle_admin']); set_flash('success', 'تم التحديث'); header('Location: users.php'); exit; }
if (isset($_GET['toggle_status'])) { $userModel->toggleStatus((int)$_GET['toggle_status']); set_flash('success', 'تم التحديث'); header('Location: users.php'); exit; }
if (isset($_GET['verify_user'])) { $userModel->verifyEmail((int)$_GET['verify_user']); set_flash('success', 'تم تفعيل الحساب بنجاح'); header('Location: users.php'); exit; }
if (isset($_GET['delete'])) { $userModel->delete((int)$_GET['delete']); set_flash('success', 'تم حذف المستخدم'); header('Location: users.php'); exit; }

$users = $userModel->getAll();
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar"><div><h1><i class="fa-solid fa-users" style="margin-left:0.4rem;color:var(--admin-gold);"></i> إدارة المستخدمين</h1><span class="breadcrumb-text"><?= count($users) ?> مستخدم</span></div></div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>مفعل</th><th>الدور</th><th>الحالة</th><th>تاريخ التسجيل</th><th>إجراءات</th></tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td style="font-weight:600;"><?= clean($u['name']) ?></td>
          <td dir="ltr" style="font-size:0.85rem;"><?= clean($u['email']) ?></td>
          <td><?= $u['is_verified'] ? '<i class="fa-solid fa-circle-check" style="color:#2A7E2A;"></i>' : '<i class="fa-solid fa-circle-xmark" style="color:var(--admin-red);"></i>' ?></td>
          <td><?= $u['is_admin'] ? '<span class="badge bg-primary"><i class="fa-solid fa-shield-halved" style="margin-left:0.2rem;"></i> أدمن</span>' : '<span style="color:var(--admin-gray);">مستخدم</span>' ?></td>
          <td><?= $u['status'] === 'active' ? '<span class="badge bg-success">نشط</span>' : '<span class="badge bg-danger">محظور</span>' ?></td>
          <td style="font-size:0.78rem;color:var(--admin-gray);"><?= date('Y/m/d', strtotime($u['created_at'])) ?></td>
          <td style="display:flex;gap:0.3rem;">
            <a href="users.php?toggle_admin=<?= $u['id'] ?>" class="btn-admin btn-admin--outline btn-admin--sm"><?= $u['is_admin'] ? '<i class="fa-solid fa-user-minus" style="margin-left:0.2rem;"></i> إزالة' : '<i class="fa-solid fa-user-shield" style="margin-left:0.2rem;"></i> أدمن' ?></a>
            <a href="users.php?toggle_status=<?= $u['id'] ?>" class="btn-admin btn-admin--sm" style="background:<?= $u['status'] === 'active' ? '#fee' : '#efe' ?>;color:<?= $u['status'] === 'active' ? 'var(--admin-red)' : '#2A7E2A' ?>;"><?= $u['status'] === 'active' ? '<i class="fa-solid fa-ban" style="margin-left:0.2rem;"></i> حظر' : '<i class="fa-solid fa-check" style="margin-left:0.2rem;"></i> رفع' ?></a>
            <?php if (!$u['is_verified']): ?><a href="users.php?verify_user=<?= $u['id'] ?>" class="btn-admin btn-admin--sm" style="background:#e8f5e9;color:#2A7E2A;" onclick="return confirm('تفعيل حساب هذا المستخدم؟')"><i class="fa-solid fa-user-check" style="margin-left:0.2rem;"></i> تفعيل</a><?php endif; ?>
            <?php if (!$u['is_admin']): ?><a href="users.php?delete=<?= $u['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف هذا المستخدم؟')"><i class="fa-solid fa-trash" style="font-size:12px;"></i></a><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
