<?php
$pageTitle = 'تواصل معنا | علامة ALAMAH';
$pageDescription = 'تواصل مع علامة ALAMAH — نسعد بخدمتك عبر الواتساب أو الهاتف أو البريد الإلكتروني.';
$activePage = 'contact';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Contact.php';
require_once __DIR__ . '/classes/Setting.php';

$settingModel = new Setting();
$siteSettings = $settingModel->getMultiple([
    'whatsapp_number','phone','email','address','map_embed_url'
]);
$socialLinks = $settingModel->getSocialLinks(true);

$waNumber = $siteSettings['whatsapp_number'] ?? '967784449090';
$phone    = $siteSettings['phone'] ?? $waNumber;
$email    = $siteSettings['email'] ?? 'info@alamah.sa';
$address  = $siteSettings['address'] ?? 'الرياض - حي الملقا';
$mapUrl   = $siteSettings['map_embed_url'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $contactModel = new Contact();
        $contactModel->create([
            'name'    => clean($_POST['cfName'] ?? ''),
            'phone'   => clean($_POST['cfPhone'] ?? ''),
            'email'   => clean($_POST['cfEmail'] ?? ''),
            'subject' => clean($_POST['cfSubject'] ?? ''),
            'message' => clean($_POST['cfMessage'] ?? '')
        ]);
        set_flash('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً');
        redirect('contact.php');
    }
}
$flash = get_flash();

require_once __DIR__ . '/includes/header.php';
?>
  <div style="height:90px;"></div>

  <!-- CONTACT SECTION -->
  <section class="section-padding">
    <div class="container">
      <?php if ($flash): ?>
      <div class="alert alert-success" style="background:#EFE;color:#2A7E2A;border:1px solid #CEC;border-radius:12px;padding:1rem;margin-bottom:2rem;font-family:var(--font-arabic);text-align:center;"><?= htmlspecialchars($flash['message']) ?></div>
      <?php endif; ?>
      <div class="row g-0 contact-wrapper reveal">
        <div class="col-lg-5">
          <div class="contact-info-card">
            <h2 class="contact-info-title">تواصل معنا</h2>
            <p class="contact-info-subtitle">يسعدنا تواصلك معنا من أي من القنوات التالية</p>
            <div class="contact-info-item"><div class="contact-info-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#fff" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div><div><strong>العنوان</strong><span><?= htmlspecialchars($address) ?></span></div></div>
            <div class="contact-info-item"><div class="contact-info-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#fff" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div><div><strong>الهاتف</strong><a href="tel:+<?= htmlspecialchars($phone) ?>" style="color:#fff;text-decoration:none;" dir="ltr"><?= htmlspecialchars($phone) ?></a></div></div>
            <div class="contact-info-item"><div class="contact-info-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a8 8 0 0 1-4.243-1.214l-.252-.151-2.734.734.734-2.734-.164-.265A7.96 7.96 0 0 1 4 12a8 8 0 1 1 16 0 8 8 0 0 1-8 8z"/></svg></div><div><strong>واتساب</strong><a href="https://wa.me/<?= htmlspecialchars($waNumber) ?>" target="_blank" style="color:#fff;text-decoration:none;" dir="ltr"><?= htmlspecialchars($waNumber) ?></a></div></div>
            <div class="contact-info-item"><div class="contact-info-icon"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#fff" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div><div><strong>البريد الإلكتروني</strong><a href="mailto:<?= htmlspecialchars($email) ?>" style="color:#fff;text-decoration:none;" dir="ltr"><?= htmlspecialchars($email) ?></a></div></div>
            <?php if (!empty($socialLinks)): ?>
            <div style="margin-top:1.5rem;display:flex;gap:0.7rem;flex-wrap:wrap;">
              <?php foreach ($socialLinks as $sl): ?>
              <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" rel="noopener" style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                <i class="<?= htmlspecialchars($sl['icon_class']) ?>"></i>
              </a>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="contact-form-card">
            <h2 class="contact-form-title">أرسل لنا رسالتك</h2>
            <p class="contact-form-subtitle">نحب نسمع منك! املأ النموذج وسنرد عليك في أقرب وقت</p>
            <form method="POST" id="contactForm">
              <input type="hidden" name="contact_submit" value="1">
              <?= csrf_field() ?>
              <div class="row g-3">
                <div class="col-md-6"><label for="cfName">الاسم الكامل *</label><input type="text" id="cfName" name="cfName" placeholder="أدخل اسمك" required></div>
                <div class="col-md-6"><label for="cfPhone">رقم الجوال *</label><input type="tel" id="cfPhone" name="cfPhone" placeholder="05XXXXXXXX" dir="ltr" required></div>
                <div class="col-md-6"><label for="cfEmail">البريد الإلكتروني</label><input type="email" id="cfEmail" name="cfEmail" placeholder="email@example.com" dir="ltr"></div>
                <div class="col-md-6"><label for="cfSubject">الموضوع *</label><select id="cfSubject" name="cfSubject" required><option value="" disabled selected>اختر الموضوع</option><option value="استفسار عام">استفسار عام</option><option value="طلب خاص">طلب خاص</option><option value="شكوى">شكوى</option><option value="اقتراح">اقتراح</option><option value="تعاون تجاري">تعاون تجاري</option></select></div>
                <div class="col-12"><label for="cfMessage">رسالتك *</label><textarea id="cfMessage" name="cfMessage" rows="4" placeholder="اكتب رسالتك هنا ..." required></textarea></div>
                <div class="col-12"><button type="submit" class="contact-submit-btn"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg> إرسال الرسالة</button></div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MAP -->
  <?php if ($mapUrl): ?>
  <section class="section-padding" style="padding-top:0;">
    <div class="container">
      <div class="contact-map-wrapper reveal">
        <iframe src="<?= htmlspecialchars($mapUrl) ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </section>
  <?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
