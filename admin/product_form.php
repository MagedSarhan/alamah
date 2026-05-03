<?php
/** Admin — Product Add/Edit Form (v2) */
$adminPageTitle = 'إضافة/تعديل منتج';
require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

$productModel = new Product();
$categoryModel = new Category();
$categories = $categoryModel->getAll(false);

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $editId ? $productModel->findById($editId) : null;

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name'          => trim($_POST['name'] ?? ''),
        'description'   => trim($_POST['description'] ?? ''),
        'price'         => (float)($_POST['price'] ?? 0),
        'category_id'   => (int)($_POST['category_id'] ?? 0),
        'badge'         => trim($_POST['badge'] ?? ''),
        'badge_color'   => trim($_POST['badge_color'] ?? ''),
        'time'          => trim($_POST['time'] ?? ''),
        'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        'is_bestseller' => isset($_POST['is_bestseller']) ? 1 : 0,
        'sort_order'    => (int)($_POST['sort_order'] ?? 0),
        'image'         => $product['image'] ?? ''
    ];

    if (!empty($_FILES['image']['name'])) {
        $uploaded = upload_image($_FILES['image'], __DIR__ . '/../uploads/products/');
        if ($uploaded) $data['image'] = 'uploads/products/' . $uploaded;
    }

    $customFields = [];
    if (!empty($_POST['cf_label'])) {
        foreach ($_POST['cf_label'] as $i => $label) {
            if (trim($label)) {
                $customFields[] = [
                    'label'       => trim($label),
                    'type'        => $_POST['cf_type'][$i] ?? 'text',
                    'is_required' => isset($_POST['cf_required'][$i]) ? 1 : 0
                ];
            }
        }
    }

    if ($editId && $product) {
        $productModel->update($editId, $data);
        $productModel->saveCustomFields($editId, $customFields);
        set_flash('success', 'تم تحديث المنتج بنجاح');
    } else {
        $newId = $productModel->create($data);
        $productModel->saveCustomFields($newId, $customFields);
        set_flash('success', 'تم إضافة المنتج بنجاح');
    }
    header('Location: products.php'); exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="admin-topbar">
  <div><h1><i class="fa-solid <?= $editId ? 'fa-pen-to-square' : 'fa-plus-circle' ?>" style="margin-left:0.4rem;color:var(--admin-gold);"></i> <?= $editId ? 'تعديل المنتج' : 'إضافة منتج جديد' ?></h1></div>
  <a href="products.php" class="btn-admin btn-admin--outline"><i class="fa-solid fa-arrow-right" style="margin-left:0.3rem;"></i> العودة</a>
