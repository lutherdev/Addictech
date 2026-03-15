-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 02:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `addictech_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(4, 5, 7, 2, '2026-03-15 09:28:12', '2026-03-15 09:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` enum('cod','gcash','bank_transfer','credit_card') NOT NULL,
  `delivery_method` enum('standard','express','pickup') NOT NULL,
  `delivery_address` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `status`, `payment_method`, `delivery_method`, `delivery_address`, `subtotal`, `shipping_fee`, `total`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 5, 'ORD-69B64A051ED34', 'pending', 'cod', 'express', 'Monkhe', 5890.00, 250.00, 6140.00, 'unpaid', '', '2026-03-15 05:56:21', '2026-03-15 05:56:21'),
(2, 5, 'ORD-69B64B67EBF48', 'pending', 'cod', 'express', 'Monkhe', 5890.00, 250.00, 6140.00, 'unpaid', '', '2026-03-15 06:02:15', '2026-03-15 06:02:15'),
(3, 5, 'ORD-69B667DD08EAC', 'pending', 'gcash', 'standard', 'Monkhe', 3490.00, 150.00, 3640.00, 'unpaid', '', '2026-03-15 08:03:41', '2026-03-15 08:03:41'),
(4, 5, 'ORD-69B6690C852DF', 'pending', 'credit_card', 'standard', 'Monkhe', 2850.00, 150.00, 3000.00, 'unpaid', '', '2026-03-15 08:08:44', '2026-03-15 08:08:44'),
(5, 5, 'ORD-69B6696F173FE', 'pending', 'credit_card', 'standard', 'Monkhe', 5990.00, 150.00, 6140.00, 'unpaid', '', '2026-03-15 08:10:23', '2026-03-15 08:10:23'),
(6, 5, 'ORD-69B66C4ED3241', 'pending', 'credit_card', 'pickup', 'Monkhe', 4350.00, 0.00, 4350.00, 'unpaid', '', '2026-03-15 08:22:38', '2026-03-15 08:22:38'),
(7, 5, 'ORD-69B66F4D14AB0', 'processing', 'credit_card', 'pickup', 'Monkhe', 19190.00, 0.00, 19190.00, 'unpaid', '', '2026-03-15 08:35:25', '2026-03-15 09:08:20');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `variant` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `variant`, `price`, `quantity`, `subtotal`) VALUES
(1, 2, 1, 'MK Pro X', 'Cherry MX Red', 5890.00, 1, 5890.00),
(2, 3, 2, 'MK Slim 60%', 'Low Profile', 3490.00, 1, 3490.00),
(3, 4, 3, 'Viper V2', 'Standard', 2850.00, 1, 2850.00),
(4, 5, 5, 'Void RGB', 'Wireless', 5990.00, 1, 5990.00),
(5, 6, 11, 'C920 HD Pro', '1080p', 4350.00, 1, 4350.00),
(6, 7, 10, 'Logitech Z200', 'Stereo', 2390.00, 1, 2390.00),
(7, 7, 9, 'Audioengine A2', 'Powered', 16800.00, 1, 16800.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category` enum('keyboard','mouse','headset','monitor','speaker','cam') NOT NULL,
  `name` varchar(150) NOT NULL,
  `variant` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category`, `name`, `variant`, `description`, `price`, `stock`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'keyboard', 'MK Pro X', 'Cherry MX Red', 'Mechanical full-size keyboard with per-key RGB lighting, tactile switches, and a durable aluminum top frame built for long gaming sessions.', 5890.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-14 22:02:16'),
(2, 'keyboard', 'MK Slim 60%', 'Low Profile', 'Ultra-compact 60% layout with low-profile switches. Perfect for minimalist desk setups and on-the-go use.', 3490.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-15 00:03:41'),
(3, 'mouse', 'Viper V2', 'Standard', 'Lightweight ambidextrous gaming mouse with a precision optical sensor and up to 20,000 DPI resolution.', 2850.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-15 00:08:45'),
(4, 'mouse', 'Basilisk X', 'Ergonomic', 'Ergonomic right-handed mouse with customizable scroll resistance and 6 programmable buttons for power users.', 4200.00, 0, NULL, 'active', '2026-03-13 10:13:46', NULL),
(5, 'headset', 'Void RGB', 'Wireless', 'Surround sound USB headset with custom-tuned 50mm drivers and long-range wireless for unrestricted play.', 5990.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-15 00:10:23'),
(6, 'headset', 'Cloud II', 'Wired', 'Award-winning gaming headset with memory foam ear cushions and detachable noise-cancelling microphone.', 4490.00, 1, NULL, 'active', '2026-03-13 10:13:46', NULL),
(7, 'monitor', 'UltraSharp 27', '4K UHD', '27-inch 4K IPS display with factory-calibrated colors, USB-C connectivity, and ultra-slim bezels for immersive work.', 21900.00, 1, NULL, 'active', '2026-03-13 10:13:46', NULL),
(8, 'monitor', 'Odyssey G5', '1440p 165Hz', '27-inch 1440p curved gaming monitor with a 165Hz refresh rate and 1ms response time for competitive play.', 14500.00, 0, NULL, 'active', '2026-03-13 10:13:46', NULL),
(9, 'speaker', 'Audioengine A2', 'Powered', 'Compact powered desktop speakers with a built-in amplifier delivering audiophile-grade stereo sound from a small footprint.', 16800.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-15 00:35:25'),
(10, 'speaker', 'Logitech Z200', 'Stereo', 'Affordable stereo speakers with clear, room-filling sound and easy-access volume control on the front panel.', 2390.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-15 00:35:25'),
(11, 'cam', 'C920 HD Pro', '1080p', 'Full HD 1080p webcam with dual built-in stereo mics and automatic low-light correction for crisp video calls.', 4350.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-15 00:22:38'),
(12, 'cam', 'StreamCam', 'USB-C 60fps', 'Premium USB-C streaming camera with smooth 60fps 1080p video and intelligent auto-focus that tracks your face.', 9290.00, 0, NULL, 'active', '2026-03-13 10:13:46', '2026-03-13 10:14:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `role`, `status`, `address`, `city`, `postal_code`, `country`, `created_at`, `updated_at`) VALUES
(5, 'DEVTURD@GMAIL.COM', '$2y$12$/QDpYJixbLC4QnRgj6K.suWsCjOgt9YtB2OTX5fvfDpWXxFX67BsC', 'LUTHER', 'SAMBELI', '096123', 'customer', 'active', 'Monkhe', 'Cainta', '1900', 'Philippines', '2026-03-13 04:46:21', '2026-03-15 05:32:31');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
