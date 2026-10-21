<?php
$page_title = 'Dashboard';
include 'includes/admin_header.php';

$count = function ($table) use ($pdo) {
    return (int)$pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
};
?>

<div class="row g-4">
  <div class="col-md-3"><a class="stat-tile" href="home.php"><i class="bi bi-speedometer"></i><div class="num"><?= $count('hero_counters') ?></div>Home counters</a></div>
  <div class="col-md-3"><a class="stat-tile" href="about.php"><i class="bi bi-shield-check"></i><div class="num"><?= $count('about_features') ?></div>About features</a></div>
  <div class="col-md-3"><a class="stat-tile" href="services.php"><i class="bi bi-tools"></i><div class="num"><?= $count('services') ?></div>Services</a></div>
  <div class="col-md-3"><a class="stat-tile" href="projects.php"><i class="bi bi-building"></i><div class="num"><?= $count('projects') ?></div>Projects</a></div>
</div>

<div class="admin-card mt-4">
  <h5>Welcome</h5>
  <p class="mb-0 text-muted">Use the menu on the left to edit the content of each section of the website. Changes appear on the site immediately.</p>
</div>

<?php include 'includes/admin_footer.php'; ?>