</div>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data" class="admin-form">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
      <div><label><i class="fa-solid fa-tag" style="margin-left:0.3rem;color:var(--admin-gold);"></i> اسم المنتج *</label><input type="text" name="name" value="<?= clean($product['name'] ?? '') ?>" required></div>
      <div><label><i class="fa-solid fa-layer-group" style="margin-left:0.3rem;color:var(--admin-gold);"></i> الفئة *</label>
        <select name="category_id" required><option value="">اختر الفئة</option>
        <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= clean($c['label']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div><label><i class="fa-solid fa-money-bill-wave" style="margin-left:0.3rem;color:var(--admin-gold);"></i> السعر (ر.س) *</label><input type="number" name="price" step="0.01" value="<?= $product['price'] ?? '' ?>" required></div>
      <div><label><i class="fa-solid fa-clock" style="margin-left:0.3rem;color:var(--admin-gold);"></i> وقت التنفيذ</label><input type="text" name="time" value="<?= clean($product['time'] ?? '') ?>" placeholder="مثال: ٢٤ ساعة"></div>
      <div><label><i class="fa-solid fa-certificate" style="margin-left:0.3rem;color:var(--admin-gold);"></i> نص الشارة (Badge)</label><input type="text" name="badge" value="<?= clean($product['badge'] ?? '') ?>" placeholder="مثل: الأكثر مبيعاً"></div>
      <div>
        <label><i class="fa-solid fa-palette" style="margin-left:0.3rem;color:var(--admin-gold);"></i> لون الشارة</label>
        <div class="badge-color-swatches" id="badgeSwatches">
          <div class="color-swatch active" data-color="#C9A96E" style="background:#C9A96E;" title="ذهبي"></div>
          <div class="color-swatch" data-color="#1B2A5B" style="background:#1B2A5B;" title="كحلي"></div>
          <div class="color-swatch" data-color="#D63B2F" style="background:#D63B2F;" title="أحمر"></div>
          <div class="color-swatch" data-color="#25D366" style="background:#25D366;" title="أخضر"></div>
          <div class="color-swatch" data-color="#E4405F" style="background:#E4405F;" title="وردي"></div>
          <div class="color-swatch" data-color="#0088CC" style="background:#0088CC;" title="أزرق"></div>
          <div class="color-swatch" data-color="#FF9900" style="background:#FF9900;" title="برتقالي"></div>
          <div class="color-swatch" data-color="#9146FF" style="background:#9146FF;" title="بنفسجي"></div>
          <div class="color-swatch" data-color="#000000" style="background:#000;" title="أسود"></div>
          <div class="color-swatch color-swatch--custom" data-color="custom" title="لون مخصص">
            <i class="fa-solid fa-plus" style="font-size:10px;color:#999;"></i>
            <input type="color" id="badgeColorPicker" value="<?= htmlspecialchars($product['badge_color'] ?? '#C9A96E') ?>" style="position:absolute;opacity:0;width:100%;height:100%;top:0;left:0;cursor:pointer;">
          </div>
        </div>
        <input type="text" name="badge_color" id="badgeColorText" value="<?= clean($product['badge_color'] ?? '') ?>" placeholder="#C9A96E" dir="ltr" style="margin-top:0.4rem;">
      </div>
      <div><label><i class="fa-solid fa-sort" style="margin-left:0.3rem;color:var(--admin-gold);"></i> الترتيب</label><input type="number" name="sort_order" value="<?= $product['sort_order'] ?? 0 ?>"></div>
      <div>
        <label><i class="fa-solid fa-image" style="margin-left:0.3rem;color:var(--admin-gold);"></i> صورة المنتج</label>
        <input type="file" name="image" accept="image/*">
        <?php if (!empty($product['image'])): ?>
        <div style="margin-top:0.4rem;"><img src="../<?= htmlspecialchars($product['image']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:2px solid #eee;"></div>
        <?php endif; ?>
      </div>
    </div>

    <label><i class="fa-solid fa-align-left" style="margin-left:0.3rem;color:var(--admin-gold);"></i> الوصف</label>
    <textarea name="description" rows="3"><?= clean($product['description'] ?? '') ?></textarea>

    <div style="display:flex;gap:2rem;margin:1rem 0;">
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;"><input type="checkbox" name="is_active" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>> <i class="fa-solid fa-eye" style="color:var(--admin-gold);"></i> فعال ومنشور</label>
      <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;"><input type="checkbox" name="is_bestseller" <?= ($product['is_bestseller'] ?? 0) ? 'checked' : '' ?>> <i class="fa-solid fa-star" style="color:var(--admin-gold);"></i> الأكثر مبيعاً</label>
    </div>

    <!-- Custom Fields -->
    <div class="admin-card" style="background:#FAFAFA;box-shadow:none;border:1px dashed var(--admin-border);">
      <h3 style="font-size:1rem;font-weight:700;color:var(--admin-navy);margin-bottom:1rem;"><i class="fa-solid fa-list-check" style="margin-left:0.3rem;color:var(--admin-gold);"></i> حقول مخصصة</h3>
      <div id="customFieldsContainer">
        <?php $fields = $product ? ($product['customFields'] ?? []) : [];
        foreach ($fields as $i => $f): ?>
        <div class="cf-row" style="display:grid;grid-template-columns:1fr 130px 80px 36px;gap:0.5rem;margin-bottom:0.5rem;align-items:center;">
          <input type="text" name="cf_label[]" value="<?= clean($f['label']) ?>" placeholder="عنوان الحقل">
          <select name="cf_type[]"><option value="text" <?= $f['type']==='text'?'selected':'' ?>>نص قصير</option><option value="textarea" <?= $f['type']==='textarea'?'selected':'' ?>>نص طويل</option></select>
          <label style="font-size:0.8rem;display:flex;align-items:center;gap:0.3rem;"><input type="checkbox" name="cf_required[<?= $i ?>]" <?= ($f['is_required'] ?? false) ? 'checked' : '' ?>> مطلوب</label>
          <button type="button" onclick="this.closest('.cf-row').remove()" class="btn-admin--icon" style="width:30px;height:30px;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <?php endforeach; ?>
      </div>
      <button type="button" onclick="addCustomField()" class="btn-admin btn-admin--outline btn-admin--sm" style="margin-top:0.5rem;"><i class="fa-solid fa-plus" style="margin-left:0.3rem;"></i> إضافة حقل</button>
    </div>

    <button type="submit" class="btn-admin btn-admin--primary" style="margin-top:1.5rem;padding:0.75rem 3rem;font-size:0.95rem;"><i class="fa-solid fa-floppy-disk" style="margin-left:0.3rem;"></i> <?= $editId ? 'تحديث المنتج' : 'حفظ المنتج' ?></button>
  </form>
</div>

<style>
.badge-color-swatches{display:flex;flex-wrap:wrap;gap:6px;margin-top:0.3rem;}
.color-swatch{width:32px;height:32px;border-radius:8px;cursor:pointer;border:2px solid transparent;transition:all 0.2s;position:relative;display:flex;align-items:center;justify-content:center;}
.color-swatch:hover{transform:scale(1.15);box-shadow:0 2px 8px rgba(0,0,0,0.2);}
.color-swatch.active{border-color:var(--admin-navy);box-shadow:0 0 0 3px rgba(27,42,91,0.2);transform:scale(1.1);}
.color-swatch.active::after{content:'✓';color:#fff;font-size:14px;font-weight:700;text-shadow:0 1px 2px rgba(0,0,0,0.3);}
.color-swatch--custom{background:#f5f5f5!important;border:2px dashed #ccc;}
.color-swatch--custom.active{border-style:solid;border-color:var(--admin-navy);}
.color-swatch--custom.active::after{display:none;}
</style>

<script>
// Color swatches
const swatches = document.getElementById('badgeSwatches');
const ct = document.getElementById('badgeColorText');
const cp = document.getElementById('badgeColorPicker');
const currentColor = ct.value || '';

// Set active swatch on load
if (currentColor) {
  const match = swatches.querySelector(`[data-color="${currentColor}"]`);
  swatches.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
  if (match) { match.classList.add('active'); }
  else {
    const custom = swatches.querySelector('[data-color="custom"]');
    custom.classList.add('active');
    custom.style.background = currentColor;
    custom.style.borderStyle = 'solid';
    cp.value = currentColor;
  }
}

swatches.addEventListener('click', e => {
  const swatch = e.target.closest('.color-swatch');
  if (!swatch || swatch.dataset.color === 'custom') return;
  swatches.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
  swatch.classList.add('active');
  ct.value = swatch.dataset.color;
  // Reset custom swatch appearance
  const custom = swatches.querySelector('[data-color="custom"]');
  custom.style.background = '#f5f5f5';
  custom.style.borderStyle = 'dashed';
});

cp.addEventListener('input', () => {
  ct.value = cp.value;
  swatches.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
  const custom = swatches.querySelector('[data-color="custom"]');
  custom.classList.add('active');
  custom.style.background = cp.value;
  custom.style.borderStyle = 'solid';
});

ct.addEventListener('input', () => {
  if (/^#[0-9A-Fa-f]{6}$/.test(ct.value)) {
    cp.value = ct.value;
    const match = swatches.querySelector(`[data-color="${ct.value}"]`);
    swatches.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
    if (match) { match.classList.add('active'); }
    else {
      const custom = swatches.querySelector('[data-color="custom"]');
      custom.classList.add('active');
      custom.style.background = ct.value;
    }
  }
});

let cfIndex = <?= count($fields) ?>;
function addCustomField() {
  const c = document.getElementById('customFieldsContainer');
  const row = document.createElement('div');
  row.className = 'cf-row';
  row.style = 'display:grid;grid-template-columns:1fr 130px 80px 36px;gap:0.5rem;margin-bottom:0.5rem;align-items:center;';
  row.innerHTML = '<input type="text" name="cf_label[]" placeholder="عنوان الحقل"><select name="cf_type[]"><option value="text">نص قصير</option><option value="textarea">نص طويل</option></select><label style="font-size:0.8rem;display:flex;align-items:center;gap:0.3rem;"><input type="checkbox" name="cf_required['+cfIndex+']"> مطلوب</label><button type="button" onclick="this.closest(\'.cf-row\').remove()" class="btn-admin--icon" style="width:30px;height:30px;"><i class="fa-solid fa-xmark"></i></button>';
  c.appendChild(row);
  cfIndex++;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
