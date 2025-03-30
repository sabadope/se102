-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 03:14 PM
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
-- Database: `intern_eval`
--

-- --------------------------------------------------------

--
-- Table structure for table `hiring_evaluations`
--

CREATE TABLE `hiring_evaluations` (
  `id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `total_score` float NOT NULL,
  `behavior_score` float NOT NULL,
  `hiring_score` float NOT NULL,
  `recommendation` varchar(50) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interns`
--

CREATE TABLE `interns` (
  `intern_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `attendance` int(11) DEFAULT 0,
  `tasks_completed` int(11) DEFAULT 0,
  `feedback` text DEFAULT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `ranking` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interns`
--

INSERT INTO `interns` (`intern_id`, `name`, `attendance`, `tasks_completed`, `feedback`, `skills`, `overall_score`, `ranking`) VALUES
(234, 'Christian Santos', 85, 15, 'Hardworking and creative', 'C++, PHP', 82.70, 3),
(342, 'Joyce Sarmiento', 95, 20, 'Excellent team player', 'Python, JavaScript', 88.50, 1),
(432, 'Diana Ross', 80, 12, 'Punctual and detail-oriented', 'HTML, CSS', 79.20, 4),
(452, 'Jane Octivano', 75, 10, 'Quick learner, needs improvement in teamwork', 'Python, React', 76.50, 5),
(821, 'John Michael Dela Cruz', 90, 18, 'Great problem solver', 'Java, SQL', 85.30, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hiring_evaluations`
--
ALTER TABLE `hiring_evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interns`
--
ALTER TABLE `interns`
  ADD PRIMARY KEY (`intern_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hiring_evaluations`
--
ALTER TABLE `hiring_evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interns`
--
ALTER TABLE `interns`
  MODIFY `intern_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=823;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
