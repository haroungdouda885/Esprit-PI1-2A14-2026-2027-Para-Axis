-- MEMBER 1: database + users table.  Import this file FIRST (phpMyAdmin > Import, or the SQL tab).
CREATE DATABASE IF NOT EXISTS paraxis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paraxis;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20)  NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  is_verified   TINYINT(1)   NOT NULL DEFAULT 0,   -- member 2: email confirmed
  verify_token  VARCHAR(64)  NULL,                 -- member 2: sha256 of the token sent by email
  is_blocked    TINYINT(1)   NOT NULL DEFAULT 0,   -- 1 = cannot log in
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Demo accounts (same as before):  admin@paraxis.demo / Admin123!    customer@paraxis.demo / Customer123!
INSERT IGNORE INTO users (id, name, email, password_hash, role, is_verified) VALUES
(1, 'Admin Demo',    'admin@paraxis.demo',    '$2y$10$EiXvkOWm.m0GXngqkRfv0.2p5MOyLcAogp0pSdqR8MwGGhYa9LSDC', 'admin',    1),
(2, 'Demo Customer', 'customer@paraxis.demo', '$2y$10$y4BCtwqvBGDTHU9MEi4KA..faiFnfrDYHGTsX2JHHP9n0Cs/7Zyea', 'customer', 1);
