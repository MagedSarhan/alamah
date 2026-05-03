<?php
/** Admin — Products Management (v2) */
$adminPageTitle = 'إدارة المنتجات';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

$productModel = new Product();
$categoryModel = new Category();

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $productModel->delete((int)$_GET['delete']);
    set_flash('success', 'تم حذف المنتج بنجاح');
    header('Location: products.php'); exit;
}

$products = $productModel->getAll(false);
$categories = $categoryModel->getAll(false);
$catMap = [];
foreach ($categories as $c) $catMap[$c['id']] = $c['label'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar">
  <div><h1><i class="fa-solid fa-box-open" style="margin-left:0.4rem;color:var(--admin-gold);"></i> إدارة المنتجات</h1><span class="breadcrumb-text"><?= count($products) ?> منتج</span></div>
  <div class="admin-topbar-actions"><a href="product_form.php" class="btn-admin btn-admin--primary"><i class="fa-solid fa-plus" style="margin-left:0.3rem;"></i> إضافة منتج</a></div>
</div>

<div class="admin-card">
  <?php if (empty($products)): ?>
  <div class="empty-state"><i class="fa-solid fa-box-open" style="font-size:40px;opacity:0.15;display:block;margin-bottom:0.5rem;"></i><p>لا توجد منتجات بعد</p><a href="product_form.php" class="btn-admin btn-admin--primary" style="margin-top:1rem;"><i class="fa-solid fa-plus" style="margin-left:0.3rem;"></i> إضافة أول منتج</a></div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>صورة</th><th>الاسم</th><th>الفئة</th><th>السعر</th><th>الشارة</th><th>الحالة</th><th>إجراءات</th></tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?php if ($p['image']): ?><img src="../<?= htmlspecialchars($p['image']) ?>" class="thumb" alt=""><?php else: ?><span style="color:#ccc;"><i class="fa-solid fa-image" style="font-size:20px;"></i></span><?php endif; ?></td>
          <td style="font-weight:600;color:var(--admin-navy);"><?= clean($p['name']) ?></td>
          <td><?= $catMap[$p['category_id']] ?? '-' ?></td>
          <td style="font-weight:700;"><?= number_format($p['price'], 0) ?> ر.س</td>
          <td><?= $p['badge'] ? '<span class="badge" style="background:' . ($p['badge_color'] ?: 'var(--admin-navy)') . ';">' . clean($p['badge']) . '</span>' : '-' ?></td>
          <td><?= $p['is_active'] ? '<span class="badge bg-success">فعال</span>' : '<span class="badge" style="background:#ccc;">معطل</span>' ?></td>
          <td style="display:flex;gap:0.3rem;">
            <a href="product_form.php?id=<?= $p['id'] ?>" class="btn-admin--icon" title="تعديل"><i class="fa-solid fa-pen" style="font-size:12px;"></i></a>
            <a href="products.php?delete=<?= $p['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف هذا المنتج؟')" title="حذف"><i class="fa-solid fa-trash" style="font-size:12px;"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
