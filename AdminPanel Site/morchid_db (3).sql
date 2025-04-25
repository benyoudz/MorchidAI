-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 12:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




CREATE TABLE `bus_lines` (
  `id` int(11) NOT NULL,
  `line_name` varchar(50) NOT NULL,
  `transport_type` enum('bus','tram','train') DEFAULT 'bus',
  `start_name` varchar(100) DEFAULT NULL,
  `start_lat` decimal(9,6) DEFAULT NULL,
  `start_lng` decimal(9,6) DEFAULT NULL,
  `end_name` varchar(100) DEFAULT NULL,
  `end_lat` decimal(9,6) DEFAULT NULL,
  `end_lng` decimal(9,6) DEFAULT NULL,
  `full_path` text DEFAULT NULL,
  `fare` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_lines`
--

INSERT INTO `bus_lines` (`id`, `line_name`, `transport_type`, `start_name`, `start_lat`, `start_lng`, `end_name`, `end_lat`, `end_lng`, `full_path`, `fare`) VALUES
(1, '34', 'bus', 'جامعة العلوم والتكنولوجيا', 35.723400, -0.582100, 'حي السلام', 35.700100, -0.636900, '[\"جامعة العلوم والتكنولوجيا\", \"بير الجير\", \"كاسطور\", \"وسط المدينة\", \"المدينة الجديدة\", \"حي السلام\"]', 30.00),
(2, 'G52', 'bus', 'المدينة الجديدة', 35.740000, -0.610000, 'إيسطو', 35.770000, -0.560000, '[\"المدينة الجديدة\", \"ريتاح مول\", \"الباهية\", \"إيسطو\"]', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `driver_details`
--

CREATE TABLE `driver_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `residence` varchar(255) NOT NULL,
  `safety_certificate_number` varchar(100) NOT NULL,
  `safety_certificate_image` varchar(255) NOT NULL,
  `certificate_expiry` date NOT NULL,
  `car_color` varchar(50) NOT NULL,
  `car_acquisition_date` date NOT NULL,
  `car_expiry_date` date NOT NULL,
  `car_document` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_details`
--

INSERT INTO `driver_details` (`id`, `user_id`, `residence`, `safety_certificate_number`, `safety_certificate_image`, `certificate_expiry`, `car_color`, `car_acquisition_date`, `car_expiry_date`, `car_document`) VALUES
(1, 6, 'orqn', '122222', '680a31f74e370_innoverse_challenge1_final.pdf', '2026-04-08', 'gsddf', '2025-04-09', '2030-06-12', '680a31f74e455_innoverse_challenge1_final.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `driver_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 5, 3, 'ييي', '2025-04-24 20:51:59'),
(2, 2, 5, 3, 'ييي', '2025-04-24 20:52:00');

-- --------------------------------------------------------

--
-- Table structure for table `road_network`
--

