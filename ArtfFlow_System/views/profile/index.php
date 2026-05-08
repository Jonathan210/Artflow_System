<?php
// ============================================================
// ArtFlow - Profile Page (Redesigned & Functional)
// ============================================================

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/PostModel.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

$userModel = new UserModel();
$postModel = new PostModel();

$profileId = isset($_GET['id']) ? (int)$_GET['id'] : currentUserId();
$isOwnProfile = ($profileId == currentUserId());

$user = $userModel->findById($profileId);
if (!$user) {
    die("User not found.");
}

$posts = $postModel->getFeed(1, 20, currentUserId(), '', '', $profileId); // Show this user's posts
$stats = $userModel->getFollowStats($profileId);
$isFollowing = $userModel->isFollowing(currentUserId(), $profileId);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isOwnProfile) {
    $bio = sanitize($_POST['bio'] ?? '');
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->execute([$bio, currentUserId()]);
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../assets/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = uniqid() . '_avatar_' . basename($_FILES['avatar']['name']);
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $filename)) {
            $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$filename, currentUserId()]);
        }
    }
    setFlash('success', 'Profile updated successfully.');
    header('Location: ' . BASE_URL . '/views/profile/index.php');
    exit;
}

$pageTitle = sanitize($user['username']) . ' — ArtFlow Profile';
require_once __DIR__ . '/../partials/head.php';
require_once __DIR__ . '/../partials/topbar.php';
?>

<main class="app-container">
    
    <!-- Left Sidebar (Profile Details) -->
    <aside class="left-sidebar">
        <div class="sidebar-box profile-card" style="padding: 30px;">
            <div class="pfp-circle" style="width: 120px; height: 120px; margin-bottom: 20px;">
                <img src="<?= avatarUrl($user['avatar'] ?? null) ?>" alt="<?= sanitize($user['username']) ?>">
            </div>
            <h2 class="user-name" style="font-size: 1.5rem; text-transform: uppercase;">@<?= sanitize($user['username']) ?></h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
                <?= sanitize($user['bio'] ?? 'Visual Storyteller & Artist') ?>
            </p>
            
            <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 20px;">
                <div class="stat-item">
                    <div style="font-weight: 700; color: var(--primary);"><?= $stats['following'] ?></div>
                    <div style="font-size: 0.65rem; color: var(--text-muted);">FOLLOWING</div>
                </div>
                <div class="stat-item">
                    <div style="font-weight: 700; color: var(--primary);"><?= $stats['followers'] ?></div>
                    <div style="font-size: 0.65rem; color: var(--text-muted);">FOLLOWERS</div>
                </div>
            </div>

            <?php if (!$isOwnProfile): ?>
                <button class="btn-post follow-btn <?= $isFollowing ? 'active' : '' ?>" 
                        data-user-id="<?= $user['id'] ?>" 
                        style="width: 100%; margin-bottom: 20px;">
                    <?= $isFollowing ? 'UNFOLLOW' : 'FOLLOW' ?>
                </button>
            <?php endif; ?>
            
            <?php if ($isOwnProfile): ?>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid rgba(0,0,0,0.05);">
            
            <form method="POST" action="<?= BASE_URL ?>/views/profile/index.php" enctype="multipart/form-data" style="text-align: left;">
                <div class="form-group">
                    <label style="font-size: 0.7rem;">MY BIO:</label>
                    <textarea name="bio" class="form-control" rows="2" style="font-size: 0.8rem; border-radius: 15px;"><?= sanitize($user['bio'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label style="font-size: 0.7rem;">CHANGE AVATAR:</label>
                    <input type="file" name="avatar" class="form-control" style="font-size: 0.7rem; padding: 8px;">
                </div>
                <button type="submit" class="btn-post" style="width: 100%; font-size: 0.8rem;">UPDATE STUDIO</button>
            </form>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Main Area (Post Grid) -->
    <div class="feed-area">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <h2 style="font-family: 'Cinzel', serif; font-size: 1.2rem;"><?= $isOwnProfile ? 'MY CREATIONS' : sanitize($user['username']) . "'s GALLERY" ?></h2>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <?php if (empty($posts)): ?>
                <div class="post-card" style="grid-column: span 2; text-align: center; padding: 60px;">
                    <div style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;">🖼️</div>
                    <h3 style="color: var(--text-muted);">No masterpieces shared yet</h3>
                </div>
            <?php else: foreach ($posts as $post): ?>
                <?php require __DIR__ . '/../partials/post_card.php'; ?>
            <?php endforeach; endif; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <aside class="right-sidebar">
        <div class="sidebar-box">
            <div class="box-title">ACHIEVEMENTS</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: center;">
                <div style="padding: 15px; background: rgba(0,0,0,0.03); border-radius: 15px;">
                    <div style="font-size: 1.2rem;">🏆</div>
                    <div style="font-size: 0.6rem; font-weight: 700; margin-top: 5px;">TOP ARTIST</div>
                </div>
                <div style="padding: 15px; background: rgba(0,0,0,0.03); border-radius: 15px;">
                    <div style="font-size: 1.2rem;">🎨</div>
                    <div style="font-size: 0.6rem; font-weight: 700; margin-top: 5px;">CREATOR</div>
                </div>
            </div>
        </div>
        
        <div class="sidebar-box">
            <div class="box-title">DETAILS</div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Member since:</span>
                    <span style="color: var(--text-main); font-weight: 600;"><?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Total Posts:</span>
                    <span style="color: var(--text-main); font-weight: 600;"><?= count($posts) ?></span>
                </div>
            </div>
        </div>
    </aside>

</main>

<style>
.stat-item {
    text-align: center;
    padding: 0 15px;
}
.stat-item:not(:last-child) {
    border-right: 1px solid rgba(0,0,0,0.05);
}
.follow-btn.active {
    background: transparent;
    border: 1px solid var(--primary);
    color: var(--primary);
}
</style>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
