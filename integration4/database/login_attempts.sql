-- MEMBER 5: failed logins, used to lock an account/IP temporarily (import after users.sql)
USE paraxis;
CREATE TABLE IF NOT EXISTS login_attempts (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  email        VARCHAR(150) NOT NULL,
  ip_address   VARCHAR(45)  NOT NULL,
  attempted_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (email), INDEX (ip_address)
) ENGINE=InnoDB;
