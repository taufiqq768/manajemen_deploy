CREATE TABLE `it_wh_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medium',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Started',
  `bpo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `progress` int NOT NULL DEFAULT '0',
  `brd_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pain_point_uraian` text COLLATE utf8mb4_unicode_ci,
  `pain_point_impact` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `it_wh_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `it_wh_project_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Fitur',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `adjustment_date` date DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Started',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `it_wh_activities_it_wh_project_id_foreign` (`it_wh_project_id`),
  CONSTRAINT `it_wh_activities_it_wh_project_id_foreign` FOREIGN KEY (`it_wh_project_id`) REFERENCES `it_wh_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `it_wh_project_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `it_wh_project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `it_wh_project_user_it_wh_project_id_foreign` (`it_wh_project_id`),
  KEY `it_wh_project_user_user_id_foreign` (`user_id`),
  CONSTRAINT `it_wh_project_user_it_wh_project_id_foreign` FOREIGN KEY (`it_wh_project_id`) REFERENCES `it_wh_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_wh_project_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `it_wh_activity_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `it_wh_activity_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `it_wh_activity_user_it_wh_activity_id_foreign` (`it_wh_activity_id`),
  KEY `it_wh_activity_user_user_id_foreign` (`user_id`),
  CONSTRAINT `it_wh_activity_user_it_wh_activity_id_foreign` FOREIGN KEY (`it_wh_activity_id`) REFERENCES `it_wh_activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_wh_activity_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

