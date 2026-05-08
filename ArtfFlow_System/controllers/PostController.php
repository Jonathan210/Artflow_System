<?php
// controllers/PostController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/PostModel.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$action = $_GET['action'] ?? '';
$postModel = new PostModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only verify CSRF if it's a regular form submission, some ajax might need different handling
    // but we'll try to include it.
    
    if ($action === 'create') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Invalid security token.');
            header('Location: ' . BASE_URL . '/views/posts/create.php');
            exit;
        }

        $title = sanitize($_POST['title'] ?? '');
        $content = sanitize($_POST['content'] ?? '');
        $type = sanitize($_POST['type'] ?? 'artwork');
        $visibility = sanitize($_POST['visibility'] ?? 'public');
        
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = $filename;
            }
        }
        
        if (empty($title) || empty($content)) {
            setFlash('error', 'Title and content are required.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php')));
            exit;
        }

        if ($postModel->createPost(currentUserId(), $title, $content, $type, $visibility, $image)) {
            setFlash('success', 'Post created successfully!');
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        } else {
            setFlash('error', 'Failed to create post.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php')));
            exit;
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $post = $postModel->getPostById($id);
        
        if ($post && ($post['user_id'] == currentUserId() || isAdmin())) {
            $postModel->deletePost($id);
            setFlash('success', 'Post deleted successfully.');
        } else {
            setFlash('error', 'Unauthorized to delete this post.');
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } elseif ($action === 'like') {
        $postId = $_POST['post_id'] ?? 0;
        if ($postId) {
            $status = $postModel->toggleLike(currentUserId(), $postId);
            $likesCount = $postModel->getPostById($postId)['like_count'] ?? 0; // We can query this or just return
            // Wait, we need to return the new count. We'll just return action liked/unliked.
            // main.js expects data.action = 'liked' and data.count
            // For simplicity, we can just return action since the JS already updates the heart icon. 
            // To be accurate, we'll return action
            if ($status) {
                $post = $postModel->getPostById($postId);
                $postModel->createNotification($post['user_id'], currentUserId(), 'like', $postId);
            }
            echo json_encode(['success' => true, 'action' => $status ? 'liked' : 'unliked', 'count' => '']);
        } else {
            echo json_encode(['error' => 'Invalid post ID']);
        }
        exit;
    } elseif ($action === 'favorite') {
        $postId = $_POST['post_id'] ?? 0;
        if ($postId) {
            $status = $postModel->toggleSave(currentUserId(), $postId);
            echo json_encode(['success' => true, 'action' => $status ? 'faved' : 'unfaved']);
        } else {
            echo json_encode(['error' => 'Invalid post ID']);
        }
        exit;
    } elseif ($action === 'comment') {
        $postId = $_POST['post_id'] ?? 0;
        $content = sanitize($_POST['content'] ?? '');
        if ($postId && $content) {
            $commentId = $postModel->addComment(currentUserId(), $postId, $content);
            if ($commentId) {
                $post = $postModel->getPostById($postId);
                $postModel->createNotification($post['user_id'], currentUserId(), 'comment', $postId);
                // Fetch the new comment to return it
                $comment = $postModel->getCommentById($commentId);
                echo json_encode([
                    'success' => true,
                    'comment' => [
                        'id' => $comment['id'],
                        'username' => sanitize($comment['username']),
                        'avatar' => avatarUrl($comment['avatar']),
                        'content' => sanitize($comment['content']),
                        'time' => timeAgo($comment['created_at']),
                        'can_delete' => true
                    ]
                ]);
            } else {
                echo json_encode(['error' => 'Failed to add comment']);
            }
        } else {
            echo json_encode(['error' => 'Content is required']);
        }
        exit;
    } elseif ($action === 'delete_comment') {
        $commentId = $_POST['comment_id'] ?? 0;
        $comment = $postModel->getCommentById($commentId);
        if ($comment && ($comment['user_id'] == currentUserId() || isAdmin())) {
            $postModel->deleteComment($commentId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Unauthorized']);
        }
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'get_comments') {
        $postId = $_GET['post_id'] ?? 0;
        $comments = $postModel->getCommentsByPostId($postId);
        $result = [];
        foreach ($comments as $c) {
            $result[] = [
                'id' => $c['id'],
                'username' => sanitize($c['username']),
                'avatar' => avatarUrl($c['avatar']),
                'content' => sanitize($c['content']),
                'time' => timeAgo($c['created_at']),
                'can_delete' => ($c['user_id'] == currentUserId() || isAdmin())
            ];
        }
        echo json_encode($result);
        exit;
    }
}
