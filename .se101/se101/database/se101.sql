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

-- Insert default Supervisor account
INSERT INTO users (username, email, password, role) 
VALUES ('Supervisor', 'supervisor@gmail.com', '$2y$10$YVGcFYmtzk9XUpJrKXxz7OAwfQ4Zd/JJk7c8el6Xuvrq8U1RB1D4C', 'Supervisor');

ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender VARCHAR(100),
    receiver VARCHAR(100),
    message TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);


SELECT * FROM messages ORDER BY timestamp DESC LIMIT 10;
