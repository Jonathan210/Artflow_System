<?php
// controllers/AuthController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/UserModel.php';

$action = $_GET['action'] ?? '';
$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        setFlash('error', 'Invalid security token. Please try again.');
        $redirect = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php');
        header('Location: ' . $redirect);
        exit;
    }

    if ($action === 'login') {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            setFlash('error', 'Please fill in all fields.');
            header('Location: ' . BASE_URL . '/views/auth/login.php');
            exit;
        }

        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            setFlash('success', 'Welcome back, ' . $user['username'] . '!');
            
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/views/admin/dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit;
        } else {
            setFlash('error', 'Invalid email or password.');
            header('Location: ' . BASE_URL . '/views/auth/login.php');
            exit;
        }
    } elseif ($action === 'register') {
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            setFlash('error', 'Please fill in all required fields.');
            header('Location: ' . BASE_URL . '/views/auth/register.php');
            exit;
        }

        if ($password !== $password_confirm) {
            setFlash('error', 'Passwords do not match.');
            header('Location: ' . BASE_URL . '/views/auth/register.php');
            exit;
        }

        if ($userModel->findByEmail($email)) {
            setFlash('error', 'Email is already taken.');
            header('Location: ' . BASE_URL . '/views/auth/register.php');
            exit;
        }

        if ($userModel->createUser($username, $email, $password)) {
            setFlash('success', 'Account created successfully! Please sign in to your studio.');
            header('Location: ' . BASE_URL . '/views/auth/login.php');
            exit;
        } else {
            setFlash('error', 'Registration failed. Please try again.');
            header('Location: ' . BASE_URL . '/views/auth/register.php');
            exit;
        }
    }
} elseif ($action === 'logout') {
    session_destroy();
    session_start();
    setFlash('success', 'You have been logged out.');
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
} else {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
