<?php
/** Admin — Wishlists */
$adminPageTitle = 'المفضلة';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Setting.php';
$settingModel = new Setting();
$wishlistTop = $settingModel->getWishlistStats(30);
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar">
  <div><h1><i class="fa-solid fa-heart" style="margin-left:0.4rem;color:#E74C3C;"></i> المنتجات المفضلة لدى العملاء</h1>
  <span class="breadcrumb-text">تعرّف على المنتجات الأكثر إضافة للمفضلة لاستهداف العملاء</span></div>
</div>

<div class="admin-card">
  <?php if (empty($wishlistTop)): ?>
  <div class="empty-state"><i class="fa-solid fa-heart" style="font-size:40px;opacity:0.15;display:block;margin-bottom:0.5rem;"></i><p>لا توجد بيانات مفضلة بعد</p></div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>صورة</th><th>اسم المنتج</th><th>عدد الإضافات للمفضلة</th><th>مستوى الاهتمام</th></tr></thead>
      <tbody>
        <?php $maxCount = $wishlistTop[0]['wish_count'] ?? 1;
        foreach ($wishlistTop as $w): $pct = round(($w['wish_count'] / $maxCount) * 100); ?>
        <tr>
          <td><img src="../<?= htmlspecialchars($w['image']) ?>" class="thumb"></td>
          <td style="font-weight:600;color:var(--admin-navy);"><?= clean($w['name']) ?></td>
          <td><span class="badge bg-danger" style="font-size:0.85rem;"><?= $w['wish_count'] ?> <i class="fa-solid fa-heart" style="font-size:10px;"></i></span></td>
          <td>
            <div style="background:#f5f5f5;border-radius:50px;height:8px;width:100%;max-width:200px;">
              <div style="background:linear-gradient(90deg,#E74C3C,#C0392B);height:100%;border-radius:50px;width:<?= $pct ?>%;"></div>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
