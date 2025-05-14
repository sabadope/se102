
-- Users Table
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','supervisor','customer','intern') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Interns Table
CREATE TABLE `interns` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL UNIQUE,
  `department` VARCHAR(50),
  `join_date` DATE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Customer Feedback Table
CREATE TABLE `customer_feedback` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `intern_id` INT NOT NULL,
  `customer_id` INT NOT NULL,
  `professionalism` TINYINT(1) NOT NULL CHECK (`professionalism` BETWEEN 1 AND 5),
  `communication` TINYINT(1) NOT NULL CHECK (`communication` BETWEEN 1 AND 5),
  `service_quality` TINYINT(1) NOT NULL CHECK (`service_quality` BETWEEN 1 AND 5),
  `comments` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`intern_id`) REFERENCES `interns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Supervisor Feedback Table
CREATE TABLE `supervisor_feedback` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `intern_id` INT NOT NULL,
  `supervisor_id` INT NOT NULL,
  `work_quality` TINYINT(1) NOT NULL CHECK (`work_quality` BETWEEN 1 AND 5),
  `communication` TINYINT(1) NOT NULL CHECK (`communication` BETWEEN 1 AND 5),
  `professionalism` TINYINT(1) NOT NULL CHECK (`professionalism` BETWEEN 1 AND 5),
  `comments` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`intern_id`) REFERENCES `interns`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`supervisor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Intern Progress Table
CREATE TABLE `intern_progress` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `intern_id` INT NOT NULL,
  `task` VARCHAR(255) NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `comments` TEXT,
  `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`intern_id`) REFERENCES `interns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User Sessions Table
CREATE TABLE `user_sessions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `session_token` VARCHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default Admin account
INSERT INTO users (first_name, last_name, email, password, role)
VALUES ('Admin', 'User', 'admin@gmail.com', '$2y$10$E6bXzjEjZztZ4S6pOxsaFOHQJ6FwG/iV4DhE5Z5INpylIztMJQ5eG', 'admin');

-- Insert supervisor with correct password hash
INSERT INTO users (first_name, last_name, email, password, role)
VALUES (
  'Supervisor', 
  'User', 
  'supervisor@gmail.com', 
  '$2y$10$HMk5mOsW2z6tN5JsfXzv9u7rOXa0i2BTcZT4VQe3X5YqTx8cr57Vi', 
  'supervisor'
);

