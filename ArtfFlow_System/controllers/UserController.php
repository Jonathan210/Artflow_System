<?php
// controllers/UserController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/UserModel.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$action = $_GET['action'] ?? '';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'follow') {
        $followingId = $_POST['user_id'] ?? 0;
        if ($followingId && $followingId != currentUserId()) {
            $status = $userModel->toggleFollow(currentUserId(), $followingId);
            echo json_encode(['success' => true, 'action' => $status ? 'followed' : 'unfollowed']);
        } else {
            echo json_encode(['error' => 'Invalid user ID or self-following']);
        }
        exit;
    } elseif ($action === 'mark_notifications_read') {
        $userModel->markNotificationsRead(currentUserId());
        echo json_encode(['success' => true]);
        exit;
    }
}
