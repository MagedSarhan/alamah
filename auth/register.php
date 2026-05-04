<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';
if (is_logged_in()) { header('Location: ../index.php'); exit; }
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="إنشاء حساب جديد في علامة ALAMAH.">
  <title>إنشاء حساب | علامة ALAMAH</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/image/logo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/image/logo.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/image/logo.png">
  <meta name="theme-color" content="#1B2A5B">
  <link rel="canonical" href="<?= SITE_URL ?>/auth/register.php">
  <meta property="og:title" content="إنشاء حساب | علامة ALAMAH">
  <meta property="og:description" content="إنشاء حساب جديد في علامة ALAMAH.">
  <meta property="og:image" content="<?= SITE_URL ?>/image/logo.png">
  <meta property="og:url" content="<?= SITE_URL ?>/auth/register.php">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="علامة | ALAMAH">
  <meta property="og:locale" content="ar_SA">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="إنشاء حساب | علامة ALAMAH">
  <meta name="twitter:description" content="إنشاء حساب جديد في علامة ALAMAH.">
  <meta name="twitter:image" content="<?= SITE_URL ?>/image/logo.png">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,#F7F1E8 0%,#F0E6D3 50%,#E8C9B8 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;direction:rtl}
    .auth-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,0.1);max-width:440px;width:90%;padding:2.5rem;position:relative;overflow:hidden}
    .auth-card::before{content:'';position:absolute;top:0;right:0;left:0;height:4px;background:linear-gradient(90deg,#1B2A5B,#C9A96E,#D63B2F)}
    .auth-logo{text-align:center;margin-bottom:1.5rem}
    .auth-logo img{height:60px}
    .auth-title{font-weight:800;color:#1B2A5B;font-size:1.5rem;text-align:center;margin-bottom:0.3rem}
    .auth-subtitle{color:#8A8580;text-align:center;font-size:0.9rem;margin-bottom:2rem}
    .form-group{margin-bottom:1.2rem}
    .form-group label{display:block;font-weight:600;color:#1B2A5B;margin-bottom:0.4rem;font-size:0.9rem}
    .form-group input{width:100%;padding:0.8rem 1rem;border:2px solid #E5D6BF;border-radius:12px;font-family:'Tajawal',sans-serif;font-size:0.95rem;transition:border-color 0.3s;outline:none;background:#FAFAFA}
    .form-group input:focus{border-color:#C9A96E;background:#fff}
    .btn-auth{width:100%;padding:0.9rem;background:linear-gradient(135deg,#D63B2F,#E05A4F);color:#fff;border:none;border-radius:12px;font-family:'Tajawal',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;transition:all 0.3s;margin-top:0.5rem}
    .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(214,59,47,0.3)}
    .auth-link{text-align:center;margin-top:1.2rem;font-size:0.9rem;color:#8A8580}
    .auth-link a{color:#D63B2F;font-weight:600;text-decoration:none}
    .alert{padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.85rem;font-weight:500}
    .alert-error{background:#FEE;color:#D63B2F;border:1px solid #FCC}
    .alert-success{background:#EFE;color:#2A7E2A;border:1px solid #CEC}
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="auth-logo"><a href="../index.php"><img src="../image/logo.png" alt="علامة"></a></div>
    <h1 class="auth-title">إنشاء حساب جديد</h1>
    <p class="auth-subtitle">انضم إلينا واستمتع بتجربة تسوق مميزة</p>

    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="process.php">
      <input type="hidden" name="action" value="register">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="name">الاسم الكامل</label>
        <input type="text" id="name" name="name" placeholder="أدخل اسمك" required>
      </div>
      <div class="form-group">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" placeholder="example@email.com" dir="ltr" required>
      </div>
      <div class="form-group">
        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password" placeholder="6 أحرف على الأقل" required minlength="6">
      </div>
      <div class="form-group">
        <label for="password_confirm">تأكيد كلمة المرور</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="أعد كتابة كلمة المرور" required>
      </div>
      <button type="submit" class="btn-auth">إنشاء الحساب</button>
    </form>

    <p class="auth-link">لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
    <p class="auth-link" style="margin-top:1.5rem"><a href="../index.php" style="color:#8A8580;">← العودة للموقع</a></p>
  </div>
</body>
</html>
