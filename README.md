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

INSERT INTO `bookings` (`id`, `car_id`, `user_id`, `start_date`, `end_date`, `total_price`, `booking_status`, `payment_status`, `created_at`, `updated_at`) VALUES
(9, 6, 1, '2025-03-31', '2025-04-01', 400000.00, 'completed', 'paid', '2025-03-31 09:49:43', '2025-03-31 12:42:32'),
(10, 5, 1, '2025-03-31', '2025-04-01', 200000.00, 'pending', 'paid', '2025-03-31 10:30:36', '2025-04-07 13:10:44'),
(11, 3, 1, '2025-03-31', '2025-04-01', 200000.00, 'completed', 'paid', '2025-03-31 10:57:04', '2025-03-31 12:17:09'),
(12, 6, 8, '2025-04-07', '2025-04-08', 400000.00, 'completed', 'paid', '2025-04-07 11:06:33', '2025-04-07 13:14:16'),
(13, 7, 8, '2025-04-10', '2025-04-11', 600000.00, 'pending', 'pending', '2025-04-10 20:27:17', '2025-04-10 20:27:17'),
(14, 5, 8, '2025-04-10', '2025-04-11', 200000.00, 'pending', 'pending', '2025-04-10 20:31:21', '2025-04-10 20:31:21');

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

INSERT INTO `cars` (`id`, `owner_id`, `brand`, `model`, `year`, `car_type`, `seats`, `price_per_day`, `address`, `latitude`, `longitude`, `description`, `status`, `created_at`, `updated_at`) VALUES
(3, 5, 'Toyota', 'Camry', 2024, 'gasoline', 4, 100000.00, '12 Nguyễn Bỉnh Khiêm, Phường 1, Gò Vấp, Hồ Chí Minh', 10.76262200, 106.66017200, 'Comfortable sedan', 'approved', '2025-03-31 08:47:13', '2025-04-02 18:22:25'),
(4, 5, 'Tesla', 'Modal 3', 2023, 'electric', 4, 100000.00, '08 Nguyễn Trung Trưc, Quận Bình Thạnh, TP.Hồ Chí Minh', 10.76262200, 106.66017200, 'Electric vehicle with autopilot', 'approved', '2025-03-31 08:51:27', '2025-04-02 18:22:35'),
(5, 5, 'Honda', 'Civic', 2023, 'diesel', 7, 100000.00, 'Võ văn kiệt', 10.75804240, 106.69163665, 'Reliable and fuel-efficient sedan', 'approved', '2025-03-31 08:59:54', '2025-03-31 09:00:03'),
(6, 5, 'Ford', 'Mustang', 2023, 'gasoline', 7, 200000.00, '12 Nguyễn Bỉnh Khiêm', 10.76262200, 106.66017200, 'Xe đẹp thế anh zai', 'approved', '2025-03-31 09:28:22', '2025-03-31 09:28:43'),
(7, 5, 'BMW', 'I8', 2022, 'gasoline', 4, 300000.00, '192 Ngô Gia Tự', 11.98067501, 108.47858602, 'BMW i8 là một siêu xe hybrid với thiết kế thể thao, sử dụng động cơ xăng 1.5L kết hợp mô-tơ điện, cho tổng công suất 369 mã lực. Xe có khả năng tăng tốc 0-100 km/h trong khoảng 4.4 giây, cửa cánh bướm độc đáo và nội thất hiện đại.', 'approved', '2025-04-01 15:23:08', '2025-04-01 15:24:31');

CREATE TABLE `car_images` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `car_images` (`id`, `car_id`, `image_path`, `is_primary`, `created_at`) VALUES
(3, 3, 'public/uploads/cars/3/67e9f4213da3f.png', 1, '2025-03-31 08:47:13'),
(4, 4, 'public/uploads/cars/4/67e9f51f777a0.png', 1, '2025-03-31 08:51:27'),
(5, 5, 'public/uploads/cars/67e9f71a157bb.png', 1, '2025-03-31 08:59:54'),
(6, 6, 'public/uploads/cars/67e9fdc673abc.png', 1, '2025-03-31 09:28:22'),
(7, 7, 'public/uploads/cars/67eba26c13f19.png', 0, '2025-04-01 15:23:08'),
(8, 7, 'public/uploads/cars/67eba26c14521.png', 0, '2025-04-01 15:23:08'),
(9, 7, 'public/uploads/cars/67eba26c1496d.jpeg', 0, '2025-04-01 15:23:08'),
(10, 7, 'public/uploads/cars/67eba26c14bc2.jpg', 0, '2025-04-01 15:23:08'),
(11, 7, 'public/uploads/cars/67eba26c14edd.png', 1, '2025-04-01 15:23:08');

