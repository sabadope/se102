-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 08:23 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perfomance_rankings`
--

-- --------------------------------------------------------

--
-- Table structure for table `rankings`
--

CREATE TABLE `rankings` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `score` double NOT NULL,
  `task_completion` int(11) NOT NULL CHECK (`task_completion` between 0 and 100),
  `quality` int(11) NOT NULL CHECK (`quality` between 0 and 100),
  `timeliness` int(11) NOT NULL CHECK (`timeliness` between 0 and 100),
  `attendance` int(11) NOT NULL CHECK (`attendance` between 0 and 100),
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `skill_growth` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rankings`
--

INSERT INTO `rankings` (`id`, `name`, `score`, `task_completion`, `quality`, `timeliness`, `attendance`, `feedback`, `created_at`, `skill_growth`) VALUES
(2, 'jayy', 63.7, 65, 43, 90, 0, '', '2025-04-20 05:41:11', NULL),
(16, 'admin', 97.3, 98, 97, 97, 0, '', '2025-04-20 05:53:49', NULL),
(17, 'Creaye', 91.2, 78, 96, 98, 0, '', '2025-04-20 05:54:07', NULL),
(18, 'Cha', 75, 95, 75, 55, 0, 'Great', '2025-04-20 05:54:54', NULL),
(19, 'Cha', 75, 95, 75, 55, 0, 'Great', '2025-04-20 06:07:20', NULL),
(20, 'Rara', 79.9, 67, 76, 98, 0, 'NICEE', '2025-04-20 06:18:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Supervisor','Intern') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'supervisor1', 'password123', 'Supervisor'),
(2, 'intern1', 'password456', 'Intern');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rankings`
--
ALTER TABLE `rankings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rankings`
--
ALTER TABLE `rankings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
