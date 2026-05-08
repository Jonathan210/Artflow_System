<?php
// ============================================================
// ArtFlow - Login Page (Redesigned)
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
$pageTitle = 'Sign In — ArtFlow';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="auth-bg">
  <h1 class="auth-header">ARTFLOW</h1>

  <div class="auth-box">
    <h2 style="font-family: 'Cinzel', serif; font-weight: 700;">WELCOME BACK</h2>

    <?php if ($flash): ?>
      <div class="flash flash-<?= $flash['type'] ?>" style="margin-bottom: 20px; color: var(--primary);">
        <?= sanitize($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/controllers/AuthController.php?action=login">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

      <div class="form-group">
        <label>Your Registered Email</label>
        <input type="email" name="email" class="form-control" placeholder="artist@email.com" required>
      </div>

      <div class="form-group">
        <label>Your Secret Password</label>
        <div class="password-wrapper" style="position: relative;">
            <input type="password" name="password" id="login-pass" class="form-control" placeholder="••••••••" required>
            <button type="button" class="toggle-password" data-target="login-pass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 0.6rem; font-weight: 700; opacity: 0.6; color: var(--primary);">SHOW</button>
        </div>
      </div>

      <button type="submit" class="btn-login">SIGN IN TO STUDIO</button>
    </form>

    <div class="auth-footer">
      New to ArtFlow? <a href="<?= BASE_URL ?>/views/auth/register.php">Create an account</a> today.
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>