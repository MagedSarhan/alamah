<?php
$pageTitle = 'حسابي | علامة ALAMAH';
$pageDescription = 'إعدادات حسابك الشخصي';
$activePage = 'account';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Order.php';

if (!is_logged_in()) { redirect('auth/login.php'); }

$userModel = new User();
$user = $userModel->findById(current_user_id());
$orderModel = new Order();
$orders = $orderModel->getByUserId(current_user_id());
?>
  <div style="height:90px;"></div>

  <section class="section-padding" style="padding-top:2rem;">
    <div class="container" style="max-width:800px;">

      <!-- Profile Header -->
      <div class="account-header reveal">
        <div class="account-avatar-wrap" id="avatarWrap">
          <?php if (!empty($user['avatar'])): ?>
            <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="account-avatar" id="avatarImg">
          <?php else: ?>
            <div class="account-avatar account-avatar--placeholder" id="avatarImg">
              <?= mb_substr($user['name'], 0, 1) ?>
            </div>
          <?php endif; ?>
          <label class="account-avatar-edit" for="avatarInput" title="تغيير الصورة">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
          </label>
          <input type="file" id="avatarInput" accept="image/*" style="display:none;">
        </div>
        <div>
          <h1 class="account-name"><?= clean($user['name']) ?></h1>
          <p class="account-email" dir="ltr"><?= clean($user['email']) ?></p>
          <span class="account-badge"><?= $user['is_verified'] ? '<i class="fa-solid fa-circle-check"></i> حساب مُفعّل' : '<i class="fa-solid fa-clock"></i> بانتظار التفعيل' ?></span>
        </div>
      </div>

      <!-- Tabs -->
      <div class="account-tabs reveal">
        <button class="account-tab active" data-tab="profile"><i class="fa-solid fa-user"></i> الملف الشخصي</button>
        <button class="account-tab" data-tab="password"><i class="fa-solid fa-lock"></i> كلمة المرور</button>
        <button class="account-tab" data-tab="orders"><i class="fa-solid fa-bag-shopping"></i> طلباتي</button>
      </div>

      <!-- Tab: Profile -->
      <div class="account-panel active" id="panel-profile">
        <form id="profileForm" class="account-form">
          <label>الاسم الكامل</label>
          <input type="text" name="name" value="<?= clean($user['name']) ?>" required>
          <label>البريد الإلكتروني</label>
          <input type="email" value="<?= clean($user['email']) ?>" disabled style="opacity:0.6;cursor:not-allowed;">
          <p style="font-size:0.78rem;color:var(--alamah-gray);margin-top:-0.5rem;">لا يمكن تغيير البريد الإلكتروني</p>
          <div id="profileMsg"></div>
          <button type="submit" class="account-btn"><i class="fa-solid fa-check" style="margin-left:0.3rem;"></i> حفظ التغييرات</button>
        </form>
      </div>

      <!-- Tab: Password -->
      <div class="account-panel" id="panel-password">
        <form id="passwordForm" class="account-form">
          <label>كلمة المرور الحالية</label>
          <input type="password" name="current_password" required>
          <label>كلمة المرور الجديدة</label>
          <input type="password" name="new_password" required minlength="6">
          <label>تأكيد كلمة المرور الجديدة</label>
          <input type="password" name="confirm_password" required minlength="6">
          <div id="passwordMsg"></div>
          <button type="submit" class="account-btn"><i class="fa-solid fa-key" style="margin-left:0.3rem;"></i> تغيير كلمة المرور</button>
        </form>
      </div>

      <!-- Tab: Orders -->
      <div class="account-panel" id="panel-orders">
        <?php if (empty($orders)): ?>
          <div style="text-align:center;padding:3rem 0;color:var(--alamah-gray);">
            <i class="fa-solid fa-bag-shopping" style="font-size:40px;opacity:0.2;display:block;margin-bottom:0.8rem;"></i>
            <p>لا توجد طلبات بعد</p>
          </div>
        <?php else: ?>
          <?php foreach ($orders as $o): ?>
          <div class="order-card">
            <div class="order-card-header">
              <span class="order-id">طلب #<?= $o['id'] ?></span>
              <span class="order-status order-status--<?= $o['status'] ?>">
                <?= ['new'=>'جديد','processing'=>'قيد التنفيذ','completed'=>'مكتمل','cancelled'=>'ملغي'][$o['status']] ?? $o['status'] ?>
              </span>
            </div>
            <div class="order-card-body">
              <span><?= number_format($o['total'], 0) ?> ر.س</span>
              <span style="color:var(--alamah-gray);font-size:0.82rem;"><?= date('Y/m/d', strtotime($o['created_at'])) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

