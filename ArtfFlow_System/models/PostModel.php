<?php
// models/PostModel.php
class PostModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getFeed($page = 1, $perPage = 10, $currentUserId = null, $type = '', $search = '') {
        $offset = ($page - 1) * $perPage;
        
        $whereClause = "WHERE (p.visibility = 'public'";
        $params = [];
        
        if ($currentUserId) {
            $whereClause .= " OR p.user_id = ?)";
            $params[] = $currentUserId;
        } else {
            $whereClause .= ")";
        }
        
        if ($type) {
            $whereClause .= " AND p.type = ?";
            $params[] = $type;
        }
        
        if ($search) {
            $whereClause .= " AND (p.title LIKE ? OR p.content LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT p.*, u.username, u.avatar, 
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                " . ($currentUserId ? ", (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = " . (int)$currentUserId . ") as is_liked" : ", 0 as is_liked") . "
                " . ($currentUserId ? ", (SELECT COUNT(*) FROM favorites WHERE post_id = p.id AND user_id = " . (int)$currentUserId . ") as is_saved" : ", 0 as is_saved") . "
                FROM posts p
                JOIN users u ON p.user_id = u.id
                $whereClause
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
                
        $params[] = (int)$perPage;
        $params[] = (int)$offset;
        
        $stmt = $this->pdo->prepare($sql);
        // Bind parameters explicitly to avoid type issues with LIMIT
        foreach ($params as $k => $v) {
            $typePDO = is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($k + 1, $v, $typePDO);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function countFeed($type = '', $search = '', $currentUserId = null) {
        $whereClause = "WHERE (p.visibility = 'public'";
        $params = [];
        
        if ($currentUserId) {
            $whereClause .= " OR p.user_id = ?)";
            $params[] = $currentUserId;
        } else {
            $whereClause .= ")";
        }
        
        if ($type) {
            $whereClause .= " AND p.type = ?";
            $params[] = $type;
        }
        
        if ($search) {
            $whereClause .= " AND (p.title LIKE ? OR p.content LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT COUNT(*) FROM posts p $whereClause";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function createPost($userId, $title, $content, $type, $visibility, $image = null) {
        $stmt = $this->pdo->prepare("INSERT INTO posts (user_id, title, content, type, visibility, image) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $title, $content, $type, $visibility, $image]);
    }
    
    public function getPostById($id) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.username, u.avatar FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function updatePost($id, $title, $content, $type, $visibility, $image = null) {
        if ($image) {
            $stmt = $this->pdo->prepare("UPDATE posts SET title = ?, content = ?, type = ?, visibility = ?, image = ? WHERE id = ?");
            return $stmt->execute([$title, $content, $type, $visibility, $image, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE posts SET title = ?, content = ?, type = ?, visibility = ? WHERE id = ?");
            return $stmt->execute([$title, $content, $type, $visibility, $id]);
        }
    }
    
    public function deletePost($id) {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getAllPosts() {
        $stmt = $this->pdo->query("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
        return $stmt->fetchAll();
    }

    public function toggleLike($userId, $postId) {
        $stmt = $this->pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$userId, $postId]);
        if ($stmt->fetch()) {
            $del = $this->pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
            $del->execute([$userId, $postId]);
            return false; // unliked
        } else {
            $ins = $this->pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
            $ins->execute([$userId, $postId]);
            return true; // liked
        }
    }

    public function toggleSave($userId, $postId) {
        $stmt = $this->pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$userId, $postId]);
        if ($stmt->fetch()) {
            $del = $this->pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND post_id = ?");
            $del->execute([$userId, $postId]);
            return false; // unsaved
        } else {
            $ins = $this->pdo->prepare("INSERT INTO favorites (user_id, post_id) VALUES (?, ?)");
            $ins->execute([$userId, $postId]);
            return true; // saved
        }
    }
    
    public function getSavedPosts($userId) {
        $sql = "SELECT p.*, u.username, u.avatar, 1 as is_saved,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = " . (int)$userId . ") as is_liked
                FROM posts p
                JOIN favorites f ON p.id = f.post_id
                JOIN users u ON p.user_id = u.id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addComment($userId, $postId, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
        if ($stmt->execute([$userId, $postId, $content])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function getCommentById($id) {
        $stmt = $this->pdo->prepare("SELECT c.*, u.username, u.avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function deleteComment($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCommentsByPostId($postId) {
        $stmt = $this->pdo->prepare("SELECT c.*, u.username, u.avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at DESC");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }
    public function createNotification($userId, $senderId, $type, $postId = null) {
        if ($userId == $senderId) return; // Don't notify self
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, sender_id, type, post_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $senderId, $type, $postId]);
    }
}
