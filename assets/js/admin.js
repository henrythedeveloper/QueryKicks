class AdminDashboard {
    constructor() {
        this.baseUrl = '/querykicks';
        this.authPath = `${this.baseUrl}/views/auth.php`;
        this.adminPath = `${this.baseUrl}/views/admin.php`;
        this.mainPath = `${this.baseUrl}/views/main.php`;
        
        this.setupEventListeners();
        this.loadDashboardData();
        this.loadProducts();
    }

    setupEventListeners() {
        // Navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => this.handleNavigation(e));
        });

        // Logout
        document.getElementById('logout-btn').addEventListener('click', () => this.handleLogout());

        // Product Management
        document.getElementById('add-product-btn').addEventListener('click', () => this.showProductModal());
        document.getElementById('product-form').addEventListener('submit', (e) => this.handleProductSubmit(e));
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', () => this.hideProductModal());
        });

        // Image preview
        document.getElementById('product-image').addEventListener('change', (e) => this.handleImagePreview(e));
    }

    handleNavigation(e) {
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to clicked item
        e.target.classList.add('active');

        // Hide all views
        document.querySelectorAll('.content-view').forEach(view => {
            view.classList.remove('active');
        });

        // Show selected view
        const viewId = `${e.target.dataset.view}-view`;
        document.getElementById(viewId).classList.add('active');

        // Load view data
        switch(e.target.dataset.view) {
            case 'products':
                this.loadProducts();
                break;
            case 'users':
                this.loadUsers();
                break;
        }
    }

    async loadDashboardData() {
        try {
            const formData = new FormData();
            formData.append('action', 'getDashboardData');

            const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                method: 'POST',
                body: formData
            });

            // Log the raw response text
            const responseText = await response.text();

            try {
                const data = JSON.parse(responseText);
                if (data.success) {
                    document.getElementById('total-products').textContent = data.totalProducts;
                    document.getElementById('total-users').textContent = data.totalUsers;
                } else {
                    alert(data.message || 'Error loading dashboard data');
                }
            } catch (parseError) {
                alert(parseError.message || 'Server returned invalid response');
                alert('Raw Response: ' + responseText);
            }
            
        } catch (error) {
            alert('Error loading dashboard data: ' + error.message);
        }
    }

    async loadProducts() {
        try {
            const formData = new FormData();
            formData.append('action', 'getProducts');

            const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                method: 'POST',
                body: formData
            });

            // Log the raw response text
            const responseText = await response.text();

            try {
                const products = JSON.parse(responseText);
                const productsGrid = document.querySelector('.products-grid');
                
                if (Array.isArray(products)) {
                    productsGrid.innerHTML = products.map(product => this.createProductCard(product)).join('');
                    
                    // Add event listeners to edit/delete buttons
                    document.querySelectorAll('.edit-product-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => this.handleEditProduct(e.target.dataset.id));
                    });
                    document.querySelectorAll('.delete-product-btn').forEach(btn => {
                        btn.addEventListener('click', (e) => this.handleDeleteProduct(e.target.dataset.id));
                    });
                } else {
                    alert('Error loading products: ' + responseText);
                }
            } catch (parseError) {
                alert('Server returned invalid response: ' + parseError.message);
            }
        } catch (error) {
            alert('Error loading products: ' + error.message);
        }
    }


    async loadUsers() {
        try {
            const formData = new FormData();
            formData.append('action', 'getUsers');

            const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                method: 'POST',
                body: formData
            });

            const users = await response.json();
            const usersList = document.querySelector('.users-list');
            
            if (Array.isArray(users)) {
                usersList.innerHTML = this.createUsersTable(users);

                // Add event listeners for any user actions
                document.querySelectorAll('.add-money-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => this.handleAddMoney(e.target.dataset.userId));
                });
            } else {
                alert('Error loading users: ' + users.message);
            }
        } catch (error) {
            alert('Error loading users: ' + error.message);
        }
    }

    createUsersTable(users) {
        return `
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                        <tr>
                            <td>#${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>$${parseFloat(user.money).toFixed(2)}</td>
                            <td>${user.role}</td>
                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                            <td>
                                <button class="add-money-btn" data-user-id="${user.id}">Add Money</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>

            <div id="add-money-modal" class="modal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>Add Money to User Account</h2>
                    <form id="add-money-form">
                        <input type="hidden" id="user-id-input">
                        <div class="form-group">
                            <label for="amount">Amount ($)</label>
                            <input type="number" id="amount" name="amount" step="0.01" required>
                        </div>
                        <button type="submit" class="primary-btn">Add Money</button>
                    </form>
                </div>
            </div>
        `;
    }

    createProductCard(product) {
        // Clean up the image URL by removing escaped slashes
        const imageUrl = product.image_url.replace(/\\/g, '');
        return `
            <div class="product-card">
                <img src="${this.baseUrl}/${imageUrl}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p>$${product.price}</p>
                <p>Stock: ${product.stock}</p>
                <div class="card-actions">
                    <button class="edit-product-btn primary-btn" data-id="${product.id}">Edit</button>
                    <button class="delete-product-btn secondary-btn" data-id="${product.id}">Delete</button>
                </div>
            </div>
        `;
    }

    showProductModal(product = null) {
        const modal = document.getElementById('product-modal');
        const form = document.getElementById('product-form');
        const modalTitle = document.getElementById('modal-title');
        const preview = document.getElementById('image-preview');
    
        if (product) {
            modalTitle.textContent = 'Edit Product';
            form.elements['product-id'].value = product.id;
            form.elements['name'].value = product.name;
            form.elements['description'].value = product.description;
            form.elements['price'].value = product.price;
            form.elements['stock'].value = product.stock;
            
            // Clean up the image URL by removing escaped slashes
            const imageUrl = product.image_url.replace(/\\/g, '');
            preview.innerHTML = `
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="${this.baseUrl}/${imageUrl}" alt="${product.name}">
                </div>
            `;
        } else {
            modalTitle.textContent = 'Add New Product';
            form.reset();
            form.elements['product-id'].value = '';
            preview.innerHTML = '';
        }
    
        modal.style.display = 'block';
    }

    hideProductModal() {
        const modal = document.getElementById('product-modal');
        modal.style.display = 'none';
    }

    handleImagePreview(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('image-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    }

    async handleAddMoney(userId) {
        const modal = document.getElementById('add-money-modal');
        const form = document.getElementById('add-money-form');
        const userIdInput = document.getElementById('user-id-input');
        
        userIdInput.value = userId;
        modal.style.display = 'block';

        form.onsubmit = async (e) => {
            e.preventDefault();
            const amount = document.getElementById('amount').value;

            try {
                const formData = new FormData();
                formData.append('action', 'addUserMoney');
                formData.append('userId', userId);
                formData.append('amount', amount);

                const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    modal.style.display = 'none';
                    this.loadUsers(); // Refresh the users table
                } else {
                    alert(result.message || 'Error adding money');
                }
            } catch (error) {
                alert('Error adding money: ' + error.message);
            }
        };
    }

    async handleProductSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        // Add action to formData
        const isEdit = form.elements['product-id'].value;
        const action = isEdit ? 'updateProduct' : 'addProduct';
        formData.append('action', action);
    
        try {
            const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                method: 'POST',
                body: formData
            });
    
            try {
                const result = JSON.parse(responseText);
    
                if (result.success) {
                    alert(result.message);
                    this.hideProductModal();
                    this.loadProducts();
                } else {
                    alert(result.message || 'Error saving product');
                }
            } catch (parseError) {
                alert('Server returned invalid response');
            }
        } catch (error) {
            alert('Error saving product: ' + error.message);
        }
    }

    async handleEditProduct(productId) {
        try {
            const formData = new FormData();
            formData.append('action', 'getProduct');
            formData.append('id', productId);
    
            const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.success) {
                this.showProductModal(result.product);
            } else {
                alert(result.message || 'Error loading product');
            }
        } catch (error) {
            alert('Error loading product: ' + error.message);
        }
    }

    async handleDeleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
            try {
                const formData = new FormData();
                formData.append('action', 'deleteProduct');
                formData.append('id', productId);
    
                const response = await fetch(`${this.baseUrl}/controllers/AdminController.php`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                if (result.success) {
                    this.loadProducts();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Error deleting product: ' + error.message);
            }
        }
    }

    async handleLogout() {
        try {
            const response = await fetch(`${this.baseUrl}/controllers/AuthController.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'logout'
                })
            });
            
            try {
                const result = JSON.parse(responseText);

                if (result.success) {
                    // Use redirectUrl from response or fallback to default path
                    const redirectPath = result.redirectUrl || `${this.baseUrl}/views/auth.php`;
                    window.location.href = redirectPath;
                } else {
                    alert(result.message || 'Error logging out');
                }
            } catch (parseError) {
                alert('Server returned invalid response');
                window.location.href = `${this.baseUrl}/views/auth.php`;
            }
        } catch (error) {
            alert('Error logging out');
            window.location.href = `${this.baseUrl}/views/auth.php`;
        }
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.adminDashboard = new AdminDashboard();
});