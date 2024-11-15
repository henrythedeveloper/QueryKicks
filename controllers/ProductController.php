<?php
require_once 'models/ProductModel.php';

class ProductController {
    public function index() {
        $productModel = new ProductModel();
        $products = $productModel->getProducts();
        require 'views/store/main.php';
    }
}
?>
