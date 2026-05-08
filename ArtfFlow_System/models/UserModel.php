<?php
// models/UserModel.php
class UserModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createUser($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }
    
    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function updateUserRole($id, $role) {
        $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    public function updateUser($id, $username, $email, $role, $bio = null) {
        $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, bio = ? WHERE id = ?");
        return $stmt->execute([$username, $email, $role, $bio, $id]);
    }
    
    // Notifications
    public function unreadNotifications($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    public function getNotifications($userId, $limit = 10) {
        $stmt = $this->pdo->prepare("SELECT n.*, u.username, u.avatar 
                                     FROM notifications n 
                                     JOIN users u ON n.sender_id = u.id 
                                     WHERE n.user_id = ? 
                                     ORDER BY n.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markNotificationsRead($userId) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function toggleFollow($followerId, $followingId) {
        $stmt = $this->pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$followerId, $followingId]);
        if ($stmt->fetch()) {
            $del = $this->pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
            $del->execute([$followerId, $followingId]);
            return false; // unfollowed
        } else {
            $ins = $this->pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
            $ins->execute([$followerId, $followingId]);
            
            // Send notification
            $notif = $this->pdo->prepare("INSERT INTO notifications (user_id, sender_id, type) VALUES (?, ?, 'follow')");
            $notif->execute([$followingId, $followerId]);

            return true; // followed
        }
    }

    public function isFollowing($followerId, $followingId) {
        $stmt = $this->pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$followerId, $followingId]);
        return (bool)$stmt->fetch();
    }

    public function getFollowStats($userId) {
        $followers = $this->pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
        $followers->execute([$userId]);
        $following = $this->pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
        $following->execute([$userId]);
        return [
            'followers' => $followers->fetchColumn(),
            'following' => $following->fetchColumn()
        ];
    }
    public function getFeaturedArtists($limit = 3) {
        $stmt = $this->pdo->prepare("SELECT u.*, (SELECT COUNT(*) FROM follows WHERE following_id = u.id) as followers_count 
                                     FROM users u 
                                     ORDER BY followers_count DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
