<?php
/** Admin — Orders Management (v2) */
$adminPageTitle = 'إدارة الطلبات';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Order.php';
$orderModel = new Order();

if (isset($_GET['status'], $_GET['id'])) {
    $orderModel->updateStatus((int)$_GET['id'], $_GET['status']);
    set_flash('success', 'تم تحديث حالة الطلب');
    header('Location: orders.php'); exit;
}
if (isset($_GET['delete'])) { $orderModel->delete((int)$_GET['delete']); set_flash('success', 'تم حذف الطلب'); header('Location: orders.php'); exit; }

$statusFilter = $_GET['filter'] ?? null;
$orders = $orderModel->getAll(100, 0, $statusFilter);
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar">
  <div><h1><i class="fa-solid fa-clipboard-list" style="margin-left:0.4rem;color:var(--admin-gold);"></i> إدارة الطلبات</h1><span class="breadcrumb-text"><?= count($orders) ?> طلب</span></div>
</div>

<div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
  <a href="orders.php" class="btn-admin <?= !$statusFilter ? 'btn-admin--primary' : 'btn-admin--outline' ?> btn-admin--sm">الكل</a>
  <a href="orders.php?filter=new" class="btn-admin <?= $statusFilter === 'new' ? 'btn-admin--primary' : 'btn-admin--outline' ?> btn-admin--sm"><i class="fa-solid fa-bell" style="margin-left:0.2rem;"></i> جديد</a>
  <a href="orders.php?filter=processing" class="btn-admin <?= $statusFilter === 'processing' ? 'btn-admin--gold' : 'btn-admin--outline' ?> btn-admin--sm"><i class="fa-solid fa-spinner" style="margin-left:0.2rem;"></i> قيد التنفيذ</a>
  <a href="orders.php?filter=completed" class="btn-admin <?= $statusFilter === 'completed' ? 'btn-admin--primary' : 'btn-admin--outline' ?> btn-admin--sm" style="<?= $statusFilter === 'completed' ? 'background:#2A7E2A;' : '' ?>"><i class="fa-solid fa-check" style="margin-left:0.2rem;"></i> مكتمل</a>
  <a href="orders.php?filter=cancelled" class="btn-admin <?= $statusFilter === 'cancelled' ? 'btn-admin--red' : 'btn-admin--outline' ?> btn-admin--sm"><i class="fa-solid fa-xmark" style="margin-left:0.2rem;"></i> ملغي</a>
</div>

<div class="admin-card">
  <?php if (empty($orders)): ?>
  <div class="empty-state"><i class="fa-solid fa-clipboard-list" style="font-size:40px;opacity:0.15;display:block;margin-bottom:0.5rem;"></i><p>لا توجد طلبات</p></div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>العميل</th><th>الهاتف</th><th>المجموع</th><th>الحالة</th><th>التاريخ</th><th>إجراءات</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><a href="order_detail.php?id=<?= $o['id'] ?>" style="color:var(--admin-navy);font-weight:600;">#<?= $o['id'] ?></a></td>
          <td><?= clean($o['customer_name']) ?></td>
          <td dir="ltr"><?= clean($o['customer_phone']) ?></td>
          <td style="font-weight:700;"><?= number_format($o['total'], 0) ?> ر.س</td>
          <td><?= order_status_label($o['status']) ?></td>
          <td style="font-size:0.78rem;color:var(--admin-gray);"><?= date('Y/m/d H:i', strtotime($o['created_at'])) ?></td>
          <td style="display:flex;gap:0.3rem;">
            <?php if ($o['status'] === 'new'): ?><a href="orders.php?id=<?= $o['id'] ?>&status=processing" class="btn-admin btn-admin--gold btn-admin--sm"><i class="fa-solid fa-check" style="margin-left:0.2rem;"></i> قبول</a><?php endif; ?>
            <?php if ($o['status'] === 'processing'): ?><a href="orders.php?id=<?= $o['id'] ?>&status=completed" class="btn-admin btn-admin--primary btn-admin--sm" style="background:#2A7E2A;"><i class="fa-solid fa-check-double" style="margin-left:0.2rem;"></i> إكمال</a><?php endif; ?>
            <?php if ($o['status'] !== 'cancelled'): ?><a href="orders.php?id=<?= $o['id'] ?>&status=cancelled" class="btn-admin btn-admin--sm" style="background:#fee;color:var(--admin-red);" onclick="return confirm('إلغاء هذا الطلب؟')"><i class="fa-solid fa-ban" style="margin-left:0.2rem;"></i> إلغاء</a><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
