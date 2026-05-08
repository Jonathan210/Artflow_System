<?php
// views/posts/saved.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/PostModel.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

$postModel = new PostModel();
$posts = $postModel->getSavedPosts(currentUserId());

$pageTitle = 'Saved Posts — ArtFlow';
$topbarTitle = '⭐ Saved Posts';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="app-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php require __DIR__ . '/../partials/topbar.php'; ?>

        <div class="feed-container">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <div class="empty-icon">⭐</div>
                    <h3>No saved posts</h3>
                    <p>When you save a post, it will appear here for easy access.</p>
                </div>
            <?php else: foreach ($posts as $post): ?>
                <?php require __DIR__ . '/../partials/post_card.php'; ?>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
