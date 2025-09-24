-- Feedback Backup SQL
-- Generated on: 2025-09-24 03:55:01
-- Total records: 1

CREATE TABLE IF NOT EXISTS feedback (
    id VARCHAR(24) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT
);

INSERT INTO feedback (id, name, email, message, status, created_at, updated_at, ip_address, user_agent) VALUES (
    '68d197c3a3715d52f90e4e45',
    'Fatima Mohamud',
    'fmoha187@gmail.com',
    'jnhbfgvcdxszaAQWE4DR5TFGYUHJIOK',
    'replied',
    '2025-09-22 20:38:59',
    '2025-09-22 21:03:00',
    '::1',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'
);
