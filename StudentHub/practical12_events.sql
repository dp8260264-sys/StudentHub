
USE studenthub_db;

CREATE TABLE IF NOT EXISTS events_pr12 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(80) NOT NULL,
    event_date DATE NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    poster VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO events_pr12
(title, category, event_date, description, status)
VALUES
('Tech Fest 2026', 'Festival', '2026-11-15',
 'Technical competitions and project exhibitions', 'open'),
('AI Workshop', 'Workshop', '2026-11-20',
 'Introduction to Artificial Intelligence', 'open'),
('Sports Day', 'Sports', '2026-11-25',
 'Annual sports activities', 'closed');
