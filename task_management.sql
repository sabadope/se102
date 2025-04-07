-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 04:08 PM
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
-- Database: `task_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `assigned_by` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `created_on` varchar(10) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `deadline` varchar(10) NOT NULL,
  `assignee` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `assigned_by`, `company_name`, `task_name`, `created_on`, `status`, `deadline`, `assignee`, `file_path`, `rating`, `feedback`, `start_date`, `start_time`, `end_date`, `end_time`) VALUES
(1, 'Intern_1', 'Registrar', '', '03-31-2025', 'Pending', '4-31-2025', 'Intern_1', NULL, 4, 'perpekk', NULL, NULL, NULL, NULL),
(2, 'intern_2', 'Registrar', 'code', '04-01-2025', 'Pending', '4-31-2025', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Intern_2', 'Faculty', 'sample', '04-01-2025', 'Pending', '4-31-2025', 'Intern_2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Intern_2', 'Faculty', 'General Classification for Java Coding and Object Oriented Programming', '04-01-2025', 'Pending', '4-31-2025', 'Intern_2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Intern_2', 'TCU', 'TASK MO DITO', '04-05-2025', 'Pending', '4-31-2025', 'Intern_2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Intern_2', 'Faculty', 'TASKKKKK', '04-05-2025', 'Pending', '4-31-2025', 'Intern_2', NULL, 3, '', NULL, NULL, NULL, NULL);

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
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
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
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
