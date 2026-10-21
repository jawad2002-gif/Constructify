
<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_section') {
        $id = (int)$_POST['id'];
        $cur = $pdo->query("SELECT side_image FROM services_section WHERE id = $id")->fetchColumn();
        $img = upload_image('side_image', $cur);

        $stmt = $pdo->prepare("UPDATE services_section SET title=?, subtitle=?, side_card_title=?, side_card_link_text=?, track_small_heading=?, track_title=?, side_image=? WHERE id=?");
        $stmt->execute([
            trim($_POST['title']), trim($_POST['subtitle']), trim($_POST['side_card_title']),
            trim($_POST['side_card_link_text']), trim($_POST['track_small_heading']), trim($_POST['track_title']),
            $img, $id
        ]);
        flash('Services section saved.');
    }

    if ($action === 'save_service') {
        $stmt = $pdo->prepare("UPDATE services SET icon=?, title=?, description=?, sort_order=? WHERE id=?");
        $stmt->execute([trim($_POST['icon']), trim($_POST['title']), trim($_POST['description']), (int)$_POST['sort_order'], (int)$_POST['id']]);
        flash('Service updated.');
    }
    if ($action === 'add_service') {
        $stmt = $pdo->prepare("INSERT INTO services (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([trim($_POST['icon']), trim($_POST['title']), trim($_POST['description']), (int)$_POST['sort_order']]);
        flash('Service added.');
    }
    if ($action === 'delete_service') {
        $pdo->prepare("DELETE FROM services WHERE id=?")->execute([(int)$_POST['id']]);
        flash('Service deleted.');
    }

    if ($action === 'save_stat') {
        $stmt = $pdo->prepare("UPDATE track_record_stats SET icon=?, number_value=?, suffix=?, label=?, sort_order=? WHERE id=?");
        $stmt->execute([trim($_POST['icon']), (int)$_POST['number_value'], trim($_POST['suffix']), trim($_POST['label']), (int)$_POST['sort_order'], (int)$_POST['id']]);
        flash('Stat updated.');
    }
    if ($action === 'add_stat') {
        $stmt = $pdo->prepare("INSERT INTO track_record_stats (icon, number_value, suffix, label, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([trim($_POST['icon']), (int)$_POST['number_value'], trim($_POST['suffix']), trim($_POST['label']), (int)$_POST['sort_order']]);
        flash('Stat added.');
    }
    if ($action === 'delete_stat') {
        $pdo->prepare("DELETE FROM track_record_stats WHERE id=?")->execute([(int)$_POST['id']]);
        flash('Stat deleted.');
    }

    header('Location: ' . admin_base() . 'services.php');
    exit;
}

$sec = $pdo->query("SELECT * FROM services_section LIMIT 1")->fetch();
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();
$stats = $pdo->query("SELECT * FROM track_record_stats ORDER BY sort_order")->fetchAll();

$page_title = 'Services Section';
include 'includes/admin_header.php';
?>

<div class="admin-card mb-4">
  <h5 class="mb-3">Section content</h5>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_section">
    <input type="hidden" name="id" value="<?= (int)$sec['id'] ?>">

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Section title</label>
        <input type="text" name="title" class="form-control" value="<?= e($sec['title']) ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Subtitle</label>
        <input type="text" name="subtitle" class="form-control" value="<?= e($sec['subtitle']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Side card title</label>
        <input type="text" name="side_card_title" class="form-control" value="<?= e($sec['side_card_title']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Side card link text</label>
        <input type="text" name="side_card_link_text" class="form-control" value="<?= e($sec['side_card_link_text']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Track record small heading</label>
        <input type="text" name="track_small_heading" class="form-control" value="<?= e($sec['track_small_heading']) ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Track record title</label>
        <input type="text" name="track_title" class="form-control" value="<?= e($sec['track_title']) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Side card image</label>
        <div class="d-flex align-items-center gap-3">
          <img src="<?= site_base() . e($sec['side_image']) ?>" alt="" style="width:90px;height:70px;object-fit:cover;border-radius:6px;background:#ccd3d9;">
          <input type="file" name="side_image" class="form-control" accept="image/*">
        </div>
        <div class="form-text">Leave empty to keep the current image.</div>
      </div>
    </div>
    <button class="btn btn-orange mt-3 px-4">Save section</button>
  </form>
</div>

<div class="admin-card mb-4">
  <h5 class="mb-1">Services</h5>
  <p class="text-muted small mb-3">
    Icons are Bootstrap Icons names, e.g. <code>bi-hammer</code>. Browse them at
    <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>.
  </p>

  <?php foreach ($services as $s): ?>
    <form method="post" class="row g-2 align-items-end mb-3 pb-3 border-bottom">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
      <div class="col-md-2"><label class="form-label small">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($s['icon']) ?>"></div>
      <div class="col-md-3"><label class="form-label small">Title</label><input type="text" name="title" class="form-control" value="<?= e($s['title']) ?>"></div>
      <div class="col-md-4"><label class="form-label small">Description</label><input type="text" name="description" class="form-control" value="<?= e($s['description']) ?>"></div>
      <div class="col-md-1"><label class="form-label small">Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$s['sort_order'] ?>"></div>
      <div class="col-md-2 d-flex gap-2">
        <button name="action" value="save_service" class="btn btn-orange">Save</button>
        <button name="action" value="delete_service" class="btn btn-outline-danger" onclick="return confirm('Delete this service?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>

  <h6>Add a new service</h6>
  <form method="post" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_service">
    <div class="col-md-2"><input type="text" name="icon" class="form-control" placeholder="bi-gear" required></div>
    <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Title" required></div>
    <div class="col-md-4"><input type="text" name="description" class="form-control" placeholder="Description"></div>
    <div class="col-md-1"><input type="number" name="sort_order" class="form-control" value="<?= count($services) + 1 ?>"></div>
    <div class="col-md-2"><button class="btn btn-dark">Add</button></div>
  </form>
</div>

<div class="admin-card">
  <h5 class="mb-3">Track record numbers</h5>

  <?php foreach ($stats as $st): ?>
    <form method="post" class="row g-2 align-items-end mb-3 pb-3 border-bottom">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$st['id'] ?>">
      <div class="col-md-2"><label class="form-label small">Icon</label><input type="text" name="icon" class="form-control" value="<?= e($st['icon']) ?>"></div>
      <div class="col-md-2"><label class="form-label small">Number</label><input type="number" name="number_value" class="form-control" value="<?= (int)$st['number_value'] ?>"></div>
      <div class="col-md-1"><label class="form-label small">Suffix</label><input type="text" name="suffix" class="form-control" value="<?= e($st['suffix']) ?>"></div>
      <div class="col-md-3"><label class="form-label small">Label</label><input type="text" name="label" class="form-control" value="<?= e($st['label']) ?>"></div>
      <div class="col-md-1"><label class="form-label small">Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$st['sort_order'] ?>"></div>
      <div class="col-md-3 d-flex gap-2">
        <button name="action" value="save_stat" class="btn btn-orange">Save</button>
        <button name="action" value="delete_stat" class="btn btn-outline-danger" onclick="return confirm('Delete this stat?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>

  <h6>Add a new stat</h6>
  <form method="post" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_stat">
    <div class="col-md-2"><input type="text" name="icon" class="form-control" placeholder="bi-trophy" required></div>
    <div class="col-md-2"><input type="number" name="number_value" class="form-control" placeholder="Number" required></div>
    <div class="col-md-1"><input type="text" name="suffix" class="form-control" value="+"></div>
    <div class="col-md-3"><input type="text" name="label" class="form-control" placeholder="Label" required></div>
    <div class="col-md-1"><input type="number" name="sort_order" class="form-control" value="<?= count($stats) + 1 ?>"></div>
    <div class="col-md-3"><button class="btn btn-dark">Add</button></div>
  </form>
</div>

<?php include 'includes/admin_footer.php'; ?>