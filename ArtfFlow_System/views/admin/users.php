<?php
// ============================================================
// ArtFlow - Admin: User Management (Enhanced CRUD)
// ============================================================
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/UserModel.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$userModel = new UserModel();
$users = $userModel->getAllUsers();

$pageTitle = 'User Management — ArtFlow Admin';
$topbarTitle = '🛡️ User Control Center';
require_once __DIR__ . '/../partials/head.php';
?>

<div class="app-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <?php require __DIR__ . '/../partials/topbar.php'; ?>

        <div class="feed-container">
            <?php $flash = getFlash(); if ($flash): ?>
                <div class="flash flash-<?= $flash['type'] ?>">
                    <?= sanitize($flash['message']) ?>
                </div>
            <?php endif; ?>

            <!-- Add User Section -->
            <div class="card" style="margin-bottom: 30px; border-left: 5px solid var(--primary);">
                <div class="card-body">
                    <h3 style="font-family: 'Cinzel', serif; margin-bottom: 20px;">+ ENROLL NEW ARTIST</h3>
                    <form method="POST" action="<?= BASE_URL ?>/controllers/AdminController.php?action=create_user" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                        <div class="form-group" style="margin:0;">
                            <label style="font-size: 0.7rem;">USERNAME</label>
                            <input type="text" name="username" class="form-control" placeholder="artist_name" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size: 0.7rem;">EMAIL</label>
                            <input type="email" name="email" class="form-control" placeholder="email@artflow.com" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size: 0.7rem;">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size: 0.7rem;">ROLE</label>
                            <select name="role" class="form-control">
                                <option value="user">USER</option>
                                <option value="admin">ADMIN</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-post" style="height: 50px;">CREATE ACCOUNT</button>
                    </form>
                </div>
            </div>

            <!-- User List Section -->
            <div class="card">
                <div class="card-body">
                    <h3 style="font-family: 'Cinzel', serif; margin-bottom: 20px;">EXISTING MEMBERS</h3>
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>USER</th>
                                    <th>EMAIL</th>
                                    <th>ROLE</th>
                                    <th>JOINED</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <img src="<?= avatarUrl($user['avatar']) ?>" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                                <span style="font-weight: 700;">@<?= sanitize($user['username']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= sanitize($user['email']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $user['role'] ?>"><?= strtoupper($user['role']) ?></span>
                                        </td>
                                        <td style="font-size: 0.8rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <div style="display: flex; gap: 10px;">
                                                <button class="btn-edit-user" 
                                                        data-id="<?= $user['id'] ?>" 
                                                        data-username="<?= sanitize($user['username']) ?>"
                                                        data-email="<?= sanitize($user['email']) ?>"
                                                        data-role="<?= $user['role'] ?>"
                                                        data-bio="<?= sanitize($user['bio']) ?>"
                                                        style="background: none; border: 1px solid var(--primary); color: var(--primary); padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; cursor: pointer;">
                                                    EDIT
                                                </button>
                                                <?php if ($user['id'] != currentUserId()): ?>
                                                <form method="POST" action="<?= BASE_URL ?>/controllers/AdminController.php?action=delete_user" onsubmit="return confirm('Erase this artist from the system?');">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <button type="submit" style="background: none; border: 1px solid var(--rose); color: var(--rose); padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; cursor: pointer;">
                                                        DELETE
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal (Hidden) -->
<div id="editModal" class="modal-overlay" style="display: none;">
    <div class="modal-content card" style="max-width: 500px; width: 90%;">
        <div class="card-body">
            <h3 style="font-family: 'Cinzel', serif; margin-bottom: 20px;">EDIT ARTIST PROFILE</h3>
            <form method="POST" action="<?= BASE_URL ?>/controllers/AdminController.php?action=update_user">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-group">
                    <label>USERNAME</label>
                    <input type="text" name="username" id="edit-username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>EMAIL</label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>ROLE</label>
                    <select name="role" id="edit-role" class="form-control">
                        <option value="user">USER</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>BIO</label>
                    <textarea name="bio" id="edit-bio" class="form-control" rows="3"></textarea>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn-post" style="flex: 1;">SAVE CHANGES</button>
                    <button type="button" onclick="closeModal()" class="btn-edit" style="flex: 1; text-align: center;">CANCEL</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.admin-table th {
    text-align: left;
    padding: 15px;
    font-size: 0.75rem;
    color: var(--text-muted);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    text-transform: uppercase;
}
.admin-table td {
    padding: 15px;
    border-bottom: 1px solid rgba(0,0,0,0.02);
}
.badge {
    padding: 4px 10px;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 700;
}
.badge-admin { background: rgba(165, 225, 125, 0.2); color: var(--primary); }
.badge-user { background: rgba(0,0,0,0.05); color: var(--text-muted); }

.modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
</style>

<script>
document.querySelectorAll('.btn-edit-user').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit-id').value = btn.dataset.id;
        document.getElementById('edit-username').value = btn.dataset.username;
        document.getElementById('edit-email').value = btn.dataset.email;
        document.getElementById('edit-role').value = btn.dataset.role;
        document.getElementById('edit-bio').value = btn.dataset.bio;
        document.getElementById('editModal').style.display = 'flex';
    });
});

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