CREATE TABLE `road_network` (
  `id` int(11) NOT NULL,
  `start_point` varchar(100) DEFAULT NULL,
  `start_lat` decimal(9,6) DEFAULT NULL,
  `start_lng` decimal(9,6) DEFAULT NULL,
  `end_point` varchar(100) DEFAULT NULL,
  `end_lat` decimal(9,6) DEFAULT NULL,
  `end_lng` decimal(9,6) DEFAULT NULL,
  `road_type` enum('وطني','ولائي','بلدي') DEFAULT 'بلدي',
  `distance_km` decimal(5,2) DEFAULT NULL,
  `via_points` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `road_network`
--

INSERT INTO `road_network` (`id`, `start_point`, `start_lat`, `start_lng`, `end_point`, `end_lat`, `end_lng`, `road_type`, `distance_km`, `via_points`) VALUES
(1, 'وسط مدينة وهران', 35.696900, -0.633900, 'حي السلام', 35.700100, -0.636900, 'وطني', 4.50, '[\"ساحة أول نوفمبر\", \"نهج محمد خميستي\", \"مستشفى وهران\"]'),
(2, 'جامعة السانية', 35.655400, -0.628000, 'حي حسيبة بن بوعلي', 35.669800, -0.606200, 'بلدي', 3.20, '[\"السانية\", \"طريق الكرمة\", \"ثانوية محمد بن عبد الله\"]'),
(3, 'حي السلام', 35.700100, -0.636900, 'إيسطو', 35.722000, -0.601000, 'ولائي', 5.10, '[\"حي النور\", \"حي الأمير عبد القادر\", \"طريق سيدي معروف\"]'),
(4, 'المدينة الجديدة', 35.735000, -0.604000, 'أرزيو', 35.845000, -0.320000, 'بلدي', 32.00, '[\"حي النجمة\", \"بطيوة\", \"قديل\", \"جيهان\"]');

-- --------------------------------------------------------

--
-- Table structure for table `taxis`
--

CREATE TABLE `taxis` (
  `id` int(11) NOT NULL,
  `driver_name` varchar(100) NOT NULL,
  `car_number` varchar(20) NOT NULL,
  `car_color` varchar(30) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `current_lat` decimal(9,6) DEFAULT NULL,
  `current_lng` decimal(9,6) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxis`
--

INSERT INTO `taxis` (`id`, `driver_name`, `car_number`, `car_color`, `phone_number`, `current_lat`, `current_lng`, `last_update`) VALUES
(1, 'بلقاسم محمد', '3125 وهران 16', 'أبيض', '0551234567', 35.699100, -0.634500, '2025-04-24 11:12:02'),
(2, 'خالد بن يوسف', '1298 وهران 25', 'أسود', '0661122334', 35.705500, -0.640200, '2025-04-24 11:12:02'),
(3, 'جمال حاجي', '8420 وهران 45', 'أحمر', '0779988776', 35.711200, -0.645900, '2025-04-24 11:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `transport_nodes`
--

CREATE TABLE `transport_nodes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `transport_type` enum('tram','train','bus') NOT NULL,
  `line_code` varchar(20) NOT NULL,
  `fare` decimal(5,2) NOT NULL,
  `stop_order` int(11) NOT NULL,
  `station_code` varchar(20) GENERATED ALWAYS AS (concat(ucase(`line_code`),'-',lpad(`stop_order`,3,'0'))) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transport_nodes`
--

INSERT INTO `transport_nodes` (`id`, `name`, `latitude`, `longitude`, `transport_type`, `line_code`, `fare`, `stop_order`) VALUES
(1, 'السانية', 35.651200, -0.623500, 'tram', 'T1', 40.00, 1),
(2, 'السانية جنوب', 35.654300, -0.620100, 'tram', 'T1', 40.00, 2),
(3, 'السانية وسط', 35.657800, -0.616700, 'tram', 'T1', 40.00, 3),
(4, 'مولاي عبد القادر', 35.661000, -0.613200, 'tram', 'T1', 40.00, 4),
(5, 'جامعة الدكتور طالب', 35.664500, -0.609800, 'tram', 'T1', 40.00, 5),
(6, 'الحي الجامعي المتطوع', 35.668000, -0.606300, 'tram', 'T1', 40.00, 6),
(7, 'ثانوية النخيل', 35.671500, -0.602900, 'tram', 'T1', 40.00, 7),
(8, 'حديقة العثمانية', 35.675000, -0.599400, 'tram', 'T1', 40.00, 8),
(9, 'الحي الجامعي البدر', 35.678500, -0.595900, 'tram', 'T1', 40.00, 9),
(10, 'نهج جيش التحرير الوطني', 35.682000, -0.592500, 'tram', 'T1', 40.00, 10),
(11, 'قصر الرياضات', 35.685500, -0.589000, 'tram', 'T1', 40.00, 11),
(12, 'دار الحياة', 35.689000, -0.585500, 'tram', 'T1', 40.00, 12),
(13, 'محطة الباهية', 35.700000, -0.650000, 'bus', '103', 30.00, 1),
(14, 'كاسطور', 35.710000, -0.640000, 'bus', '103', 30.00, 2),
(15, 'بير الجير', 35.720000, -0.630000, 'bus', '103', 30.00, 3),
(16, 'جامعة بالقايد', 35.730000, -0.620000, 'bus', '103', 30.00, 4),
(17, 'المدينة الجديدة', 35.740000, -0.610000, 'bus', 'G52', 30.00, 1),
(18, 'ريتاح مول', 35.750000, -0.600000, 'bus', 'G52', 30.00, 2),
(19, 'الباهية', 35.760000, -0.590000, 'bus', 'G52', 30.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('person','driver') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `phone`, `password`, `user_type`, `created_at`) VALUES
(1, 'أحمد', 'بن يوسف', '0612345678', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'person', '2025-04-24 11:27:51'),
(2, 'ليلى', 'العربي', '0623456789', 'e05f79651d465214e7558a382ed0f0e5a77380a649f4573f3a1036dc4ee10c0b', 'person', '2025-04-24 11:27:51'),
(3, 'szas', 'asasa', '033421222', '$2y$10$R4nfRq5YZIhIusle4Y7iy.Vp3lflw5oSHqvfcngBv7M4On4k9ORHy', 'person', '2025-04-24 12:13:43'),
(4, 'salhi', 'salhi', '123', '$2y$10$BDgiX58DjzMauDW916bRcuPxKOJ9U0O3bG4xQy1Y1dghogee5Qc1G', 'driver', '2025-04-24 12:16:24'),
(5, 'zz', 'zz', '11', '$2y$10$y.Yx3WDarRw5/.IDieoUHeB4MvhbFbnf6I2sVcqVDJJ/02cvZKiNy', 'driver', '2025-04-24 12:34:55'),
(6, 'hh', 'yqsin', '123456', '$2y$10$a5iK5PguenmNa2dlRa1KD.w7df.x353lcJwgT1AUAEVdHj2z/RQxe', 'driver', '2025-04-24 12:43:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus_lines`
--
ALTER TABLE `bus_lines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `road_network`
--
ALTER TABLE `road_network`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxis`
--
ALTER TABLE `taxis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transport_nodes`
--
ALTER TABLE `transport_nodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bus_lines`
--
ALTER TABLE `bus_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `driver_details`
--
ALTER TABLE `driver_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `road_network`
--
ALTER TABLE `road_network`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `taxis`
--
ALTER TABLE `taxis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transport_nodes`
--
ALTER TABLE `transport_nodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `driver_details`
--
ALTER TABLE `driver_details`
  ADD CONSTRAINT `driver_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
