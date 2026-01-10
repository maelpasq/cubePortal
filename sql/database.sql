-- Database Schema for Cube Portal
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin Account
-- Email: admin@cubeportal.com
-- Password: password123 (Hash provided below is for 'password123')
-- You should change this immediately after first login or via database.
INSERT INTO users (email, password_hash, role) VALUES 
('admin@cubeportal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
