-- Skema Database MySQL untuk Website Profil SMK Bangun Nusa Bangsa
-- Database: smk_bangun_nusa_bangsa

CREATE DATABASE IF NOT EXISTS `smk_bangun_nusa_bangsa` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smk_bangun_nusa_bangsa`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'editor') DEFAULT 'admin',
    `avatar` VARCHAR(255) DEFAULT 'assets/images/admin-avatar.png',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'bi-bookmark',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Articles Table
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category_id` INT NOT NULL,
    `author_id` INT NOT NULL,
    `thumbnail` VARCHAR(255) NULL,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `views` INT DEFAULT 0,
    `status` ENUM('published', 'draft') DEFAULT 'published',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (`category_id`),
    INDEX (`slug`),
    INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Comments Table
CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `article_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NULL,
    `is_anonymous` TINYINT(1) DEFAULT 0,
    `comment` TEXT NOT NULL,
    `status` ENUM('approved', 'pending', 'spam') DEFAULT 'approved',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`article_id`),
    INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Majors Table
CREATE TABLE IF NOT EXISTS `majors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `tagline` VARCHAR(255) NULL,
    `description` TEXT NOT NULL,
    `competencies` TEXT NULL,
    `careers` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `badge_color` VARCHAR(30) DEFAULT 'primary',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Site Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(50) NOT NULL UNIQUE,
    `setting_value` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Messages Table
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
