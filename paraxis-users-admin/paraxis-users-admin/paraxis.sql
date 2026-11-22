-- Par'Axis - Users module (back office)
-- Run this in phpMyAdmin (SQL tab). The site does NOT create anything by itself.

CREATE DATABASE IF NOT EXISTS paraxis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paraxis;

-- table users
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20)  NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  is_verified   TINYINT(1)   NOT NULL DEFAULT 0,
  verify_token  VARCHAR(64)  NULL,
  is_blocked    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- table auth_tokens ("remember me")
CREATE TABLE auth_tokens (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT         NOT NULL,
  selector   VARCHAR(18) NOT NULL UNIQUE,
  token_hash VARCHAR(64) NOT NULL,
  expires_at DATETIME    NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- table login_attempts (failed logins)
CREATE TABLE login_attempts (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  email        VARCHAR(150) NOT NULL,
  ip_address   VARCHAR(45)  NOT NULL,
  attempted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (email), INDEX (ip_address)
) ENGINE=InnoDB;

-- demo accounts:  admin@paraxis.demo / Admin123!   and   customer@paraxis.demo / Customer123!
INSERT INTO users (name, email, password_hash, role, is_verified) VALUES
('Admin Demo',    'admin@paraxis.demo',    '$2y$10$EiXvkOWm.m0GXngqkRfv0.2p5MOyLcAogp0pSdqR8MwGGhYa9LSDC', 'admin',    1),
('Demo Customer', 'customer@paraxis.demo', '$2y$10$y4BCtwqvBGDTHU9MEi4KA..faiFnfrDYHGTsX2JHHP9n0Cs/7Zyea', 'customer', 1);