CREATE TABLE `car_owner_contracts` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `contract_fee` decimal(10,2) NOT NULL,
  `status` enum('pending_payment','paid','cancelled','expired') DEFAULT 'pending_payment',
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `car_owner_contracts` (`id`, `owner_id`, `start_date`, `end_date`, `contract_fee`, `status`, `approved`, `created_at`, `updated_at`) VALUES
(1, 5, '2025-04-03', '2026-04-03', 40000.00, 'pending_payment', 0, '2025-04-03 02:17:11', '2025-04-03 02:17:11');

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

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `payment_status`, `created_at`, `updated_at`) VALUES
(3, 11, 200000.00, 'MoMo', '1743394416_11', 'paid', '2025-03-31 11:13:37', '2025-04-07 13:11:24'),
(4, 11, 200000.00, 'MoMo', '1743398029_11', 'paid', '2025-03-31 12:13:52', '2025-03-31 12:14:10'),
(5, 12, 400000.00, 'MoMo', '1743998931_12', 'paid', '2025-04-07 11:08:51', '2025-04-07 13:11:19'),
(6, 13, 600000.00, 'MoMo', '1744291651_13', 'failed', '2025-04-10 20:27:32', '2025-04-10 20:31:05'),
(7, 14, 200000.00, 'MoMo', '1744291882_14', 'failed', '2025-04-10 20:31:22', '2025-04-10 20:32:15'),
(8, 14, 200000.00, 'MoMo', '1744291943_14', 'failed', '2025-04-10 20:32:24', '2025-04-10 20:33:48');

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

INSERT INTO `promotions` (`id`, `code`, `discount_percentage`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'DISCOUNT10', 10.00, '2024-10-01 00:00:00', '2024-10-31 23:59:59', 'active', '2025-03-28 22:47:40', '2025-03-28 22:47:40');

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

INSERT INTO `reviews` (`id`, `booking_id`, `user_id`, `car_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(2, 12, 8, 6, 5, 'Trải nghiệm thật thú vị', '2025-04-07 13:16:09', '2025-04-07 13:16:09');

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

INSERT INTO `users` (`id`, `username`, `email`, `license`, `password`, `fullname`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'toan123qn', 'toan123qn@gmail.com', NULL, '$2y$10$hat3wYMaxxpVKVSaFIfpP.cSwccH7gc.jBfEo1Piae09B3ETixTHi', 'Võ Hữu Toàn', '0393303222', '12', 'regular', 'blocked', '2025-03-28 22:40:21', '2025-04-01 16:04:39'),
(5, 'toan2', 'toan2@gmail.com', NULL, '$2y$10$zZOFVOeoMzaqz3FLHtN7yeRlnlBvYdz0qyh.Erdbq7Gf2DpuexTgi', 'Chủ toàn', '0393303024', '12 Hà huy giáp', 'owner', 'active', '2025-03-31 08:35:05', '2025-03-31 08:35:05'),
(6, 'admin', 'admin@gmail.com', NULL, '$2y$10$TTRBJMObCZLPOrg2052cQOHnOrr94OqmYu6nqueFNkMdf6HiIGaaa', 'Võ Hữu Toàn', '0393303025', 'TP. Hồ Chí Minh', 'admin', 'active', '2025-03-31 08:36:34', '2025-03-31 08:36:47'),
(7, 'user', 'user1@gmai.com', '123641723123', '$2y$10$j3Ek.zZlPXBke8uh/EOs8.h8DfqQ9V12WEuNjTcXgYkU8cRhZqRz2', 'Võ Hữu Toàn', '0393303021', 'S7.03 Vinhomes', 'regular', 'active', '2025-04-07 10:40:25', '2025-04-07 10:58:03'),
(8, 'user1', 'toan17@gmail.com', '123142123123', '$2y$10$y5zfBBb5LCrS1ihteWRSJOeJlxtUL.O1JuLBmArcJfCNS.rwzr0Nq', 'Vox Huu Toan', '0339321123', 'Sai` gon', 'regular', 'active', '2025-04-07 11:02:13', '2025-04-07 11:02:30');

ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

ALTER TABLE `car_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

ALTER TABLE `car_owner_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_owner` (`owner_id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);
  
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `car_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `car_owner_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;

ALTER TABLE `car_owner_contracts`
  ADD CONSTRAINT `fk_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;
COMMIT;

