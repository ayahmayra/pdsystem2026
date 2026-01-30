-- Restore number_sequences table and data (FK added separately if needed)
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `number_sequences`;

CREATE TABLE `number_sequences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `doc_type` varchar(255) NOT NULL,
  `unit_scope_id` bigint(20) UNSIGNED DEFAULT NULL,
  `year_scope` smallint(5) UNSIGNED DEFAULT NULL,
  `month_scope` tinyint(3) UNSIGNED DEFAULT NULL,
  `current_value` bigint(20) UNSIGNED NOT NULL,
  `last_generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `number_sequences` (`id`, `doc_type`, `unit_scope_id`, `year_scope`, `month_scope`, `current_value`, `last_generated_at`, `created_at`, `updated_at`) VALUES
(1, 'ND', 3, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-08-28 23:15:12', '2026-01-27 07:06:02'),
(2, 'SPT', NULL, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-08-29 05:02:07', '2026-01-27 07:06:02'),
(3, 'SPPD', 3, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-08-29 08:37:58', '2026-01-27 07:06:02'),
(4, 'ND', 5, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-09-09 04:34:10', '2026-01-27 07:06:02'),
(5, 'SPPD', 5, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-09-09 04:35:09', '2026-01-27 07:06:02'),
(6, 'ND', 2, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-09-09 22:11:13', '2026-01-27 07:06:02'),
(7, 'ND', 6, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-09-21 08:55:31', '2026-01-27 07:06:02'),
(8, 'SPPD', 6, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-09-21 08:56:57', '2026-01-27 07:06:02'),
(9, 'ND', 1, 2025, NULL, 0, '2026-01-27 07:06:02', '2025-09-23 01:58:01', '2026-01-27 07:06:02'),
(10, 'ND', 6, 2026, NULL, 4, '2026-01-28 20:58:16', '2026-01-04 21:07:57', '2026-01-28 20:58:16'),
(11, 'SPT', NULL, 2026, NULL, 3, '2026-01-28 21:24:02', '2026-01-04 21:09:01', '2026-01-28 21:24:02'),
(12, 'SPPD', 6, 2026, NULL, 3, '2026-01-28 21:24:38', '2026-01-04 21:10:57', '2026-01-28 21:24:38'),
(13, 'ND', 1, 2026, NULL, 0, '2026-01-27 07:06:02', '2026-01-05 22:15:02', '2026-01-27 07:06:02'),
(14, 'SPPD', 1, 2026, NULL, 0, '2026-01-27 07:06:02', '2026-01-05 22:18:51', '2026-01-27 07:06:02'),
(15, 'ND', 3, 2026, NULL, 0, '2026-01-27 07:06:02', '2026-01-26 22:59:05', '2026-01-27 07:06:02');

ALTER TABLE `number_sequences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `number_sequences_unit_scope_id_foreign` (`unit_scope_id`);

ALTER TABLE `number_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
