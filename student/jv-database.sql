-- Database: `nattendance`

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('intern', 'supervisor') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: attendance
-- --------------------------------------------------------

CREATE TABLE `attendance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `date` DATE NOT NULL,
  `check_in` TIME DEFAULT NULL,
  `check_out` TIME DEFAULT NULL,
  `status` ENUM('present', 'absent', 'late') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insert default Supervisor account
INSERT INTO `users` (`username`, `email`, `password`, `role`) 
VALUES ('Supervisor', 'supervisor@gmail.com', '$2y$10$YVGcFYmtzk9XUpJrKXxz7OAwfQ4Zd/JJk7c8el6Xuvrq8U1RB1D4C', 'supervisor');
