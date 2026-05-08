<?php
// ============================================================
// ArtFlow - Topbar Partial (Redesigned)
// ============================================================
$search = sanitize($_GET['q'] ?? '');
?>
<header class="top-nav">
  <a href="<?= BASE_URL ?>/index.php" class="nav-logo">ARTFLOW</a>

  <form class="nav-search" method="GET" action="<?= BASE_URL ?>/index.php">
    <input type="text" name="q" placeholder="SEARCH"
           value="<?= $search ?>" autocomplete="off">
    <?php if (!empty($_GET['type'])): ?>
    <input type="hidden" name="type" value="<?= sanitize($_GET['type']) ?>">
    <?php endif; ?>
  </form>

  <nav class="nav-links">
    <a href="<?= BASE_URL ?>/index.php" class="nav-item">HOME</a>
    <a href="<?= BASE_URL ?>/index.php?type=artwork" class="nav-item">TAGS</a>
    <a href="<?= BASE_URL ?>/index.php?type=lesson" class="nav-item">LESSONS</a>
    
    <?php if (isLoggedIn()): ?>
      <?php if (isAdmin()): ?>
        <a href="<?= BASE_URL ?>/views/admin/dashboard.php" class="nav-item btn-nav">DASHBOARD</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/views/profile/index.php" class="nav-item btn-nav">PROFILE</a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/controllers/AuthController.php?action=logout" class="nav-item btn-logout">LOG OUT</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/views/auth/login.php" class="nav-item btn-nav">LOG IN</a>
    <?php endif; ?>
  </nav>
</header>