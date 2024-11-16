<?php
require_once 'models/ProductModel.php';
require_once 'models/CartModel.php';

class StoreController {
    private $productModel;
    private $cartModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->cartModel = new CartModel();
    }

    public function index() {
        // Fetch products
        $products = $this->productModel->getProducts();
        
        // Get cart items if user is logged in
        $cartItems = [];
        if (isset($_SESSION['user_id'])) {
            $cartItems = $this->cartModel->getCartItems($_SESSION['user_id']);
        }
        
        // Load the main store view with both products and cart data
        require 'views/store/main.php';
    }
}