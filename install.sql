CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(32) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS news (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  teaser TEXT NULL,
  body MEDIUMTEXT NOT NULL,
  author_id INT UNSIGNED NULL,
  published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  body MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO pages (title, slug, body) VALUES
('Team', 'team', '<h2>Unser Team</h2><p>Trage hier dein Team ein.</p>'),
('Radio', 'radio', '<h2>Radio</h2><p>Hier kannst du später deinen Radio-Player einbauen.</p>')
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO news (title, slug, teaser, body, published) VALUES
('Willkommen auf unserer Fansite', 'willkommen', 'Die neue Community-Seite ist online.', '<p>Willkommen! Dieses CMS läuft modern mit PHP 8 und MySQL.</p>', 1)
ON DUPLICATE KEY UPDATE title = VALUES(title);
