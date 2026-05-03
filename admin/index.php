<?php
/** Admin Dashboard — Main Page (v2) */
$adminPageTitle = 'الرئيسية';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Contact.php';
require_once __DIR__ . '/../classes/Setting.php';

$productModel = new Product();
$orderModel = new Order();
$userModel = new User();
$contactModel = new Contact();
$settingModel = new Setting();

$totalProducts = $productModel->count();
$totalOrders = $orderModel->count();
$totalUsers = $userModel->count();
$unreadMessages = $contactModel->countUnread();
$newOrders = $orderModel->count('new');
$revenue = $orderModel->getTotalRevenue();
$recentOrders = $orderModel->getRecent(5);
$abandonedCount = $settingModel->countAbandonedCarts();
$wishlistCount = $settingModel->countWishlists();
$wishlistTop = $settingModel->getWishlistStats(5);

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar">
  <div>
    <h1>مرحباً، <?= clean($_SESSION['user_name'] ?? 'مدير') ?></h1>
    <span class="breadcrumb-text">إليك نظرة عامة على أداء متجرك</span>
  </div>
  <div class="admin-topbar-actions">
    <a href="profile.php" class="btn-admin btn-admin--outline"><i class="fa-solid fa-user-pen" style="margin-left:0.3rem;"></i> الملف الشخصي</a>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon stat-icon--navy"><i class="fa-solid fa-box-open" style="font-size:20px;"></i></div>
    <div class="stat-info"><h3><?= $totalProducts ?></h3><p>إجمالي المنتجات</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-icon--gold"><i class="fa-solid fa-clipboard-list" style="font-size:20px;"></i></div>
    <div class="stat-info"><h3><?= $totalOrders ?></h3><p>إجمالي الطلبات</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-icon--green"><i class="fa-solid fa-users" style="font-size:20px;"></i></div>
    <div class="stat-info"><h3><?= $totalUsers ?></h3><p>المستخدمين المسجلين</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon stat-icon--red"><i class="fa-solid fa-envelope" style="font-size:20px;"></i></div>
    <div class="stat-info"><h3><?= $unreadMessages ?></h3><p>رسائل غير مقروءة</p></div>
  </div>
</div>

<!-- Revenue + New Orders + Abandoned + Wishlist -->
<div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1.2rem;margin-bottom:1.5rem;">
  <div class="admin-card" style="text-align:center;">
    <p style="color:var(--admin-gray);font-size:0.8rem;margin-bottom:0.5rem;"><i class="fa-solid fa-coins" style="margin-left:0.3rem;"></i> الإيرادات</p>
    <div style="font-size:1.5rem;font-weight:800;color:var(--admin-navy);"><?= number_format($revenue, 0) ?> <span style="font-size:0.85rem;color:var(--admin-gold);">ر.س</span></div>
  </div>
  <div class="admin-card" style="text-align:center;">
    <p style="color:var(--admin-gray);font-size:0.8rem;margin-bottom:0.5rem;"><i class="fa-solid fa-bell" style="margin-left:0.3rem;"></i> طلبات جديدة</p>
    <div style="font-size:1.5rem;font-weight:800;color:var(--admin-red);"><?= $newOrders ?></div>
  </div>
  <div class="admin-card" style="text-align:center;">
    <a href="abandoned_carts.php" style="text-decoration:none;">
      <p style="color:var(--admin-gray);font-size:0.8rem;margin-bottom:0.5rem;"><i class="fa-solid fa-cart-shopping" style="margin-left:0.3rem;"></i> سلات متروكة</p>
      <div style="font-size:1.5rem;font-weight:800;color:#E67E22;"><?= $abandonedCount ?></div>
    </a>
  </div>
  <div class="admin-card" style="text-align:center;">
    <a href="wishlists.php" style="text-decoration:none;">
      <p style="color:var(--admin-gray);font-size:0.8rem;margin-bottom:0.5rem;"><i class="fa-solid fa-heart" style="margin-left:0.3rem;"></i> في المفضلة</p>
      <div style="font-size:1.5rem;font-weight:800;color:#E74C3C;"><?= $wishlistCount ?></div>
    </a>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
  <!-- Recent Orders -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2><i class="fa-solid fa-clock-rotate-left" style="margin-left:0.4rem;color:var(--admin-gold);"></i> آخر الطلبات</h2>
      <a href="orders.php" class="btn-admin btn-admin--outline btn-admin--sm">عرض الكل</a>
    </div>
    <?php if (empty($recentOrders)): ?>
    <div class="empty-state"><i class="fa-solid fa-inbox" style="font-size:40px;opacity:0.2;display:block;margin-bottom:0.5rem;"></i><p>لا توجد طلبات بعد</p></div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="admin-table">
        <thead><tr><th>#</th><th>العميل</th><th>المجموع</th><th>الحالة</th><th>التاريخ</th></tr></thead>
        <tbody>
          <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><a href="order_detail.php?id=<?= $o['id'] ?>" style="color:var(--admin-navy);font-weight:600;">#<?= $o['id'] ?></a></td>
            <td><?= clean($o['customer_name']) ?></td>
            <td style="font-weight:700;"><?= number_format($o['total'], 0) ?> ر.س</td>
            <td><?= order_status_label($o['status']) ?></td>
            <td style="font-size:0.78rem;color:var(--admin-gray);"><?= time_ago($o['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Top Wishlist Products -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2><i class="fa-solid fa-heart" style="margin-left:0.4rem;color:#E74C3C;"></i> الأكثر تفضيلاً</h2>
    </div>
    <?php if (empty($wishlistTop)): ?>
    <div class="empty-state"><p style="font-size:0.85rem;">لا توجد بيانات بعد</p></div>
    <?php else: ?>
    <?php foreach ($wishlistTop as $w): ?>
    <div style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem 0;border-bottom:1px solid #f5f5f5;">
      <img src="../<?= htmlspecialchars($w['image']) ?>" style="width:36px;height:36px;object-fit:cover;border-radius:8px;">
      <div style="flex:1;"><p style="font-size:0.85rem;font-weight:600;color:var(--admin-navy);margin:0;"><?= clean($w['name']) ?></p></div>
      <span class="badge bg-danger"><?= $w['wish_count'] ?> <i class="fa-solid fa-heart" style="font-size:10px;"></i></span>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
