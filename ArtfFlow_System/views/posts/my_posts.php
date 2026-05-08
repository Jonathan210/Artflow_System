<?php
// views/my_posts.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/PostModel.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$postModel = new PostModel();

// Since getFeed handles the 'OR user_id = currentUserId()' for visibility, 
// we can't cleanly use it to ONLY get my posts without modifying it.
// Let's create a custom query or add a method to get specific user's posts.
// For now, I'll use a direct PDO call in the view to save time, or I can add to PostModel.
// Actually, I'll add `getUserPosts` to PostModel via replace_file_content if needed.
// Wait, I will just write a direct fetch for simplicity here to keep the file self-contained for the user.

$offset = ($page - 1) * $perPage;
global $pdo;

$sql = "SELECT p.*, u.username, u.avatar, 
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = " . (int)currentUserId() . ") as is_liked,
        (SELECT COUNT(*) FROM favorites WHERE post_id = p.id AND user_id = " . (int)currentUserId() . ") as is_saved
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, currentUserId(), PDO::PARAM_INT);
$stmt->bindValue(2, (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$pageTitle = 'My Posts — ArtFlow';
$topbarTitle = '📁 My Studio Posts';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="app-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php require __DIR__ . '/../partials/topbar.php'; ?>

        <div class="feed-container">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📁</div>
                    <h3>No posts yet</h3>
                    <p>Start sharing your creative journey with the community.</p>
                    <a href="<?= BASE_URL ?>/views/posts/create.php" class="btn btn-primary">✏️ Create First Post</a>
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
