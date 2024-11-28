-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 28, 2024 at 06:20 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `query_kicks`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`) VALUES
(1, 4, '2024-11-27 02:22:31'),
(2, 5, '2024-11-28 03:11:25'),
(3, 6, '2024-11-28 04:24:33');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock`, `created_at`) VALUES
(1, 'Type 1 - Shoe 1', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-1.webp', 0, '2024-11-20 00:54:49'),
(3, 'Type 1 - Shoe 3', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-3.webp', 0, '2024-11-20 00:54:49'),
(4, 'Type 1 - Shoe 4', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-4.webp', 0, '2024-11-20 00:54:49'),
(5, 'Type 1 - Shoe 5', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-5.webp', 11, '2024-11-20 00:54:49'),
(6, 'Type 1 - Shoe 6', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-6.webp', 0, '2024-11-20 00:54:49'),
(7, 'Type 1 - Shoe 7', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-7.webp', 11, '2024-11-20 00:54:49'),
(8, 'Type 1 - Shoe 8', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-8.webp', 2, '2024-11-20 00:54:49'),
(9, 'Type 1 - Shoe 9', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-9.webp', 11, '2024-11-20 00:54:49'),
(10, 'Type 1 - Shoe 10', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-10.webp', 10, '2024-11-20 00:54:49'),
(11, 'Type 1 - Shoe 11', 'A comfortable and stylish shoe.', 99.99, 'assets/images/shoes/type1-11.webp', 11, '2024-11-20 00:54:49'),
(12, 'test', 'test', 1000.00, 'assets/images/shoes/1.webp', 100, '2024-11-26 16:42:48'),
(13, 'ELF OG', 'ELF OG', 1000.00, 'assets/images/shoes/5-2.webp', 12, '2024-11-26 16:43:20'),
(14, 'Yolo', 'yolo', 999999.00, 'assets/images/shoes/3.webp', 1, '2024-11-26 16:43:44'),
(15, 'OG', 'Og shoe', 600.00, 'assets/images/shoes/Pixel-Art-Sneaker-6.webp', 30, '2024-11-27 21:50:38'),
(16, 'Shoooooo', 'shooooo', 20.00, 'assets/images/shoes/6.webp', 18, '2024-11-28 04:29:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `money` decimal(10,2) NOT NULL DEFAULT 100.00,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `money`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@querykicks.com', '$2y$10$PJaKKEsB6aw883deAyNrSONEnlOy8PBmBeQbQy8GiksUyXT1emulK', 1000.00, 'admin', '2024-11-20 00:54:49'),
(2, 'test6', 'test6@t.com', '$2y$10$Gz2HsncTkzaRCac3kMt3Uugql2d8HgYOrnJ1K.XcbsQqtyO0UI3A2', 200.00, 'user', '2024-11-20 22:56:25'),
(3, 'test2', '2@2.com', '$2y$10$Y3LTopiE176TjwTKYldz7OpK6A5LJwPdHg1LhiNVg82ZFE.OwXmMW', 10100.00, 'user', '2024-11-22 22:09:31'),
(4, 'Henry', 'henry@h.com', '$2y$10$1eGQT0nrne72HfglF2nDE.JEHN5aiWEIRF0ATp7E2jxDBgAPIHljy', 142300.30, 'user', '2024-11-26 17:02:23'),
(5, 'David Le', 'rikule1234@gmail.com', '$2y$10$m11CmDHDwgFjSQ.POrN/0.ro7r6it9qRC0UBe.P3jS5IEjLsTBnrG', 18972599.30, 'user', '2024-11-28 03:09:53'),
(6, 'final test', 'final@f.com', '$2y$10$qhG7EQbChRtbV3t3Ktm4BeB1rnj6kceY9Q9wFQ9NKELRXhqrikGf6', 100509.38, 'user', '2024-11-28 04:24:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
