<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

function make_slug($s) {
    $s = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($s)));
    $s = trim($s, '-');
    return $s !== '' ? $s : 'category';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    try {
        /* Section text */
        if ($action === 'save_section') {
            $stmt = $pdo->prepare("UPDATE projects_section SET title=?, subtitle=?, all_filter_label=? WHERE id=?");
            $stmt->execute([trim($_POST['title']), trim($_POST['subtitle']), trim($_POST['all_filter_label']), (int)$_POST['id']]);
            flash('Projects section saved.');
        }

        /* Categories */
        if ($action === 'save_category') {
            $slug = trim($_POST['slug']) !== '' ? make_slug($_POST['slug']) : make_slug($_POST['name']);
            $stmt = $pdo->prepare("UPDATE project_categories SET name=?, slug=?, sort_order=? WHERE id=?");
            $stmt->execute([trim($_POST['name']), $slug, (int)$_POST['sort_order'], (int)$_POST['id']]);
            flash('Category updated.');
        }
        if ($action === 'add_category') {
            $slug = trim($_POST['slug']) !== '' ? make_slug($_POST['slug']) : make_slug($_POST['name']);
            $stmt = $pdo->prepare("INSERT INTO project_categories (name, slug, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([trim($_POST['name']), $slug, (int)$_POST['sort_order']]);
            flash('Category added.');
        }
        if ($action === 'delete_category') {
            $pdo->prepare("DELETE FROM project_categories WHERE id=?")->execute([(int)$_POST['id']]);
            flash('Category deleted (and its projects).');
        }

        /* Projects */
        if ($action === 'save_project') {
            $id = (int)$_POST['id'];
            $q = $pdo->prepare("SELECT image FROM projects WHERE id=?");
            $q->execute([$id]);
            $img = upload_image('image', (string)$q->fetchColumn());

            $stmt = $pdo->prepare("UPDATE projects SET category_id=?, title=?, description=?, sort_order=?, image=? WHERE id=?");
            $stmt->execute([(int)$_POST['category_id'], trim($_POST['title']), trim($_POST['description']), (int)$_POST['sort_order'], $img, $id]);
            flash('Project updated.');
        }
        if ($action === 'add_project') {
            $img = upload_image('image', '');
            $stmt = $pdo->prepare("INSERT INTO projects (category_id, title, description, image, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([(int)$_POST['category_id'], trim($_POST['title']), trim($_POST['description']), $img, (int)$_POST['sort_order']]);
            flash('Project added.');
        }
        if ($action === 'delete_project') {
            $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([(int)$_POST['id']]);
            flash('Project deleted.');
        }
    } catch (PDOException $ex) {
        flash('Could not save: that category slug may already exist.', 'danger');
    }

    header('Location: ' . admin_base() . 'projects.php');
    exit;
}

$sec = $pdo->query("SELECT * FROM projects_section LIMIT 1")->fetch();
$categories = $pdo->query("SELECT * FROM project_categories ORDER BY sort_order")->fetchAll();
$projects = $pdo->query("SELECT * FROM projects ORDER BY sort_order")->fetchAll();

$page_title = 'Projects Section';
include 'includes/admin_header.php';
?>

<!-- Section content -->
<div class="admin-card mb-4">
  <h5 class="mb-3">Section content</h5>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_section">
    <input type="hidden" name="id" value="<?= (int)$sec['id'] ?>">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?= e($sec['title']) ?>">
      </div>
      <div class="col-md-5">
        <label class="form-label">Subtitle</label>
        <input type="text" name="subtitle" class="form-control" value="<?= e($sec['subtitle']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">"All" filter label</label>
        <input type="text" name="all_filter_label" class="form-control" value="<?= e($sec['all_filter_label']) ?>">
      </div>
    </div>
    <button class="btn btn-orange mt-3 px-4">Save section</button>
  </form>
</div>

<!-- Categories -->
<div class="admin-card mb-4">
  <h5 class="mb-1">Categories (filter buttons)</h5>
  <p class="text-muted small mb-3">Deleting a category also deletes the projects inside it.</p>

  <?php foreach ($categories as $c): ?>
    <form method="post" class="row g-2 align-items-end mb-3">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
      <div class="col-md-4"><label class="form-label small">Name</label><input type="text" name="name" class="form-control" value="<?= e($c['name']) ?>"></div>
      <div class="col-md-3"><label class="form-label small">Slug</label><input type="text" name="slug" class="form-control" value="<?= e($c['slug']) ?>"></div>
      <div class="col-md-1"><label class="form-label small">Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$c['sort_order'] ?>"></div>
      <div class="col-md-4 d-flex gap-2">
        <button name="action" value="save_category" class="btn btn-orange">Save</button>
        <button name="action" value="delete_category" class="btn btn-outline-danger" onclick="return confirm('Delete this category AND all its projects?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>

  <hr>
  <h6>Add a category</h6>
  <form method="post" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_category">
    <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
    <div class="col-md-3"><input type="text" name="slug" class="form-control" placeholder="Slug (optional)"></div>
    <div class="col-md-1"><input type="number" name="sort_order" class="form-control" value="<?= count($categories) + 1 ?>"></div>
    <div class="col-md-4"><button class="btn btn-dark">Add category</button></div>
  </form>
</div>

<!-- Projects -->
<div class="admin-card">
  <h5 class="mb-3">Projects</h5>

  <?php foreach ($projects as $p): ?>
    <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end mb-3 pb-3 border-bottom">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <div class="col-md-1">
        <img src="<?= site_base() . e($p['image']) ?>" alt="" style="width:70px;height:55px;object-fit:cover;border-radius:6px;background:#ccd3d9;">
      </div>
      <div class="col-md-2">
        <label class="form-label small">Category</label>
        <select name="category_id" class="form-select">
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $c['id'] == $p['category_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Title</label><input type="text" name="title" class="form-control" value="<?= e($p['title']) ?>"></div>
      <div class="col-md-3"><label class="form-label small">Description</label><input type="text" name="description" class="form-control" value="<?= e($p['description']) ?>"></div>
      <div class="col-md-1"><label class="form-label small">Order</label><input type="number" name="sort_order" class="form-control" value="<?= (int)$p['sort_order'] ?>"></div>
      <div class="col-md-3"><label class="form-label small">New image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
      <div class="col-12 d-flex gap-2">
        <button name="action" value="save_project" class="btn btn-orange btn-sm">Save</button>
        <button name="action" value="delete_project" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this project?')">Delete</button>
      </div>
    </form>
  <?php endforeach; ?>

  <h6>Add a project</h6>
  <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_project">
    <div class="col-md-2">
      <select name="category_id" class="form-select" required>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><input type="text" name="title" class="form-control" placeholder="Title" required></div>
    <div class="col-md-3"><input type="text" name="description" class="form-control" placeholder="Description"></div>
    <div class="col-md-1"><input type="number" name="sort_order" class="form-control" value="<?= count($projects) + 1 ?>"></div>
    <div class="col-md-3"><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="col-md-1"><button class="btn btn-dark">Add</button></div>
  </form>
</div>

<?php include 'includes/admin_footer.php'; ?>
