<?php
/** Admin Sidebar (v2) — with Font Awesome icons */
require_once __DIR__ . '/../../classes/Contact.php';
$contactModel = new Contact();
$unreadCount = $contactModel->countUnread();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <a href="../index.php"><img src="../image/logo.png" alt="علامة"></a>
    <p>لوحة التحكم</p>
  </div>
  <nav class="sidebar-nav">
    <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">
      <i class="fa-solid fa-chart-pie"></i> الرئيسية
    </a>
    <a href="products.php" class="<?= $currentPage === 'products' || $currentPage === 'product_form' ? 'active' : '' ?>">
      <i class="fa-solid fa-box-open"></i> المنتجات
    </a>
    <a href="categories.php" class="<?= $currentPage === 'categories' ? 'active' : '' ?>">
      <i class="fa-solid fa-layer-group"></i> الفئات
    </a>
    <a href="orders.php" class="<?= $currentPage === 'orders' || $currentPage === 'order_detail' ? 'active' : '' ?>">
      <i class="fa-solid fa-clipboard-list"></i> الطلبات
    </a>
    <div class="nav-divider"></div>
    <a href="users.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
      <i class="fa-solid fa-users"></i> المستخدمين
    </a>
    <a href="messages.php" class="<?= $currentPage === 'messages' ? 'active' : '' ?>">
      <i class="fa-solid fa-envelope"></i> الرسائل
      <?php if ($unreadCount > 0): ?><span class="sidebar-badge"><?= $unreadCount ?></span><?php endif; ?>
    </a>
    <div class="nav-divider"></div>
    <a href="abandoned_carts.php" class="<?= $currentPage === 'abandoned_carts' ? 'active' : '' ?>">
      <i class="fa-solid fa-cart-shopping"></i> السلات المتروكة
    </a>
    <a href="wishlists.php" class="<?= $currentPage === 'wishlists' ? 'active' : '' ?>">
      <i class="fa-solid fa-heart"></i> المفضلة
    </a>
    <div class="nav-divider"></div>
    <a href="hero_slides.php" class="<?= $currentPage === 'hero_slides' ? 'active' : '' ?>">
      <i class="fa-solid fa-images"></i> السلايدر
    </a>
    <a href="settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
      <i class="fa-solid fa-gear"></i> الإعدادات
    </a>
    <a href="profile.php" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
      <i class="fa-solid fa-user-pen"></i> الملف الشخصي
    </a>
    <div class="nav-divider"></div>
    <a href="../index.php" style="color:rgba(255,255,255,0.4);">
      <i class="fa-solid fa-house"></i> العودة للموقع
    </a>
    <a href="../auth/logout.php" style="color:rgba(255,255,255,0.4);">
      <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
    </a>
  </nav>
</aside>
<button class="mobile-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')">
  <i class="fa-solid fa-bars"></i>
</button>
