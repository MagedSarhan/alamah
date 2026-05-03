<?php
/** Admin — Order Detail (v2) */
$adminPageTitle = 'تفاصيل الطلب';
require_once __DIR__ . '/../classes/Order.php';
$orderModel = new Order();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $id ? $orderModel->findById($id) : null;
if (!$order) { header('Location: orders.php'); exit; }
require_once __DIR__ . '/includes/header.php';
?>
<div class="admin-topbar">
  <div><h1><i class="fa-solid fa-receipt" style="margin-left:0.4rem;color:var(--admin-gold);"></i> طلب #<?= $order['id'] ?></h1><span class="breadcrumb-text"><?= date('Y/m/d H:i', strtotime($order['created_at'])) ?></span></div>
  <a href="orders.php" class="btn-admin btn-admin--outline"><i class="fa-solid fa-arrow-right" style="margin-left:0.3rem;"></i> العودة</a>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
  <div class="admin-card">
    <h2 style="font-size:1rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-user" style="margin-left:0.3rem;color:var(--admin-gold);"></i> معلومات العميل</h2>
    <p><strong>الاسم:</strong> <?= clean($order['customer_name']) ?></p>
    <p><strong>الهاتف:</strong> <span dir="ltr"><?= clean($order['customer_phone']) ?></span></p>
    <?php if ($order['customer_email']): ?><p><strong>الإيميل:</strong> <?= clean($order['customer_email']) ?></p><?php endif; ?>
    <p style="margin-top:0.5rem;"><strong>الحالة:</strong> <?= order_status_label($order['status']) ?></p>
    <p><strong>المجموع:</strong> <span style="font-weight:800;color:var(--admin-red);font-size:1.2rem;"><?= number_format($order['total'], 0) ?> ر.س</span></p>
    <?php if ($order['notes']): ?><p style="margin-top:0.5rem;"><strong>ملاحظات:</strong> <?= clean($order['notes']) ?></p><?php endif; ?>
  </div>
  <div class="admin-card">
    <h2 style="font-size:1rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-boxes-stacked" style="margin-left:0.3rem;color:var(--admin-gold);"></i> عناصر الطلب</h2>
    <?php foreach ($order['items'] as $item): ?>
    <div style="padding:0.75rem 0;border-bottom:1px solid #f0f0f0;">
      <div style="display:flex;justify-content:space-between;"><strong><?= clean($item['product_name']) ?></strong><span>×<?= $item['qty'] ?></span></div>
      <div style="color:var(--admin-gray);font-size:0.85rem;"><?= number_format($item['price'], 0) ?> ر.س / قطعة</div>
      <?php if ($item['custom_data']): ?>
      <div style="background:#FAFAFA;border-radius:8px;padding:0.5rem;margin-top:0.3rem;font-size:0.82rem;">
        <?php foreach ($item['custom_data'] as $k => $v): ?><p><strong><?= clean($k) ?>:</strong> <?= clean($v) ?></p><?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
