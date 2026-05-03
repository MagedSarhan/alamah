<?php
/** Admin — Hero Slides (v2) */
$adminPageTitle = 'إدارة السلايدر';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Setting.php';
$settingModel = new Setting();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['slide_image']['name'])) {
    $uploaded = upload_image($_FILES['slide_image'], __DIR__ . '/../uploads/slides/');
    if ($uploaded) {
        $settingModel->createSlide(['image' => 'uploads/slides/' . $uploaded, 'alt_text' => trim($_POST['alt_text'] ?? ''), 'sort_order' => (int)($_POST['sort_order'] ?? 0), 'is_active' => isset($_POST['is_active']) ? 1 : 0]);
        set_flash('success', 'تم إضافة الشريحة');
    }
    header('Location: hero_slides.php'); exit;
}
if (isset($_GET['delete'])) { $settingModel->deleteSlide((int)$_GET['delete']); set_flash('success', 'تم الحذف'); header('Location: hero_slides.php'); exit; }
if (isset($_GET['toggle'])) { $settingModel->toggleSlide((int)$_GET['toggle']); header('Location: hero_slides.php'); exit; }

$slides = $settingModel->getHeroSlides(false);
require_once __DIR__ . '/includes/header.php';
?>
<div class="admin-topbar"><div><h1><i class="fa-solid fa-images" style="margin-left:0.4rem;color:var(--admin-gold);"></i> إدارة السلايدر</h1></div></div>
<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:1.5rem;">
  <div class="admin-card">
    <h2 style="font-size:1rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;">رفع شريحة جديدة</h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form">
      <label>الصورة *</label><input type="file" name="slide_image" accept="image/*" required>
      <label>النص البديل</label><input type="text" name="alt_text" placeholder="وصف الصورة">
      <label>الترتيب</label><input type="number" name="sort_order" value="0">
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;margin-bottom:1rem;"><input type="checkbox" name="is_active" checked> فعالة</label>
      <button type="submit" class="btn-admin btn-admin--primary"><i class="fa-solid fa-cloud-arrow-up" style="margin-left:0.3rem;"></i> رفع</button>
    </form>
  </div>
  <div class="admin-card">
    <h2 style="font-size:1rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;">الشرائح الحالية</h2>
    <?php if (empty($slides)): ?><div class="empty-state"><p>لا توجد شرائح</p></div>
    <?php else: foreach ($slides as $s): ?>
    <div style="display:flex;align-items:center;gap:1rem;padding:0.75rem 0;border-bottom:1px solid #f0f0f0;">
      <img src="../<?= htmlspecialchars($s['image']) ?>" style="width:100px;height:50px;object-fit:cover;border-radius:8px;" alt="">
      <div style="flex:1;"><p style="font-size:0.85rem;font-weight:600;"><?= $s['alt_text'] ?: 'بدون وصف' ?></p><p style="font-size:0.75rem;color:var(--admin-gray);">ترتيب: <?= $s['sort_order'] ?></p></div>
      <a href="hero_slides.php?toggle=<?= $s['id'] ?>" class="btn-admin btn-admin--outline btn-admin--sm"><?= $s['is_active'] ? 'تعطيل' : 'تفعيل' ?></a>
      <a href="hero_slides.php?delete=<?= $s['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف؟')"><i class="fa-solid fa-trash" style="font-size:12px;"></i></a>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
