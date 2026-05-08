<?php
// views/admin/dashboard.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

global $pdo;

// Fetch some basic stats
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$commentCount = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();

$pageTitle = 'Admin Dashboard — ArtFlow';
$topbarTitle = '⚙️ Admin Dashboard';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="app-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php require __DIR__ . '/../partials/topbar.php'; ?>

        <div class="feed-container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div class="card" style="padding: 24px; text-align: center;">
                    <div style="font-size: 2rem; color: var(--rose);">👥</div>
                    <h3>Total Users</h3>
                    <p style="font-size: 1.5rem; font-weight: bold;"><?= $userCount ?></p>
                </div>
                <div class="card" style="padding: 24px; text-align: center;">
                    <div style="font-size: 2rem; color: var(--amber);">📋</div>
                    <h3>Total Posts</h3>
                    <p style="font-size: 1.5rem; font-weight: bold;"><?= $postCount ?></p>
                </div>
                <div class="card" style="padding: 24px; text-align: center;">
                    <div style="font-size: 2rem; color: var(--sage);">💬</div>
                    <h3>Total Comments</h3>
                    <p style="font-size: 1.5rem; font-weight: bold;"><?= $commentCount ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2>Admin Quick Actions</h2>
                    <div style="display: flex; gap: 12px; margin-top: 16px;">
                        <a href="<?= BASE_URL ?>/views/admin/users.php" class="btn btn-primary">Manage Users</a>
                        <a href="<?= BASE_URL ?>/views/admin/posts.php" class="btn" style="background:var(--parchment); border:1px solid var(--border);">Moderate Content</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
