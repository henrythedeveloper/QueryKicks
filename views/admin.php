<?php
/**
 * admin.php: This file serves as the admin dashboard view for the Query Kicks application. 
 * It provides an interface for administrators to manage products, users, and view application statistics.
 *
 * Features:
 *  - **Sidebar Navigation**: Allows navigation between Dashboard, Products, and Users views.
 *  - **Dashboard View**: Displays statistics such as total products and users.
 *  - **Products View**: Enables administrators to manage the product inventory with options to add, edit, or delete products.
 *  - **Users View**: Provides tools to manage user accounts and view user information.
 *  - **Add/Edit Product Modal**: A modal form for creating or updating product information, including image upload functionality.
 *  - **Logout**: Allows administrators to securely log out of the admin panel.
 *
 * Linked Assets:
 *  - `/querykicks/assets/css/admin.css`: Stylesheet for the admin dashboard layout and components.
 *  - `/querykicks/assets/js/admin.js`: JavaScript for handling admin interactions like loading data, tab switching, and form submission.
 *  - `/querykicks/assets/js/notification.js`: Manages notifications or feedback for admin actions.
 *
 * Data Dependencies:
 *  - Relies on session data to validate the admin's role and display personalized information.
 *  - Dynamic content is loaded into views using AJAX calls from `admin.js`.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /querykicks/controllers/AuthController.php');
    exit();
}

error_log('Admin view loaded. Session data: ' . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QueryKicks - Admin Dashboard</title>
    <link rel="stylesheet" href="/querykicks/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>QueryKicks</h2>
                <p>Admin Panel</p>
            </div>
            <nav class="nav-menu">
                <button class="nav-item active" data-view="dashboard">
                    Dashboard
                </button>
                <button class="nav-item" data-view="products">
                    Products
                </button>
                <button class="nav-item" data-view="users">
                    Users
                </button>
            </nav>
            <div class="admin-info">
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                <button id="logout-btn">Logout</button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dashboard View -->
            <div id="dashboard-view" class="content-view active">
                <h2>Dashboard Overview</h2>
                <div class="dashboard-cards">
                    <div class="card">
                        <h3>Total Products</h3>
                        <p id="total-products">Loading...</p>
                    </div>
                    <div class="card">
                        <h3>Total Users</h3>
                        <p id="total-users">Loading...</p>
                    </div>
                </div>
            </div>

            <!-- Products View -->
            <div id="products-view" class="content-view">
                <div class="view-header">
                    <h2>Manage Products</h2>
                    <button id="add-product-btn" class="primary-btn">Add New Product</button>
                </div>
                <div class="products-grid">
                    <!-- Products will be loaded here -->
                </div>
            </div>

            <!-- Users View -->
            <div id="users-view" class="content-view">
                <h2>User Management</h2>
                <div class="users-list">
                    <!-- Users will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->

    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="modal-title">Add New Product</h2>
            <form id="product-form" enctype="multipart/form-data">
                <input type="hidden" id="product-id" name="id">
                <div class="form-group">
                    <label for="product-name">Name</label>
                    <input type="text" id="product-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="product-description">Description</label>
                    <textarea id="product-description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="product-price">Price</label>
                    <input type="number" id="product-price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="product-stock">Stock</label>
                    <input type="number" id="product-stock" name="stock" required>
                </div>
                <div class="form-group">
                    <label for="product-image">Product Image</label>
                    <div class="image-upload-container">
                        <input type="file" id="product-image" name="image" accept="image/*">
                        <div id="image-preview"></div>
                    </div>
                </div>
                <div class="form-buttons">
                    <button type="button" class="secondary-btn close-modal">Cancel</button>
                    <button type="submit" class="primary-btn">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/querykicks/assets/js/admin.js"></script>
    <script src="/querykicks/assets/js/notification.js"></script>
</body>
</html>