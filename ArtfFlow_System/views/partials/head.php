<?php
// ============================================================
// ArtFlow - HTML Head Partial
// ============================================================
$pageTitle = $pageTitle ?? 'ArtFlow — Community Art Platform';
?>
<!DOCTYPE html>
<html lang="en" data-base-url="<?= BASE_URL ?>" data-csrf="<?= csrfToken() ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="ArtFlow — A vibrant community for art lovers, creators, and learners.">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎨</text></svg>">

  <!-- CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">

  <?php if (isset($extraCSS)): ?>
  <style><?= $extraCSS ?></style>
  <?php endif; ?>
</head>
<body>
<?php require_once __DIR__ . '/video_bg.php'; ?>