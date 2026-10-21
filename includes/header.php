<?php
require_once __DIR__ . '/db.php';

$settings = [];
foreach ($pdo->query("SELECT setting_key, setting_value FROM site_settings") as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}
function s($key) {
    global $settings;
    return htmlspecialchars($settings[$key] ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= s('site_name') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  
</head>
<body>

<header class="site-header">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="logo" href="#home">
        <span class="logo-icon"><i class="bi bi-bar-chart-fill"></i></span>
        <?= s('site_name') ?>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#home"><?= s('nav_home') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="#about"><?= s('nav_about') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="#services"><?= s('nav_services') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="#projects"><?= s('nav_projects') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="#"><?= s('nav_team') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="#"><?= s('nav_pages') ?> <i class="bi bi-chevron-down small"></i></a></li>
          <li class="nav-item"><a class="nav-link" href="#"><?= s('nav_contact') ?></a></li>
        </ul>
        <div class="d-flex align-items-center gap-3">
          <a class="header-phone" href="#"><i class="bi bi-telephone"></i><?= s('header_phone') ?></a>
          <button class="btn btn-orange"><?= s('header_cta_text') ?></button>
        </div>
      </div>
    </div>
  </nav>
</header>