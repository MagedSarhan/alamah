<?php
/** Admin — Abandoned Carts */
$adminPageTitle = 'السلات المتروكة';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Setting.php';
$settingModel = new Setting();

if (isset($_GET['delete'])) { $settingModel->deleteAbandonedCart((int)$_GET['delete']); set_flash('success', 'تم الحذف'); header('Location: abandoned_carts.php'); exit; }

$carts = $settingModel->getAbandonedCarts();
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar">
  <div><h1><i class="fa-solid fa-cart-shopping" style="margin-left:0.4rem;color:#E67E22;"></i> السلات المتروكة</h1>
  <span class="breadcrumb-text">تتبع العملاء الذين أضافوا منتجات للسلة ولم يكملوا الطلب</span></div>
</div>

<div class="admin-card">
  <?php if (empty($carts)): ?>
  <div class="empty-state"><i class="fa-solid fa-cart-shopping" style="font-size:40px;opacity:0.15;display:block;margin-bottom:0.5rem;"></i><p>لا توجد سلات متروكة حالياً</p></div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>المستخدم</th><th>المنتجات</th><th>المجموع</th><th>آخر تحديث</th><th>إجراءات</th></tr></thead>
      <tbody>
        <?php foreach ($carts as $c): ?>
        <tr>
          <td><?= $c['id'] ?></td>
          <td><?= $c['user_name'] ? clean($c['user_name']) . '<br><span style="font-size:0.78rem;color:var(--admin-gray);">' . clean($c['user_email']) . '</span>' : '<span style="color:var(--admin-gray);">زائر (Session)</span>' ?></td>
          <td>
            <?php foreach (array_slice($c['items'], 0, 3) as $item): ?>
            <span class="badge" style="background:#f5f5f5;color:var(--admin-navy);margin:0.1rem;"><?= clean($item['name'] ?? '—') ?></span>
            <?php endforeach; ?>
            <?php if (count($c['items']) > 3): ?><span class="badge" style="background:#eee;color:var(--admin-gray);">+<?= count($c['items']) - 3 ?></span><?php endif; ?>
          </td>
          <td style="font-weight:700;"><?= number_format($c['total'], 0) ?> ر.س</td>
          <td style="font-size:0.78rem;color:var(--admin-gray);"><?= time_ago($c['updated_at']) ?></td>
          <td><a href="abandoned_carts.php?delete=<?= $c['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف؟')"><i class="fa-solid fa-trash" style="font-size:12px;"></i></a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
