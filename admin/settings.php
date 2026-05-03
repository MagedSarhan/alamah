<?php
/** Admin — Site Settings (v2) */
$adminPageTitle = 'إعدادات الموقع';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Setting.php';
$settingModel = new Setting();

// Handle settings save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $keys = ['site_name','site_description','whatsapp_number','phone','email','address','map_embed_url',
             'stats_orders','stats_designs','stats_satisfaction','stats_categories'];
    foreach ($keys as $k) {
        if (isset($_POST[$k])) $settingModel->set($k, trim($_POST[$k]));
    }
    set_flash('success', 'تم حفظ الإعدادات بنجاح');
    header('Location: settings.php'); exit;
}

// Handle social link add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_social'])) {
    $settingModel->createSocialLink([
        'platform'   => trim($_POST['platform'] ?? ''),
        'icon_class' => trim($_POST['icon_class'] ?? ''),
        'url'        => trim($_POST['url'] ?? ''),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active'  => 1
    ]);
    set_flash('success', 'تم إضافة رابط التواصل');
    header('Location: settings.php#social'); exit;
}

// Handle social link update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_social'])) {
    $settingModel->updateSocialLink((int)$_POST['social_id'], [
        'platform'   => trim($_POST['platform'] ?? ''),
        'icon_class' => trim($_POST['icon_class'] ?? ''),
        'url'        => trim($_POST['url'] ?? ''),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'is_active'  => isset($_POST['is_active']) ? 1 : 0
    ]);
    set_flash('success', 'تم تحديث رابط التواصل');
    header('Location: settings.php#social'); exit;
}

if (isset($_GET['delete_social'])) {
    $settingModel->deleteSocialLink((int)$_GET['delete_social']);
    set_flash('success', 'تم حذف رابط التواصل');
    header('Location: settings.php#social'); exit;
}

$settings = $settingModel->getAll();
$socialLinks = $settingModel->getSocialLinks(false);

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar"><div><h1><i class="fa-solid fa-gear" style="margin-left:0.4rem;color:var(--admin-gold);"></i> إعدادات الموقع</h1></div></div>

<!-- ═══ MAIN SETTINGS FORM ═══ -->
<form method="POST" class="admin-form">
  <input type="hidden" name="save_settings" value="1">

  <!-- Site Info -->
  <div class="admin-card">
    <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-globe" style="margin-left:0.3rem;color:var(--admin-gold);"></i> معلومات الموقع</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
      <div><label>اسم الموقع</label><input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"></div>
      <div><label>وصف الموقع</label><input type="text" name="site_description" value="<?= htmlspecialchars($settings['site_description'] ?? '') ?>"></div>
    </div>
  </div>

  <!-- Contact Info (for footer + contact page) -->
  <div class="admin-card">
    <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-address-card" style="margin-left:0.3rem;color:var(--admin-gold);"></i> بيانات التواصل</h2>
    <p style="color:var(--admin-gray);font-size:0.82rem;margin-bottom:1rem;">هذه البيانات تظهر في صفحة تواصل معنا وأسفل الموقع (Footer) وزر الواتساب العائم</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
      <div><label><i class="fa-brands fa-whatsapp" style="margin-left:0.3rem;color:#25D366;"></i> رقم واتساب</label><input type="text" name="whatsapp_number" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>" dir="ltr" placeholder="967784449090"></div>
      <div><label><i class="fa-solid fa-phone" style="margin-left:0.3rem;color:var(--admin-gold);"></i> رقم الهاتف</label><input type="text" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" dir="ltr"></div>
      <div><label><i class="fa-solid fa-envelope" style="margin-left:0.3rem;color:var(--admin-gold);"></i> البريد الإلكتروني</label><input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" dir="ltr"></div>
      <div><label><i class="fa-solid fa-location-dot" style="margin-left:0.3rem;color:var(--admin-gold);"></i> العنوان</label><input type="text" name="address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>"></div>
    </div>
    <label><i class="fa-solid fa-map" style="margin-left:0.3rem;color:var(--admin-gold);"></i> رابط خريطة Google Maps (embed URL)</label>
    <input type="text" name="map_embed_url" value="<?= htmlspecialchars($settings['map_embed_url'] ?? '') ?>" dir="ltr" placeholder="https://www.google.com/maps/embed?pb=...">
  </div>

  <!-- Stats -->
  <div class="admin-card">
    <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-chart-bar" style="margin-left:0.3rem;color:var(--admin-gold);"></i> إحصائيات الموقع (تظهر في الصفحة الرئيسية)</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;">
      <div><label>طلبات منجزة</label><input type="text" name="stats_orders" value="<?= htmlspecialchars($settings['stats_orders'] ?? '') ?>"></div>
      <div><label>تصاميم فريدة</label><input type="text" name="stats_designs" value="<?= htmlspecialchars($settings['stats_designs'] ?? '') ?>"></div>
      <div><label>رضا العملاء</label><input type="text" name="stats_satisfaction" value="<?= htmlspecialchars($settings['stats_satisfaction'] ?? '') ?>"></div>
      <div><label>فئات المنتجات</label><input type="text" name="stats_categories" value="<?= htmlspecialchars($settings['stats_categories'] ?? '') ?>"></div>
    </div>
  </div>

  <button type="submit" class="btn-admin btn-admin--primary" style="padding:0.75rem 3rem;font-size:0.95rem;"><i class="fa-solid fa-floppy-disk" style="margin-left:0.3rem;"></i> حفظ الإعدادات</button>
