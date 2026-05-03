<?php
/**
 * Auth Process — handles login, register, verify, forgot, reset
 */
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Mailer.php';

$userModel = new User();
$action = $_POST['action'] ?? '';

if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
    set_flash('error', 'خطأ في الحماية، حاول مرة أخرى');
    header('Location: login.php');
    exit;
}

switch ($action) {

    // ── REGISTER ──
    case 'register':
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!$name || !$email || !$password) {
            set_flash('error', 'يرجى تعبئة جميع الحقول');
            header('Location: register.php'); exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'البريد الإلكتروني غير صحيح');
            header('Location: register.php'); exit;
        }
        if (strlen($password) < 6) {
            set_flash('error', 'كلمة المرور يجب أن تكون 6 أحرف على الأقل');
            header('Location: register.php'); exit;
        }
        if ($password !== $confirm) {
            set_flash('error', 'كلمة المرور وتأكيدها غير متطابقتين');
            header('Location: register.php'); exit;
        }
        if ($userModel->findByEmail($email)) {
            set_flash('error', 'هذا البريد الإلكتروني مسجل مسبقاً');
            header('Location: register.php'); exit;
        }

        $userId = $userModel->create($name, $email, $password);
        $code = $userModel->createVerificationCode($userId);

        // Send verification email
        Mailer::sendVerificationCode($email, $name, $code);

        $_SESSION['pending_verify_user_id'] = $userId;
        $_SESSION['pending_verify_email'] = $email;
        set_flash('success', 'تم إنشاء الحساب! تحقق من بريدك الإلكتروني');
        header('Location: verify.php');
        exit;

    // ── VERIFY EMAIL ──
    case 'verify':
        $code = trim($_POST['code'] ?? '');
        $userId = $_SESSION['pending_verify_user_id'] ?? null;

        if (!$userId || !$code) {
            set_flash('error', 'بيانات غير صحيحة');
            header('Location: verify.php'); exit;
        }

        if ($userModel->verifyCode($userId, $code, 'email_verify')) {
            $userModel->verifyEmail($userId);
            $user = $userModel->findById($userId);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? null;
            $_SESSION['is_admin'] = (bool) $user['is_admin'];
            unset($_SESSION['pending_verify_user_id'], $_SESSION['pending_verify_email']);
            set_flash('success', 'تم تفعيل حسابك بنجاح!');
            header('Location: ../index.php'); exit;
        } else {
            set_flash('error', 'الرمز غير صحيح أو منتهي الصلاحية');
            header('Location: verify.php'); exit;
        }

    // ── RESEND CODE ──
    case 'resend':
        $userId = $_SESSION['pending_verify_user_id'] ?? null;
        $email = $_SESSION['pending_verify_email'] ?? null;
        if ($userId && $email) {
            $user = $userModel->findById($userId);
            $code = $userModel->createVerificationCode($userId);
            Mailer::sendVerificationCode($email, $user['name'], $code);
            set_flash('success', 'تم إعادة إرسال رمز التحقق');
        }
        header('Location: verify.php'); exit;

    // ── LOGIN ──
    case 'login':
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            set_flash('error', 'يرجى إدخال البريد وكلمة المرور');
            header('Location: login.php'); exit;
        }

        $user = $userModel->findByEmail($email);
        if (!$user || !$userModel->verifyPassword($password, $user['password_hash'])) {
            set_flash('error', 'البريد أو كلمة المرور غير صحيحة');
            header('Location: login.php'); exit;
        }
        if ($user['status'] === 'banned') {
            set_flash('error', 'تم حظر حسابك. تواصل مع الإدارة');
            header('Location: login.php'); exit;
        }
        if (!$user['is_verified']) {
            $_SESSION['pending_verify_user_id'] = $user['id'];
            $_SESSION['pending_verify_email'] = $user['email'];
            $code = $userModel->createVerificationCode($user['id']);
            Mailer::sendVerificationCode($user['email'], $user['name'], $code);
            set_flash('info', 'حسابك غير مفعل. تحقق من بريدك');
            header('Location: verify.php'); exit;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? null;
        $_SESSION['is_admin'] = (bool) $user['is_admin'];
        set_flash('success', 'أهلاً بك ' . $user['name']);

        // Redirect admin to dashboard
        if ($user['is_admin']) {
            header('Location: ../admin/index.php');
        } else {
            header('Location: ../index.php');
        }
        exit;

    // ── FORGOT PASSWORD ──
    case 'forgot':
        $email = trim($_POST['email'] ?? '');
        $user = $userModel->findByEmail($email);
        if ($user) {
            $code = $userModel->createVerificationCode($user['id'], 'password_reset');
            Mailer::sendPasswordResetCode($email, $user['name'], $code);
            $_SESSION['reset_user_id'] = $user['id'];
        }
        set_flash('success', 'إذا كان البريد مسجلاً، ستتلقى رمز إعادة التعيين');
        header('Location: reset_password.php');
        exit;

    // ── RESET PASSWORD ──
    case 'reset':
        $code = trim($_POST['code'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        $userId = $_SESSION['reset_user_id'] ?? null;

        if (!$userId || !$code || !$password) {
            set_flash('error', 'بيانات ناقصة');
            header('Location: reset_password.php'); exit;
        }
        if (strlen($password) < 6) {
            set_flash('error', 'كلمة المرور يجب أن تكون 6 أحرف على الأقل');
            header('Location: reset_password.php'); exit;
        }
        if ($password !== $confirm) {
            set_flash('error', 'كلمة المرور وتأكيدها غير متطابقتين');
            header('Location: reset_password.php'); exit;
        }
        if ($userModel->verifyCode($userId, $code, 'password_reset')) {
            $userModel->updatePassword($userId, $password);
            unset($_SESSION['reset_user_id']);
            set_flash('success', 'تم تغيير كلمة المرور بنجاح');
            header('Location: login.php'); exit;
        } else {
            set_flash('error', 'الرمز غير صحيح أو منتهي الصلاحية');
            header('Location: reset_password.php'); exit;
        }

    default:
        header('Location: login.php');
        exit;
}
