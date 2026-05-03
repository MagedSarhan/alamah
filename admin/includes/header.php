<?php
/** Admin Header */
if (!function_exists('is_logged_in')) {
    require_once __DIR__ . '/../../includes/auth_middleware.php';
}
$adminPageTitle = $adminPageTitle ?? 'لوحة التحكم';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= clean($adminPageTitle) ?> | علامة ALAMAH</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" type="image/png" href="../image/logo.png">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<?php require_once __DIR__ . '/sidebar.php'; ?>
<main class="admin-main">
<?php if ($flash): ?>
<div class="admin-alert admin-alert--<?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'info' ? 'info' : 'success') ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
