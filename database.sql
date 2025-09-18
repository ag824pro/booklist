CREATE DATABASE IF NOT EXISTS book_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE book_manager;

CREATE TABLE IF NOT EXISTS books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT,
    status ENUM('прочитана', 'в процессе', 'в планах') DEFAULT 'в планах',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO books (title, author, year, status) VALUES 
('Война и мир', 'Лев Толстой', 1869, 'прочитана'),
('1984', 'Джордж Оруэлл', 1949, 'в процессе'),
('Мастер и Маргарита', 'Михаил Булгаков', 1967, 'в планах');