</form>

<hr style="border:none;border-top:2px dashed var(--admin-border);margin:2rem 0;">

<!-- ═══ SOCIAL LINKS ═══ -->
<div id="social">
  <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:1.5rem;">
    <!-- Add Social -->
    <div class="admin-card">
      <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-share-nodes" style="margin-left:0.3rem;color:var(--admin-gold);"></i> إضافة موقع تواصل</h2>
      <form method="POST" class="admin-form" id="socialForm">
        <input type="hidden" name="add_social" value="1">
        
        <label><i class="fa-solid fa-icons" style="margin-left:0.3rem;color:var(--admin-gold);"></i> اختر المنصة</label>
        <div class="social-dropdown" id="socialDropdown">
          <div class="social-dropdown-trigger" id="socialTrigger">
            <span class="social-dropdown-preview" id="socialPreview">
              <i class="fa-solid fa-plus" style="opacity:0.3;"></i>
              <span>اختر منصة التواصل...</span>
            </span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <div class="social-dropdown-menu" id="socialMenu">
            <div class="social-dropdown-search">
              <i class="fa-solid fa-search" style="color:#ccc;font-size:12px;"></i>
              <input type="text" id="socialSearch" placeholder="ابحث عن المنصة..." autocomplete="off">
            </div>
            <div class="social-dropdown-list" id="socialList"></div>
          </div>
        </div>
        <input type="hidden" name="icon_class" id="socialIconClass" required>
        <input type="hidden" name="platform" id="socialPlatform" required>

        <label>الرابط</label><input type="text" name="url" placeholder="https://instagram.com/alamah" dir="ltr" required>
        <label>الترتيب</label><input type="number" name="sort_order" value="0">
        <button type="submit" class="btn-admin btn-admin--primary"><i class="fa-solid fa-plus" style="margin-left:0.3rem;"></i> إضافة</button>
      </form>
    </div>

    <!-- Social Links List -->
    <div class="admin-card">
      <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-list" style="margin-left:0.3rem;color:var(--admin-gold);"></i> مواقع التواصل الحالية</h2>
      <?php if (empty($socialLinks)): ?>
      <div class="empty-state"><i class="fa-solid fa-link" style="font-size:30px;opacity:0.2;display:block;margin-bottom:0.5rem;"></i><p>لا توجد روابط تواصل</p></div>
      <?php else: ?>
      <?php foreach ($socialLinks as $sl): ?>
      <form method="POST" style="display:flex;align-items:center;gap:0.5rem;padding:0.6rem 0;border-bottom:1px solid #f5f5f5;flex-wrap:wrap;">
        <input type="hidden" name="update_social" value="1">
        <input type="hidden" name="social_id" value="<?= $sl['id'] ?>">
        <div style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:var(--admin-navy);border-radius:8px;color:#fff;font-size:16px;flex-shrink:0;">
          <i class="<?= htmlspecialchars($sl['icon_class']) ?>"></i>
        </div>
        <input type="text" name="platform" value="<?= clean($sl['platform']) ?>" style="width:100px;padding:0.4rem 0.6rem;border:1px solid #eee;border-radius:6px;font-size:0.82rem;">
        <input type="text" name="icon_class" value="<?= clean($sl['icon_class']) ?>" style="width:160px;padding:0.4rem 0.6rem;border:1px solid #eee;border-radius:6px;font-size:0.82rem;" dir="ltr">
        <input type="text" name="url" value="<?= clean($sl['url']) ?>" style="flex:1;min-width:120px;padding:0.4rem 0.6rem;border:1px solid #eee;border-radius:6px;font-size:0.82rem;" dir="ltr">
        <input type="number" name="sort_order" value="<?= $sl['sort_order'] ?>" style="width:50px;padding:0.4rem;border:1px solid #eee;border-radius:6px;font-size:0.82rem;">
        <label style="font-size:0.78rem;display:flex;align-items:center;gap:0.3rem;"><input type="checkbox" name="is_active" <?= $sl['is_active'] ? 'checked' : '' ?>> فعال</label>
        <button type="submit" class="btn-admin btn-admin--primary btn-admin--sm"><i class="fa-solid fa-check"></i></button>
        <a href="settings.php?delete_social=<?= $sl['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف؟')"><i class="fa-solid fa-trash" style="font-size:11px;"></i></a>
      </form>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
