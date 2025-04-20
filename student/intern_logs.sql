-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Must store hashed passwords
    role ENUM('Admin', 'Supervisor', 'User', 'Intern') NOT NULL DEFAULT 'User'
);

-- Logs Table
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Link to user who made the log
    type VARCHAR(50) NOT NULL,
    task_name VARCHAR(255),
    task_desc TEXT,
    start_time DATETIME, -- Changed from TIME to DATETIME
    end_time DATETIME, -- Changed from TIME to DATETIME
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    weekly_goals TEXT,
    achievements TEXT,
    challenges TEXT,
    lessons TEXT,
    log_date DATE DEFAULT CURRENT_DATE, -- Moved from ALTER to here
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Supervisor Reviews Table
CREATE TABLE supervisor_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_id INT NOT NULL, -- Links to a log entry
    feedback TEXT NOT NULL,
    rating ENUM('Poor', 'Fair', 'Good', 'Excellent') NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (log_id) REFERENCES logs(id) ON DELETE CASCADE
);

-- Insert Admin User (password should be hashed)
INSERT INTO users (username, password, role) VALUES ('admin', 'admin123', 'Admin');

-- Insert Admin User (password should be hashed)
INSERT INTO users (username, password, role) VALUES ('intern', 'intern123', 'Intern');

-- Insert Admin User (password should be hashed)
INSERT INTO users (username, password, role) VALUES ('supervisor', 'supervisor123', 'Supervisor');
