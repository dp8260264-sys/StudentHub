
CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year_of_study TINYINT UNSIGNED NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO students
(full_name, email, mobile, course, year_of_study)
VALUES
('Aarav Patel', 'aarav.patel@example.com', '9876500001', 'Computer Engineering', 3),
('Diya Shah', 'diya.shah@example.com', '9876500002', 'Information Technology', 2),
('Krish Mehta', 'krish.mehta@example.com', '9876500003', 'Computer Engineering', 1),
('Ananya Desai', 'ananya.desai@example.com', '9876500004', 'Information Technology', 3),
('Vivaan Joshi', 'vivaan.joshi@example.com', '9876500005', 'Computer Engineering', 2),
('Riya Patel', 'riya.patel@example.com', '9876500006', 'Artificial Intelligence', 3),
('Aditya Shah', 'aditya.shah@example.com', '9876500007', 'Computer Engineering', 1),
('Myra Mehta', 'myra.mehta@example.com', '9876500008', 'Information Technology', 2),
('Arjun Desai', 'arjun.desai@example.com', '9876500009', 'Computer Engineering', 3),
('Kiara Joshi', 'kiara.joshi@example.com', '9876500010', 'Artificial Intelligence', 1),
('Reyansh Patel', 'reyansh.patel@example.com', '9876500011', 'Information Technology', 3),
('Saanvi Shah', 'saanvi.shah@example.com', '9876500012', 'Computer Engineering', 2),
('Atharv Mehta', 'atharv.mehta@example.com', '9876500013', 'Artificial Intelligence', 2),
('Pari Desai', 'pari.desai@example.com', '9876500014', 'Information Technology', 1),
('Ishaan Joshi', 'ishaan.joshi@example.com', '9876500015', 'Computer Engineering', 3),
('Navya Patel', 'navya.patel@example.com', '9876500016', 'Artificial Intelligence', 2),
('Dhruv Shah', 'dhruv.shah@example.com', '9876500017', 'Information Technology', 1),
('Aadhya Mehta', 'aadhya.mehta@example.com', '9876500018', 'Computer Engineering', 2),
('Kabir Desai', 'kabir.desai@example.com', '9876500019', 'Artificial Intelligence', 3),
('Ira Joshi', 'ira.joshi@example.com', '9876500020', 'Information Technology', 2);
