<?php
// ============================================================
// ArtFlow - Video Background Partial
// ============================================================
?>
<div class="video-bg-container">
    <video autoplay muted loop playsinline id="bg-video">
        <source src="<?= BASE_URL ?>/assets/videos/bg2.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
</div>

<style>
.video-bg-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -2;
    overflow: hidden;
}

#bg-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.6) saturate(1.2); /* Adjust for readability */
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(44, 36, 32, 0.4); /* Match the Espresso theme */
    z-index: -1;
}

/* Ensure body is transparent to see video */
body {
    background-color: transparent !important;
}

/* Add a bit more blur to cards if video is busy */
.sidebar-box, .post-card, .share-progress, .top-nav {
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
}
</style>
