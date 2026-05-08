<?php
// seed_db.php - Run this to add sample data to your active database
require_once __DIR__ . '/config/database.php';

echo "Seeding ArtFlow Database...\n";

try {
    // 1. Add Users
    $users = [
        ['admin', 'admin@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'admin'],
        ['aria_painter', 'aria@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'],
        ['leo_sketch', 'leo@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'],
        ['pixel_pioneer', 'pixel@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'],
        ['clay_master', 'clay@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    echo "Users seeded.\n";

    // 2. Add Posts
    $posts = [
        [3, 'Midnight Serenity', 'Working on this oil painting for 3 weeks now. The blue tones are finally coming together.', 'artwork'],
        [4, 'Character Design Basics', 'Quick sketch exploring anatomy and silhouette for a new project.', 'artwork'],
        [5, 'Retro Vibes', 'Experimenting with 16-bit color palettes in my latest digital piece.', 'artwork'],
        [6, 'Sculpting the Future', 'Timelapse of my latest clay sculpture. It is all about the texture.', 'project']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO posts (user_id, title, content, type) VALUES (?, ?, ?, ?)");
    foreach ($posts as $p) {
        $stmt->execute($p);
    }
    echo "Posts seeded.\n";

    // 3. Social Data
    $pdo->exec("INSERT IGNORE INTO follows (follower_id, following_id) VALUES (1,3), (1,4), (2,3), (3,1), (4,5)");
    $pdo->exec("INSERT IGNORE INTO likes (user_id, post_id) VALUES (1,3), (2,3), (3,2), (4,5)");
    echo "Social data seeded.\n";

    echo "Success! Database is now populated with sample artists.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
