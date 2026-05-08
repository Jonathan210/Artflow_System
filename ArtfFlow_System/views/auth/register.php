<?php
// ============================================================
// ArtFlow - Register Page (Redesigned)
// ============================================================

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ' . BASE_URL . '/views/admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . '/index.php');
    }
    exit;
}

$flash = getFlash();
$pageTitle = 'Join ArtFlow — Create Account';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="auth-bg">
  <h1 class="auth-header">ARTFLOW</h1>

  <div class="auth-box">
    <h2 style="font-family: 'Cinzel', serif; font-weight: 700;">CREATE ACCOUNT</h2>
    
    <?php if ($flash): ?>
      <div class="flash flash-<?= $flash['type'] ?>" style="margin-bottom: 20px; color: var(--primary);">
        <?= sanitize($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/controllers/AuthController.php?action=register">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

      <div class="form-group">
        <label>Pick a unique username</label>
        <input type="text" name="username" class="form-control" placeholder="e.g. leonardo_da_vinci" required>
      </div>

      <div class="form-group">
        <label>Your Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="artist@email.com" required>
      </div>

      <div class="form-group">
        <label>Secure Password</label>
        <div class="password-wrapper" style="position: relative;">
            <input type="password" name="password" id="reg-pass" class="form-control" placeholder="••••••••" required>
            <button type="button" class="toggle-password" data-target="reg-pass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 0.6rem; font-weight: 700; opacity: 0.6; color: var(--primary);">SHOW</button>
        </div>
      </div>

      <div class="form-group">
        <label>Confirm Your Password</label>
        <div class="password-wrapper" style="position: relative;">
            <input type="password" name="password_confirm" id="reg-pass-confirm" class="form-control" placeholder="••••••••" required>
            <button type="button" class="toggle-password" data-target="reg-pass-confirm" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 0.6rem; font-weight: 700; opacity: 0.6; color: var(--primary);">SHOW</button>
        </div>
      </div>

      <button type="submit" class="btn-login">JOIN THE COMMUNITY</button>
    </form>

    <div class="auth-footer">
      Already part of ArtFlow? <a href="<?= BASE_URL ?>/views/auth/login.php">Sign in</a> here.
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>