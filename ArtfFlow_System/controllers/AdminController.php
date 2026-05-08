<?php
// controllers/AdminController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Unauthorized access.');
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$action = $_GET['action'] ?? '';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'delete_user') {
        $id = $_POST['id'] ?? 0;
        if ($id == currentUserId()) {
            setFlash('error', 'You cannot delete yourself.');
        } else {
            $userModel->deleteUser($id);
            setFlash('success', 'User deleted successfully.');
        }
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    } elseif ($action === 'create_user') {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if ($userModel->findByEmail($email)) {
            setFlash('error', 'Email already exists.');
        } else {
            $userModel->createUser($username, $email, $password);
            // New users are created with 'user' role by default in createUser, so we update it
            $newUser = $userModel->findByEmail($email);
            $userModel->updateUserRole($newUser['id'], $role);
            setFlash('success', 'User created successfully.');
        }
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    } elseif ($action === 'update_user') {
        $id = $_POST['id'] ?? 0;
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $bio = sanitize($_POST['bio'] ?? '');

        if ($id == currentUserId() && $role !== 'admin') {
            setFlash('error', 'You cannot demote yourself.');
        } else {
            $userModel->updateUser($id, $username, $email, $role, $bio);
            setFlash('success', 'User updated successfully.');
        }
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    } elseif ($action === 'update_role') {
        $id = $_POST['id'] ?? 0;
        $role = $_POST['role'] ?? 'user';
        if ($id == currentUserId()) {
            setFlash('error', 'You cannot change your own role.');
        } else {
            $userModel->updateUserRole($id, $role);
            setFlash('success', 'User role updated.');
        }
        header('Location: ' . BASE_URL . '/views/admin/users.php');
        exit;
    }
}
