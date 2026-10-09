-- Database Readify
CREATE DATABASE IF NOT EXISTS readify_db;
USE readify_db;

-- Tabel akun pengguna
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pembaca') DEFAULT 'pembaca',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel buku
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    genre VARCHAR(100),
    cover VARCHAR(255),
    sinopsis TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel bab buku
CREATE TABLE chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    judul_bab VARCHAR(200) NOT NULL,
    isi LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id)
    ON DELETE CASCADE
);