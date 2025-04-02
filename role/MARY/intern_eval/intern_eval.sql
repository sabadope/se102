-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 03:32 AM
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
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `intern_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `marked` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `intern_id`, `date`, `time_in`, `time_out`, `marked`) VALUES
(61, 234, '2025-03-03', '08:00:00', '17:00:00', 'PRESENT'),
(62, 234, '2025-03-04', NULL, NULL, 'ABSENT'),
(63, 234, '2025-03-05', '08:00:00', '17:00:00', 'PRESENT'),
(64, 234, '2025-03-06', '08:00:00', '17:00:00', 'PRESENT'),
(65, 234, '2025-03-07', '08:00:00', '17:00:00', 'PRESENT'),
(66, 234, '2025-03-10', NULL, NULL, 'ABSENT'),
(67, 234, '2025-03-11', NULL, NULL, 'ABSENT'),
(68, 234, '2025-03-12', '08:00:00', '17:00:00', 'PRESENT'),
(69, 234, '2025-03-13', '08:00:00', '17:00:00', 'PRESENT'),
(70, 234, '2025-03-14', '08:00:00', '17:00:00', 'PRESENT'),
(71, 234, '2025-03-17', '08:00:00', '17:00:00', 'PRESENT'),
(72, 234, '2025-03-18', '08:00:00', '17:00:00', 'PRESENT'),
(73, 234, '2025-03-19', '08:00:00', '17:00:00', 'PRESENT'),
(74, 234, '2025-03-20', '08:00:00', '17:00:00', 'PRESENT'),
(75, 234, '2025-03-21', '08:00:00', '17:00:00', 'PRESENT'),
(76, 234, '2025-03-24', '08:00:00', '17:00:00', 'PRESENT'),
(77, 234, '2025-03-25', '08:00:00', '17:00:00', 'PRESENT'),
(78, 234, '2025-03-26', '08:00:00', '17:00:00', 'PRESENT'),
(79, 234, '2025-03-27', '08:00:00', '17:00:00', 'PRESENT'),
(80, 234, '2025-03-28', '08:00:00', '17:00:00', 'PRESENT'),
(81, 234, '2025-03-31', '08:00:00', '17:00:00', 'PRESENT'),
(82, 342, '2025-03-03', '08:00:00', '17:00:00', 'PRESENT'),
(83, 342, '2025-03-04', '08:00:00', '17:00:00', 'PRESENT'),
(84, 342, '2025-03-05', '08:00:00', '17:00:00', 'PRESENT'),
(85, 342, '2025-03-06', '08:00:00', '17:00:00', 'PRESENT'),
(86, 342, '2025-03-07', '08:00:00', '17:00:00', 'PRESENT'),
(87, 342, '2025-03-10', '08:00:00', '17:00:00', 'PRESENT'),
(88, 342, '2025-03-11', '08:00:00', '17:00:00', 'ABSENT'),
(89, 342, '2025-03-12', '08:00:00', '17:00:00', 'PRESENT'),
(90, 342, '2025-03-13', '08:00:00', '17:00:00', 'PRESENT'),
(91, 342, '2025-03-14', NULL, NULL, 'ABSENT'),
(92, 342, '2025-03-17', '08:00:00', '17:00:00', 'PRESENT'),
(93, 342, '2025-03-18', '08:00:00', '17:00:00', 'PRESENT'),
(94, 342, '2025-03-19', '08:00:00', '17:00:00', 'PRESENT'),
(95, 342, '2025-03-20', '08:00:00', '17:00:00', 'PRESENT'),
(96, 342, '2025-03-21', '08:00:00', '17:00:00', 'PRESENT'),
(97, 342, '2025-03-24', '08:00:00', '17:00:00', 'PRESENT'),
(98, 342, '2025-03-25', '08:00:00', '17:00:00', 'PRESENT'),
(99, 342, '2025-03-26', '08:00:00', '17:00:00', 'PRESENT'),
(100, 342, '2025-03-27', '08:00:00', '17:00:00', 'PRESENT'),
(101, 342, '2025-03-28', '08:00:00', '17:00:00', 'PRESENT'),
(102, 342, '2025-03-31', '08:00:00', '17:00:00', 'PRESENT'),
(103, 821, '2025-03-03', '08:00:00', '17:00:00', 'PRESENT'),
(104, 821, '2025-03-04', '08:00:00', '17:00:00', 'PRESENT'),
(105, 821, '2025-03-05', '08:00:00', '17:00:00', 'PRESENT'),
(106, 821, '2025-03-06', '08:00:00', '17:00:00', 'PRESENT'),
(107, 821, '2025-03-07', '08:00:00', '17:00:00', 'PRESENT'),
(108, 821, '2025-03-10', '08:00:00', '17:00:00', 'PRESENT'),
(109, 821, '2025-03-11', '08:00:00', '17:00:00', 'PRESENT'),
(110, 821, '2025-03-12', '08:00:00', '17:00:00', 'PRESENT'),
(111, 821, '2025-03-13', '08:00:00', '17:00:00', 'PRESENT'),
(112, 821, '2025-03-14', '08:00:00', '17:00:00', 'PRESENT'),
(113, 821, '2025-03-17', '08:00:00', '17:00:00', 'PRESENT'),
(114, 821, '2025-03-18', '08:00:00', '17:00:00', 'PRESENT'),
(115, 821, '2025-03-19', '08:00:00', '17:00:00', 'PRESENT'),
(116, 821, '2025-03-20', '08:00:00', '17:00:00', 'PRESENT'),
(117, 821, '2025-03-21', '08:00:00', '17:00:00', 'PRESENT'),
(118, 821, '2025-03-24', NULL, NULL, 'ABSENT'),
(119, 821, '2025-03-25', NULL, NULL, 'ABSENT'),
(120, 821, '2025-03-26', '08:00:00', '17:00:00', 'PRESENT'),
(121, 821, '2025-03-27', '08:00:00', '17:00:00', 'PRESENT'),
(122, 821, '2025-03-28', '08:00:00', '17:00:00', 'PRESENT'),
(123, 821, '2025-03-31', '08:00:00', '17:00:00', 'PRESENT'),
(124, 432, '2025-03-03', '08:00:00', '17:00:00', 'PRESENT'),
(125, 432, '2025-03-04', NULL, NULL, 'ABSENT'),
(126, 432, '2025-03-05', '08:00:00', '17:00:00', 'PRESENT'),
(127, 432, '2025-03-06', '08:00:00', '17:00:00', 'PRESENT'),
(128, 432, '2025-03-07', '08:00:00', '17:00:00', 'PRESENT'),
(129, 432, '2025-03-10', '08:00:00', '17:00:00', 'PRESENT'),
(130, 432, '2025-03-11', '08:00:00', '17:00:00', 'PRESENT'),
(131, 432, '2025-03-12', NULL, NULL, 'ABSENT'),
(132, 432, '2025-03-13', '08:00:00', '17:00:00', 'PRESENT'),
(133, 432, '2025-03-14', '08:00:00', '17:00:00', 'PRESENT'),
(134, 432, '2025-03-17', NULL, NULL, 'ABSENT'),
(135, 432, '2025-03-18', '08:00:00', '17:00:00', 'PRESENT'),
(136, 432, '2025-03-19', '08:00:00', '17:00:00', 'PRESENT'),
(137, 432, '2025-03-20', '08:00:00', '17:00:00', 'PRESENT'),
(138, 432, '2025-03-21', '08:00:00', '17:00:00', 'PRESENT'),
(139, 432, '2025-03-24', '08:00:00', '17:00:00', 'PRESENT'),
(140, 432, '2025-03-25', '08:00:00', '17:00:00', 'PRESENT'),
(141, 432, '2025-03-26', '08:00:00', '17:00:00', 'PRESENT'),
(142, 432, '2025-03-27', NULL, NULL, 'ABSENT'),
(143, 432, '2025-03-28', '08:00:00', '17:00:00', 'PRESENT'),
(144, 432, '2025-03-31', '08:00:00', '17:00:00', 'PRESENT'),
(145, 452, '2025-03-03', '08:00:00', '17:00:00', 'PRESENT'),
(146, 452, '2025-03-04', NULL, NULL, 'ABSENT'),
(147, 452, '2025-03-05', '08:00:00', '17:00:00', 'PRESENT'),
(148, 452, '2025-03-06', '08:00:00', '17:00:00', 'PRESENT'),
(149, 452, '2025-03-07', '08:00:00', '17:00:00', 'PRESENT'),
(150, 452, '2025-03-10', '08:00:00', '17:00:00', 'PRESENT'),
(151, 452, '2025-03-11', '08:00:00', '17:00:00', 'PRESENT'),
(152, 452, '2025-03-12', NULL, NULL, 'ABSENT'),
(153, 452, '2025-03-13', '08:00:00', '17:00:00', 'PRESENT'),
(154, 452, '2025-03-14', '08:00:00', '17:00:00', 'PRESENT'),
(155, 452, '2025-03-17', NULL, NULL, 'ABSENT'),
(156, 452, '2025-03-18', '08:00:00', '17:00:00', 'PRESENT'),
(157, 452, '2025-03-19', '08:00:00', '17:00:00', 'PRESENT'),
(158, 452, '2025-03-20', '08:00:00', '17:00:00', 'PRESENT'),
(159, 452, '2025-03-21', NULL, NULL, 'ABSENT'),
(160, 452, '2025-03-24', '08:00:00', '17:00:00', 'PRESENT'),
(161, 452, '2025-03-25', '08:00:00', '17:00:00', 'PRESENT'),
(162, 452, '2025-03-26', '08:00:00', '17:00:00', 'PRESENT'),
(163, 452, '2025-03-27', NULL, NULL, 'ABSENT'),
(164, 452, '2025-03-28', '08:00:00', '17:00:00', 'PRESENT'),
(165, 452, '2025-03-31', '08:00:00', '17:00:00', 'PRESENT');

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
(342, 'Joyce Sarmiento', 95, 20, 'Excellent Leadership!', 'Python, JavaScript', 88.50, 1),
(432, 'Diana Ross', 80, 12, 'Punctual and detail-oriented', 'HTML, CSS', 79.20, 4),
(452, 'Jane Octivano', 75, 10, 'Quick learner, needs improvement in teamwork', 'Python, React', 76.50, 5),
(821, 'John Michael Dela Cruz', 90, 18, 'Great problem solver', 'Java, SQL', 85.30, 2);

-- --------------------------------------------------------

--
-- Table structure for table `task_comp`
--

CREATE TABLE `task_comp` (
  `intern_id` int(11) NOT NULL,
  `task` text DEFAULT NULL,
  `Marked` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intern_id` (`intern_id`);

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
-- Indexes for table `task_comp`
--
ALTER TABLE `task_comp`
  ADD KEY `intern_id` (`intern_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

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

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `interns` (`intern_id`);

--
-- Constraints for table `task_comp`
--
ALTER TABLE `task_comp`
  ADD CONSTRAINT `task_comp_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `interns` (`intern_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
