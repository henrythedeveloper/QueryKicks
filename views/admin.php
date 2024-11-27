<?php
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
                <button class="nav-item" data-view="orders">
                    Orders
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
                        <h3>Total Orders</h3>
                        <p id="total-orders">Loading...</p>
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

            <!-- Orders View -->
            <div id="orders-view" class="content-view">
                <h2>Order History</h2>
                <div class="orders-list">
                    <!-- Orders will be loaded here -->
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
                <input type="hidden" id="product-id">
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
</body>
</html>