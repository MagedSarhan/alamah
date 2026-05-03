<?php
require_once __DIR__ . '/../config/session.php';
if (!isset($_SESSION['pending_verify_user_id'])) { header('Location: login.php'); exit; }
$flash = get_flash();
$email = $_SESSION['pending_verify_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تأكيد البريد | علامة ALAMAH</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../image/logo.png">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,#F7F1E8,#F0E6D3,#E8C9B8);min-height:100vh;display:flex;align-items:center;justify-content:center;direction:rtl}
    .auth-card{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,0.1);max-width:440px;width:90%;padding:2.5rem;position:relative;overflow:hidden;text-align:center}
    .auth-card::before{content:'';position:absolute;top:0;right:0;left:0;height:4px;background:linear-gradient(90deg,#1B2A5B,#C9A96E,#D63B2F)}
    .auth-logo{margin-bottom:1.5rem}
    .auth-logo img{height:60px}
    .verify-icon{width:80px;height:80px;background:linear-gradient(135deg,#C9A96E,#D4BC8E);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem}
    .auth-title{font-weight:800;color:#1B2A5B;font-size:1.4rem;margin-bottom:0.5rem}
    .auth-subtitle{color:#8A8580;font-size:0.9rem;margin-bottom:2rem;line-height:1.7}
    .code-inputs{display:flex;gap:8px;justify-content:center;direction:ltr;margin-bottom:1.5rem}
    .code-inputs input{width:48px;height:56px;text-align:center;font-size:1.5rem;font-weight:700;border:2px solid #E5D6BF;border-radius:12px;outline:none;font-family:'Outfit',sans-serif;transition:border-color 0.3s;background:#FAFAFA}
    .code-inputs input:focus{border-color:#C9A96E;background:#fff}
    .btn-auth{width:100%;padding:0.9rem;background:linear-gradient(135deg,#1B2A5B,#2A3F7E);color:#fff;border:none;border-radius:12px;font-family:'Tajawal',sans-serif;font-weight:700;font-size:1rem;cursor:pointer;transition:all 0.3s}
    .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(27,42,91,0.3)}
    .resend-link{margin-top:1.5rem;font-size:0.85rem;color:#8A8580}
    .resend-link a{color:#D63B2F;font-weight:600;text-decoration:none}
    .alert{padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.85rem;font-weight:500;text-align:right}
    .alert-error{background:#FEE;color:#D63B2F;border:1px solid #FCC}
    .alert-success{background:#EFE;color:#2A7E2A;border:1px solid #CEC}
    .alert-info{background:#EEF;color:#1B2A5B;border:1px solid #CCE}
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="auth-logo"><a href="../index.php"><img src="../image/logo.png" alt="علامة"></a></div>
    <div class="verify-icon">📧</div>
    <h1 class="auth-title">تأكيد البريد الإلكتروني</h1>
    <p class="auth-subtitle">أرسلنا رمز تحقق مكون من 6 أرقام إلى<br><strong dir="ltr"><?= htmlspecialchars($email) ?></strong></p>

    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="process.php" id="verifyForm">
      <input type="hidden" name="action" value="verify">
      <input type="hidden" name="code" id="fullCode">
      <?= csrf_field() ?>
      <div class="code-inputs">
        <input type="text" maxlength="1" class="code-input" autofocus>
        <input type="text" maxlength="1" class="code-input">
        <input type="text" maxlength="1" class="code-input">
        <input type="text" maxlength="1" class="code-input">
        <input type="text" maxlength="1" class="code-input">
        <input type="text" maxlength="1" class="code-input">
      </div>
      <button type="submit" class="btn-auth">تأكيد</button>
    </form>

    <p class="resend-link">لم يصلك الرمز؟
      <form method="POST" action="process.php" style="display:inline">
        <input type="hidden" name="action" value="resend">
        <?= csrf_field() ?>
        <button type="submit" style="background:none;border:none;color:#D63B2F;font-weight:600;cursor:pointer;font-family:inherit;font-size:inherit;">إعادة الإرسال</button>
      </form>
    </p>
  </div>

  <script>
    const inputs = document.querySelectorAll('.code-input');
    inputs.forEach((inp, i) => {
      inp.addEventListener('input', () => { if (inp.value && i < inputs.length - 1) inputs[i + 1].focus(); });
      inp.addEventListener('keydown', (e) => { if (e.key === 'Backspace' && !inp.value && i > 0) inputs[i - 1].focus(); });
      inp.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text').trim();
        [...paste].forEach((c, j) => { if (inputs[j]) inputs[j].value = c; });
        if (inputs[paste.length - 1]) inputs[paste.length - 1].focus();
      });
    });
    document.getElementById('verifyForm').addEventListener('submit', (e) => {
      document.getElementById('fullCode').value = [...inputs].map(i => i.value).join('');
    });
  </script>
</body>
</html>
