-- ============================================================
-- Visitor analytics table (admin dashboard – frontend portal)
-- ============================================================

-- Create table (run once)
CREATE TABLE IF NOT EXISTS `visitor_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(191) NOT NULL,
  `visited_at` timestamp NOT NULL,
  `time_spent_seconds` int unsigned DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL COMMENT 'frontend, admin, portal',
  `path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visitor_logs_session_id_index` (`session_id`),
  KEY `visitor_logs_path_index` (`path`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If table already exists without path, run this instead:
-- ALTER TABLE `visitor_logs`
--   ADD COLUMN `path` varchar(500) DEFAULT NULL AFTER `source`,
--   ADD INDEX `visitor_logs_path_index` (`path`(191));
