<?php
/**
 * Product.php: This file defines the Product model, which encapsulates the logic for 
 * managing products in the Query Kicks application.
 *
 * The Product class:
 *  - Provides methods to retrieve, create, update, and delete product records.
 *  - Uses prepared statements with PDO for secure database operations.
 *  - Includes error logging for debugging database interactions.
 *
 * Features:
 *  - `getAll()`: Retrieves all products, ordered by the creation date (newest first).
 *  - `getProductById($id)`: Fetches details of a specific product by its ID.
 *  - `create($data)`: Adds a new product to the database.
 *  - `update($id, $data)`: Updates an existing product's details by its ID.
 *  - `delete($id)`: Deletes a product by its ID.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

class Product {
    private $conn;
    private $table = 'products';

    public $id;
    public $name;
    public $description;
    public $price;
    public $image_url;
    public $stock;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $products;
        } catch (PDOException $e) {
            error_log('Database Error in Product::getAll(): ' . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        /**
         * Creates a new product in the database
         * The required data that needs to be passed in is:
         * - name: The name of the product
         * - description: A short description of the product
         * - price: The price of the product         
         * - stock: The current stock count of the product
         * - image_url: The URL to the product image
         * 
         * Returns true if the product is successfully created, false otherwise
         */
        $query = "INSERT INTO " . $this->table . " 
                (name, description, price, stock, image_url) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['image_url']
        ]);
    }

    public function update($id, $data) {
        $fields = []; // Array to hold field names
        $values = []; // Array to hold field values
        
        // Prepare the SQL query parts for updating the fields, excluding 'id'
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        // Add the 'id' value to the end of the $values array
        $values[] = $id;
        
        // Construct the SQL query
        $query = "UPDATE " . $this->table . " 
                SET " . implode(', ', $fields) . "
                WHERE id = ?";

        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}