<?php
include_once('header.php');
?>
<style>
:root {
    --primary-color: #1a78c2;
    --primary-hover: #1565a7;
    --secondary-color: #64748b;
    --danger-color: #dc2626;
    --success-color: #059669;
    --background-color: #f8fafc;
    --card-background: #ffffff;
    --border-radius: 0.75rem;
    --transition: all 0.3s ease;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

body {
    background-color: var(--background-color);
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    padding: 2rem 0;
    margin-bottom: 2rem;
    color: white;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    box-shadow: var(--shadow);
}

/* Add User Button */
.btn-primary {
    background: var(--primary-color);
    border: none;
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: var(--transition);
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 120, 194, 0.2);
}

/* User Cards */
#allUserList {
    margin: 0 -0.75rem;
}

.user-card {
    background: var(--card-background);
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    transition: var(--transition);
    height: 100%;
    overflow: hidden;
}

.user-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.user-card .card-header {
    background: none;
    border: none;
    padding: 1.5rem 1.5rem 0;
}

.user-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: var(--shadow-sm);
}

.user-role {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-admin { background: #fee2e2; color: #dc2626; }
.role-author { background: #e0e7ff; color: #4f46e5; }
.role-student { background: #d1fae5; color: #059669; }

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border: none;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    padding: 1.5rem;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-body {
    padding: 2rem;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    transition: var(--transition);
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(26, 120, 194, 0.1);
}

.btn-group-user-actions {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.5rem;
    border-radius: 0.5rem;
    background: white;
    border: none;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.btn-edit { color: var(--primary-color); }
.btn-delete { color: var(--danger-color); }
</style>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">User Management</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-2"></i>Add User
        </button>
    </div>

    <div class="row g-4" id="allUserList">
        <!-- Users will be loaded dynamically -->
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select role</option>
                            <option value="1">Admin</option>
                            <option value="2">Author</option>
                            <option value="3">Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="profileImage" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="profileImage" name="profileImage" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" aria-labelledby="viewProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProfileModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserModal" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="viewUserName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="viewUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="viewUserEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="viewUserEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="viewUserRole" class="form-label">Role</label>
                        <select class="form-select" id="viewUserRole" name="role" required>
                            <option value="">Select role</option>
                            <option value="1">Admin</option>
                            <option value="2">Author</option>
                            <option value="3">Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="viewUserProfileImage" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="viewUserProfileImage" name="viewUserProfileImage" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editUserModal" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update User
                </button>
            </div>
        </div>
    </div>
</div>

<script src="js/user.js"></script>