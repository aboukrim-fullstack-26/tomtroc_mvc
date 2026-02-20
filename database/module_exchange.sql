-- Module Exchange (Demandes d'échange) - Auteur: @aboukrim
-- Base: tomtroc (MySQL/MariaDB)
-- IMPORTANT: INT UNSIGNED pour compatibilité avec users/books.

CREATE TABLE IF NOT EXISTS exchanges (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  requester_id INT UNSIGNED NOT NULL,
  owner_id INT UNSIGNED NOT NULL,
  book_id INT UNSIGNED NOT NULL,
  status ENUM('pending','accepted','rejected','cancelled') NOT NULL DEFAULT 'pending',
  message VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  KEY idx_requester (requester_id),
  KEY idx_owner (owner_id),
  KEY idx_book (book_id),
  KEY idx_status (status),
  CONSTRAINT fk_exchanges_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_exchanges_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_exchanges_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
