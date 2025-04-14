-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 03:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
(11, 3, 1, '2025-03-31', '2025-04-01', 200000.00, 'completed', 'paid', '2025-03-31 10:57:04', '2025-03-31 12:17:09'),
(12, 4, 1, '2025-04-07', '2025-04-08', 200000.00, 'completed', 'paid', '2025-04-07 17:44:37', '2025-04-07 21:17:09'),
(13, 7, 5, '2025-04-14', '2025-04-15', 300000.00, 'completed', 'paid', '2025-04-14 06:11:06', '2025-04-14 07:03:35');

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
(3, 5, 'Toyota', 'Camry', 2024, 'gasoline', 4, 100000.00, 'Xã Lùng Thàng, Huyện Sìn Hồ, Tỉnh Lai Châu', 22.28251635, 103.40197210, 'Comfortable sedan', 'approved', '2025-03-31 08:47:13', '2025-04-07 20:55:44'),
(4, 5, 'Tesla', 'Modal 3', 2023, 'electric', 4, 100000.00, 'Phường Hòa Minh, Quận Liên Chiểu, Thành phố Đà Nẵng', 16.06260280, 108.16836230, 'Electric vehicle with autopilot', 'approved', '2025-03-31 08:51:27', '2025-04-07 20:56:27'),
(5, 5, 'Honda', 'Civic', 2023, 'diesel', 7, 100000.00, 'Phường Tăng Nhơn Phú B, Thành phố Thủ Đức, Thành phố Hồ Chí Minh', 10.83566650, 106.78067200, 'Reliable and fuel-efficient sedan', 'approved', '2025-03-31 08:59:54', '2025-04-07 20:56:57'),
(6, 5, 'Ford', 'Mustang', 2023, 'gasoline', 7, 200000.00, 'Điện Biên Phủ, Phường 25, Quận Bình Thạnh, Thành phố Hồ Chí Minh', 10.79892086, 106.72170115, 'Xe đẹp thế anh zai', 'approved', '2025-03-31 09:28:22', '2025-04-07 20:57:17'),
(7, 7, 'Mitsubishi', 'Mitsubishi Xforce', 2017, 'gasoline', 5, 150000.00, 'Phường Ghềnh Ráng, Thành phố Quy Nhơn, Tỉnh Bình Định', 13.74605060, 109.21091190, 'Mitsubishi Xforce xe đẹp', 'approved', '2025-04-07 21:00:52', '2025-04-07 21:05:03'),
(8, 7, 'Nissan', 'Nissan Patrol 2025', 2025, 'gasoline', 6, 170000.00, 'Xã Nhơn Tân, Thị xã An Nhơn, Tỉnh Bình Định', 13.92020255, 109.08312439, 'Nissan Patrol 2025', 'approved', '2025-04-07 21:02:46', '2025-04-07 21:05:02'),
(9, 7, 'Audi', 'Audi R8', 2017, 'gasoline', 6, 300000.00, 'Phường Kim Liên, Quận Đống Đa, Thành phố Hà Nội', 21.00623710, 105.83524700, 'Audi R8 sang trọng', 'approved', '2025-04-07 21:04:46', '2025-04-07 21:05:01'),
(10, 7, 'Lamborghini', 'Lamborghini Urus', 2015, 'electric', 4, 220000.00, 'Phường 5, Quận 5, Thành phố Hồ Chí Minh', 10.75148070, 106.67497150, 'Lamborghini Urus', 'approved', '2025-04-07 21:09:07', '2025-04-07 21:09:34');

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
(6, 6, 'public/uploads/cars/67e9fdc673abc.png', 1, '2025-03-31 09:28:22'),
(7, 7, 'public/uploads/cars/67f3da948a53c.jpg', 1, '2025-04-07 21:00:52'),
(8, 7, 'public/uploads/cars/67f3da948caf6.jpg', 0, '2025-04-07 21:00:52'),
(9, 7, 'public/uploads/cars/67f3da948dfd4.jpg', 0, '2025-04-07 21:00:52'),
(10, 8, 'public/uploads/cars/67f3db06d0284.jpg', 1, '2025-04-07 21:02:46'),
(11, 8, 'public/uploads/cars/67f3db06d121c.jpg', 0, '2025-04-07 21:02:46'),
(12, 8, 'public/uploads/cars/67f3db06d3d85.jpg', 0, '2025-04-07 21:02:46'),
(13, 8, 'public/uploads/cars/67f3db06d4e2e.jpg', 0, '2025-04-07 21:02:46'),
(14, 9, 'public/uploads/cars/67f3db7e192d4.jpg', 1, '2025-04-07 21:04:46'),
(15, 9, 'public/uploads/cars/67f3db7e1b7ba.jpg', 0, '2025-04-07 21:04:46'),
(16, 9, 'public/uploads/cars/67f3db7e1cdcb.jpg', 0, '2025-04-07 21:04:46'),
(17, 10, 'public/uploads/cars/67f3dc8305771.jpg', 1, '2025-04-07 21:09:07'),
(18, 10, 'public/uploads/cars/67f3dc8307a78.jpg', 0, '2025-04-07 21:09:07'),
(19, 10, 'public/uploads/cars/67f3dc83089f1.jpg', 0, '2025-04-07 21:09:07');

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
  `status` enum('paid','pending payment','cancelled','expired') DEFAULT 'paid',
  `approved` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_owner_contracts`
--

INSERT INTO `car_owner_contracts` (`id`, `owner_id`, `start_date`, `end_date`, `contract_fee`, `status`, `approved`, `created_at`, `updated_at`) VALUES
(1, 7, '2025-04-07', '2026-04-07', 40000.00, 'paid', 1, '2025-04-07 20:59:53', '2025-04-07 20:59:53');

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
(4, 11, 200000.00, 'MoMo', '1743398029_11', 'paid', '2025-03-31 12:13:52', '2025-03-31 12:14:10'),
(5, 12, 200000.00, 'MoMo', '1744022679_12', 'paid', '2025-04-07 17:44:40', '2025-04-07 17:46:01'),
(6, 13, 300000.00, 'MoMo', '1744585869_13', 'paid', '2025-04-14 06:11:10', '2025-04-14 06:12:18');

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
(1, 'DISCOUNT10', 10.00, '2025-04-07 00:00:00', '2025-05-09 00:00:00', 'active', '2025-03-28 22:47:40', '2025-04-07 21:28:31'),
(2, 'SPECIAL44', 13.00, '2025-04-07 00:00:00', '2025-04-11 00:00:00', 'active', '2025-04-07 21:31:21', '2025-04-07 21:31:45');

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

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `booking_id`, `user_id`, `car_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(2, 11, 1, 3, 4, 'Trải nghiệm khá ok', '2025-04-07 21:16:35', '2025-04-07 21:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `license` varchar(255) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `username`, `email`, `license`, `password`, `fullname`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'toan123qn', 'toan123qn@gmail.com', '0326504055512', '$2y$10$hat3wYMaxxpVKVSaFIfpP.cSwccH7gc.jBfEo1Piae09B3ETixTHi', 'Võ Hữu Toàn', '0393303222', '12', 'regular', 'active', '2025-03-28 22:40:21', '2025-04-07 17:44:28'),
(5, 'toan2', 'toan2@gmail.com', '0326504055512', '$2y$10$zZOFVOeoMzaqz3FLHtN7yeRlnlBvYdz0qyh.Erdbq7Gf2DpuexTgi', 'Chủ toàn', '0393303024', '12 Hà huy giáp', 'owner', 'active', '2025-03-31 08:35:05', '2025-04-14 06:10:50'),
(6, 'admin', 'admin@gmail.com', NULL, '$2y$10$TTRBJMObCZLPOrg2052cQOHnOrr94OqmYu6nqueFNkMdf6HiIGaaa', 'Võ Hữu Toàn', '0393303025', 'TP. Hồ Chí Minh', 'admin', 'active', '2025-03-31 08:36:34', '2025-03-31 08:36:47'),
(7, 'cuong', 'huynhminhcuong.270403@gmail.com', NULL, '$2y$10$sT2iWuNxdS58xur78SkoR.NMpbs/pgPpefHT.u2gCsKDgl8QKnS0.', 'Cuong Huynh', '0326504055', 'Ho Chi Minh city', 'owner', 'active', '2025-04-07 20:57:58', '2025-04-07 20:57:58');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `car_owner_contracts`
--
ALTER TABLE `car_owner_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
