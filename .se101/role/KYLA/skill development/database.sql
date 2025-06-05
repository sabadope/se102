CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Student', 'Supervisor', 'Client') NOT NULL
);

-- Insert default Admin account
INSERT INTO users (username, email, password, role) 
VALUES ('Admin', 'admin@gmail.com', '$2y$10$E6bXzjEjZztZ4S6pOxsaFOHQJ6FwG/iV4DhE5Z5INpylIztMJQ5eG', 'Admin');

ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

