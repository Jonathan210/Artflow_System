<?php
// ============================================================
// ArtFlow - Post Card Partial (Redesigned)
// ============================================================
$liked = !empty($post['is_liked']);
$faved = !empty($post['is_saved']);
?>
<article class="post-card" data-id="<?= $post['id'] ?>">
    
    <!-- Actions (Top Right) -->
    <div class="post-actions-top">
        <div class="post-save action-btn <?= $faved ? 'active' : '' ?>" data-action="favorite">
            <span><?= $faved ? '★' : '☆' ?></span>
        </div>
    </div>

    <!-- Post Header -->
    <div class="post-header">
        <div class="post-pfp">
            <img src="<?= avatarUrl($post['avatar']) ?>" alt="<?= sanitize($post['username']) ?>" style="width:100%; height:100%; object-fit:cover;">
        </div>
        <div class="post-user-info">
            <span class="post-username">@<?= sanitize($post['username']) ?></span>
            <span class="post-time"><?= timeAgo($post['created_at']) ?></span>
        </div>
    </div>

    <!-- Post Image -->
    <div class="post-image-container">
        <?php if (!empty($post['image'])): ?>
            <img src="<?= postImageUrl($post['image']) ?>" alt="<?= sanitize($post['title']) ?>" class="post-image" loading="lazy">
        <?php else: ?>
            <div class="post-image-placeholder">
                <?= sanitize($post['title']) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Post Footer -->
    <div class="post-footer">
        <div class="post-meta-actions">
            <div class="post-like action-btn <?= $liked ? 'active' : '' ?>" data-action="like">
                <span class="icon"><?= $liked ? '❤️' : '🤍' ?></span>
                <span class="count"><?= $post['likes_count'] ?></span>
            </div>
            <div class="post-comment-btn action-btn">
                <span class="icon">💬</span>
                <span class="count"><?= $post['comments_count'] ?></span>
            </div>
        </div>

        <h3 class="post-title" style="font-size: 1.1rem; margin: 10px 0 5px;"><?= sanitize($post['title']) ?></h3>
        <?php if (!empty($post['content'])): ?>
            <p class="post-excerpt" style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.4;">
                <?= nl2br(sanitize(truncate($post['content'], 120))) ?>
            </p>
        <?php endif; ?>
    </div>

</article>