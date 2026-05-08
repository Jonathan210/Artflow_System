-- ArtFlow Database Schema
CREATE DATABASE IF NOT EXISTS artflow_db;
USE artflow_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    bio TEXT,
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    type ENUM('artwork', 'lesson', 'project') NOT NULL,
    visibility ENUM('public', 'private') DEFAULT 'public',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Insert sample users
-- Password is 'password' (hashed with bcrypt)
INSERT IGNORE INTO users (id, username, email, password, role) VALUES
(1, 'admin', 'admin@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'admin'),
(2, 'luna', 'luna@example.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'),
(3, 'aria_painter', 'aria@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'),
(4, 'leo_sketch', 'leo@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'),
(5, 'pixel_pioneer', 'pixel@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user'),
(6, 'clay_master', 'clay@artflow.com', '$2y$10$WkG.1nE/3B.8UjI./3mI/uH4Q26a/2iQn7QxT5P5pM1/462bA7QkK', 'user');

-- Insert sample posts
INSERT IGNORE INTO posts (id, user_id, title, content, type, visibility) VALUES
(1, 2, 'My First Artwork', 'Hello world! This is my first artwork on ArtFlow.', 'artwork', 'public'),
(2, 1, 'How to blend colors', 'In this lesson, we will learn how to blend colors perfectly...', 'lesson', 'public'),
(3, 3, 'Midnight Serenity', 'Working on this oil painting for 3 weeks now. The blue tones are finally coming together.', 'artwork', 'public'),
(4, 4, 'Character Design Basics', 'Quick sketch exploring anatomy and silhouette for a new project.', 'artwork', 'public'),
(5, 5, 'Retro Vibes', 'Experimenting with 16-bit color palettes in my latest digital piece.', 'artwork', 'public'),
(6, 6, 'Sculpting the Future', 'Timelapse of my latest clay sculpture. It is all about the texture.', 'project', 'public'),
(7, 3, 'Mastering Oil Glazing', 'A deep dive into the technique of layering thin, transparent colors.', 'lesson', 'public'),
(8, 4, 'Anatomy for Character Artists', 'Breaking down the human muscular system for better character silhouettes.', 'lesson', 'public'),
(9, 5, 'Intro to Pixel Animation', 'Learn the basics of frame-by-frame animation in a 64x64 canvas.', 'lesson', 'public');

CREATE TABLE IF NOT EXISTS follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender_id INT NOT NULL,
    type ENUM('like', 'comment', 'follow') NOT NULL,
    post_id INT DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Insert sample follows
INSERT IGNORE INTO follows (follower_id, following_id) VALUES
(1, 3), (1, 4), (1, 5),
(2, 3), (2, 6),
(3, 1), (3, 4),
(4, 5), (4, 1),
(5, 3), (5, 4);

-- Insert sample likes
INSERT IGNORE INTO likes (user_id, post_id) VALUES
(1, 3), (1, 4), (1, 5),
(2, 1), (2, 3),
(3, 2), (3, 4),
(4, 5), (4, 6),
(5, 3), (6, 5);

-- Insert sample notifications
INSERT IGNORE INTO notifications (user_id, sender_id, type, post_id) VALUES
(3, 1, 'like', 3),
(4, 1, 'like', 4),
(3, 2, 'like', 3),
(1, 3, 'follow', NULL);
