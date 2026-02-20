-- Module Reports (Signalements) - Auteur @aboukrim
CREATE TABLE IF NOT EXISTS reports (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT(11) UNSIGNED NOT NULL,
  target_type ENUM('book','message') NOT NULL,
  target_id INT(11) UNSIGNED NOT NULL,
  reason VARCHAR(50) NOT NULL,
  comment TEXT NULL,
  status ENUM('open','reviewed','closed') NOT NULL DEFAULT 'open',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_report (user_id, target_type, target_id),
  KEY idx_user_date (user_id, created_at),
  KEY idx_target (target_type, target_id),
  CONSTRAINT fk_reports_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
