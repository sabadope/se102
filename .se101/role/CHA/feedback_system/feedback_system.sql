-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2025 at 08:34 AM
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
-- Database: `feedback_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_feedback`
--

CREATE TABLE `customer_feedback` (
  `id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `professionalism` tinyint(1) NOT NULL CHECK (`professionalism` between 1 and 5),
  `communication` tinyint(1) NOT NULL CHECK (`communication` between 1 and 5),
  `service_quality` tinyint(1) NOT NULL CHECK (`service_quality` between 1 and 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_feedback`
--

INSERT INTO `customer_feedback` (`id`, `intern_id`, `customer_id`, `professionalism`, `communication`, `service_quality`, `comments`, `created_at`) VALUES
(1, 1, 3, 4, 4, 4, 'Good!', '2025-03-27 12:32:30');

-- --------------------------------------------------------

--
-- Table structure for table `interns`
--

CREATE TABLE `interns` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `join_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interns`
--

INSERT INTO `interns` (`id`, `user_id`, `department`, `join_date`) VALUES
(1, 4, 'Pending Assignment', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `intern_progress`
--

CREATE TABLE `intern_progress` (
  `id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `comments` text DEFAULT NULL,
  `date_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_feedback`
--

CREATE TABLE `supervisor_feedback` (
  `id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `work_quality` tinyint(1) NOT NULL CHECK (`work_quality` between 1 and 5),
  `communication` tinyint(1) NOT NULL CHECK (`communication` between 1 and 5),
  `professionalism` tinyint(1) NOT NULL CHECK (`professionalism` between 1 and 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor_feedback`
--

INSERT INTO `supervisor_feedback` (`id`, `intern_id`, `supervisor_id`, `work_quality`, `communication`, `professionalism`, `comments`, `created_at`) VALUES
(1, 1, 2, 5, 5, 5, 'Goodjob!!', '2025-03-27 12:32:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','supervisor','customer','intern') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Charles', 'Banias', 'charlesgbanias1@gmail.com', '$2y$10$F35DPd6QLY14mXlhKW3.Zu/PR0UnsH5S42LcOWeAk8Tmm6eq1XTZm', 'admin', '2025-03-27 09:45:23'),
(2, 'Charles', 'Banias', 'charlesgbanias2@gmail.com', '$2y$10$AFQEfdlQ7q.dU7tDaUCAG.KpZEFJ18iZspSb5VdSvlE.l4jvCN.Fu', 'supervisor', '2025-03-27 09:53:53'),
(3, 'Charles G', 'Banias', 'charlesgbanias3@gmail.com', '$2y$10$6Jj4bZyBlhB.baf0uhJNxucAvD4a0j11l2GKxvSVaNNJtyi/svg26', 'customer', '2025-03-27 11:38:24'),
(4, 'Charles Gannaban', 'Banias', 'charlesgbanias4@gmail.com', '$2y$10$6RhAiYdnXxRC9yImlbuAEe1MYM1YpyEWLBN4L591H/V8M/pUTTSoS', 'intern', '2025-03-27 12:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_token`, `expires_at`) VALUES
(1, 1, 'aff5fbf2e8f5cb42e5b6b44abf04cc1fd288897578dc9c007946c2d68d02fb6a', '2025-03-27 18:45:32'),
(2, 2, '0a04a34e604ee45a03b082162600e444e12d6a993d2efae66795bee1260869f0', '2025-03-27 18:54:09'),
(3, 1, '609ffc5fe90836b72afbd1032ab000fc60ec03a635eb8964e2081f76f16d3c2c', '2025-03-27 19:16:01'),
(4, 3, '5b8e22725856c5558d9479ead0ba2cc3470ee9ca0540467537e5671610dc6290', '2025-03-27 20:38:33'),
(5, 4, '69685f256e06c02c2830a460577112e7fbb304092bf425e3d5271ef6f25146d3', '2025-03-27 21:30:49'),
(6, 1, '3c33d53065fe08d58964985c0834d894270ae54f240520b6d668696568f9a29a', '2025-03-27 21:31:39'),
(7, 2, 'f2e6eae38dd96885bb27be17ea926a622aedf1c937c37b82384812d29e2ede0b', '2025-03-27 21:31:49'),
(8, 3, 'c7675afd8c6f22ec1ad9b720e0b3c7dc93ec6cc854ca441fcb77f37a3e6cf715', '2025-03-27 21:32:18'),
(9, 4, 'fada96d68cb5dff07b993b056fadac31c0e3207cdd6285f8f3d83bd55ea1f0b0', '2025-03-27 21:32:38'),
(10, 1, '322c48b6af5041a92d8d0e5a02fe7690b27a7ffcc3e0bb8296be981285006df7', '2025-03-27 21:35:05'),
(11, 4, 'b485a18b458aeaedb9663b80fae4015f9fbe8756c091b142bd6f3325c8b3fd97', '2025-03-27 21:36:19'),
(12, 4, '8e41da2d6b21ff7e3caa4727b62fd29c8697030712d02440618e117e218bf5d9', '2025-03-27 23:00:46'),
(13, 1, '91b600c699373318b2bfdefe8ab1e766d351b79918b185899d5949844f912ec2', '2025-03-27 23:12:54'),
(14, 1, '553f57406f37c0ef652e4cf30de2d2b8634af17f9c122403f5b1145f5086c22b', '2025-03-27 23:34:20'),
(15, 1, '3878cccb69db23e9f5d23dd93b44781c33a8d6ab3b6ca95ed6949eb56ee8d30e', '2025-03-27 23:42:31'),
(16, 2, 'f1ff2401be6eb8ce3c182e10034717f73f984c926846a26c0c012392b754eef3', '2025-03-27 23:43:24'),
(17, 3, '3a8d4d333ac8584e6141fccdeae2cf9ad550c7cb838631d4060d6bbf4fe1f014', '2025-03-27 23:43:56'),
(18, 4, 'bfa4a6dcb5cfb9e254ea8c9df689aac04e4826300336b4ecdd19080d0e56e273', '2025-03-27 23:44:27'),
(19, 1, '87a442ce3c5c1e571803d7ccc7df7b29399ac506b17845ddf463183b9d56ab88', '2025-03-27 23:45:18'),
(20, 2, '1af17f1ee63e8868578dd924c735fd53b144be702d65fcb080b250f27923cbf8', '2025-03-27 23:45:28'),
(21, 3, 'f610ce6529f732fdbe5b1f36bcb9e59a962c19a95d34362be0641df39c4eee0b', '2025-03-27 23:45:37'),
(22, 4, '32b04c0a38e25499d04944ef4b133d82784bd5485786d62b81ccb220913802c1', '2025-03-27 23:45:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intern_id` (`intern_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `interns`
--
ALTER TABLE `interns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `intern_progress`
--
ALTER TABLE `intern_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intern_id` (`intern_id`);

--
-- Indexes for table `supervisor_feedback`
--
ALTER TABLE `supervisor_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intern_id` (`intern_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interns`
--
ALTER TABLE `interns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `intern_progress`
--
ALTER TABLE `intern_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor_feedback`
--
ALTER TABLE `supervisor_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_feedback`
--
ALTER TABLE `customer_feedback`
  ADD CONSTRAINT `customer_feedback_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `interns` (`id`),
  ADD CONSTRAINT `customer_feedback_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `interns`
--
ALTER TABLE `interns`
  ADD CONSTRAINT `interns_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `intern_progress`
--
ALTER TABLE `intern_progress`
  ADD CONSTRAINT `intern_progress_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `interns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supervisor_feedback`
--
ALTER TABLE `supervisor_feedback`
  ADD CONSTRAINT `supervisor_feedback_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `interns` (`id`),
  ADD CONSTRAINT `supervisor_feedback_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
