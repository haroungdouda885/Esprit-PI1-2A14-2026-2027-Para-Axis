<?php

namespace App;

use PDO;

/**
 * Creates the tables of this part automatically (no SQL file to import).
 * Called by Database::pdo() at each connection: CREATE TABLE IF NOT EXISTS does nothing when the table exists.
 * Tables of this part: users, auth_tokens, login_attempts, password_resets.
 */
class Schema
{
    private const TABLES = [
        // table users
        <<<SQL
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB
SQL,
        // table auth_tokens
        <<<SQL
CREATE TABLE IF NOT EXISTS auth_tokens (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT         NOT NULL,
  selector   VARCHAR(18) NOT NULL UNIQUE,
  token_hash VARCHAR(64) NOT NULL,
  expires_at DATETIME    NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB
SQL,
        // table login_attempts
        <<<SQL
CREATE TABLE IF NOT EXISTS login_attempts (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  email        VARCHAR(150) NOT NULL,
  ip_address   VARCHAR(45)  NOT NULL,
  attempted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (email), INDEX (ip_address)
) ENGINE=InnoDB
SQL,
        // table password_resets
        <<<SQL
CREATE TABLE IF NOT EXISTS password_resets (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT         NOT NULL,
  token_hash VARCHAR(64) NOT NULL,
  expires_at DATETIME    NOT NULL,
  used       TINYINT(1)  NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (token_hash)
) ENGINE=InnoDB
SQL,
    ];

    public static function install(PDO $pdo): void
    {
        foreach (self::TABLES as $sql) {
            $pdo->exec($sql);
        }

        // Demo accounts, only when the users table is empty (first start):
        // admin@paraxis.demo / Admin123!   and   customer@paraxis.demo / Customer123!
        if ((int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0) {
            $pdo->exec("INSERT INTO users (name, email, password_hash, role, is_verified) VALUES
                ('Admin Demo',    'admin@paraxis.demo',    '\$2y\$10\$EiXvkOWm.m0GXngqkRfv0.2p5MOyLcAogp0pSdqR8MwGGhYa9LSDC', 'admin',    1),
                ('Demo Customer', 'customer@paraxis.demo', '\$2y\$10\$y4BCtwqvBGDTHU9MEi4KA..faiFnfrDYHGTsX2JHHP9n0Cs/7Zyea', 'customer', 1)");
        }
    }
}
