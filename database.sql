CREATE DATABASE IF NOT EXISTS task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE task_manager;


CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('не выполнена', 'выполнена') DEFAULT 'не выполнена',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tasks (title, description, status) VALUES 
('Изучить PHP', 'Освоить основы программирования на PHP', 'не выполнена'),
('Создать базу данных', 'Спроектировать и создать структуру БД для проекта', 'выполнена'),
('Написать документацию', 'Создать README файл с описанием проекта', 'не выполнена');
