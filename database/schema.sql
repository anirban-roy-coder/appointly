-- AppointEase Database Schema
-- Run: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS appointment_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE appointment_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    phone      VARCHAR(20)   DEFAULT '',
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT           NOT NULL,
    title            VARCHAR(200)  NOT NULL,
    description      TEXT          DEFAULT '',
    appointment_date DATE          NOT NULL,
    appointment_time TIME          NOT NULL,
    status           ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index for fast slot-conflict checking
CREATE INDEX idx_appt_slot ON appointments (appointment_date, appointment_time, status);
