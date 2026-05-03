<?php
$pageTitle = 'المنتجات | علامة ALAMAH';
$pageDescription = 'تسوّق منتجات علامة الفاخرة — هدايا دعائية، معلقات سيارات، حفر بالليزر، ستيكرات والمزيد.';
$activePage = 'products';
require_once __DIR__ . '/includes/header.php';
?>
  <div style="height: 80px;"></div>

  <!-- CATEGORY TABS -->
  <div class="products-tabs-wrapper" id="productsTabs"><div class="container"><div class="products-tabs" id="tabsContainer"></div></div></div>

  <!-- PRODUCTS SECTION -->
  <section class="section-padding" style="padding-top: 2rem;">
    <div class="container">
      <div class="text-center mb-4">
        <h1 class="section-title" id="productsPageTitle">جميع المنتجات</h1>
        <p class="section-subtitle mx-auto">اكتشف تشكيلتنا الفاخرة واختر ما يناسبك</p>
      </div>
      <div class="products-grid" id="productsGrid"></div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
