-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 31, 2025 at 07:52 AM
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
-- Database: `car_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','completed','canceled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `car_id`, `user_id`, `start_date`, `end_date`, `total_price`, `booking_status`, `payment_status`, `created_at`, `updated_at`) VALUES
(9, 6, 1, '2025-03-31', '2025-04-01', 400000.00, 'completed', 'paid', '2025-03-31 09:49:43', '2025-03-31 12:42:32'),
(10, 5, 1, '2025-03-31', '2025-04-01', 200000.00, 'pending', 'pending', '2025-03-31 10:30:36', '2025-03-31 10:30:36'),
(11, 3, 1, '2025-03-31', '2025-04-01', 200000.00, 'completed', 'paid', '2025-03-31 10:57:04', '2025-03-31 12:17:09');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `car_type` enum('electric','gasoline','diesel') NOT NULL,
  `seats` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `address` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','rented','pending','hidden','approved','unapproved','rejected') DEFAULT 'unapproved',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `owner_id`, `brand`, `model`, `year`, `car_type`, `seats`, `price_per_day`, `address`, `latitude`, `longitude`, `description`, `status`, `created_at`, `updated_at`) VALUES
(3, 5, 'Toyota', 'Camry', 2024, 'gasoline', 4, 100000.00, 'Hutech khu E', 10.76262200, 106.66017200, 'Comfortable sedan', 'approved', '2025-03-31 08:47:13', '2025-03-31 08:48:32'),
(4, 5, 'Tesla', 'Modal 3', 2023, 'electric', 4, 100000.00, 'Hutech Khu AB', 10.76262200, 106.66017200, 'Electric vehicle with autopilot', 'approved', '2025-03-31 08:51:27', '2025-03-31 09:00:48'),
(5, 5, 'Honda', 'Civic', 2023, 'diesel', 7, 100000.00, 'Võ văn kiệt', 10.75804240, 106.69163665, 'Reliable and fuel-efficient sedan', 'approved', '2025-03-31 08:59:54', '2025-03-31 09:00:03'),
(6, 5, 'Ford', 'Mustang', 2023, 'gasoline', 7, 200000.00, '12 Nguyễn Bỉnh Khiêm', 10.76262200, 106.66017200, 'Xe đẹp thế anh zai', 'approved', '2025-03-31 09:28:22', '2025-03-31 09:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `car_images`
--

CREATE TABLE `car_images` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_images`
--

INSERT INTO `car_images` (`id`, `car_id`, `image_path`, `is_primary`, `created_at`) VALUES
(3, 3, 'public/uploads/cars/3/67e9f4213da3f.png', 1, '2025-03-31 08:47:13'),
(4, 4, 'public/uploads/cars/4/67e9f51f777a0.png', 1, '2025-03-31 08:51:27'),
(5, 5, 'public/uploads/cars/67e9f71a157bb.png', 1, '2025-03-31 08:59:54'),
(6, 6, 'public/uploads/cars/67e9fdc673abc.png', 1, '2025-03-31 09:28:22');

-- --------------------------------------------------------

--
-- Table structure for table `car_owner_contracts`
--

CREATE TABLE `car_owner_contracts` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `contract_fee` decimal(10,2) NOT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('MoMo') DEFAULT 'MoMo',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, 11, 200000.00, 'MoMo', '1743394416_11', 'pending', '2025-03-31 11:13:37', '2025-03-31 11:13:37'),
(4, 11, 200000.00, 'MoMo', '1743398029_11', 'paid', '2025-03-31 12:13:52', '2025-03-31 12:14:10');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `code`, `discount_percentage`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DISCOUNT10', 10.00, '2024-10-01 00:00:00', '2024-10-31 23:59:59', 'active', '2025-03-28 22:47:40', '2025-03-28 22:47:40');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('regular','owner','admin') DEFAULT 'regular',
  `status` enum('active','blocked') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `fullname`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'toan123qn', 'toan123qn@gmail.com', '$2y$10$hat3wYMaxxpVKVSaFIfpP.cSwccH7gc.jBfEo1Piae09B3ETixTHi', 'Võ Hữu Toàn', '0393303222', '12', 'regular', 'active', '2025-03-28 22:40:21', '2025-03-29 13:02:49'),
(5, 'toan2', 'toan2@gmail.com', '$2y$10$zZOFVOeoMzaqz3FLHtN7yeRlnlBvYdz0qyh.Erdbq7Gf2DpuexTgi', 'Chủ toàn', '0393303024', '12 Hà huy giáp', 'owner', 'active', '2025-03-31 08:35:05', '2025-03-31 08:35:05'),
(6, 'admin', 'admin@gmail.com', '$2y$10$TTRBJMObCZLPOrg2052cQOHnOrr94OqmYu6nqueFNkMdf6HiIGaaa', 'Võ Hữu Toàn', '0393303025', 'TP. Hồ Chí Minh', 'admin', 'active', '2025-03-31 08:36:34', '2025-03-31 08:36:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `car_images`
--
ALTER TABLE `car_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `car_owner_contracts`
--
ALTER TABLE `car_owner_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `car_owner_contracts`
--
ALTER TABLE `car_owner_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `car_images`
--
ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `car_owner_contracts`
--
ALTER TABLE `car_owner_contracts`
  ADD CONSTRAINT `car_owner_contracts_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
