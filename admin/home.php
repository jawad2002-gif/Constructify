<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_hero') {
        $id = (int)$_POST['id'];
        $current = $pdo->query("SELECT background_image FROM hero_section WHERE id = $id")->fetchColumn();
        $image = upload_image('background_image', $current);

        $stmt = $pdo->prepare("UPDATE hero_section SET badge_text=?, title_main=?, title_highlight=?, description=?, button1_text=?, button2_text=?, background_image=? WHERE id=?");
        $stmt->execute([
            trim($_POST['badge_text']), trim($_POST['title_main']), trim($_POST['title_highlight']),
            trim($_POST['description']), trim($_POST['button1_text']), trim($_POST['button2_text']),
            $image, $id
        ]);
        flash('Hero section saved.');
    }

    if ($action === 'save_counter') {
        $stmt = $pdo->prepare("UPDATE hero_counters SET number_value=?, suffix=?, label=?, sort_order=? WHERE id=?");
        $stmt->execute([(int)$_POST['number_value'], trim($_POST['suffix']), trim($_POST['label']), (int)$_POST['sort_order'], (int)$_POST['id']]);
        flash('Counter updated.');
    }

    if ($action === 'add_counter') {
        $stmt = $pdo->prepare("INSERT INTO hero_counters (number_value, suffix, label, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([(int)$_POST['number_value'], trim($_POST['suffix']), trim($_POST['label']), (int)$_POST['sort_order']]);
        flash('Counter added.');
    }

    if ($action === 'delete_counter') {
        $pdo->prepare("DELETE FROM hero_counters WHERE id=?")->execute([(int)$_POST['id']]);
        flash('Counter deleted.');
    }

    header('Location: ' . admin_base() . 'home.php');
    exit;
}

$hero = $pdo->query("SELECT * FROM hero_section LIMIT 1")->fetch();
$counters = $pdo->query("SELECT * FROM hero_counters ORDER BY sort_order")->fetchAll();

$page_title = 'Home Section';
include 'includes/admin_header.php';
?>

<div class="admin-card mb-4">
  <h5 class="mb-3">Hero content</h5>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_hero">
    <input type="hidden" name="id" value="<?= (int)$hero['id'] ?>">

    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Small heading (badge)</label>
        <input type="text" name="badge_text" class="form-control" value="<?= e($hero['badge_text']) ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Main title</label>
        <input type="text" name="title_main" class="form-control" value="<?= e($hero['title_main']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Highlighted word (orange)</label>
        <input type="text" name="title_highlight" class="form-control" value="<?= e($hero['title_highlight']) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control"><?= e($hero['description']) ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Button 1 text</label>
        <input type="text" name="button1_text" class="form-control" value="<?= e($hero['button1_text']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Button 2 text</label>
        <input type="text" name="button2_text" class="form-control" value="<?= e($hero['button2_text']) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Background image</label>
        <div class="d-flex align-items-center gap-3">
          <img src="<?= site_base() . e($hero['background_image']) ?>" alt="" style="width:120px;height:70px;object-fit:cover;border-radius:6px;background:#ccd3d9;">
          <input type="file" name="background_image" class="form-control" accept="image/*">
        </div>
        <div class="form-text">Leave empty to keep the current image.</div>
      </div>
    </div>
    <button class="btn btn-orange mt-4 px-4">Save hero</button>
  </form>
</div>

<div class="admin-card">
  <h5 class="mb-3">Counters</h5>

  <?php foreach ($counters as $c): ?>
    <form method="post" class="row g-2 align-items-end mb-3">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
      <div class="col-md-2"><label class="form-label small">Number</label><input type="number" name="number_value" class="form-control" value="<?= (int)$c['number_value'] ?>"></div>
      <div class="col-md-1"><label class="form-label small">Suffix</label><input type="text" name="suffix" class="form-control" value="<?= e($c['suffix']) ?>"></div>
      <div class="col-md-4"><label class="form-label small">Label</label><input type="text" name="label" class="form-control" value="<?= e($c['label']) ?>"></div>
      <div class="col-md-1"><label class="form-label small">Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$c['sort_order'] ?>"></div>
      <div class="col-md-4 d-flex gap-2">
        <button name="action" value="save_counter" class="btn btn-orange">Save</button>
        <button name="action" value="delete_counter" class="btn btn-outline-danger" onclick="return confirm('Delete this counter?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>

  <hr>
  <h6>Add a new counter</h6>
  <form method="post" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_counter">
    <div class="col-md-2"><input type="number" name="number_value" class="form-control" placeholder="Number" required></div>
    <div class="col-md-1"><input type="text" name="suffix" class="form-control" value="+"></div>
    <div class="col-md-4"><input type="text" name="label" class="form-control" placeholder="Label" required></div>
    <div class="col-md-1"><input type="number" name="sort_order" class="form-control" value="<?= count($counters) + 1 ?>"></div>
    <div class="col-md-4"><button class="btn btn-dark">Add counter</button></div>
  </form>
</div>

<?php include 'includes/admin_footer.php'; ?>