<?php
// ============================================================
// ArtFlow - Left Sidebar Partial (Redesigned)
// ============================================================
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/config/database.php';
    require_once dirname(__DIR__, 2) . '/config/auth.php';
}

$um = new UserModel();
$sUser = isLoggedIn() ? $um->findById(currentUserId()) : null;
?>

<aside class="left-sidebar">
  
  <!-- Profile Section -->
  <div class="sidebar-box">
    <?php if ($sUser): ?>
      <div class="profile-card">
        <div class="pfp-circle">
          <img src="<?= avatarUrl($sUser['avatar'] ?? null) ?>" alt="<?= sanitize($sUser['username']) ?>">
        </div>
        <div class="user-name">@<?= sanitize($sUser['username']) ?></div>
        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 15px;">Digital Illustrator</div>
        <a href="<?= BASE_URL ?>/views/profile/index.php" class="btn-edit">PROFILE SETTINGS</a>
      </div>
    <?php else: ?>
      <div class="text-center">
        <p style="font-size: 0.85rem; margin-bottom: 15px; color: var(--text-muted);">Join our creative community to share your journey.</p>
        <a href="<?= BASE_URL ?>/views/auth/login.php" class="btn-login">SIGN IN NOW</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Recently Searched Tags -->
  <div class="sidebar-box">
    <div class="box-title">LEARNING HUB</div>
    <div style="display: flex; flex-direction: column; gap: 8px;">
        <a href="<?= BASE_URL ?>/index.php?type=lesson" class="nav-item" style="font-size: 0.8rem; text-decoration: none; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
            <span>🎓</span> ALL LESSONS
        </a>
        <a href="<?= BASE_URL ?>/index.php?q=color&type=lesson" class="nav-item" style="font-size: 0.8rem; text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 10px;">
            <span>🎨</span> COLOR THEORY
        </a>
        <a href="<?= BASE_URL ?>/index.php?q=anatomy&type=lesson" class="nav-item" style="font-size: 0.8rem; text-decoration: none; color: var(--text-muted); display: flex; align-items: center; gap: 10px;">
            <span>👤</span> ANATOMY
        </a>
    </div>
  </div>

  <!-- Recently Viewed Artists -->
  <div class="sidebar-box">
    <div class="box-title">FEATURED ARTISTS</div>
    
    <div class="artist-list">
      <?php 
      $featured = $um->getFeaturedArtists(3);
      foreach ($featured as $artist): 
      ?>
      <div class="artist-item">
        <a href="<?= BASE_URL ?>/views/profile/index.php?id=<?= $artist['id'] ?>" class="artist-pfp" style="text-decoration:none; background: var(--bg-canvas); overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 700; color: var(--secondary);">
            <img src="<?= avatarUrl($artist['avatar']) ?>" style="width:100%; height:100%; object-fit:cover;">
        </a>
        <div style="display: flex; flex-direction: column;">
            <a href="<?= BASE_URL ?>/views/profile/index.php?id=<?= $artist['id'] ?>" class="artist-name" style="text-decoration:none; color:inherit;">@<?= sanitize($artist['username']) ?></a>
            <div style="font-size: 0.65rem; color: var(--text-muted);"><?= $artist['followers_count'] ?> Followers</div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (isAdmin()): ?>
  <!-- Admin Quick Actions -->
  <div class="sidebar-box" style="border: 1px solid var(--primary);">
    <div class="box-title" style="color: var(--primary);">CONTROL PANEL</div>
    <div style="display: flex; flex-direction: column; gap: 10px;">
        <a href="<?= BASE_URL ?>/views/admin/dashboard.php" class="btn-edit" style="background: var(--primary); color: var(--bg-accent); font-size: 0.7rem; text-align: center;">ADMIN DASHBOARD</a>
        <a href="<?= BASE_URL ?>/views/admin/users.php" class="btn-edit" style="font-size: 0.7rem; text-align: center;">USER MANAGEMENT</a>
    </div>
  </div>
  <?php endif; ?>

</aside>