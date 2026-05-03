<?php
$pageTitle = 'المفضلة | علامة ALAMAH';
$pageDescription = 'المنتجات المحفوظة في قائمة المفضلة';
$activePage = 'wishlist';
require_once __DIR__ . '/includes/header.php';
?>
  <div style="height:90px;"></div>

  <section class="section-padding" style="min-height:60vh;">
    <div class="container">
      <div class="text-center mb-4 reveal">
        <div class="section-divider mx-auto"></div>
        <h1 class="section-title" style="font-size:1.8rem;">
          <svg viewBox="0 0 24 24" width="28" height="28" fill="var(--alamah-red)" style="vertical-align:middle;margin-left:0.4rem;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          قائمة المفضلة
        </h1>
        <p class="section-subtitle mx-auto">المنتجات التي أعجبتك وحفظتها لوقت لاحق</p>
      </div>

      <div id="wishlistGrid" class="row g-3">
        <!-- Products loaded via JS -->
      </div>

      <div id="wishlistEmpty" class="text-center" style="display:none;padding:4rem 0;">
        <svg viewBox="0 0 24 24" width="60" height="60" fill="none" stroke="var(--alamah-gray-light)" stroke-width="1.5" style="opacity:0.4;">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <h3 style="color:var(--alamah-navy);margin-top:1rem;">قائمة المفضلة فارغة</h3>
        <p style="color:var(--alamah-gray);">تصفح منتجاتنا وأضف ما يعجبك بالضغط على أيقونة القلب</p>
        <a href="products.php" class="btn-product-order" style="display:inline-block;margin-top:1rem;text-decoration:none;padding:0.7rem 2rem;">تصفح المنتجات</a>
      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
