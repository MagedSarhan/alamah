<?php
/**
 * Shared Footer — Dynamic: loads contact info + social links from DB
 */
require_once __DIR__ . '/../classes/Setting.php';
$_footerSettings = new Setting();
$_siteSettings = $_footerSettings->getMultiple([
    'whatsapp_number','phone','email','address','map_embed_url'
]);
$_socialLinks = $_footerSettings->getSocialLinks(true);
$_waNumber = $_siteSettings['whatsapp_number'] ?? '967784449090';
$_phone    = $_siteSettings['phone'] ?? $_waNumber;
$_email    = $_siteSettings['email'] ?? 'info@alamah.sa';
$_address  = $_siteSettings['address'] ?? 'الرياض - حي الملقا';
?>
  <!-- FOOTER -->
  <footer class="footer-alamah" id="footer">
    <div class="container">
      <div class="row g-5 justify-content-between">
        <div class="col-lg-4 col-md-12 mb-4">
          <div class="footer-brand">
            <img src="image/logo.png" alt="علامة | ALAMAH" class="mb-3">
            <p>شركة علامة هي شريكك الموثوق في عالم الهدايا، نقدم لكم أرقى المنتجات بمعايير عالية.</p>
            <?php if (!empty($_socialLinks)): ?>
            <div class="footer-social mt-4 d-flex justify-content-start">
              <?php foreach ($_socialLinks as $sl): ?>
              <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($sl['platform']) ?>">
                <i class="<?= htmlspecialchars($sl['icon_class']) ?>"></i>
              </a>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-4 col-md-12 mb-4">
          <h5 class="footer-heading">روابط سريعة</h5>
          <div class="row pt-2">
            <div class="col-6"><ul class="footer-links list-unstyled pe-0 mb-0 d-flex flex-column align-items-start"><li><a href="index.php">الرئيسية</a></li><li><a href="products.php">المنتجات</a></li></ul></div>
            <div class="col-6"><ul class="footer-links list-unstyled pe-0 mb-0 d-flex flex-column align-items-start"><li><a href="index.php#why">لماذا علامة</a></li><li><a href="contact.php">تواصل معنا</a></li></ul></div>
          </div>
        </div>
        <div class="col-lg-4 col-md-12 mb-4">
          <h5 class="footer-heading">تواصل معنا</h5>
          <ul class="footer-contact-new list-unstyled pe-0 pt-2">
            <li class="d-flex align-items-start justify-content-start mb-3"><svg class="me-3 flex-shrink-0 mt-1" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><span><?= htmlspecialchars($_address) ?></span></li>
            <li class="mb-3"><a href="tel:+<?= htmlspecialchars($_phone) ?>" class="d-flex align-items-center justify-content-start footer-contact-link text-decoration-none"><svg class="me-3 flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.908.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg><span dir="ltr"><?= htmlspecialchars($_phone) ?></span></a></li>
            <li class="mb-3"><a href="mailto:<?= htmlspecialchars($_email) ?>" class="d-flex align-items-center justify-content-start footer-contact-link text-decoration-none"><svg class="me-3 flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg><span dir="ltr"><?= htmlspecialchars($_email) ?></span></a></li>
            <li class="mb-3"><a href="https://wa.me/<?= htmlspecialchars($_waNumber) ?>" target="_blank" rel="noopener noreferrer" class="d-flex align-items-center justify-content-start footer-contact-link text-decoration-none"><svg class="me-3 flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.74.45 3.37 1.23 4.79L2 22l5.35-1.12c1.37.7 2.95 1.12 4.65 1.12 5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg><span>واتساب</span></a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom"><p>جميع الحقوق محفوظة © <?= date('Y') ?> — <span class="footer-brand-en">ALAMAH</span></p></div>
    </div>
  </footer>

  <!-- CART DRAWER -->
  <div class="cart-overlay" id="cartOverlay"></div>
  <div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header"><h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:0.3rem;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> سلة التسوق <span class="cart-count-label" id="cartCountLabel"></span></h3><button class="cart-close-btn" id="cartCloseBtn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>
    <div class="cart-drawer-body" id="cartDrawerBody"></div>
    <div class="cart-drawer-footer" id="cartDrawerFooter" style="display:none;">
      <div class="cart-total-row"><span class="cart-total-label">المجموع</span><span class="cart-total-value" id="cartTotalValue">0</span></div>
      <button class="btn-checkout-wa" id="btnCheckoutWa" style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>
        أكّد الطلب عبر واتساب</button>
    </div>
  </div>

  <!-- CHECKOUT MODAL -->
  <div class="modal-overlay" id="checkoutModal"><div class="modal-box"><div class="modal-header"><h3 style="display:flex;align-items:center;gap:0.4rem;color:var(--alamah-navy);">تأكيد الطلب</h3><button class="cart-close-btn" id="checkoutModalClose"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div><div class="modal-body"><label for="customerName">الاسم الكريم *</label><input type="text" id="customerName" placeholder="أدخل اسمك الكامل" required><label for="customerPhone">رقم الجوال *</label><input type="tel" id="customerPhone" placeholder="05XXXXXXXX" dir="ltr" required><div id="orderSummaryPreview" style="background:#FAFAFA;border-radius:8px;padding:1rem;margin-top:0.5rem;font-size:0.85rem;color:var(--alamah-navy);"></div></div><div class="modal-footer"><button class="btn-checkout-wa" id="btnConfirmWa" data-wa="<?= htmlspecialchars($_waNumber) ?>" style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;">إرسال</button></div></div></div>

  <!-- CUSTOM FIELD MODAL -->
  <div class="modal-overlay" id="customFieldModal"><div class="modal-box"><div class="modal-header"><h3 style="display:flex;align-items:center;gap:0.4rem;color:var(--alamah-navy);">بيانات المنتج</h3><button class="cart-close-btn" id="customFieldModalClose"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div><div class="modal-body" id="customFieldBody"></div><div class="modal-footer"><button class="btn-modal-confirm" id="btnCustomFieldConfirm" style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;">أضف للسلة</button></div></div></div>

  <!-- FLOATING WHATSAPP (dynamic number) -->
  <a href="https://wa.me/<?= htmlspecialchars($_waNumber) ?>" target="_blank" rel="noopener" class="whatsapp-float" aria-label="واتساب">
    <svg viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a8 8 0 0 1-4.243-1.214l-.252-.151-2.734.734.734-2.734-.164-.265A7.96 7.96 0 0 1 4 12a8 8 0 1 1 16 0 8 8 0 0 1-8 8z"/></svg>
    <span class="whatsapp-float-label">تواصل معنا</span>
  </a>

  <!-- BACK TO TOP -->
  <button class="back-to-top" id="backToTop" aria-label="العودة للأعلى"><svg viewBox="0 0 24 24"><path d="M18 15l-6-6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>var WA_NUMBER = '<?= htmlspecialchars($_waNumber) ?>';</script>
  <script src="js/main.js?v=<?= filemtime(__DIR__ . '/../js/main.js') ?>"></script>
</body>
</html>
