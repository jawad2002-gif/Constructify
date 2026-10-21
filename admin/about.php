
<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_about') {
        $id = (int)$_POST['id'];
        $cur = $pdo->query("SELECT image_main, image_secondary FROM about_section WHERE id = $id")->fetch();
        $img1 = upload_image('image_main', $cur['image_main']);
        $img2 = upload_image('image_secondary', $cur['image_secondary']);

        $stmt = $pdo->prepare("UPDATE about_section SET small_heading=?, title=?, description=?, badge_number=?, badge_label=?, button1_text=?, button2_text=?, image_main=?, image_secondary=? WHERE id=?");
        $stmt->execute([
            trim($_POST['small_heading']), trim($_POST['title']), trim($_POST['description']),
            trim($_POST['badge_number']), trim($_POST['badge_label']),
            trim($_POST['button1_text']), trim($_POST['button2_text']),
            $img1, $img2, $id
        ]);
        flash('About section saved.');
    }

    if ($action === 'save_feature') {
        $stmt = $pdo->prepare("UPDATE about_features SET icon=?, title=?, description=?, sort_order=? WHERE id=?");
        $stmt->execute([trim($_POST['icon']), trim($_POST['title']), trim($_POST['description']), (int)$_POST['sort_order'], (int)$_POST['id']]);
        flash('Feature updated.');
    }

    if ($action === 'add_feature') {
        $stmt = $pdo->prepare("INSERT INTO about_features (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([trim($_POST['icon']), trim($_POST['title']), trim($_POST['description']), (int)$_POST['sort_order']]);
        flash('Feature added.');
    }

    if ($action === 'delete_feature') {
        $pdo->prepare("DELETE FROM about_features WHERE id=?")->execute([(int)$_POST['id']]);
        flash('Feature deleted.');
    }

    header('Location: ' . admin_base() . 'about.php');
    exit;
}

$about = $pdo->query("SELECT * FROM about_section LIMIT 1")->fetch();
$features = $pdo->query("SELECT * FROM about_features ORDER BY sort_order")->fetchAll();

$page_title = 'About Section';
include 'includes/admin_header.php';
?>

<div class="admin-card mb-4">
  <h5 class="mb-3">About content</h5>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_about">
    <input type="hidden" name="id" value="<?= (int)$about['id'] ?>">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Small heading</label>
        <input type="text" name="small_heading" class="form-control" value="<?= e($about['small_heading']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?= e($about['title']) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control"><?= e($about['description']) ?></textarea>
      </div>
      <div class="col-md-3">
        <label class="form-label">Badge number (circle)</label>
        <input type="text" name="badge_number" class="form-control" value="<?= e($about['badge_number']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Badge label</label>
        <input type="text" name="badge_label" class="form-control" value="<?= e($about['badge_label']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Button 1 text</label>
        <input type="text" name="button1_text" class="form-control" value="<?= e($about['button1_text']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Button 2 text</label>
        <input type="text" name="button2_text" class="form-control" value="<?= e($about['button2_text']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Main image</label>
        <div class="d-flex align-items-center gap-3">
          <img src="<?= site_base() . e($about['image_main']) ?>" alt="" style="width:90px;height:70px;object-fit:cover;border-radius:6px;background:#ccd3d9;">
          <input type="file" name="image_main" class="form-control" accept="image/*">
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Second image</label>
        <div class="d-flex align-items-center gap-3">
          <img src="<?= site_base() . e($about['image_secondary']) ?>" alt="" style="width:90px;height:70px;object-fit:cover;border-radius:6px;background:#ccd3d9;">
          <input type="file" name="image_secondary" class="form-control" accept="image/*">
        </div>
      </div>
    </div>
    <div class="form-text mt-2">Leave an image field empty to keep the current image.</div>
    <button class="btn btn-orange mt-3 px-4">Save about</button>
  </form>
</div>

<div class="admin-card">
  <h5 class="mb-1">Features</h5>
  <p class="text-muted small mb-3">
    The icon is a Bootstrap Icons name, for example <code>bi-shield-check</code>.
    Browse the names at <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>.
  </p>

  <?php foreach ($features as $f): ?>
    <form method="post" class="row g-2 align-items-end mb-3 pb-3 border-bottom">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
      <div class="col-md-2"><label class="form-label small">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($f['icon']) ?>"></div>
      <div class="col-md-3"><label class="form-label small">Title</label><input type="text" name="title" class="form-control" value="<?= e($f['title']) ?>"></div>
      <div class="col-md-4"><label class="form-label small">Description</label><input type="text" name="description" class="form-control" value="<?= e($f['description']) ?>"></div>
      <div class="col-md-1"><label class="form-label small">Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$f['sort_order'] ?>"></div>
      <div class="col-md-2 d-flex gap-2">
        <button name="action" value="save_feature" class="btn btn-orange">Save</button>
        <button name="action" value="delete_feature" class="btn btn-outline-danger" onclick="return confirm('Delete this feature?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>

  <h6>Add a new feature</h6>
  <form method="post" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_feature">
    <div class="col-md-2"><input type="text" name="icon" class="form-control" placeholder="bi-star" required></div>
    <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Title" required></div>
    <div class="col-md-4"><input type="text" name="description" class="form-control" placeholder="Description"></div>
    <div class="col-md-1"><input type="number" name="sort_order" class="form-control" value="<?= count($features) + 1 ?>"></div>
    <div class="col-md-2"><button class="btn btn-dark">Add</button></div>
  </form>
</div>

<?php include 'includes/admin_footer.php'; ?>