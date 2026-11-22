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
