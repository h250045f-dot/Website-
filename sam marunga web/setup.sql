-- Run this once in phpMyAdmin (or MySQL) on your hosting account
CREATE DATABASE IF NOT EXISTS gaming_club CHARACTER SET utf8mb4;
USE gaming_club;
CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  purpose ENUM('join','contact') NOT NULL,
  fullname VARCHAR(100) NOT NULL,
  regnumber VARCHAR(20) NOT NULL,
  program VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  phone VARCHAR(20),
  games VARCHAR(255),
  message TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
