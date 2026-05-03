<?php
/**
 * علامة | ALAMAH — الصفحة الرئيسية
 */
$pageTitle = 'علامة | ALAMAH — اترك علامتك';
$pageDescription = 'منتجات مخصصة فاخرة، حفر بالليزر، هدايا دعائية، ومعلقات سيارات. اترك علامتك مع أرقى المنتجات المصنوعة حسب الطلب.';
$activePage = 'index';

require_once __DIR__ . '/classes/Setting.php';
$settingModel = new Setting();
$slides = $settingModel->getHeroSlides();

require_once __DIR__ . '/includes/header.php';
?>

  <!-- HERO SLIDER -->
  <div class="hero-wrapper">
    <section class="hero-section" id="hero">
      <div class="hero-slider">
        <?php foreach ($slides as $i => $slide): ?>
        <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>">
          <img src="<?= htmlspecialchars($slide['image']) ?>" alt="<?= htmlspecialchars($slide['alt_text'] ?? 'علامة') ?>">
        </div>
        <?php endforeach; ?>
      </div>
      <div class="hero-dots" id="heroDots">
        <?php foreach ($slides as $i => $slide): ?>
        <div class="hero-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
        <?php endforeach; ?>
      </div>
      <div class="hero-progress"><div class="hero-progress-bar" id="heroProgressBar"></div></div>
    </section>
  </div>

