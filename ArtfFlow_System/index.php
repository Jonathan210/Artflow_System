<?php
// ============================================================
// ArtFlow - Homepage / Feed (Redesigned)
// ============================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PostModel.php';

$postModel = new PostModel();

$page    = max(1, (int)($_GET['page'] ?? 1));
$type    = in_array($_GET['type'] ?? '', ['artwork', 'lesson', 'project']) ? $_GET['type'] : '';
$search  = sanitize(trim($_GET['q'] ?? ''));
$perPage = 10;

$posts     = $postModel->getFeed($page, $perPage, currentUserId(), $type, $search);
$totalPosts = $postModel->countFeed($type, $search);
$totalPages = max(1, ceil($totalPosts / $perPage));

$flash = getFlash();
$pageTitle  = 'ArtFlow — Community Art & Learning';

require_once __DIR__ . '/views/partials/head.php';
require_once __DIR__ . '/views/partials/topbar.php';
?>

<main class="app-container">
    
    <!-- Left Sidebar -->
    <?php require __DIR__ . '/views/partials/sidebar.php'; ?>

    <!-- Main Feed Area -->
    <div class="feed-area">
        
        <?php if ($flash): ?>
            <div class="flash flash-<?= $flash['type'] ?>" id="flash-message">
                <?= sanitize($flash['message']) ?>
            </div>
            <style>
                .flash {
                    background: var(--bg-accent);
                    color: var(--primary);
                    padding: 15px 25px;
                    border-radius: var(--radius-pill);
                    margin-bottom: 25px;
                    box-shadow: var(--shadow-md);
                    border-left: 5px solid var(--primary);
                    animation: fadeInRight 0.5s ease-out;
                }
                @keyframes fadeInRight {
                    from { opacity: 0; transform: translateX(20px); }
                    to { opacity: 1; transform: translateX(0); }
                }
            </style>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('flash-message');
                    if(msg) {
                        msg.style.opacity = '0';
                        msg.style.transition = '0.5s';
                        setTimeout(() => msg.remove(), 500);
                    }
                }, 4000);
            </script>
        <?php endif; ?>

        <!-- Share Progress Box -->
        <?php if (isLoggedIn()): ?>
        <div class="share-progress">
            <form action="<?= BASE_URL ?>/controllers/PostController.php?action=create" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="text" name="title" class="share-input" placeholder="Title of your progress..." required>
                <textarea name="content" class="share-content" placeholder="Share your creative process..." required></textarea>
                
                <div class="share-actions">
                    <label class="share-image-label" title="Add Image">
                        <input type="file" name="image" accept="image/*" style="display:none;">
                        📸 ADD IMAGE
                    </label>
                    <select name="type" class="share-select">
                        <option value="artwork">🖼️ Artwork</option>
                        <option value="lesson">🎓 Lesson</option>
                        <option value="project">🛠️ Project</option>
                    </select>
                    <button type="submit" class="btn-post">POST TO STUDIO</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Posts -->
        <?php if (empty($posts)): ?>
            <div class="post-card text-center">
                <h3>No posts found</h3>
                <p>Be the first to share something!</p>
            </div>
        <?php else: foreach ($posts as $post): ?>
            <?php require __DIR__ . '/views/partials/post_card.php'; ?>
        <?php endforeach; endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination text-center" style="margin-top: 20px;">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="nav-item btn-nav" style="margin: 0 5px;"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Sidebar -->
    <aside class="right-sidebar">
        <div class="sidebar-box">
            <div class="box-title">WAHT'S NEW?</div>
            
            <div class="lesson-update">LESSON UPDATES</div>
            <div class="lesson-update">LESSON UPDATES</div>
        </div>
    </aside>

</main>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>