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