<style>
.account-header{display:flex;align-items:center;gap:1.5rem;padding:2rem;background:var(--surface-card);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);margin-bottom:1.5rem;}
.account-avatar-wrap{position:relative;flex-shrink:0;}
.account-avatar{width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--alamah-beige);}
.account-avatar--placeholder{display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--alamah-navy),var(--alamah-navy-light));color:#fff;font-size:2rem;font-weight:700;}
.account-avatar-edit{position:absolute;bottom:0;right:0;width:28px;height:28px;border-radius:50%;background:var(--alamah-gold);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;transition:transform 0.2s;}
.account-avatar-edit:hover{transform:scale(1.15);}
.account-name{font-family:var(--font-arabic);font-size:1.3rem;font-weight:700;color:var(--alamah-navy);margin-bottom:0.1rem;}
.account-email{font-size:0.85rem;color:var(--alamah-gray);margin-bottom:0.3rem;}
.account-badge{font-size:0.75rem;padding:0.2rem 0.7rem;border-radius:50px;background:#e8f5e9;color:#2A7E2A;}
.account-tabs{display:flex;gap:0;background:var(--surface-card);border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-card);margin-bottom:1.5rem;}
.account-tab{flex:1;padding:0.8rem;border:none;background:none;font-family:var(--font-arabic);font-weight:600;font-size:0.88rem;color:var(--alamah-gray);cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.4rem;}
.account-tab:hover{color:var(--alamah-navy);background:rgba(201,169,110,0.05);}
.account-tab.active{color:var(--alamah-navy);background:rgba(201,169,110,0.1);border-bottom:2px solid var(--alamah-gold);}
.account-panel{display:none;background:var(--surface-card);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);padding:2rem;}
.account-panel.active{display:block;}
.account-form label{display:block;font-weight:600;font-size:0.88rem;color:var(--alamah-navy);margin-bottom:0.3rem;margin-top:1rem;}
.account-form label:first-child{margin-top:0;}
.account-form input{width:100%;padding:0.7rem 1rem;border:1.5px solid #eee;border-radius:10px;font-family:var(--font-arabic);font-size:0.9rem;outline:none;transition:border-color 0.2s;}
.account-form input:focus{border-color:var(--alamah-gold);}
.account-btn{display:inline-flex;align-items:center;gap:0.3rem;margin-top:1.5rem;padding:0.7rem 2rem;background:var(--alamah-navy);color:#fff;border:none;border-radius:10px;font-family:var(--font-arabic);font-weight:600;font-size:0.9rem;cursor:pointer;transition:all 0.2s;}
.account-btn:hover{background:var(--alamah-navy-light);transform:translateY(-1px);}
.order-card{border:1px solid #f0f0f0;border-radius:12px;margin-bottom:0.8rem;overflow:hidden;}
.order-card-header{display:flex;justify-content:space-between;align-items:center;padding:0.8rem 1rem;background:#FAFAFA;}
.order-id{font-weight:700;color:var(--alamah-navy);font-size:0.9rem;}
.order-status{font-size:0.75rem;padding:0.2rem 0.7rem;border-radius:50px;font-weight:600;}
.order-status--new{background:#e3f2fd;color:#1565c0;}
.order-status--processing{background:#fff3e0;color:#e65100;}
.order-status--completed{background:#e8f5e9;color:#2e7d32;}
.order-status--cancelled{background:#fce4ec;color:#c62828;}
.order-card-body{display:flex;justify-content:space-between;align-items:center;padding:0.8rem 1rem;}
.msg-success{padding:0.6rem 1rem;border-radius:8px;background:#e8f5e9;color:#2A7E2A;font-size:0.85rem;margin-top:0.5rem;}
.msg-error{padding:0.6rem 1rem;border-radius:8px;background:#fce4ec;color:#c62828;font-size:0.85rem;margin-top:0.5rem;}
@media(max-width:575px){.account-header{flex-direction:column;text-align:center;}.account-tabs{flex-wrap:wrap;}.account-tab{font-size:0.8rem;padding:0.6rem;}}
</style>

<script>
// Tabs
document.querySelectorAll('.account-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.account-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.account-panel').forEach(p => p.classList.remove('active'));
    tab.classList.add('active');
    document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
  });
});

// Avatar upload
document.getElementById('avatarInput').addEventListener('change', async function() {
  if (!this.files[0]) return;
  const fd = new FormData();
  fd.append('action', 'update_avatar');
  fd.append('avatar', this.files[0]);
  try {
    const res = await fetch('api/profile.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.ok) {
      const wrap = document.getElementById('avatarImg');
      if (wrap.tagName === 'IMG') { wrap.src = data.avatar; }
      else { wrap.outerHTML = `<img src="${data.avatar}" alt="Avatar" class="account-avatar" id="avatarImg">`; }
    } else { alert(data.error); }
  } catch(e) { alert('حدث خطأ'); }
});

// Profile form
document.getElementById('profileForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  fd.append('action', 'update_profile');
  try {
    const res = await fetch('api/profile.php', { method: 'POST', body: fd });
    const data = await res.json();
    document.getElementById('profileMsg').innerHTML = data.ok
      ? `<div class="msg-success">${data.message}</div>`
      : `<div class="msg-error">${data.error}</div>`;
  } catch(e) { document.getElementById('profileMsg').innerHTML = '<div class="msg-error">حدث خطأ</div>'; }
});

// Password form
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  fd.append('action', 'update_password');
  try {
    const res = await fetch('api/profile.php', { method: 'POST', body: fd });
    const data = await res.json();
    document.getElementById('passwordMsg').innerHTML = data.ok
      ? `<div class="msg-success">${data.message}</div>`
      : `<div class="msg-error">${data.error}</div>`;
    if (data.ok) this.reset();
  } catch(e) { document.getElementById('passwordMsg').innerHTML = '<div class="msg-error">حدث خطأ</div>'; }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
