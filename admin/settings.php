<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
    foreach (($_POST['s'] ?? []) as $key => $value) {
        $stmt->execute([trim($value), $key]);
    }
    flash('Site settings saved.');
    header('Location: ' . admin_base() . 'settings.php');
    exit;
}

$rows = $pdo->query("SELECT * FROM site_settings WHERE setting_key != 'logo_image' ORDER BY id")->fetchAll();

$page_title = 'Site Settings';
include 'includes/admin_header.php';
?>

<div class="admin-card">
  <form method="post">
    <?= csrf_field() ?>
    <div class="row g-3">
      <?php foreach ($rows as $r): ?>
        <div class="col-md-6">
          <label class="form-label"><?= e($r['label']) ?></label>
          <input type="text" class="form-control" name="s[<?= e($r['setting_key']) ?>]" value="<?= e($r['setting_value']) ?>">
        </div>
      <?php endforeach; ?>
    </div>
    <button class="btn btn-orange mt-4 px-4">Save changes</button>
  </form>
</div>

<?php include 'includes/admin_footer.php'; ?>