

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    task_name VARCHAR(255),
    task_desc TEXT,
    start_time TIME,
    end_time TIME,
    status VARCHAR(50),
    weekly_goals TEXT,
    achievements TEXT,
    challenges TEXT,
    lessons TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

SELECT * FROM logs;

CREATE TABLE supervisor_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback TEXT NOT NULL,
    rating VARCHAR(50) NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SHOW COLUMNS FROM logs;

SELECT * FROM logs WHERE log_date >= '2025-03-18' AND log_date <= '2025-03-24';

ALTER TABLE logs
ADD COLUMN log_date DATE DEFAULT CURRENT_DATE;

DESCRIBE logs;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES ('admin', 'admin123');
