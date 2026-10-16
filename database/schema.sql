CREATE DATABASE IF NOT EXISTS booknest_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE booknest_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','member') NOT NULL DEFAULT 'member',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(150) NOT NULL,
  isbn VARCHAR(20) NULL UNIQUE,
  category_id INT NULL,
  copies_total INT NOT NULL DEFAULT 1,
  copies_available INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS borrowings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  borrowed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  due_date DATE NOT NULL,
  returned_at DATETIME NULL,
  status ENUM('borrowed','returned') NOT NULL DEFAULT 'borrowed',
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
('Fiction'), ('Technology'), ('Science'), ('Filipino Literature'), ('Self-Help');

INSERT INTO books (title, author, isbn, category_id, copies_total, copies_available) VALUES
('Noli Me Tangere', 'Jose Rizal', '9789712715686', 4, 3, 3),
('El Filibusterismo', 'Jose Rizal', '9789712715693', 4, 3, 3),
('Florante at Laura', 'Francisco Balagtas', '9789710804511', 4, 2, 2),
('Clean Code', 'Robert C. Martin', '9780132350884', 2, 2, 2),
('The Pragmatic Programmer', 'Andrew Hunt', '9780135957059', 2, 2, 2),
('A Brief History of Time', 'Stephen Hawking', '9780553380163', 3, 2, 2),
('Atomic Habits', 'James Clear', '9780735211292', 5, 4, 4),
('The Alchemist', 'Paulo Coelho', '9780062315007', 1, 3, 3);
