<?php
include 'includes/header.php';

$hero = $pdo->query("SELECT * FROM hero_section LIMIT 1")->fetch();
$counters = $pdo->query("SELECT * FROM hero_counters ORDER BY sort_order")->fetchAll();
$about = $pdo->query("SELECT * FROM about_section LIMIT 1")->fetch();
$features = $pdo->query("SELECT * FROM about_features ORDER BY sort_order")->fetchAll();
$services_sec = $pdo->query("SELECT * FROM services_section LIMIT 1")->fetch();
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();
$stats = $pdo->query("SELECT * FROM track_record_stats ORDER BY sort_order")->fetchAll();
$projects_sec = $pdo->query("SELECT * FROM projects_section LIMIT 1")->fetch();
$categories = $pdo->query("SELECT * FROM project_categories ORDER BY sort_order")->fetchAll();
$projects = $pdo->query("
    SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
    FROM projects p
    JOIN project_categories c ON c.id = p.category_id
    ORDER BY p.sort_order
")->fetchAll();
?>

<main>

  <!-- HOME -->
  <section id="home">
    <div class="hero" style="background-image: url('<?= htmlspecialchars($hero['background_image']) ?>');">
      <div class="container">
        <div class="row">
          <div class="col-lg-6">
            <span class="hero-badge"><i class="bi bi-building"></i> <?= htmlspecialchars($hero['badge_text']) ?></span>
            <h1><?= htmlspecialchars($hero['title_main']) ?> <span><?= htmlspecialchars($hero['title_highlight']) ?></span></h1>
            <p><?= htmlspecialchars($hero['description']) ?></p>
            <div class="d-flex flex-wrap gap-3">
              <button class="btn btn-orange"><?= htmlspecialchars($hero['button1_text']) ?></button>
              <button class="btn-outline-light-custom"><?= htmlspecialchars($hero['button2_text']) ?> <i class="bi bi-arrow-right"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container counters-wrap">
      <div class="counters-bar">
        <div class="row text-center g-4">
          <?php foreach ($counters as $c): ?>
            <div class="col-6 col-md-3">
              <div class="counter-number">
                <span class="count" data-target="<?= (int)$c['number_value'] ?>">0</span><?= htmlspecialchars($c['suffix']) ?>
              </div>
              <div class="counter-label"><?= htmlspecialchars($c['label']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about" class="about-section">
    <div class="container">
      <div class="row align-items-center g-5">

        <div class="col-lg-6">
          <div class="about-images">
            <img class="about-img-main" src="<?= htmlspecialchars($about['image_main']) ?>" alt="About image">
            <img class="about-img-second" src="<?= htmlspecialchars($about['image_secondary']) ?>" alt="About image 2">
            <div class="about-badge">
              <strong><?= htmlspecialchars($about['badge_number']) ?></strong>
              <small><?= htmlspecialchars($about['badge_label']) ?></small>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="section-eyebrow"><i class="bi bi-building"></i> <?= htmlspecialchars($about['small_heading']) ?></div>
          <h2 class="section-title"><?= htmlspecialchars($about['title']) ?></h2>
          <p><?= htmlspecialchars($about['description']) ?></p>

          <div class="row g-4 my-3">
            <?php foreach ($features as $f): ?>
              <div class="col-sm-6">
                <div class="feature">
                  <div class="feature-icon"><i class="bi <?= htmlspecialchars($f['icon']) ?>"></i></div>
                  <div>
                    <h6><?= htmlspecialchars($f['title']) ?></h6>
                    <p><?= htmlspecialchars($f['description']) ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="d-flex flex-wrap gap-3 mt-4">
            <button class="btn btn-orange" style="border-radius:6px;"><?= htmlspecialchars($about['button1_text']) ?> <i class="bi bi-arrow-right"></i></button>
            <button class="btn-outline-dark-custom"><?= htmlspecialchars($about['button2_text']) ?></button>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- SERVICES -->
  <section id="services" class="services-section">
    <div class="container">

      <div class="text-center mb-5">
        <h2 class="section-title center-title"><?= htmlspecialchars($services_sec['title']) ?></h2>
        <div class="title-line"></div>
        <p class="section-sub"><?= htmlspecialchars($services_sec['subtitle']) ?></p>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="service-side" style="background-image: url('<?= htmlspecialchars($services_sec['side_image']) ?>');">
            <div class="service-side-text">
              <h4><?= htmlspecialchars($services_sec['side_card_title']) ?></h4>
              <a href="#"><?= htmlspecialchars($services_sec['side_card_link_text']) ?> <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="row g-3">
            <?php foreach ($services as $s): ?>
              <div class="col-md-6">
                <div class="service-card">
                  <div class="feature-icon"><i class="bi <?= htmlspecialchars($s['icon']) ?>"></i></div>
                  <div>
                    <h6><?= htmlspecialchars($s['title']) ?></h6>
                    <p><?= htmlspecialchars($s['description']) ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Track record -->
      <div class="row align-items-center g-4 mt-5 pt-3">
        <div class="col-lg-4">
          <div class="section-eyebrow"><?= htmlspecialchars($services_sec['track_small_heading']) ?></div>
          <h3 class="track-title"><?= htmlspecialchars($services_sec['track_title']) ?></h3>
        </div>
        <div class="col-lg-8">
          <div class="row g-3 text-center text-lg-start">
            <?php foreach ($stats as $st): ?>
              <div class="col-6 col-md-3">
                <div class="stat-box">
                  <i class="bi <?= htmlspecialchars($st['icon']) ?>"></i>
                  <div class="stat-number">
                    <span class="count" data-target="<?= (int)$st['number_value'] ?>">0</span><?= htmlspecialchars($st['suffix']) ?>
                  </div>
                  <div class="stat-label"><?= htmlspecialchars($st['label']) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- PROJECTS -->
  <section id="projects" class="projects-section">
    <div class="container">

      <div class="text-center mb-4">
        <h2 class="section-title center-title"><?= htmlspecialchars($projects_sec['title']) ?></h2>
        <div class="title-line"></div>
        <p class="section-sub"><?= htmlspecialchars($projects_sec['subtitle']) ?></p>
      </div>

      <div class="filter-bar">
        <button class="filter-btn active" data-filter="all"><?= htmlspecialchars($projects_sec['all_filter_label']) ?></button>
        <?php foreach ($categories as $cat): ?>
          <button class="filter-btn" data-filter="<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></button>
        <?php endforeach; ?>
      </div>

      <div class="row g-4" id="projectGrid">
        <?php foreach ($projects as $p): ?>
          <div class="col-md-6 col-lg-4 project-item" data-category="<?= htmlspecialchars($p['cat_slug']) ?>">
            <div class="project-card" style="background-image: url('<?= htmlspecialchars($p['image']) ?>');">
              <div class="project-actions">
                <span><i class="bi bi-arrows-fullscreen"></i></span>
                <span><i class="bi bi-arrow-right"></i></span>
              </div>
              <div class="project-info">
                <small><?= htmlspecialchars($p['cat_name']) ?></small>
                <h5><?= htmlspecialchars($p['title']) ?></h5>
                <p><?= htmlspecialchars($p['description']) ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

</main>

<?php include 'includes/footer.php'; ?>