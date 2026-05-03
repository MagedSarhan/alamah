<?php
/** Admin — Categories Management (v2) with Edit + Image Upload */
$adminPageTitle = 'إدارة الفئات';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Category.php';
$catModel = new Category();

$editCat = null;
if (isset($_GET['edit'])) {
    $editCat = $catModel->findById((int)$_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $imgPath = $_POST['current_image'] ?? null;

    if (!empty($_FILES['cat_image']['name'])) {
        $uploaded = upload_image($_FILES['cat_image'], __DIR__ . '/../uploads/products/');
        if ($uploaded) $imgPath = 'uploads/products/' . $uploaded;
    }

    $data = [
        'key_name'    => trim($_POST['key_name'] ?? ''),
        'label'       => trim($_POST['label'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image'       => $imgPath,
        'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0
    ];

    if ($action === 'create') {
        $catModel->create($data);
        set_flash('success', 'تم إضافة الفئة بنجاح');
    } elseif ($action === 'update' && !empty($_POST['id'])) {
        $catModel->update((int)$_POST['id'], $data);
        set_flash('success', 'تم تحديث الفئة بنجاح');
    }
    header('Location: categories.php'); exit;
}

if (isset($_GET['delete'])) {
    $catModel->delete((int)$_GET['delete']);
    set_flash('success', 'تم حذف الفئة');
    header('Location: categories.php'); exit;
}

$categories = $catModel->getAll(false);
require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar"><div><h1><i class="fa-solid fa-layer-group" style="margin-left:0.4rem;color:var(--admin-gold);"></i> إدارة الفئات</h1></div></div>

<div style="display:grid;grid-template-columns:1fr 1.5fr;gap:1.5rem;">
  <!-- Add/Edit Form -->
  <div class="admin-card">
    <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;">
      <i class="fa-solid <?= $editCat ? 'fa-pen' : 'fa-plus-circle' ?>" style="margin-left:0.3rem;color:var(--admin-gold);"></i>
      <?= $editCat ? 'تعديل الفئة' : 'إضافة فئة جديدة' ?>
    </h2>
    <form method="POST" enctype="multipart/form-data" class="admin-form">
      <input type="hidden" name="action" value="<?= $editCat ? 'update' : 'create' ?>">
      <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><input type="hidden" name="current_image" value="<?= htmlspecialchars($editCat['image'] ?? '') ?>"><?php endif; ?>

      <label>المفتاح (بالإنجليزي) *</label>
      <input type="text" name="key_name" value="<?= clean($editCat['key_name'] ?? '') ?>" placeholder="مثال: giveaways" required <?= $editCat ? '' : '' ?>>

      <label>الاسم (بالعربي) *</label>
      <input type="text" name="label" value="<?= clean($editCat['label'] ?? '') ?>" placeholder="هدايا دعائية" required>

      <label>الوصف</label>
      <input type="text" name="description" value="<?= clean($editCat['description'] ?? '') ?>" placeholder="وصف مختصر للفئة">

      <label><i class="fa-solid fa-image" style="margin-left:0.3rem;color:var(--admin-gold);"></i> صورة الفئة</label>
      <input type="file" name="cat_image" accept="image/*">
      <?php if (!empty($editCat['image'])): ?>
      <div style="margin-top:0.3rem;margin-bottom:0.5rem;"><img src="../<?= htmlspecialchars($editCat['image']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:2px solid #eee;"></div>
      <?php endif; ?>

      <label>الترتيب</label>
      <input type="number" name="sort_order" value="<?= $editCat['sort_order'] ?? 0 ?>">

      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;margin-bottom:1rem;">
        <input type="checkbox" name="is_active" <?= ($editCat['is_active'] ?? 1) ? 'checked' : '' ?>> فعالة
      </label>

      <div style="display:flex;gap:0.5rem;">
        <button type="submit" class="btn-admin btn-admin--primary"><i class="fa-solid fa-floppy-disk" style="margin-left:0.3rem;"></i> <?= $editCat ? 'تحديث' : 'إضافة' ?></button>
        <?php if ($editCat): ?><a href="categories.php" class="btn-admin btn-admin--outline">إلغاء</a><?php endif; ?>
      </div>
    </form>
  </div>

  <!-- List -->
  <div class="admin-card">
    <h2 style="font-size:1.05rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-list" style="margin-left:0.3rem;color:var(--admin-gold);"></i> الفئات الحالية</h2>
    <?php if (empty($categories)): ?>
    <div class="empty-state"><i class="fa-solid fa-folder-open" style="font-size:36px;opacity:0.2;display:block;margin-bottom:0.5rem;"></i><p>لا توجد فئات</p></div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="admin-table">
        <thead><tr><th>صورة</th><th>المفتاح</th><th>الاسم</th><th>الترتيب</th><th>الحالة</th><th>إجراءات</th></tr></thead>
        <tbody>
          <?php foreach ($categories as $c): ?>
          <tr>
            <td><?php if ($c['image']): ?><img src="../<?= htmlspecialchars($c['image']) ?>" class="thumb"><?php else: ?><span style="color:#ccc;"><i class="fa-solid fa-image" style="font-size:20px;"></i></span><?php endif; ?></td>
            <td style="font-family:'Outfit',sans-serif;font-size:0.82rem;color:var(--admin-gray);"><?= clean($c['key_name']) ?></td>
            <td style="font-weight:600;"><?= clean($c['label']) ?></td>
            <td><?= $c['sort_order'] ?></td>
            <td><?= $c['is_active'] ? '<span class="badge bg-success">فعالة</span>' : '<span class="badge" style="background:#ccc;">معطلة</span>' ?></td>
            <td style="display:flex;gap:0.3rem;">
              <a href="categories.php?edit=<?= $c['id'] ?>" class="btn-admin--icon" title="تعديل"><i class="fa-solid fa-pen" style="font-size:12px;"></i></a>
              <a href="categories.php?delete=<?= $c['id'] ?>" class="btn-admin--icon" onclick="return confirm('حذف هذه الفئة وجميع منتجاتها؟')" title="حذف"><i class="fa-solid fa-trash" style="font-size:12px;"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
