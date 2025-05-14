

-- Create users table with role support
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create logs table with supervisor feedback support
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    task_desc TEXT,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('Completed', 'In Progress', 'Pending') DEFAULT 'Pending',
    weekly_goals TEXT,
    achievements TEXT,
    challenges TEXT,
    lessons TEXT,
    supervisor_feedback TEXT, -- For storing supervisor's feedback on the log
    supervisor_rating INT,    -- For storing supervisor's rating (1-5 stars)
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES
('supervisor', 'supervisor1230', 'supervisor@gmail.com', 'System Administrator', 'admin');


-- Insert sample regular user (password: user123)
INSERT INTO users (username, password, email, full_name, role) VALUES
('user', 'user1230', 'user@gmail.com', 'Regular User', 'user');

-- Insert sample logs
INSERT INTO logs (user_id, task_name, task_desc, start_time, end_time, status, weekly_goals, achievements, challenges, lessons) VALUES
(2, 'Website Development', 'Worked on frontend development', '09:00:00', '17:00:00', 'Completed', 'Complete website layout', 'Finished homepage design', 'Responsive design issues', 'Learned new CSS techniques'),
(2, 'Database Design', 'Created database schema', '09:00:00', '12:00:00', 'In Progress', 'Design database structure', 'Created tables', 'Complex relationships', 'Better understanding of normalization');

