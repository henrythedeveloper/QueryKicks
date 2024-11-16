-- Create the database
CREATE DATABASE query_kicks;

-- Use the database
USE query_kicks;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    money DECIMAL(10,2) DEFAULT 100.00 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Create the products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the cart table (optional, for user-specific cart)
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the cart_items table (optional, for storing items in the cart)
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create the orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the order_items table (to store products in an order)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Example data for products table
INSERT INTO products (name, description, price, image_url, stock) VALUES
('Type 1 - Shoe 1', 'A comfortable and stylish shoe.', 99.99, '/Querykicks/assets/shoes/type1/type1-1.webp', 10),
('Type 1 - Shoe 2', 'A comfortable and stylish shoe.', 99.99, '/Querykicks/assets/shoes/type1/type1-2.webp', 8),
('Type 1 - Shoe 3', 'A comfortable and stylish shoe.', 99.99, '/Querykicks/assets/shoes/type1/type1-3.webp', 12);

-- Example data for users table
INSERT INTO users (name, email, password) VALUES
('John Doe', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuvwx'), -- Replace with a hashed password
('Jane Smith', 'jane@example.com', '$2y$10$abcdefghijklmnopqrstuvwx'); -- Replace with a hashed password