.social-dropdown{position:relative;margin-bottom:1rem;}
.social-dropdown-trigger{display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.8rem;border:2px solid var(--admin-border);border-radius:8px;cursor:pointer;background:#fff;transition:all 0.2s;}
.social-dropdown-trigger:hover{border-color:var(--admin-gold);}
.social-dropdown.open .social-dropdown-trigger{border-color:var(--admin-gold);box-shadow:0 0 0 3px rgba(201,169,110,0.15);}
.social-dropdown-preview{display:flex;align-items:center;gap:0.5rem;font-size:0.9rem;color:var(--admin-navy);}
.social-dropdown-preview i{width:20px;text-align:center;font-size:16px;}
.social-dropdown-menu{display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:2px solid var(--admin-border);border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,0.12);z-index:100;max-height:320px;overflow:hidden;}
.social-dropdown.open .social-dropdown-menu{display:block;animation:sdFadeIn 0.2s ease;}
@keyframes sdFadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.social-dropdown-search{display:flex;align-items:center;gap:0.5rem;padding:0.6rem 0.8rem;border-bottom:1px solid #f0f0f0;}
.social-dropdown-search input{border:none;outline:none;font-size:0.85rem;flex:1;font-family:inherit;}
.social-dropdown-list{max-height:260px;overflow-y:auto;padding:0.3rem 0;}
.social-dropdown-item{display:flex;align-items:center;gap:0.6rem;padding:0.55rem 0.8rem;cursor:pointer;font-size:0.85rem;transition:background 0.15s;}
.social-dropdown-item:hover{background:rgba(201,169,110,0.08);}
.social-dropdown-item i{width:22px;height:22px;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:14px;color:#fff;flex-shrink:0;}
.social-dropdown-item span{color:var(--admin-navy);font-weight:500;}
.social-dropdown-item small{color:var(--admin-gray);font-size:0.72rem;margin-right:auto;direction:ltr;}
.social-dropdown-list::-webkit-scrollbar{width:5px;}
.social-dropdown-list::-webkit-scrollbar-thumb{background:#ddd;border-radius:3px;}
</style>

<script>
const SOCIAL_PLATFORMS = [
  {name:'Instagram',icon:'fa-brands fa-instagram',color:'#E4405F'},
  {name:'Twitter / X',icon:'fa-brands fa-x-twitter',color:'#000'},
  {name:'Facebook',icon:'fa-brands fa-facebook-f',color:'#1877F2'},
  {name:'TikTok',icon:'fa-brands fa-tiktok',color:'#000'},
  {name:'Snapchat',icon:'fa-brands fa-snapchat',color:'#FFFC00',textColor:'#333'},
  {name:'YouTube',icon:'fa-brands fa-youtube',color:'#FF0000'},
  {name:'WhatsApp',icon:'fa-brands fa-whatsapp',color:'#25D366'},
  {name:'Telegram',icon:'fa-brands fa-telegram',color:'#0088CC'},
  {name:'LinkedIn',icon:'fa-brands fa-linkedin-in',color:'#0A66C2'},
  {name:'Pinterest',icon:'fa-brands fa-pinterest-p',color:'#BD081C'},
  {name:'Reddit',icon:'fa-brands fa-reddit-alien',color:'#FF4500'},
  {name:'Discord',icon:'fa-brands fa-discord',color:'#5865F2'},
  {name:'Twitch',icon:'fa-brands fa-twitch',color:'#9146FF'},
  {name:'GitHub',icon:'fa-brands fa-github',color:'#181717'},
  {name:'Dribbble',icon:'fa-brands fa-dribbble',color:'#EA4C89'},
  {name:'Behance',icon:'fa-brands fa-behance',color:'#1769FF'},
  {name:'Medium',icon:'fa-brands fa-medium',color:'#000'},
  {name:'Spotify',icon:'fa-brands fa-spotify',color:'#1DB954'},
  {name:'Apple',icon:'fa-brands fa-apple',color:'#000'},
  {name:'Google',icon:'fa-brands fa-google',color:'#4285F4'},
  {name:'Amazon',icon:'fa-brands fa-amazon',color:'#FF9900'},
  {name:'Threads',icon:'fa-brands fa-threads',color:'#000'},
  {name:'Mastodon',icon:'fa-brands fa-mastodon',color:'#6364FF'},
  {name:'SoundCloud',icon:'fa-brands fa-soundcloud',color:'#FF5500'},
  {name:'Vimeo',icon:'fa-brands fa-vimeo-v',color:'#1AB7EA'},
  {name:'Flickr',icon:'fa-brands fa-flickr',color:'#0063DC'},
  {name:'Steam',icon:'fa-brands fa-steam',color:'#171A21'},
  {name:'Xbox',icon:'fa-brands fa-xbox',color:'#107C10'},
  {name:'PlayStation',icon:'fa-brands fa-playstation',color:'#003087'},
  {name:'الموقع الإلكتروني',icon:'fa-solid fa-globe',color:'#1B2A5B'},
  {name:'البريد الإلكتروني',icon:'fa-solid fa-envelope',color:'#C9A96E'},
  {name:'الهاتف',icon:'fa-solid fa-phone',color:'#25D366'},
  {name:'الموقع / الخريطة',icon:'fa-solid fa-location-dot',color:'#D63B2F'},
];

const dropdown = document.getElementById('socialDropdown');
const trigger = document.getElementById('socialTrigger');
const menu = document.getElementById('socialMenu');
const list = document.getElementById('socialList');
const search = document.getElementById('socialSearch');
const preview = document.getElementById('socialPreview');
const iconInput = document.getElementById('socialIconClass');
const platformInput = document.getElementById('socialPlatform');

function renderList(filter = '') {
  const f = filter.toLowerCase();
  list.innerHTML = SOCIAL_PLATFORMS
    .filter(p => p.name.toLowerCase().includes(f) || p.icon.toLowerCase().includes(f))
    .map(p => `<div class="social-dropdown-item" data-icon="${p.icon}" data-name="${p.name}" data-color="${p.color}">
      <i class="${p.icon}" style="background:${p.color};${p.textColor?'color:'+p.textColor:''}"></i>
      <span>${p.name}</span>
      <small>${p.icon}</small>
    </div>`).join('') || '<div style="padding:1rem;text-align:center;color:#ccc;font-size:0.85rem;">لا توجد نتائج</div>';
}

trigger.addEventListener('click', () => { dropdown.classList.toggle('open'); if(dropdown.classList.contains('open')){search.focus();renderList();} });
document.addEventListener('click', e => { if(!dropdown.contains(e.target)) dropdown.classList.remove('open'); });
search.addEventListener('input', () => renderList(search.value));

list.addEventListener('click', e => {
  const item = e.target.closest('.social-dropdown-item');
  if (!item) return;
  const icon = item.dataset.icon, name = item.dataset.name, color = item.dataset.color;
  iconInput.value = icon;
  platformInput.value = name;
  preview.innerHTML = `<i class="${icon}" style="background:${color};color:#fff;width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;font-size:13px;"></i><span>${name}</span>`;
  dropdown.classList.remove('open');
  search.value = '';
});

renderList();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

