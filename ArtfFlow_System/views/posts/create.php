<?php
// views/posts/create.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

$pageTitle = 'Create Post — ArtFlow';
$topbarTitle = '✏️ Create New Post';
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
                    <form method="POST" action="<?= BASE_URL ?>/controllers/PostController.php?action=create" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                        <div class="form-group">
                            <label class="form-label">Post Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="Give your post a catchy title">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Post Type</label>
                            <select name="type" class="form-control" required>
                                <option value="artwork">🎨 Artwork</option>
                                <option value="lesson">📚 Lesson / Tutorial</option>
                                <option value="project">🛠 Project</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control" rows="6" required placeholder="Write your post content here..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Upload Image (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Visibility</label>
                            <select name="visibility" class="form-control">
                                <option value="public">🌍 Public (Visible to everyone)</option>
                                <option value="private">🔒 Private (Only visible to you)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Publish Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
