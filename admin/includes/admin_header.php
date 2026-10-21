<?php
require_once __DIR__ . '/auth.php';
require_login();
$current = basename($_SERVER['SCRIPT_NAME']);
$A = admin_base();
$S = site_base();

function nav_link($file, $icon, $label) {
    global $current, $A;
    $active = $current === $file ? 'active' : '';
    echo "<a class=\"$active\" href=\"{$A}{$file}\"><i class=\"bi $icon\"></i> $label</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - <?= e($page_title ?? 'Dashboard') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="<?= $S ?>assets/css/admin.css" rel="stylesheet">
</head>
<body>

<aside class="admin-sidebar">
  <div class="brand">Construct<span>ify</span> Admin</div>
  <nav class="mt-3">
    <?php
      nav_link('index.php', 'bi-speedometer2', 'Dashboard');
      nav_link('settings.php', 'bi-gear', 'Site Settings');
      nav_link('home.php', 'bi-house', 'Home Section');
      nav_link('about.php', 'bi-info-circle', 'About Section');
      nav_link('services.php', 'bi-tools', 'Services Section');
      nav_link('projects.php', 'bi-building', 'Projects Section');
    ?>
    <a href="<?= $S ?>index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View Website</a>
    <a href="<?= $A ?>logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
  </nav>
</aside>

<main class="admin-main">
  <div class="admin-topbar">
    <h2><?= e($page_title ?? 'Dashboard') ?></h2>
    <span class="text-muted">Hi, <?= e($_SESSION['admin_name']) ?></span>
  </div>

  <?php if ($f = flash()): ?>
    <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show">
      <?= e($f['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>