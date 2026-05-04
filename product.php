<?php
$pageTitle = 'تفاصيل المنتج | علامة ALAMAH';
$pageDescription = 'عالم من الهدايا الراقية والمنتجات المخصصة.';
$activePage = 'products';

if (!empty($_GET['id'])) {
    require_once __DIR__ . '/classes/Product.php';
    $productModel = new Product();
    $product = $productModel->findById((int)$_GET['id']);
    if ($product) {
        $pageTitle = clean($product['name'] ?? '') . ' | علامة ALAMAH';
        $pageDescription = !empty($product['short_description']) ? $product['short_description'] : ($product['description'] ?? $pageDescription);
        $ogImage = $product['image'] ?? 'image/logo.png';
        $ogType = 'product';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
  <div style="height:90px;"></div>

  <!-- BREADCRUMB -->
  <div class="container" style="padding-top:1rem;">
    <nav aria-label="breadcrumb" id="productBreadcrumb">
      <ol style="display:flex;gap:0.5rem;list-style:none;padding:0;font-size:0.85rem;color:var(--alamah-gray);">
        <li><a href="index.php" style="color:var(--alamah-gray);">الرئيسية</a></li><li>›</li>
        <li><a href="products.php" style="color:var(--alamah-gray);">المنتجات</a></li><li>›</li>
        <li id="breadcrumbProductName" style="color:var(--alamah-navy);font-weight:600;"></li>
      </ol>
    </nav>
  </div>

  <!-- PRODUCT DETAIL -->
  <section class="section-padding" style="padding-top:1.5rem;" id="productDetailSection">
    <div class="container"><div class="row g-4 g-lg-5" id="productDetailContent"></div></div>
  </section>

  <!-- RELATED PRODUCTS -->
  <section class="section-padding" style="background:#FAFAFA;padding-top:2rem;">
    <div class="container">
      <div class="text-center mb-4"><h2 class="section-title" style="font-size:1.5rem;">منتجات مشابهة</h2></div>
      <div class="row g-3" id="relatedProductsGrid"></div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