<?php
require_once __DIR__ . '/classes/Category.php';
$categoryModel = new Category();
$allCategories = $categoryModel->getAll(true);
$tileColors = ['blush', 'sage', 'butter', 'lavender', 'peach', 'sky'];
?>
  <!-- CATEGORIES -->
  <section class="categories-section section-padding" id="categories">
    <div class="container">
      <div class="text-center mb-5 reveal">
        <div class="section-divider mx-auto"></div>
        <h2 class="section-title">أين تريد أن تترك علامتك؟</h2>
        <p class="section-subtitle mx-auto">اختر من تشكيلتنا الفاخرة واجعل كل قطعة تحكي قصتك</p>
      </div>
      <div class="row g-3">
        <?php foreach ($allCategories as $ci => $cat): 
          $colorClass = $tileColors[$ci % count($tileColors)];
          $delay = ($ci % 6) + 1;
        ?>
        <div class="col-lg-4 col-md-6 reveal reveal-delay-<?= $delay ?>">
          <a href="products.php?cat=<?= htmlspecialchars($cat['key_name']) ?>" style="text-decoration:none;">
            <div class="category-tile category-tile--<?= $colorClass ?>">
              <div class="category-tile-content">
                <h3 class="category-tile-title"><?= htmlspecialchars($cat['label']) ?></h3>
                <p class="category-tile-desc"><?= htmlspecialchars($cat['description'] ?? '') ?></p>
              </div>
              <div class="category-tile-image">
                <?php if (!empty($cat['image'])): ?>
                  <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['label']) ?>">
                <?php else: ?>
                  <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="20" y="20" width="60" height="60" rx="12" stroke="#1B2A5B" stroke-width="2.5" fill="rgba(27,42,91,0.04)"/><path d="M40 50 L50 40 L60 50 L50 60 Z" stroke="#C9A96E" stroke-width="2" fill="rgba(201,169,110,0.1)"/></svg>
                <?php endif; ?>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- BEST SELLERS -->
  <section class="bestsellers-section section-padding" id="bestsellers">
    <div class="container">
      <div class="text-center mb-5 reveal"><div class="section-divider mx-auto"></div><h2 class="section-title">الأكثر طلباً</h2><p class="section-subtitle mx-auto">قطع مختارة بعناية، صُنعت لتبقى</p></div>
      <div class="row flex-nowrap overflow-auto custom-product-slider pb-4" id="bestsellerSlider">
        <!-- Products injected by JS -->
      </div>
    </div>
  </section>

  <!-- CTA BANNER -->
  <section class="cta-banner-section reveal" id="customOrder">
    <img src="image/custom-order-banner.png" alt="صمم طلبك الخاص مع علامة">
  </section>

  <!-- WHY CHOOSE US -->
  <section class="why-section section-padding" id="why">
    <div class="container">
      <div class="text-center mb-5 reveal"><div class="section-divider mx-auto"></div><h2 class="section-title">لماذا علامة؟</h2><p class="section-subtitle mx-auto">نصنع لك قطعاً استثنائية تعكس ذوقك وهويتك</p></div>
      <div class="row g-3 g-md-4">
        <div class="col-6 col-lg-3 reveal reveal-delay-1"><div class="why-card why-card--red"><div class="why-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/></svg></div><h4>جودة فاخرة</h4><p>نستخدم أجود الخامات لنضمن منتجاً يليق بك</p></div></div>
        <div class="col-6 col-lg-3 reveal reveal-delay-2"><div class="why-card why-card--gold"><div class="why-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h4>تخصيص كامل</h4><p>كل قطعة تُصنع حسب رؤيتك ورغبتك</p></div></div>
        <div class="col-6 col-lg-3 reveal reveal-delay-3"><div class="why-card why-card--navy"><div class="why-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h4>تسليم سريع</h4><p>نلتزم بالمواعيد ونوصلك طلبك بأسرع وقت</p></div></div>
        <div class="col-6 col-lg-3 reveal reveal-delay-4"><div class="why-card why-card--blush"><div class="why-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h4>عناية بالتفاصيل</h4><p>نهتم بأدق التفاصيل لنصنع لك تجربة لا تُنسى</p></div></div>
      </div>
    </div>
  </section>

  <!-- CRAFTSMANSHIP STATS -->
  <section class="craftsmanship-section section-padding">
    <div class="container">
      <div class="row align-items-center justify-content-center">
        <div class="col-md-3 col-6 reveal reveal-delay-1"><div class="craft-stat"><div class="craft-stat-number"><span>+</span>5000</div><div class="craft-stat-label">طلب منجز</div></div></div>
        <div class="col-auto d-none d-md-block"><div class="craft-divider-line"></div></div>
        <div class="col-md-3 col-6 reveal reveal-delay-2"><div class="craft-stat"><div class="craft-stat-number"><span>+</span>200</div><div class="craft-stat-label">تصميم فريد</div></div></div>
        <div class="col-auto d-none d-md-block"><div class="craft-divider-line"></div></div>
        <div class="col-md-3 col-6 reveal reveal-delay-3"><div class="craft-stat"><div class="craft-stat-number">98<span>%</span></div><div class="craft-stat-label">رضا العملاء</div></div></div>
        <div class="col-auto d-none d-md-block"><div class="craft-divider-line"></div></div>
        <div class="col-md-2 col-6 reveal reveal-delay-4"><div class="craft-stat"><div class="craft-stat-number"><span>+</span>15</div><div class="craft-stat-label">فئة منتجات</div></div></div>
      </div>
    </div>
  </section>

  <!-- ORDERING STEPS -->
  <section class="steps-section section-padding" id="steps">
    <div class="container">
      <div class="text-center mb-5 reveal"><h2 class="steps-title">كيف تبدأ طلبك؟</h2><div class="steps-divider-line mx-auto"></div><p class="steps-subtitle">ثلاث خطوات بسيطة لتحصل على قطعة الميزة</p></div>
      <div class="steps-row reveal">
        <div class="steps-bg-logo"><img src="image/bg-logo.png" alt="" aria-hidden="true"></div>
        <svg class="steps-arrow" viewBox="0 0 800 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M650 100 C580 100, 520 30, 400 30 C280 30, 220 100, 150 100" stroke="#D63B2F" stroke-width="3" fill="none" stroke-linecap="round"/><polygon points="650,92 670,100 650,108" fill="#D63B2F"/></svg>
        <div class="step-card"><div class="step-circle"><span class="step-circle-num">01</span></div><h4 class="step-card-title">اختر المنتج</h4><p class="step-card-desc">تصفّح تشكيلتنا واختر نوع المنتج الذي يناسبك</p></div>
        <div class="step-card step-card--center"><div class="step-circle"><span class="step-circle-num">02</span></div><h4 class="step-card-title">شاركنا تصميمك</h4><p class="step-card-desc">أرسل لنا النص أو الشعار أو الفكرة التي تريد تنفيذها</p></div>
        <div class="step-card"><div class="step-circle"><span class="step-circle-num">03</span></div><h4 class="step-card-title">استلم طلبك</h4><p class="step-card-desc">نصنع قطعتك بعناية ونوصلها لك أينما كنت</p></div>
      </div>
    </div>
  </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
