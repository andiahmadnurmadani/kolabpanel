CREATE DATABASE IF NOT EXISTS kolabpanel;
USE kolabpanel;

CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(50) PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  role VARCHAR(50) DEFAULT 'USER',
  plan VARCHAR(50) DEFAULT 'Basic',
  avatar VARCHAR(255),
  status VARCHAR(50) DEFAULT 'ACTIVE',
  email_verified BOOLEAN DEFAULT FALSE,
  verification_token VARCHAR(255),
  verification_expires DATETIME,
  mysql_username VARCHAR(255),
  mysql_password VARCHAR(255),
  mysql_database VARCHAR(255),
  INDEX idx_verification_token (verification_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sites (
  id VARCHAR(50) PRIMARY KEY,
  user_id VARCHAR(50),
  name VARCHAR(255),
  subdomain VARCHAR(255) UNIQUE,
  framework VARCHAR(50),
  status VARCHAR(50),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  storage_used INT DEFAULT 0,
  has_database BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plans (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(100),
  price INT,
  currency VARCHAR(10),
  features JSON,
  limits JSON,
  is_popular BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS domains (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255) UNIQUE,
  is_primary BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id VARCHAR(50) PRIMARY KEY,
  user_id VARCHAR(50),
  amount INT,
  plan VARCHAR(50),
  method VARCHAR(10) DEFAULT 'BANK',
  status VARCHAR(50),
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  proof_url VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tickets (
  id VARCHAR(50) PRIMARY KEY,
  user_id VARCHAR(50),
  subject VARCHAR(255),
  status VARCHAR(50) DEFAULT 'OPEN',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_message_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id VARCHAR(50) PRIMARY KEY,
  ticket_id VARCHAR(50),
  sender_id VARCHAR(50),
  text TEXT,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_admin BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Initial Data
INSERT IGNORE INTO users (id, username, password, email, role, plan, avatar, status, email_verified) VALUES 
('u1', 'demo_user', 'password', 'user@example.com', 'USER', 'Basic', 'https://picsum.photos/200', 'ACTIVE', TRUE),
('a1', 'sys_admin', 'admin', 'admin@kolabpanel.com', 'ADMIN', 'Premium', 'https://picsum.photos/201', 'ACTIVE', TRUE);

INSERT IGNORE INTO plans (id, name, price, currency, features, limits, is_popular) VALUES 
('plan_basic', 'Basic', 0, 'Rp', '["1 Site", "100MB Storage", "Shared Database"]', '{"sites": 1, "storage": 100, "databases": 0}', FALSE),
('plan_pro', 'Pro', 50000, 'Rp', '["5 Sites", "1GB Storage", "Private Database"]', '{"sites": 5, "storage": 1024, "databases": 1}', TRUE),
INSERT IGNORE INTO domains (id, name, is_primary) VALUES ('d1', 'kolabpanel.com', TRUE);
