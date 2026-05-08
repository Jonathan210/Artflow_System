<?php
// views/admin/posts.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/PostModel.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$postModel = new PostModel();
$posts = $postModel->getAllPosts();

$pageTitle = 'Admin Dashboard - Content Moderation';
$topbarTitle = '📋 Admin: Content Moderation';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="app-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php require __DIR__ . '/../partials/topbar.php'; ?>

        <div class="feed-container">
            <?php $flash = getFlash(); if ($flash): ?>
                <div class="flash flash-<?= $flash['type'] ?>">
                    <?= sanitize($flash['message']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <h2>Manage Posts</h2>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                                <th style="padding: 12px;">ID</th>
                                <th style="padding: 12px;">Author</th>
                                <th style="padding: 12px;">Title</th>
                                <th style="padding: 12px;">Type</th>
                                <th style="padding: 12px;">Visibility</th>
                                <th style="padding: 12px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 12px;"><?= $post['id'] ?></td>
                                    <td style="padding: 12px;"><?= sanitize($post['username']) ?></td>
                                    <td style="padding: 12px;"><?= sanitize($post['title']) ?></td>
                                    <td style="padding: 12px;"><?= ucfirst($post['type']) ?></td>
                                    <td style="padding: 12px;"><?= ucfirst($post['visibility']) ?></td>
                                    <td style="padding: 12px;">
                                        <form method="POST" action="<?= BASE_URL ?>/controllers/PostController.php?action=delete" onsubmit="return confirm('Are you sure you want to delete this post?');" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                            <button type="submit" class="btn btn-sm" style="background:var(--rose); color:white;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
