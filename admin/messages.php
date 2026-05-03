<?php
/** Admin — Messages (v2) */
$adminPageTitle = 'الرسائل';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Contact.php';
$contactModel = new Contact();

if (isset($_GET['read'])) { $contactModel->markRead((int)$_GET['read']); header('Location: messages.php'); exit; }
if (isset($_GET['delete'])) { $contactModel->delete((int)$_GET['delete']); set_flash('success', 'تم حذف الرسالة'); header('Location: messages.php'); exit; }

$messages = $contactModel->getAll();
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar"><div><h1><i class="fa-solid fa-envelope" style="margin-left:0.4rem;color:var(--admin-gold);"></i> رسائل التواصل</h1><span class="breadcrumb-text"><?= count($messages) ?> رسالة</span></div></div>

<div class="admin-card">
  <?php if (empty($messages)): ?>
  <div class="empty-state"><i class="fa-solid fa-envelope-open" style="font-size:40px;opacity:0.15;display:block;margin-bottom:0.5rem;"></i><p>لا توجد رسائل</p></div>
  <?php else: ?>
  <?php foreach ($messages as $m): ?>
  <div style="padding:1.2rem;border-bottom:1px solid #f0f0f0;background:<?= $m['is_read'] ? '#fff' : '#FDFBF7' ?>;border-radius:8px;margin-bottom:0.5rem;<?= !$m['is_read'] ? 'border-right:3px solid var(--admin-gold);' : '' ?>">
    <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:0.5rem;">
      <div>
        <strong style="color:var(--admin-navy);"><?= clean($m['name']) ?></strong>
        <?php if (!$m['is_read']): ?><span class="badge bg-danger" style="margin-right:0.5rem;"><i class="fa-solid fa-circle" style="font-size:6px;margin-left:0.2rem;"></i> جديد</span><?php endif; ?>
        <p style="font-size:0.82rem;color:var(--admin-gray);margin:0.2rem 0;">
          <?php if ($m['phone']): ?><i class="fa-solid fa-phone" style="font-size:10px;margin-left:0.2rem;"></i> <span dir="ltr"><?= clean($m['phone']) ?></span> · <?php endif; ?>
          <?php if ($m['email']): ?><i class="fa-solid fa-envelope" style="font-size:10px;margin-left:0.2rem;"></i> <?= clean($m['email']) ?> · <?php endif; ?>
          <i class="fa-solid fa-clock" style="font-size:10px;margin-left:0.2rem;"></i> <?= time_ago($m['created_at']) ?>
        </p>
        <?php if ($m['subject']): ?><p style="font-size:0.85rem;font-weight:600;color:var(--admin-navy);margin:0.3rem 0;"><i class="fa-solid fa-tag" style="font-size:10px;margin-left:0.2rem;color:var(--admin-gold);"></i> <?= clean($m['subject']) ?></p><?php endif; ?>
        <p style="font-size:0.9rem;line-height:1.7;margin-top:0.3rem;"><?= nl2br(clean($m['message'])) ?></p>
      </div>
      <div style="display:flex;gap:0.3rem;">
        <?php if (!$m['is_read']): ?><a href="messages.php?read=<?= $m['id'] ?>" class="btn-admin btn-admin--outline btn-admin--sm"><i class="fa-solid fa-check" style="margin-left:0.2rem;"></i> كمقروء</a><?php endif; ?>
        <a href="messages.php?delete=<?= $m['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف هذه الرسالة؟')"><i class="fa-solid fa-trash" style="font-size:12px;"></i></a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
