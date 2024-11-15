<?php
require_once 'models/ProductModel.php';

class StoreController {
    public function index() {
        // Fetch products from the ProductModel
        $productModel = new ProductModel();
        $products = $productModel->getProducts();

        // Load the main store view
        require 'views/store/main.php';
    }
}
