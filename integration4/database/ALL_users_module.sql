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
-- MEMBER 3: "remember me" tokens (import after users.sql)
USE paraxis;
CREATE TABLE IF NOT EXISTS auth_tokens (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT         NOT NULL,
  selector   VARCHAR(18) NOT NULL UNIQUE,   -- public part stored in the cookie
  token_hash VARCHAR(64) NOT NULL,          -- sha256 of the secret part stored in the cookie
  expires_at DATETIME    NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- MEMBER 4: password reset tokens (import after users.sql)
USE paraxis;
CREATE TABLE IF NOT EXISTS password_resets (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT         NOT NULL,
  token_hash VARCHAR(64) NOT NULL,          -- sha256 of the token sent by email
  expires_at DATETIME    NOT NULL,
  used       TINYINT(1)  NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (token_hash)
) ENGINE=InnoDB;
-- MEMBER 5: failed logins, used to lock an account/IP temporarily (import after users.sql)
USE paraxis;
CREATE TABLE IF NOT EXISTS login_attempts (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  email        VARCHAR(150) NOT NULL,
  ip_address   VARCHAR(45)  NOT NULL,
  attempted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (email), INDEX (ip_address)
) ENGINE=InnoDB;
-- MEMBER 6: delivery addresses (import after users.sql). The Orders module can use this table at checkout.
USE paraxis;
CREATE TABLE IF NOT EXISTS addresses (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT          NOT NULL,
  street      VARCHAR(200) NOT NULL,
  city        VARCHAR(100) NOT NULL,
  governorate VARCHAR(100) NOT NULL,
  postal_code VARCHAR(10)  NULL,
  phone       VARCHAR(20)  NULL,
  is_default  TINYINT(1)   NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
