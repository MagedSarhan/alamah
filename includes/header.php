<?php
/**
 * Shared Header — head + navbar + offcanvas
 * Expects: $pageTitle, $pageDescription, $activePage
 */
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'علامة | ALAMAH — اترك علامتك';
$pageDescription = $pageDescription ?? 'منتجات مخصصة فاخرة، حفر بالليزر، هدايا دعائية، ومعلقات سيارات.';
$activePage = $activePage ?? 'index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= clean($pageDescription) ?>">
  <title><?= clean($pageTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Aref+Ruqaa:wght@400;700&family=Tajawal:wght@300;400;500;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/png" href="image/logo.png">
  <meta property="og:title" content="<?= clean($pageTitle) ?>">
  <meta property="og:description" content="<?= clean($pageDescription) ?>">
  <meta property="og:image" content="image/logo.png">
</head>
<body>
  <div class="page-loader" id="pageLoader"><img src="image/logo.png" alt="علامة"><div class="loader-bar"></div></div>

  <nav class="navbar navbar-expand-lg navbar-alamah" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="index.php"><img src="image/logo.png" alt="علامة | ALAMAH"></a>
      <div class="d-flex align-items-center d-lg-none">
        <a href="wishlist.php" class="nav-cart-btn me-1" aria-label="المفضلة" style="text-decoration:none;">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <span class="cart-badge wishlist-badge">0</span>
        </a>
        <button class="nav-cart-btn me-2" id="cartBtnMobile" type="button" aria-label="السلة">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          <span class="cart-badge" id="cartBadgeMobile">0</span>
        </button>
        <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas"><div class="hamburger-icon" id="hamburgerIcon"><span></span><span></span><span></span></div></button>
      </div>
      <div class="collapse navbar-collapse d-none d-lg-block" id="navbarContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link <?= is_active_page('index') ?>" href="index.php">الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link <?= is_active_page('products') ?>" href="products.php">المنتجات</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php#why">لماذا علامة</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php#steps">كيف نعمل</a></li>
          <li class="nav-item"><a class="nav-link <?= is_active_page('contact') ?>" href="contact.php">تواصل معنا</a></li>
        </ul>
        <?php if (is_logged_in()): ?>
        <div class="dropdown me-2">
          <button class="btn btn-sm nav-account-btn" type="button" data-bs-toggle="dropdown">
            <?php if (!empty($_SESSION['user_avatar'])): ?>
            <img src="<?= htmlspecialchars($_SESSION['user_avatar']) ?>" class="nav-account-avatar">
            <?php else: ?>
            <i class="fa-solid fa-user-circle" style="font-size:1.1rem;margin-left:0.3rem;"></i>
            <?php endif; ?>
            <?= clean($_SESSION['user_name'] ?? 'حسابي') ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" style="font-family:var(--font-arabic);min-width:180px;">
            <li><a class="dropdown-item" href="account.php"><i class="fa-solid fa-gear" style="margin-left:0.4rem;opacity:0.5;"></i>إعدادات الحساب</a></li>
            <li><a class="dropdown-item" href="wishlist.php"><i class="fa-solid fa-heart" style="margin-left:0.4rem;color:#E74C3C;opacity:0.7;"></i>المفضلة</a></li>
            <?php if (is_admin()): ?><li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="admin/index.php"><i class="fa-solid fa-shield-halved" style="margin-left:0.4rem;opacity:0.5;"></i>لوحة التحكم</a></li><?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="auth/logout.php"><i class="fa-solid fa-right-from-bracket" style="margin-left:0.4rem;"></i>تسجيل الخروج</a></li>
          </ul>
        </div>
        <?php else: ?>
        <a href="auth/login.php" class="btn btn-sm me-2" style="background:var(--alamah-beige);color:var(--alamah-navy);border-radius:50px;padding:0.4rem 1rem;font-family:var(--font-arabic);font-weight:600;">دخول</a>
        <?php endif; ?>
        <a href="wishlist.php" class="nav-cart-btn me-1" aria-label="المفضلة" style="text-decoration:none;">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <span class="cart-badge wishlist-badge">0</span>
        </a>
        <button class="nav-cart-btn me-2" id="cartBtnDesktop" type="button" aria-label="السلة">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          <span class="cart-badge" id="cartBadgeDesktop">0</span>
        </button>
        <a href="products.php" class="btn-alamah-cta d-inline-block"><span>تسوّق الآن</span></a>
      </div>
    </div>
  </nav>

  <!-- Mobile Offcanvas -->
  <div class="offcanvas offcanvas-start bg-white" tabindex="-1" id="navbarOffcanvas" style="width:320px!important;">
    <div class="offcanvas-header align-items-center justify-content-between" style="border-bottom:2px solid rgba(0,0,0,0.04);padding:1.5rem;">
      <h5 class="offcanvas-title mb-0"><img src="image/logo.png" alt="علامة" style="height:40px!important;width:auto!important;"></h5>
      <button type="button" class="offcanvas-close-btn" data-bs-dismiss="offcanvas" aria-label="Close"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="offcanvas-body d-flex flex-column" style="padding:1.5rem;">
      <ul class="navbar-nav pe-0 mb-auto">
        <li class="nav-item border-bottom py-2"><a class="nav-link d-flex align-items-center fw-bold" href="index.php" style="color:var(--alamah-navy);"><span class="fs-5">الرئيسية</span></a></li>
        <li class="nav-item border-bottom py-2"><a class="nav-link d-flex align-items-center fw-bold" href="products.php" style="color:var(--alamah-navy);"><span class="fs-5">المنتجات</span></a></li>
        <li class="nav-item border-bottom py-2"><a class="nav-link d-flex align-items-center fw-bold" href="wishlist.php" style="color:var(--alamah-navy);"><span class="fs-5"><i class="fa-solid fa-heart" style="color:#E74C3C;margin-left:0.4rem;"></i>المفضلة</span></a></li>
        <li class="nav-item border-bottom py-2"><a class="nav-link d-flex align-items-center fw-bold" href="index.php#why" data-bs-dismiss="offcanvas" style="color:var(--alamah-navy);"><span class="fs-5">لماذا علامة</span></a></li>
        <li class="nav-item border-bottom py-2"><a class="nav-link d-flex align-items-center fw-bold" href="index.php#steps" data-bs-dismiss="offcanvas" style="color:var(--alamah-navy);"><span class="fs-5">كيف نعمل</span></a></li>
        <li class="nav-item py-2"><a class="nav-link d-flex align-items-center fw-bold" href="contact.php" style="color:var(--alamah-navy);"><span class="fs-5">تواصل معنا</span></a></li>
      </ul>
      <div class="mt-auto pt-4 text-center">
        <?php if (is_logged_in()): ?>
        <a href="account.php" class="offcanvas-cta-btn mb-2" style="background:var(--alamah-gold);">
          <span><i class="fa-solid fa-gear" style="margin-left:0.3rem;"></i>إعدادات الحساب</span>
        </a>
        <?php if (is_admin()): ?><a href="admin/index.php" class="offcanvas-cta-btn mb-2" style="background:var(--alamah-navy);"><span>لوحة التحكم</span></a><?php endif; ?>
        <a href="auth/logout.php" class="btn btn-outline-danger w-100" style="border-radius:var(--radius-md);font-family:var(--font-arabic);font-weight:600;">تسجيل الخروج</a>
        <?php else: ?>
        <a href="auth/login.php" class="offcanvas-cta-btn mb-2" style="background:var(--alamah-navy);"><span>تسجيل الدخول</span></a>
        <a href="auth/register.php" class="offcanvas-cta-btn"><span>إنشاء حساب</span></a>
        <?php endif; ?>
        <p class="text-muted mt-4 mb-0" style="font-size:0.8rem;">جميع الحقوق محفوظة © ALAMAH</p>
      </div>
    </div>
  </div>
