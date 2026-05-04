<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="استعادة كلمة المرور في علامة ALAMAH.">
  <title>نسيت كلمة المرور | علامة ALAMAH</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/image/logo.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/image/logo.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/image/logo.png">
  <meta name="theme-color" content="#1B2A5B">
  <link rel="canonical" href="<?= SITE_URL ?>/auth/forgot_password.php">
  <meta property="og:title" content="نسيت كلمة المرور | علامة ALAMAH">
  <meta property="og:description" content="استعادة كلمة المرور في علامة ALAMAH.">
  <meta property="og:image" content="<?= SITE_URL ?>/image/logo.png">
  <meta property="og:url" content="<?= SITE_URL ?>/auth/forgot_password.php">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="علامة | ALAMAH">
  <meta property="og:locale" content="ar_SA">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="نسيت كلمة المرور | علامة ALAMAH">
  <meta name="twitter:description" content="استعادة كلمة المرور في علامة ALAMAH.">
  <meta name="twitter:image" content="<?= SITE_URL ?>/image/logo.png">
  <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,#F7F1E8,#F0E6D3,#E8C9B8);min-height:100vh;display:flex;align-items:center;justify-content:center;direction:rtl}.auth-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,0.1);max-width:440px;width:90%;padding:2.5rem;position:relative;overflow:hidden}.auth-card::before{content:'';position:absolute;top:0;right:0;left:0;height:4px;background:linear-gradient(90deg,#1B2A5B,#C9A96E,#D63B2F)}.auth-logo{text-align:center;margin-bottom:1.5rem}.auth-logo img{height:60px}.auth-title{font-weight:800;color:#1B2A5B;font-size:1.4rem;text-align:center;margin-bottom:0.3rem}.auth-subtitle{color:#8A8580;text-align:center;font-size:0.9rem;margin-bottom:2rem}.form-group{margin-bottom:1.2rem}.form-group label{display:block;font-weight:600;color:#1B2A5B;margin-bottom:0.4rem;font-size:0.9rem}.form-group input{width:100%;padding:0.8rem 1rem;border:2px solid #E5D6BF;border-radius:12px;font-family:'Tajawal',sans-serif;font-size:0.95rem;transition:border-color 0.3s;outline:none;background:#FAFAFA}.form-group input:focus{border-color:#C9A96E;background:#fff}.btn-auth{width:100%;padding:0.9rem;background:linear-gradient(135deg,#1B2A5B,#2A3F7E);color:#fff;border:none;border-radius:12px;font-family:'Tajawal',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;transition:all 0.3s}.btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(27,42,91,0.3)}.auth-link{text-align:center;margin-top:1.2rem;font-size:0.9rem;color:#8A8580}.auth-link a{color:#D63B2F;font-weight:600;text-decoration:none}.alert{padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.85rem}.alert-error{background:#FEE;color:#D63B2F}.alert-success{background:#EFE;color:#2A7E2A}</style>
</head>
<body>
  <div class="auth-card">
    <div class="auth-logo"><a href="../index.php"><img src="../image/logo.png" alt="علامة"></a></div>
    <h1 class="auth-title">نسيت كلمة المرور</h1>
    <p class="auth-subtitle">أدخل بريدك الإلكتروني وسنرسل لك رمز إعادة التعيين</p>
    <?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
    <form method="POST" action="process.php">
      <input type="hidden" name="action" value="forgot"><?= csrf_field() ?>
      <div class="form-group"><label for="email">البريد الإلكتروني</label><input type="email" id="email" name="email" placeholder="example@email.com" dir="ltr" required></div>
      <button type="submit" class="btn-auth">إرسال رمز التحقق</button>
    </form>
    <p class="auth-link"><a href="login.php">← العودة لتسجيل الدخول</a></p>
  </div>
</body>
</html>
