-- PowerBlink FC academy dump
-- Generated: 2026-06-25T19:49:08+00:00
-- Git commit: cd330f2
-- Seeders: RolesSeeder, AcademyPermissionsSeeder, PowerblinkSiteSettingsSeeder, CmsPagesSeeder, PowerblinkDemoSeeder, MediaSeeder

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `academy_payments`;
CREATE TABLE `academy_payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `type` TEXT NOT NULL,
  `provider` TEXT NOT NULL DEFAULT 'paystack',
  `reference` TEXT NOT NULL,
  `status` TEXT NOT NULL DEFAULT 'pending',
  `amount` BIGINT UNSIGNED NOT NULL,
  `currency` TEXT NOT NULL DEFAULT 'NGN',
  `gateway_payload` TEXT,
  `paid_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `academy_payments_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `academy_payments_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  UNIQUE KEY `academy_payments_reference_unique` (`reference`),
  KEY `academy_payments_player_id_status_index` (`player_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admin_audit_trails`;
CREATE TABLE `admin_audit_trails` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED,
  `method` TEXT NOT NULL,
  `path` TEXT NOT NULL,
  `route_name` TEXT,
  `status_code` BIGINT UNSIGNED,
  `ip_address` TEXT,
  `user_agent` TEXT,
  `meta` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `admin_audit_trails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `admin_audit_trails_user_id_index` (`user_id`),
  KEY `admin_audit_trails_method_index` (`method`),
  KEY `admin_audit_trails_route_name_index` (`route_name`),
  KEY `admin_audit_trails_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `season_id` BIGINT UNSIGNED,
  `title` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  `audience` TEXT NOT NULL DEFAULT 'all',
  `channel` TEXT NOT NULL DEFAULT 'in_app',
  `published_at` DATETIME,
  `created_by` BIGINT UNSIGNED,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `announcements_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `announcements_published_at_index` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` TEXT,
  `value` TEXT NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` TEXT,
  `owner` TEXT NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` TEXT NOT NULL,
  `title` TEXT NOT NULL,
  `meta_description` TEXT,
  `content_html` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `is_active` BIGINT UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `coaches`;
CREATE TABLE `coaches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED,
  `name` TEXT NOT NULL,
  `title` TEXT,
  `bio` TEXT,
  `specialization` TEXT,
  `certifications` TEXT,
  `experience_years` BIGINT UNSIGNED,
  `license_level` TEXT,
  `email` TEXT,
  `phone` TEXT,
  `photo_media_id` BIGINT UNSIGNED,
  `is_active` BIGINT UNSIGNED NOT NULL DEFAULT '1',
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `coaches_photo_media_id_foreign` FOREIGN KEY (`photo_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `coaches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `coaches_is_active_sort_order_index` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` TEXT NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` TEXT NOT NULL,
  `exception` TEXT NOT NULL,
  `failed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `gallery_items`;
CREATE TABLE `gallery_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `media_id` BIGINT UNSIGNED NOT NULL,
  `title` TEXT,
  `category` TEXT,
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `is_published` BIGINT UNSIGNED NOT NULL DEFAULT '1',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `gallery_items_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `gallery_items_is_published_sort_order_index` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `guardians`;
CREATE TABLE `guardians` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED,
  `name` TEXT NOT NULL,
  `phone` TEXT,
  `email` TEXT NOT NULL,
  `address` TEXT,
  `relationship` TEXT,
  `emergency_contact_name` TEXT,
  `emergency_contact_phone` TEXT,
  `emergency_contact_relationship` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `guardians_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `installment_plans`;
CREATE TABLE `installment_plans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `player_id` BIGINT UNSIGNED,
  `amount` BIGINT UNSIGNED NOT NULL,
  `due_date` DATE NOT NULL,
  `status` TEXT NOT NULL DEFAULT 'pending',
  `registration_payment_id` BIGINT UNSIGNED,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `installment_plans_registration_payment_id_foreign` FOREIGN KEY (`registration_payment_id`) REFERENCES `registration_payments` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `installment_plans_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `installment_plans_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `installment_plans_registration_id_due_date_index` (`registration_id`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` TEXT,
  `name` TEXT NOT NULL,
  `total_jobs` BIGINT UNSIGNED NOT NULL,
  `pending_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_jobs` BIGINT UNSIGNED NOT NULL,
  `failed_job_ids` TEXT NOT NULL,
  `options` TEXT,
  `cancelled_at` BIGINT UNSIGNED,
  `created_at` BIGINT UNSIGNED NOT NULL,
  `finished_at` BIGINT UNSIGNED,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` TEXT NOT NULL,
  `payload` TEXT NOT NULL,
  `attempts` BIGINT UNSIGNED NOT NULL,
  `reserved_at` BIGINT UNSIGNED,
  `available_at` BIGINT UNSIGNED NOT NULL,
  `created_at` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `leadership_members`;
CREATE TABLE `leadership_members` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `title` TEXT,
  `bio` TEXT,
  `photo_media_id` BIGINT UNSIGNED,
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `leadership_members_photo_media_id_foreign` FOREIGN KEY (`photo_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `leadership_members_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` TEXT NOT NULL,
  `original_name` TEXT,
  `file_path` TEXT NOT NULL,
  `file_type` TEXT,
  `file_size` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `uploaded_by` BIGINT UNSIGNED,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `category` TEXT,
  `alt_text` TEXT,
  PRIMARY KEY (`id`),
  CONSTRAINT `media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `media_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` TEXT NOT NULL,
  `batch` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type` TEXT,
  `model_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`permission_id`, `model_type`, `model_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type` TEXT,
  `model_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`role_id`, `model_type`, `model_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` TEXT,
  `type` TEXT NOT NULL,
  `notifiable_type` TEXT NOT NULL,
  `notifiable_id` BIGINT UNSIGNED NOT NULL,
  `data` TEXT NOT NULL,
  `read_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `page_sections`;
CREATE TABLE `page_sections` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page` TEXT NOT NULL,
  `section_key` TEXT NOT NULL,
  `content_type` TEXT NOT NULL DEFAULT 'text',
  `content` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `page_sections_page_index` (`page`),
  UNIQUE KEY `page_sections_page_section_key_unique` (`page`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` TEXT,
  `token` TEXT NOT NULL,
  `created_at` DATETIME,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `performance_reports`;
CREATE TABLE `performance_reports` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `coach_id` BIGINT UNSIGNED,
  `passing` BIGINT UNSIGNED,
  `dribbling` BIGINT UNSIGNED,
  `speed` BIGINT UNSIGNED,
  `fitness` BIGINT UNSIGNED,
  `discipline` BIGINT UNSIGNED,
  `teamwork` BIGINT UNSIGNED,
  `overall_score` DECIMAL(15, 2),
  `comments` TEXT,
  `reported_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `performance_reports_coach_id_foreign` FOREIGN KEY (`coach_id`) REFERENCES `coaches` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `performance_reports_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `performance_reports_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `performance_reports_player_id_reported_at_index` (`player_id`, `reported_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `guard_name` TEXT NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `player_documents`;
CREATE TABLE `player_documents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `registration_id` BIGINT UNSIGNED,
  `document_type` TEXT NOT NULL,
  `media_id` BIGINT UNSIGNED NOT NULL,
  `status` TEXT NOT NULL DEFAULT 'pending',
  `verified_by` BIGINT UNSIGNED,
  `verified_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `player_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `player_documents_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `player_documents_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `player_documents_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `player_documents_player_id_document_type_index` (`player_id`, `document_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED,
  `user_id` BIGINT UNSIGNED,
  `guardian_id` BIGINT UNSIGNED NOT NULL,
  `program_id` BIGINT UNSIGNED NOT NULL,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `player_code` TEXT NOT NULL,
  `photo_media_id` BIGINT UNSIGNED,
  `name` TEXT NOT NULL,
  `date_of_birth` DATE,
  `nationality` TEXT,
  `primary_position` TEXT,
  `secondary_position` TEXT,
  `years_experience` BIGINT UNSIGNED,
  `technical_strengths` TEXT,
  `allergies` TEXT,
  `medical_history` TEXT,
  `status` TEXT NOT NULL DEFAULT 'active',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `players_photo_media_id_foreign` FOREIGN KEY (`photo_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `players_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `players_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `players_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `players_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `players_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  UNIQUE KEY `players_registration_id_unique` (`registration_id`),
  UNIQUE KEY `players_player_code_unique` (`player_code`),
  KEY `players_program_id_status_index` (`program_id`, `status`),
  KEY `players_season_id_status_index` (`season_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `name` TEXT NOT NULL,
  `age_group` TEXT NOT NULL,
  `description` TEXT,
  `monthly_fee` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `registration_fee` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `max_capacity` BIGINT UNSIGNED,
  `sessions_per_week` BIGINT UNSIGNED,
  `is_active` BIGINT UNSIGNED NOT NULL DEFAULT '1',
  `hero_image_media_id` BIGINT UNSIGNED,
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `programs_hero_image_media_id_foreign` FOREIGN KEY (`hero_image_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `programs_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `programs_season_id_is_active_index` (`season_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `registration_payments`;
CREATE TABLE `registration_payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_id` BIGINT UNSIGNED NOT NULL,
  `player_id` BIGINT UNSIGNED,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `type` TEXT NOT NULL,
  `provider` TEXT NOT NULL DEFAULT 'paystack',
  `reference` TEXT NOT NULL,
  `status` TEXT NOT NULL DEFAULT 'pending',
  `amount` BIGINT UNSIGNED NOT NULL,
  `currency` TEXT NOT NULL DEFAULT 'NGN',
  `gateway_payload` TEXT,
  `paid_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `registration_payments_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `registration_payments_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `registration_payments_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  UNIQUE KEY `registration_payments_reference_unique` (`reference`),
  KEY `registration_payments_registration_id_status_index` (`registration_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `registrations`;
CREATE TABLE `registrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference_code` TEXT NOT NULL,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `program_id` BIGINT UNSIGNED NOT NULL,
  `guardian_id` BIGINT UNSIGNED NOT NULL,
  `status` TEXT NOT NULL DEFAULT 'pending_review',
  `payment_plan` TEXT NOT NULL DEFAULT 'lump_sum',
  `payment_token` TEXT,
  `payment_token_expires_at` DATETIME,
  `payment_token_used_at` DATETIME,
  `player_name` TEXT NOT NULL,
  `date_of_birth` DATE,
  `nationality` TEXT,
  `primary_position` TEXT,
  `secondary_position` TEXT,
  `years_experience` BIGINT UNSIGNED,
  `technical_strengths` TEXT,
  `allergies` TEXT,
  `medical_history` TEXT,
  `fitness_certified` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `profile_photo_media_id` BIGINT UNSIGNED,
  `emergency_contact_name` TEXT,
  `emergency_contact_phone` TEXT,
  `emergency_contact_relationship` TEXT,
  `approved_by` BIGINT UNSIGNED,
  `approved_at` DATETIME,
  `rejected_reason` TEXT,
  `rejected_at` DATETIME,
  `submitted_at` DATETIME,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `registrations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `registrations_profile_photo_media_id_foreign` FOREIGN KEY (`profile_photo_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `registrations_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `registrations_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `registrations_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  UNIQUE KEY `registrations_payment_token_unique` (`payment_token`),
  UNIQUE KEY `registrations_reference_code_unique` (`reference_code`),
  KEY `registrations_season_id_status_index` (`season_id`, `status`),
  KEY `registrations_status_submitted_at_index` (`status`, `submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`permission_id`, `role_id`),
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `guard_name` TEXT NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `seasons`;
CREATE TABLE `seasons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_active` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `seasons_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `session_attendance`;
CREATE TABLE `session_attendance` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `training_session_id` BIGINT UNSIGNED NOT NULL,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `status` TEXT NOT NULL DEFAULT 'present',
  `remarks` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `session_attendance_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `session_attendance_training_session_id_foreign` FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  UNIQUE KEY `session_attendance_training_session_id_player_id_unique` (`training_session_id`, `player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` TEXT,
  `user_id` BIGINT UNSIGNED,
  `ip_address` TEXT,
  `user_agent` TEXT,
  `payload` TEXT NOT NULL,
  `last_activity` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  KEY `sessions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` TEXT NOT NULL,
  `value` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_traffic_events`;
CREATE TABLE `site_traffic_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` TEXT NOT NULL,
  `route_name` TEXT,
  `url` TEXT,
  `method` TEXT NOT NULL DEFAULT 'GET',
  `referrer_host` TEXT,
  `referrer_url` TEXT,
  `user_agent` TEXT,
  `ip_hash` TEXT,
  `session_id` TEXT,
  `viewed_at` DATETIME NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `site_traffic_events_viewed_at_session_id_index` (`viewed_at`, `session_id`),
  KEY `site_traffic_events_viewed_at_route_name_index` (`viewed_at`, `route_name`),
  KEY `site_traffic_events_viewed_at_path_index` (`viewed_at`, `path`),
  KEY `site_traffic_events_viewed_at_index` (`viewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `timeline_events`;
CREATE TABLE `timeline_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `year` BIGINT UNSIGNED NOT NULL,
  `title` TEXT NOT NULL,
  `description` TEXT,
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `timeline_events_year_sort_order_index` (`year`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tournament_squads`;
CREATE TABLE `tournament_squads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tournament_id` BIGINT UNSIGNED NOT NULL,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `position` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `tournament_squads_player_id_foreign` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tournament_squads_tournament_id_foreign` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  UNIQUE KEY `tournament_squads_tournament_id_player_id_unique` (`tournament_id`, `player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tournaments`;
CREATE TABLE `tournaments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `title` TEXT NOT NULL,
  `category` TEXT,
  `start_date` DATE,
  `end_date` DATE,
  `location` TEXT,
  `description` TEXT,
  `status` TEXT NOT NULL DEFAULT 'upcoming',
  `max_teams` BIGINT UNSIGNED,
  `featured_image_media_id` BIGINT UNSIGNED,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `tournaments_featured_image_media_id_foreign` FOREIGN KEY (`featured_image_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `tournaments_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `tournaments_season_id_status_index` (`season_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `training_sessions`;
CREATE TABLE `training_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `program_id` BIGINT UNSIGNED NOT NULL,
  `coach_id` BIGINT UNSIGNED,
  `title` TEXT NOT NULL,
  `session_type` TEXT,
  `date` DATE NOT NULL,
  `start_time` TEXT NOT NULL,
  `end_time` TEXT NOT NULL,
  `location` TEXT,
  `notes` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `deleted_at` DATETIME,
  PRIMARY KEY (`id`),
  CONSTRAINT `training_sessions_coach_id_foreign` FOREIGN KEY (`coach_id`) REFERENCES `coaches` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `training_sessions_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `training_sessions_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `training_sessions_program_id_date_index` (`program_id`, `date`),
  KEY `training_sessions_season_id_date_index` (`season_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `email_verified_at` DATETIME,
  `password` TEXT NOT NULL,
  `remember_token` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `google_id` TEXT,
  `avatar` TEXT,
  `email_login_otp_enabled` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `is_super_admin` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `users_is_super_admin_index` (`is_super_admin`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (1, 1, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-001', 'pending', 4500000, 'NGN', NULL, NULL, '2026-06-25 19:47:27', '2026-06-25 19:47:27');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (2, 2, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-002', 'success', 4750000, 'NGN', NULL, '2026-06-02 00:00:00', '2026-06-25 19:47:27', '2026-06-25 19:47:27');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (3, 3, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-003', 'success', 5000000, 'NGN', NULL, '2026-06-03 00:00:00', '2026-06-25 19:47:27', '2026-06-25 19:47:27');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (4, 4, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-004', 'success', 5250000, 'NGN', NULL, '2026-06-04 00:00:00', '2026-06-25 19:47:27', '2026-06-25 19:47:27');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (5, 5, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-005', 'pending', 5500000, 'NGN', NULL, NULL, '2026-06-25 19:47:28', '2026-06-25 19:47:28');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (6, 6, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-006', 'success', 5750000, 'NGN', NULL, '2026-06-06 00:00:00', '2026-06-25 19:47:28', '2026-06-25 19:47:28');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (7, 7, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-007', 'success', 6000000, 'NGN', NULL, '2026-06-07 00:00:00', '2026-06-25 19:47:28', '2026-06-25 19:47:28');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (8, 8, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-008', 'success', 6250000, 'NGN', NULL, '2026-06-08 00:00:00', '2026-06-25 19:47:28', '2026-06-25 19:47:28');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (9, 9, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-009', 'pending', 6500000, 'NGN', NULL, NULL, '2026-06-25 19:47:28', '2026-06-25 19:47:28');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (10, 10, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-010', 'success', 6750000, 'NGN', NULL, '2026-06-10 00:00:00', '2026-06-25 19:47:28', '2026-06-25 19:47:28');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (11, 11, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-011', 'success', 7000000, 'NGN', NULL, '2026-06-11 00:00:00', '2026-06-25 19:47:29', '2026-06-25 19:47:29');
INSERT INTO `academy_payments` (`id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (12, 12, 1, 'monthly_fee', 'paystack', 'PB-PAY-MONTHLY-012', 'success', 7250000, 'NGN', NULL, '2026-06-12 00:00:00', '2026-06-25 19:47:29', '2026-06-25 19:47:29');

INSERT INTO `announcements` (`id`, `season_id`, `title`, `body`, `audience`, `channel`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES (1, 1, '2026 Season Registration Now Open', 'Applications are open for U7 through U15 programs.', 'parents', 'in_app', '2026-01-05 08:00:00', 1, '2026-06-25 19:48:38', '2026-06-25 19:48:38');
INSERT INTO `announcements` (`id`, `season_id`, `title`, `body`, `audience`, `channel`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES (2, 1, 'Independence Day Tournament Squad Selection', 'Coaches will announce preliminary tournament squads after the June assessment window.', 'all', 'in_app', '2026-06-10 09:00:00', 1, '2026-06-25 19:48:38', '2026-06-25 19:48:38');
INSERT INTO `announcements` (`id`, `season_id`, `title`, `body`, `audience`, `channel`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES (3, 1, 'Parent-Coach Meeting — July Schedule', 'Monthly parent-coach meetings resume every first Saturday at 10:00 AM.', 'parents', 'in_app', '2026-06-12 09:00:00', 1, '2026-06-25 19:48:38', '2026-06-25 19:48:38');
INSERT INTO `announcements` (`id`, `season_id`, `title`, `body`, `audience`, `channel`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES (4, 1, 'U15 Elite Trial Day', 'Selected U15 players invited for advanced trial sessions on Pitch B.', 'players', 'in_app', '2026-06-14 11:00:00', 1, '2026-06-25 19:48:38', '2026-06-25 19:48:38');
INSERT INTO `announcements` (`id`, `season_id`, `title`, `body`, `audience`, `channel`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES (5, 1, 'Coach Development Workshop', 'All coaching staff attend safeguarding and curriculum refresh workshop.', 'coaches', 'in_app', '2026-06-16 08:30:00', 1, '2026-06-25 19:48:38', '2026-06-25 19:48:38');
INSERT INTO `announcements` (`id`, `season_id`, `title`, `body`, `audience`, `channel`, `published_at`, `created_by`, `created_at`, `updated_at`) VALUES (6, 1, 'Payment Reminder — July Installments', 'Families on installment plans should complete July dues by the 5th.', 'parents', 'in_app', '2026-06-18 07:00:00', 1, '2026-06-25 19:48:38', '2026-06-25 19:48:38');

INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (1, 'home', 'Home', 'Powerblink FC — Elite football academy in Ibeju Lekki developing tomorrow''s stars.', '', '2026-06-25 19:47:09', '2026-06-25 19:48:44', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (2, 'about', 'About Powerblink FC', 'Learn about our mission, coaching philosophy, and elite academy facilities.', '', '2026-06-25 19:47:09', '2026-06-25 19:48:44', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (3, 'programs', 'Programs', 'Age-group pathways and academy programs.', '', '2026-06-25 19:47:09', '2026-06-25 19:47:09', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (4, 'coaching', 'Coaching Team', 'Meet our licensed coaching staff.', '', '2026-06-25 19:47:09', '2026-06-25 19:47:09', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (5, 'gallery', 'Gallery', 'Academy moments and match highlights.', '', '2026-06-25 19:47:09', '2026-06-25 19:47:09', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (6, 'tournaments', 'Tournaments', 'Competitive fixtures and academy tournaments.', '', '2026-06-25 19:47:09', '2026-06-25 19:47:09', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (7, 'faq', 'FAQ', 'Frequently asked questions about programs, fees, and registration.', '', '2026-06-25 19:47:10', '2026-06-25 19:48:44', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (8, 'contact', 'Contact Us', 'Reach Powerblink FC for registrations, tours, and academy enquiries.', '', '2026-06-25 19:47:10', '2026-06-25 19:48:44', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (9, 'privacy-policy', 'Privacy Policy', 'How we collect, use, and protect your information.', '', '2026-06-25 19:47:10', '2026-06-25 19:47:10', 1);
INSERT INTO `cms_pages` (`id`, `slug`, `title`, `meta_description`, `content_html`, `created_at`, `updated_at`, `is_active`) VALUES (10, 'terms', 'Terms & Conditions', 'Terms governing use of our academy platform.', '', '2026-06-25 19:47:10', '2026-06-25 19:47:10', 1);

INSERT INTO `coaches` (`id`, `user_id`, `name`, `title`, `bio`, `specialization`, `certifications`, `experience_years`, `license_level`, `email`, `phone`, `photo_media_id`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 2, 'Coach Elijah Opetunde', 'Head Coach', 'Former professional midfielder with 18 years coaching elite academy squads across West Africa.', 'Youth Development', '["UEFA B License","CAF C License","Safeguarding Level 2"]', 18, 'UEFA B', 'coach@powerblinkfc.com', '+234 801 234 5678', 5, 1, 1, '2026-06-25 19:47:18', '2026-06-25 19:47:18', NULL);
INSERT INTO `coaches` (`id`, `user_id`, `name`, `title`, `bio`, `specialization`, `certifications`, `experience_years`, `license_level`, `email`, `phone`, `photo_media_id`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, NULL, 'Coach Amara Nwosu', 'Technical Director', 'Architect of Powerblink FC curriculum with a focus on positional intelligence and match analysis.', 'Tactical Systems', '["UEFA A License","FA Level 3","Sports Analytics Certificate"]', 14, 'UEFA A', 'amara.nwosu@powerblinkfc.com', '+234 802 345 6789', 6, 1, 2, '2026-06-25 19:47:19', '2026-06-25 19:47:19', NULL);
INSERT INTO `coaches` (`id`, `user_id`, `name`, `title`, `bio`, `specialization`, `certifications`, `experience_years`, `license_level`, `email`, `phone`, `photo_media_id`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, NULL, 'Coach Kunle Adeyemi', 'Goalkeeper Coach', 'Specialist goalkeeper coach developing reflexes, distribution, and command of area.', 'Shot Stopping', '["GK Level 2","CAF C License","First Aid Certified"]', 9, 'GK Level 2', 'kunle.adeyemi@powerblinkfc.com', '+234 803 456 7890', 7, 1, 3, '2026-06-25 19:47:19', '2026-06-25 19:47:19', NULL);
INSERT INTO `coaches` (`id`, `user_id`, `name`, `title`, `bio`, `specialization`, `certifications`, `experience_years`, `license_level`, `email`, `phone`, `photo_media_id`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, NULL, 'Coach Priya Mensah', 'Sports Science Lead', 'Leads academy fitness testing, load management, and recovery protocols.', 'Conditioning', '["MSc Sports Science","Strength & Conditioning Level 1","Nutrition Basics"]', 11, 'S&C Level 1', 'priya.mensah@powerblinkfc.com', '+234 804 567 8901', 8, 1, 4, '2026-06-25 19:47:19', '2026-06-25 19:47:19', NULL);

INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (1, 26, 'Golden Hour Training', 'training', 1, 1, '2026-06-25 19:48:36', '2026-06-25 19:48:36');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (2, 14, 'U7 Grassroots Joy', 'programs', 2, 1, '2026-06-25 19:48:36', '2026-06-25 19:48:36');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (3, 27, 'Tactical Briefing', 'coaching', 3, 1, '2026-06-25 19:48:36', '2026-06-25 19:48:36');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (4, 23, 'Tournament Action', 'tournaments', 4, 1, '2026-06-25 19:48:36', '2026-06-25 19:48:36');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (5, 28, 'Academy Facilities', 'facilities', 5, 1, '2026-06-25 19:48:37', '2026-06-25 19:48:37');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (6, 24, 'Match Day Energy', 'tournaments', 6, 1, '2026-06-25 19:48:37', '2026-06-25 19:48:37');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (7, 25, 'Goalkeeper Training', 'training', 7, 1, '2026-06-25 19:48:37', '2026-06-25 19:48:37');
INSERT INTO `gallery_items` (`id`, `media_id`, `title`, `category`, `sort_order`, `is_published`, `created_at`, `updated_at`) VALUES (8, 29, 'Team Celebration', 'programs', 8, 1, '2026-06-25 19:48:37', '2026-06-25 19:48:37');

INSERT INTO `guardians` (`id`, `user_id`, `name`, `phone`, `email`, `address`, `relationship`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 3, 'Adaeze Okonkwo', '+234 805 678 9012', 'parent@powerblinkfc.com', '12 Admiralty Way, Lekki Phase 1, Lagos', 'Mother', 'Chidi Okonkwo', '+234 806 789 0123', 'Father', '2026-06-25 19:47:20', '2026-06-25 19:47:20', NULL);
INSERT INTO `guardians` (`id`, `user_id`, `name`, `phone`, `email`, `address`, `relationship`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, NULL, 'Chidi Okonkwo', '+234 807 890 1234', 'chidi.okonkwo@example.com', '45 Victoria Island, Lagos', 'Father', 'Adaeze Okonkwo', '+234 805 678 9012', 'Mother', '2026-06-25 19:47:20', '2026-06-25 19:47:20', NULL);
INSERT INTO `guardians` (`id`, `user_id`, `name`, `phone`, `email`, `address`, `relationship`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, NULL, 'Fatima Yusuf', '+234 808 111 2233', 'fatima.yusuf@example.com', '8 Chevron Drive, Lekki', 'Mother', 'Academy Office', '+234 700 000 0000', 'Staff', '2026-06-25 19:47:20', '2026-06-25 19:47:20', NULL);
INSERT INTO `guardians` (`id`, `user_id`, `name`, `phone`, `email`, `address`, `relationship`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, NULL, 'James Okafor', '+234 809 222 3344', 'james.okafor@example.com', '22 Ajah Road, Lagos', 'Father', 'Academy Office', '+234 700 000 0000', 'Staff', '2026-06-25 19:47:20', '2026-06-25 19:47:20', NULL);
INSERT INTO `guardians` (`id`, `user_id`, `name`, `phone`, `email`, `address`, `relationship`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, NULL, 'Blessing Nnamdi', '+234 810 333 4455', 'blessing.nnamdi@example.com', '5 Sangotedo, Lagos', 'Guardian', 'Academy Office', '+234 700 000 0000', 'Staff', '2026-06-25 19:47:20', '2026-06-25 19:47:20', NULL);
INSERT INTO `guardians` (`id`, `user_id`, `name`, `phone`, `email`, `address`, `relationship`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, NULL, 'Henry Davies', '+234 811 444 5566', 'henry.davies@example.com', '14 Ikoyi, Lagos', 'Father', 'Academy Office', '+234 700 000 0000', 'Staff', '2026-06-25 19:47:20', '2026-06-25 19:47:20', NULL);

INSERT INTO `installment_plans` (`id`, `registration_id`, `player_id`, `amount`, `due_date`, `status`, `registration_payment_id`, `created_at`, `updated_at`) VALUES (1, 3, NULL, 1333333, '2026-07-01 00:00:00', 'pending', NULL, '2026-06-25 19:47:29', '2026-06-25 19:47:29');
INSERT INTO `installment_plans` (`id`, `registration_id`, `player_id`, `amount`, `due_date`, `status`, `registration_payment_id`, `created_at`, `updated_at`) VALUES (2, 3, NULL, 1333333, '2026-08-01 00:00:00', 'pending', NULL, '2026-06-25 19:47:29', '2026-06-25 19:47:29');
INSERT INTO `installment_plans` (`id`, `registration_id`, `player_id`, `amount`, `due_date`, `status`, `registration_payment_id`, `created_at`, `updated_at`) VALUES (3, 3, NULL, 1333334, '2026-09-01 00:00:00', 'pending', NULL, '2026-06-25 19:47:29', '2026-06-25 19:47:29');
INSERT INTO `installment_plans` (`id`, `registration_id`, `player_id`, `amount`, `due_date`, `status`, `registration_payment_id`, `created_at`, `updated_at`) VALUES (4, 1, 1, 3500000, '2026-01-13 00:00:00', 'paid', NULL, '2026-06-25 19:47:29', '2026-06-25 19:47:29');
INSERT INTO `installment_plans` (`id`, `registration_id`, `player_id`, `amount`, `due_date`, `status`, `registration_payment_id`, `created_at`, `updated_at`) VALUES (5, 1, 1, 6500000, '2026-06-01 00:00:00', 'paid', NULL, '2026-06-25 19:47:29', '2026-06-25 19:47:29');
INSERT INTO `installment_plans` (`id`, `registration_id`, `player_id`, `amount`, `due_date`, `status`, `registration_payment_id`, `created_at`, `updated_at`) VALUES (6, 3, NULL, 1333333, '2026-06-01 00:00:00', 'overdue', NULL, '2026-06-25 19:47:30', '2026-06-25 19:47:30');

INSERT INTO `leadership_members` (`id`, `name`, `title`, `bio`, `photo_media_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 'Elijah Opetunde', 'Director of Football', 'Leads the football philosophy and long-term player pathway at Powerblink FC.', 5, 1, '2026-06-25 19:48:43', '2026-06-25 19:48:43', NULL);
INSERT INTO `leadership_members` (`id`, `name`, `title`, `bio`, `photo_media_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 'Ngozi Ekeh', 'Academy Chair', 'Oversees governance, partnerships, and community engagement for the academy.', 32, 2, '2026-06-25 19:48:43', '2026-06-25 19:48:43', NULL);

INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (1, 'programs-powerblink-fc-074.jpg', 'programs-powerblink-fc-074.jpg', 'asset/images/powerblink/programs-powerblink-fc-074.jpg', 'jpg', 380903, NULL, '2026-06-25 19:47:17', '2026-06-25 19:49:05', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (2, 'programs-powerblink-fc-075.jpg', 'programs-powerblink-fc-075.jpg', 'asset/images/powerblink/programs-powerblink-fc-075.jpg', 'jpg', 418850, NULL, '2026-06-25 19:47:17', '2026-06-25 19:49:05', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (3, 'programs-powerblink-fc-076.jpg', 'programs-powerblink-fc-076.jpg', 'asset/images/powerblink/programs-powerblink-fc-076.jpg', 'jpg', 345044, NULL, '2026-06-25 19:47:17', '2026-06-25 19:49:05', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (4, 'programs-powerblink-fc-077.jpg', 'programs-powerblink-fc-077.jpg', 'asset/images/powerblink/programs-powerblink-fc-077.jpg', 'jpg', 342692, NULL, '2026-06-25 19:47:18', '2026-06-25 19:49:06', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (5, 'coaching-team-powerblink-fc-025.jpg', 'coaching-team-powerblink-fc-025.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-025.jpg', 'jpg', 376996, NULL, '2026-06-25 19:47:18', '2026-06-25 19:48:56', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (6, 'coaching-team-management-powerblink-fc-020.jpg', 'coaching-team-management-powerblink-fc-020.jpg', 'asset/images/powerblink/coaching-team-management-powerblink-fc-020.jpg', 'jpg', 391766, NULL, '2026-06-25 19:47:18', '2026-06-25 19:48:56', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (7, 'coaching-team-management-powerblink-fc-021.jpg', 'coaching-team-management-powerblink-fc-021.jpg', 'asset/images/powerblink/coaching-team-management-powerblink-fc-021.jpg', 'jpg', 401237, NULL, '2026-06-25 19:47:19', '2026-06-25 19:48:56', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (8, 'coaching-team-management-powerblink-fc-022.jpg', 'coaching-team-management-powerblink-fc-022.jpg', 'asset/images/powerblink/coaching-team-management-powerblink-fc-022.jpg', 'jpg', 390276, NULL, '2026-06-25 19:47:19', '2026-06-25 19:48:56', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (9, 'admin-dashboard-powerblink-fc-011.jpg', 'admin-dashboard-powerblink-fc-011.jpg', 'asset/images/powerblink/admin-dashboard-powerblink-fc-011.jpg', 'jpg', 190856, NULL, '2026-06-25 19:47:21', '2026-06-25 19:48:54', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (10, 'registrations-powerblink-fc-080.jpg', 'registrations-powerblink-fc-080.jpg', 'asset/images/powerblink/registrations-powerblink-fc-080.jpg', 'jpg', 340975, NULL, '2026-06-25 19:47:21', '2026-06-25 19:49:06', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (11, 'performance-analytics-powerblink-fc-055.jpg', 'performance-analytics-powerblink-fc-055.jpg', 'asset/images/powerblink/performance-analytics-powerblink-fc-055.jpg', 'jpg', 369492, NULL, '2026-06-25 19:47:22', '2026-06-25 19:49:01', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (12, 'player-dashboard-powerblink-fc-060.jpg', 'player-dashboard-powerblink-fc-060.jpg', 'asset/images/powerblink/player-dashboard-powerblink-fc-060.jpg', 'jpg', 337537, NULL, '2026-06-25 19:47:22', '2026-06-25 19:49:03', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (13, 'attendance-tracking-powerblink-fc-015.jpg', 'attendance-tracking-powerblink-fc-015.jpg', 'asset/images/powerblink/attendance-tracking-powerblink-fc-015.jpg', 'jpg', 416201, NULL, '2026-06-25 19:47:23', '2026-06-25 19:48:55', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (14, 'home-powerblink-fc-046.jpg', 'home-powerblink-fc-046.jpg', 'asset/images/powerblink/home-powerblink-fc-046.jpg', 'jpg', 378546, NULL, '2026-06-25 19:47:23', '2026-06-25 19:49:00', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (15, 'player-management-powerblink-fc-061.jpg', 'player-management-powerblink-fc-061.jpg', 'asset/images/powerblink/player-management-powerblink-fc-061.jpg', 'jpg', 0, NULL, '2026-06-25 19:47:24', '2026-06-25 19:47:24', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (16, 'player-management-powerblink-fc-062.jpg', 'player-management-powerblink-fc-062.jpg', 'asset/images/powerblink/player-management-powerblink-fc-062.jpg', 'jpg', 0, NULL, '2026-06-25 19:47:24', '2026-06-25 19:47:24', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (17, 'player-management-powerblink-fc-063.jpg', 'player-management-powerblink-fc-063.jpg', 'asset/images/powerblink/player-management-powerblink-fc-063.jpg', 'jpg', 0, NULL, '2026-06-25 19:47:24', '2026-06-25 19:47:24', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (18, 'player-management-powerblink-fc-064.jpg', 'player-management-powerblink-fc-064.jpg', 'asset/images/powerblink/player-management-powerblink-fc-064.jpg', 'jpg', 358990, NULL, '2026-06-25 19:47:25', '2026-06-25 19:49:03', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (19, 'player-management-powerblink-fc-065.jpg', 'player-management-powerblink-fc-065.jpg', 'asset/images/powerblink/player-management-powerblink-fc-065.jpg', 'jpg', 252831, NULL, '2026-06-25 19:47:25', '2026-06-25 19:49:03', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (20, 'player-dashboard-powerblink-fc-061.jpg', 'player-dashboard-powerblink-fc-061.jpg', 'asset/images/powerblink/player-dashboard-powerblink-fc-061.jpg', 'jpg', 421294, NULL, '2026-06-25 19:47:25', '2026-06-25 19:49:03', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (21, 'player-dashboard-powerblink-fc-062.jpg', 'player-dashboard-powerblink-fc-062.jpg', 'asset/images/powerblink/player-dashboard-powerblink-fc-062.jpg', 'jpg', 390516, NULL, '2026-06-25 19:47:26', '2026-06-25 19:49:03', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (22, 'player-dashboard-powerblink-fc-063.jpg', 'player-dashboard-powerblink-fc-063.jpg', 'asset/images/powerblink/player-dashboard-powerblink-fc-063.jpg', 'jpg', 392948, NULL, '2026-06-25 19:47:26', '2026-06-25 19:49:03', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (23, 'home-powerblink-fc-054.jpg', 'home-powerblink-fc-054.jpg', 'asset/images/powerblink/home-powerblink-fc-054.jpg', 'jpg', 412635, NULL, '2026-06-25 19:48:29', '2026-06-25 19:49:01', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (24, 'home-powerblink-fc-047.jpg', 'home-powerblink-fc-047.jpg', 'asset/images/powerblink/home-powerblink-fc-047.jpg', 'jpg', 354336, NULL, '2026-06-25 19:48:31', '2026-06-25 19:49:00', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (25, 'home-powerblink-fc-048.jpg', 'home-powerblink-fc-048.jpg', 'asset/images/powerblink/home-powerblink-fc-048.jpg', 'jpg', 320250, NULL, '2026-06-25 19:48:34', '2026-06-25 19:49:00', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (26, 'home-powerblink-fc-044.jpg', 'home-powerblink-fc-044.jpg', 'asset/images/powerblink/home-powerblink-fc-044.jpg', 'jpg', 446847, NULL, '2026-06-25 19:48:36', '2026-06-25 19:49:00', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (27, 'home-powerblink-fc-045.jpg', 'home-powerblink-fc-045.jpg', 'asset/images/powerblink/home-powerblink-fc-045.jpg', 'jpg', 404670, NULL, '2026-06-25 19:48:36', '2026-06-25 19:49:00', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (28, 'about-us-powerblink-fc-001.jpg', 'about-us-powerblink-fc-001.jpg', 'asset/images/powerblink/about-us-powerblink-fc-001.jpg', 'jpg', 382506, NULL, '2026-06-25 19:48:37', '2026-06-25 19:48:53', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (29, 'home-powerblink-fc-049.jpg', 'home-powerblink-fc-049.jpg', 'asset/images/powerblink/home-powerblink-fc-049.jpg', 'jpg', 342884, NULL, '2026-06-25 19:48:37', '2026-06-25 19:49:01', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (30, 'registration-powerblink-fc-078.jpg', 'registration-powerblink-fc-078.jpg', 'asset/images/powerblink/registration-powerblink-fc-078.jpg', 'jpg', 0, NULL, '2026-06-25 19:48:39', '2026-06-25 19:48:39', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (31, 'registration-powerblink-fc-079.jpg', 'registration-powerblink-fc-079.jpg', 'asset/images/powerblink/registration-powerblink-fc-079.jpg', 'jpg', 0, NULL, '2026-06-25 19:48:39', '2026-06-25 19:48:39', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (32, 'coaching-team-powerblink-fc-027.jpg', 'coaching-team-powerblink-fc-027.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-027.jpg', 'jpg', 343266, NULL, '2026-06-25 19:48:43', '2026-06-25 19:48:57', 'powerblink', NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (33, 'about-us-powerblink-fc-002.jpg', 'about-us-powerblink-fc-002.jpg', 'asset/images/powerblink/about-us-powerblink-fc-002.jpg', 'jpg', 489244, NULL, '2026-06-25 19:48:53', '2026-06-25 19:48:53', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (34, 'about-us-powerblink-fc-003.jpg', 'about-us-powerblink-fc-003.jpg', 'asset/images/powerblink/about-us-powerblink-fc-003.jpg', 'jpg', 388498, NULL, '2026-06-25 19:48:53', '2026-06-25 19:48:53', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (35, 'about-us-powerblink-fc-004.jpg', 'about-us-powerblink-fc-004.jpg', 'asset/images/powerblink/about-us-powerblink-fc-004.jpg', 'jpg', 386218, NULL, '2026-06-25 19:48:53', '2026-06-25 19:48:53', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (36, 'about-us-powerblink-fc-005.jpg', 'about-us-powerblink-fc-005.jpg', 'asset/images/powerblink/about-us-powerblink-fc-005.jpg', 'jpg', 385688, NULL, '2026-06-25 19:48:53', '2026-06-25 19:48:53', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (37, 'about-us-powerblink-fc-006.jpg', 'about-us-powerblink-fc-006.jpg', 'asset/images/powerblink/about-us-powerblink-fc-006.jpg', 'jpg', 459308, NULL, '2026-06-25 19:48:54', '2026-06-25 19:48:54', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (38, 'about-us-powerblink-fc-007.jpg', 'about-us-powerblink-fc-007.jpg', 'asset/images/powerblink/about-us-powerblink-fc-007.jpg', 'jpg', 356782, NULL, '2026-06-25 19:48:54', '2026-06-25 19:48:54', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (39, 'about-us-powerblink-fc-008.jpg', 'about-us-powerblink-fc-008.jpg', 'asset/images/powerblink/about-us-powerblink-fc-008.jpg', 'jpg', 352932, NULL, '2026-06-25 19:48:54', '2026-06-25 19:48:54', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (40, 'about-us-powerblink-fc-009.jpg', 'about-us-powerblink-fc-009.jpg', 'asset/images/powerblink/about-us-powerblink-fc-009.jpg', 'jpg', 442767, NULL, '2026-06-25 19:48:54', '2026-06-25 19:48:54', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (41, 'admin-dashboard-powerblink-fc-010.jpg', 'admin-dashboard-powerblink-fc-010.jpg', 'asset/images/powerblink/admin-dashboard-powerblink-fc-010.jpg', 'jpg', 390710, NULL, '2026-06-25 19:48:54', '2026-06-25 19:48:54', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (42, 'admin-dashboard-powerblink-fc-012.jpg', 'admin-dashboard-powerblink-fc-012.jpg', 'asset/images/powerblink/admin-dashboard-powerblink-fc-012.jpg', 'jpg', 262549, NULL, '2026-06-25 19:48:55', '2026-06-25 19:48:55', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (43, 'admin-dashboard-powerblink-fc-013.jpg', 'admin-dashboard-powerblink-fc-013.jpg', 'asset/images/powerblink/admin-dashboard-powerblink-fc-013.jpg', 'jpg', 187745, NULL, '2026-06-25 19:48:55', '2026-06-25 19:48:55', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (44, 'attendance-tracking-powerblink-fc-014.jpg', 'attendance-tracking-powerblink-fc-014.jpg', 'asset/images/powerblink/attendance-tracking-powerblink-fc-014.jpg', 'jpg', 398461, NULL, '2026-06-25 19:48:55', '2026-06-25 19:48:55', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (45, 'attendance-tracking-powerblink-fc-016.jpg', 'attendance-tracking-powerblink-fc-016.jpg', 'asset/images/powerblink/attendance-tracking-powerblink-fc-016.jpg', 'jpg', 374512, NULL, '2026-06-25 19:48:55', '2026-06-25 19:48:55', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (46, 'attendance-tracking-powerblink-fc-017.jpg', 'attendance-tracking-powerblink-fc-017.jpg', 'asset/images/powerblink/attendance-tracking-powerblink-fc-017.jpg', 'jpg', 408752, NULL, '2026-06-25 19:48:55', '2026-06-25 19:48:55', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (47, 'attendance-tracking-powerblink-fc-018.jpg', 'attendance-tracking-powerblink-fc-018.jpg', 'asset/images/powerblink/attendance-tracking-powerblink-fc-018.jpg', 'jpg', 190728, NULL, '2026-06-25 19:48:55', '2026-06-25 19:48:55', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (48, 'coaching-team-management-powerblink-fc-019.jpg', 'coaching-team-management-powerblink-fc-019.jpg', 'asset/images/powerblink/coaching-team-management-powerblink-fc-019.jpg', 'jpg', 381446, NULL, '2026-06-25 19:48:56', '2026-06-25 19:48:56', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (49, 'coaching-team-management-powerblink-fc-023.jpg', 'coaching-team-management-powerblink-fc-023.jpg', 'asset/images/powerblink/coaching-team-management-powerblink-fc-023.jpg', 'jpg', 415561, NULL, '2026-06-25 19:48:56', '2026-06-25 19:48:56', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (50, 'coaching-team-powerblink-fc-024.jpg', 'coaching-team-powerblink-fc-024.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-024.jpg', 'jpg', 357543, NULL, '2026-06-25 19:48:56', '2026-06-25 19:48:56', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (51, 'coaching-team-powerblink-fc-026.jpg', 'coaching-team-powerblink-fc-026.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-026.jpg', 'jpg', 379190, NULL, '2026-06-25 19:48:57', '2026-06-25 19:48:57', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (52, 'coaching-team-powerblink-fc-028.jpg', 'coaching-team-powerblink-fc-028.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-028.jpg', 'jpg', 351558, NULL, '2026-06-25 19:48:57', '2026-06-25 19:48:57', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (53, 'coaching-team-powerblink-fc-029.jpg', 'coaching-team-powerblink-fc-029.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-029.jpg', 'jpg', 423340, NULL, '2026-06-25 19:48:57', '2026-06-25 19:48:57', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (54, 'coaching-team-powerblink-fc-030.jpg', 'coaching-team-powerblink-fc-030.jpg', 'asset/images/powerblink/coaching-team-powerblink-fc-030.jpg', 'jpg', 458733, NULL, '2026-06-25 19:48:57', '2026-06-25 19:48:57', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (55, 'communications-center-powerblink-fc-031.jpg', 'communications-center-powerblink-fc-031.jpg', 'asset/images/powerblink/communications-center-powerblink-fc-031.jpg', 'jpg', 363026, NULL, '2026-06-25 19:48:57', '2026-06-25 19:48:57', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (56, 'communications-center-powerblink-fc-032.jpg', 'communications-center-powerblink-fc-032.jpg', 'asset/images/powerblink/communications-center-powerblink-fc-032.jpg', 'jpg', 467328, NULL, '2026-06-25 19:48:58', '2026-06-25 19:48:58', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (57, 'contact-us-powerblink-fc-033.jpg', 'contact-us-powerblink-fc-033.jpg', 'asset/images/powerblink/contact-us-powerblink-fc-033.jpg', 'jpg', 446469, NULL, '2026-06-25 19:48:58', '2026-06-25 19:48:58', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (58, 'contact-us-powerblink-fc-034.jpg', 'contact-us-powerblink-fc-034.jpg', 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg', 'jpg', 352002, NULL, '2026-06-25 19:48:58', '2026-06-25 19:48:58', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (59, 'financial-management-powerblink-fc-035.jpg', 'financial-management-powerblink-fc-035.jpg', 'asset/images/powerblink/financial-management-powerblink-fc-035.jpg', 'jpg', 382580, NULL, '2026-06-25 19:48:58', '2026-06-25 19:48:58', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (60, 'financial-management-powerblink-fc-036.jpg', 'financial-management-powerblink-fc-036.jpg', 'asset/images/powerblink/financial-management-powerblink-fc-036.jpg', 'jpg', 391615, NULL, '2026-06-25 19:48:58', '2026-06-25 19:48:58', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (61, 'gallery-powerblink-fc-037.jpg', 'gallery-powerblink-fc-037.jpg', 'asset/images/powerblink/gallery-powerblink-fc-037.jpg', 'jpg', 367183, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (62, 'gallery-powerblink-fc-038.jpg', 'gallery-powerblink-fc-038.jpg', 'asset/images/powerblink/gallery-powerblink-fc-038.jpg', 'jpg', 405136, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (63, 'gallery-powerblink-fc-039.jpg', 'gallery-powerblink-fc-039.jpg', 'asset/images/powerblink/gallery-powerblink-fc-039.jpg', 'jpg', 296197, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (64, 'gallery-powerblink-fc-040.jpg', 'gallery-powerblink-fc-040.jpg', 'asset/images/powerblink/gallery-powerblink-fc-040.jpg', 'jpg', 492288, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (65, 'gallery-powerblink-fc-041.jpg', 'gallery-powerblink-fc-041.jpg', 'asset/images/powerblink/gallery-powerblink-fc-041.jpg', 'jpg', 454348, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (66, 'gallery-powerblink-fc-042.jpg', 'gallery-powerblink-fc-042.jpg', 'asset/images/powerblink/gallery-powerblink-fc-042.jpg', 'jpg', 429240, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (67, 'gallery-powerblink-fc-043.jpg', 'gallery-powerblink-fc-043.jpg', 'asset/images/powerblink/gallery-powerblink-fc-043.jpg', 'jpg', 388274, NULL, '2026-06-25 19:48:59', '2026-06-25 19:48:59', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (68, 'home-powerblink-fc-050.jpg', 'home-powerblink-fc-050.jpg', 'asset/images/powerblink/home-powerblink-fc-050.jpg', 'jpg', 395872, NULL, '2026-06-25 19:49:01', '2026-06-25 19:49:01', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (69, 'home-powerblink-fc-051.jpg', 'home-powerblink-fc-051.jpg', 'asset/images/powerblink/home-powerblink-fc-051.jpg', 'jpg', 440905, NULL, '2026-06-25 19:49:01', '2026-06-25 19:49:01', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (70, 'home-powerblink-fc-052.jpg', 'home-powerblink-fc-052.jpg', 'asset/images/powerblink/home-powerblink-fc-052.jpg', 'jpg', 465276, NULL, '2026-06-25 19:49:01', '2026-06-25 19:49:01', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (71, 'home-powerblink-fc-053.jpg', 'home-powerblink-fc-053.jpg', 'asset/images/powerblink/home-powerblink-fc-053.jpg', 'jpg', 437934, NULL, '2026-06-25 19:49:01', '2026-06-25 19:49:01', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (72, 'performance-analytics-powerblink-fc-056.jpg', 'performance-analytics-powerblink-fc-056.jpg', 'asset/images/powerblink/performance-analytics-powerblink-fc-056.jpg', 'jpg', 391944, NULL, '2026-06-25 19:49:02', '2026-06-25 19:49:02', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (73, 'performance-analytics-powerblink-fc-057.jpg', 'performance-analytics-powerblink-fc-057.jpg', 'asset/images/powerblink/performance-analytics-powerblink-fc-057.jpg', 'jpg', 260447, NULL, '2026-06-25 19:49:02', '2026-06-25 19:49:02', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (74, 'performance-analytics-powerblink-fc-058.jpg', 'performance-analytics-powerblink-fc-058.jpg', 'asset/images/powerblink/performance-analytics-powerblink-fc-058.jpg', 'jpg', 391803, NULL, '2026-06-25 19:49:02', '2026-06-25 19:49:02', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (75, 'performance-analytics-powerblink-fc-059.jpg', 'performance-analytics-powerblink-fc-059.jpg', 'asset/images/powerblink/performance-analytics-powerblink-fc-059.jpg', 'jpg', 302344, NULL, '2026-06-25 19:49:02', '2026-06-25 19:49:02', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (76, 'placeholder.svg', 'placeholder.svg', 'asset/images/powerblink/placeholder.svg', 'svg', 727, NULL, '2026-06-25 19:49:02', '2026-06-25 19:49:02', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (77, 'player-management-powerblink-fc-066.jpg', 'player-management-powerblink-fc-066.jpg', 'asset/images/powerblink/player-management-powerblink-fc-066.jpg', 'jpg', 249520, NULL, '2026-06-25 19:49:04', '2026-06-25 19:49:04', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (78, 'player-management-powerblink-fc-067.jpg', 'player-management-powerblink-fc-067.jpg', 'asset/images/powerblink/player-management-powerblink-fc-067.jpg', 'jpg', 271184, NULL, '2026-06-25 19:49:04', '2026-06-25 19:49:04', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (79, 'player-management-powerblink-fc-068.jpg', 'player-management-powerblink-fc-068.jpg', 'asset/images/powerblink/player-management-powerblink-fc-068.jpg', 'jpg', 266592, NULL, '2026-06-25 19:49:04', '2026-06-25 19:49:04', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (80, 'programs-management-powerblink-fc-069.jpg', 'programs-management-powerblink-fc-069.jpg', 'asset/images/powerblink/programs-management-powerblink-fc-069.jpg', 'jpg', 371506, NULL, '2026-06-25 19:49:04', '2026-06-25 19:49:04', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (81, 'programs-management-powerblink-fc-070.jpg', 'programs-management-powerblink-fc-070.jpg', 'asset/images/powerblink/programs-management-powerblink-fc-070.jpg', 'jpg', 476156, NULL, '2026-06-25 19:49:04', '2026-06-25 19:49:04', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (82, 'programs-management-powerblink-fc-071.jpg', 'programs-management-powerblink-fc-071.jpg', 'asset/images/powerblink/programs-management-powerblink-fc-071.jpg', 'jpg', 218050, NULL, '2026-06-25 19:49:04', '2026-06-25 19:49:04', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (83, 'programs-management-powerblink-fc-072.jpg', 'programs-management-powerblink-fc-072.jpg', 'asset/images/powerblink/programs-management-powerblink-fc-072.jpg', 'jpg', 428983, NULL, '2026-06-25 19:49:05', '2026-06-25 19:49:05', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (84, 'programs-management-powerblink-fc-073.jpg', 'programs-management-powerblink-fc-073.jpg', 'asset/images/powerblink/programs-management-powerblink-fc-073.jpg', 'jpg', 412097, NULL, '2026-06-25 19:49:05', '2026-06-25 19:49:05', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (85, 'programs-powerblink-fc-078.jpg', 'programs-powerblink-fc-078.jpg', 'asset/images/powerblink/programs-powerblink-fc-078.jpg', 'jpg', 415748, NULL, '2026-06-25 19:49:06', '2026-06-25 19:49:06', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (86, 'registrations-powerblink-fc-079.jpg', 'registrations-powerblink-fc-079.jpg', 'asset/images/powerblink/registrations-powerblink-fc-079.jpg', 'jpg', 400083, NULL, '2026-06-25 19:49:06', '2026-06-25 19:49:06', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (87, 'registrations-powerblink-fc-081.jpg', 'registrations-powerblink-fc-081.jpg', 'asset/images/powerblink/registrations-powerblink-fc-081.jpg', 'jpg', 499869, NULL, '2026-06-25 19:49:06', '2026-06-25 19:49:06', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (88, 'registrations-powerblink-fc-082.jpg', 'registrations-powerblink-fc-082.jpg', 'asset/images/powerblink/registrations-powerblink-fc-082.jpg', 'jpg', 372242, NULL, '2026-06-25 19:49:06', '2026-06-25 19:49:06', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (89, 'tournament-management-powerblink-fc-083.jpg', 'tournament-management-powerblink-fc-083.jpg', 'asset/images/powerblink/tournament-management-powerblink-fc-083.jpg', 'jpg', 358656, NULL, '2026-06-25 19:49:06', '2026-06-25 19:49:06', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (90, 'tournament-management-powerblink-fc-084.jpg', 'tournament-management-powerblink-fc-084.jpg', 'asset/images/powerblink/tournament-management-powerblink-fc-084.jpg', 'jpg', 445623, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (91, 'training-schedule-powerblink-fc-085.jpg', 'training-schedule-powerblink-fc-085.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-085.jpg', 'jpg', 372893, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (92, 'training-schedule-powerblink-fc-086.jpg', 'training-schedule-powerblink-fc-086.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-086.jpg', 'jpg', 392913, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (93, 'training-schedule-powerblink-fc-087.jpg', 'training-schedule-powerblink-fc-087.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-087.jpg', 'jpg', 356847, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (94, 'training-schedule-powerblink-fc-088.jpg', 'training-schedule-powerblink-fc-088.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-088.jpg', 'jpg', 385152, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (95, 'training-schedule-powerblink-fc-089.jpg', 'training-schedule-powerblink-fc-089.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-089.jpg', 'jpg', 374911, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (96, 'training-schedule-powerblink-fc-090.jpg', 'training-schedule-powerblink-fc-090.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-090.jpg', 'jpg', 242186, NULL, '2026-06-25 19:49:07', '2026-06-25 19:49:07', NULL, NULL);
INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `created_at`, `updated_at`, `category`, `alt_text`) VALUES (97, 'training-schedule-powerblink-fc-091.jpg', 'training-schedule-powerblink-fc-091.jpg', 'asset/images/powerblink/training-schedule-powerblink-fc-091.jpg', 'jpg', 258902, NULL, '2026-06-25 19:49:08', '2026-06-25 19:49:08', NULL, NULL);

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2026_04_16_163235_create_permission_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2026_04_17_200000_create_site_settings_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2026_04_18_100000_create_cms_pages_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2026_04_20_220000_add_is_active_to_cms_pages_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2026_04_20_220100_create_page_sections_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2026_04_20_220200_create_media_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10, '2026_04_20_220300_drop_legacy_wordpress_tables_if_present', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11, '2026_04_21_000000_create_site_traffic_events_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12, '2026_04_22_120000_create_admin_audit_trails_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13, '2026_04_29_000001_add_google_otp_avatar_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14, '2026_06_22_000001_add_is_super_admin_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15, '2026_06_24_100000_create_academy_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16, '2026_06_24_100100_extend_media_and_create_notifications_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17, '2026_06_24_100200_drop_ecommerce_tables', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18, '2026_06_25_100000_add_academy_payment_integrity_constraints', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19, '2026_06_25_100100_remove_vehicle_references_from_site_traffic_events', 1);

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (1, 'App\\Models\\User', 1);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (2, 'App\\Models\\User', 2);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (3, 'App\\Models\\User', 3);
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES (4, 'App\\Models\\User', 4);

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('8850862f-77f9-4375-9f06-a1ce0e12c434', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 1, '{"title":"New registration submitted","body":"A parent submitted a new player registration for review.","action_url":"\\/admin\\/registrations"}', NULL, '2026-06-25 19:48:41', '2026-06-25 19:48:41');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('85fd18db-27bb-49a3-99af-51a01c0eb642', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 1, '{"title":"Payment received","body":"Registration fee payment confirmed for PB-REG-2026-001.","action_url":"\\/admin\\/payments"}', NULL, '2026-06-25 19:48:41', '2026-06-25 19:48:41');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('143aa087-60ba-4c96-85aa-330ef56d0b91', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 2, '{"title":"Session roster updated","body":"Attendance has been updated for your upcoming U13 session.","action_url":"\\/admin\\/attendance"}', NULL, '2026-06-25 19:48:41', '2026-06-25 19:48:41');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('672e0abb-9ae3-4dad-8d4e-a5ef4952d84b', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 2, '{"title":"Performance reports due","body":"Submit June performance reports for your squad.","action_url":"\\/admin\\/performance-reports"}', NULL, '2026-06-25 19:48:41', '2026-06-25 19:48:41');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('7bad1ce3-2bcb-4632-8f25-4058b7f34054', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 3, '{"title":"Registration approved","body":"Your registration has been approved. Complete payment to activate.","action_url":"\\/register\\/pay"}', NULL, '2026-06-25 19:48:42', '2026-06-25 19:48:42');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('d0193706-8054-4f22-bf50-811cd32bc65f', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 3, '{"title":"Installment reminder","body":"Your July installment is due on the 1st.","action_url":"\\/portal"}', NULL, '2026-06-25 19:48:42', '2026-06-25 19:48:42');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('6a1bf279-9ddf-4b27-828d-ae095f91c2bb', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 4, '{"title":"Training session tomorrow","body":"U13 technical session scheduled for 4:00 PM on Pitch A.","action_url":"\\/portal"}', NULL, '2026-06-25 19:48:42', '2026-06-25 19:48:42');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('e1284986-3041-429a-8074-4fb158c903a1', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 4, '{"title":"Performance report published","body":"Your June performance report is now available.","action_url":"\\/portal"}', NULL, '2026-06-25 19:48:42', '2026-06-25 19:48:42');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('658a38fa-da72-4e72-aa9c-855c2ddf9c80', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 1, '{"title":"Tournament squad finalized","body":"Independence Day tournament squads have been published.","action_url":"\\/admin\\/tournaments"}', NULL, '2026-06-25 19:48:42', '2026-06-25 19:48:42');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES ('3cdb9344-e58a-4a67-bcdc-4172b2b94eee', 'App\\Notifications\\AcademyAlert', 'App\\Models\\User', 3, '{"title":"Academy announcement","body":"Parent-coach meeting scheduled for the first Saturday of July.","action_url":"\\/portal"}', NULL, '2026-06-25 19:48:42', '2026-06-25 19:48:42');

INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (1, 'home', 'hero_title', 'text', 'Developing Tomorrow''s Football Stars Today', '2026-06-25 19:48:44', '2026-06-25 19:48:44');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (2, 'home', 'hero_subtitle', 'text', 'Elite youth development in Ibeju Lekki with world-class coaching and structured pathways from U7 to U15.', '2026-06-25 19:48:44', '2026-06-25 19:48:44');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (3, 'home', 'hero_cta_text', 'text', 'Register Now', '2026-06-25 19:48:44', '2026-06-25 19:48:44');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (4, 'home', 'hero_cta_href', 'text', '/register', '2026-06-25 19:48:45', '2026-06-25 19:48:45');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (5, 'home', 'home_search_label', 'text', 'Explore Powerblink FC programs', '2026-06-25 19:48:45', '2026-06-25 19:48:45');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (6, 'home', 'recent_title', 'text', 'Why Our Academy Stands Out', '2026-06-25 19:48:45', '2026-06-25 19:48:45');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (7, 'home', 'recent_subtitle', 'textarea', 'The Powerblink Edge combines elite coaching, modern facilities, and a player-first culture that develops disciplined leaders on and off the pitch.', '2026-06-25 19:48:45', '2026-06-25 19:48:45');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (8, 'home', 'hero_image', 'image', 'asset/images/powerblink/home-powerblink-fc-044.jpg', '2026-06-25 19:48:45', '2026-06-25 19:48:45');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (9, 'home', 'cta_left_image', 'image', 'asset/images/powerblink/home-powerblink-fc-045.jpg', '2026-06-25 19:48:45', '2026-06-25 19:48:45');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (10, 'home', 'cta_right_image', 'image', 'asset/images/powerblink/home-powerblink-fc-046.jpg', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (11, 'home', 'cta_left_title', 'text', 'Elite Excellence in Ibeju Lekki', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (12, 'home', 'cta_left_body', 'textarea', 'Powerblink Football Club Limited provides a safe, world-class environment where young athletes transform raw passion into professional competence.', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (13, 'home', 'cta_right_title', 'text', 'Programs for Every Stage', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (14, 'home', 'cta_right_body', 'textarea', 'From U7 grassroots joy to U15 elite competition, every pathway is designed with technical mastery, tactical intelligence, and character development.', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (15, 'home', 'feat1_title', 'text', 'Licensed Coaching Staff', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (16, 'home', 'feat1_body', 'textarea', 'UEFA- and CAF-certified coaches deliver structured sessions backed by performance analytics.', '2026-06-25 19:48:46', '2026-06-25 19:48:46');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (17, 'home', 'feat2_title', 'text', 'Modern Training Facilities', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (18, 'home', 'feat2_body', 'textarea', 'Premium pitches, recovery spaces, and sports science support in the heart of Ibeju Lekki.', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (19, 'home', 'feat3_title', 'text', 'Clear Player Pathway', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (20, 'home', 'feat3_body', 'textarea', 'Transparent progression from grassroots to elite squads with tournament and scouting exposure.', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (21, 'home', 'welcome_title', 'text', 'Welcome to Powerblink FC', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (22, 'home', 'welcome_body', 'textarea', 'We develop the person first and the player second — raising disciplined leaders who happen to be incredible footballers.', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (23, 'home', 'prefooter_title', 'text', 'Ready to join the academy?', '2026-06-25 19:48:47', '2026-06-25 19:48:47');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (24, 'home', 'prefooter_button_text', 'text', 'Start Registration', '2026-06-25 19:48:48', '2026-06-25 19:48:48');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (25, 'home', 'prefooter_button_href', 'text', '/register', '2026-06-25 19:48:48', '2026-06-25 19:48:48');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (26, 'home', 'testimonial_name', 'text', 'Coach Elijah Opetunde', '2026-06-25 19:48:48', '2026-06-25 19:48:48');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (27, 'home', 'testimonial_role', 'text', 'Head Coach, Powerblink FC', '2026-06-25 19:48:48', '2026-06-25 19:48:48');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (28, 'home', 'testimonial_quote', 'textarea', 'Develop the person first, the player second. Our goal at Powerblink is to raise disciplined leaders who happen to be incredible footballers.', '2026-06-25 19:48:48', '2026-06-25 19:48:48');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (29, 'contact', 'heading', 'text', 'Contact Powerblink FC', '2026-06-25 19:48:48', '2026-06-25 19:48:48');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (30, 'contact', 'intro', 'textarea', 'Visit our academy in Ibeju Lekki or reach out for registration support, facility tours, and partnership enquiries.', '2026-06-25 19:48:49', '2026-06-25 19:48:49');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (31, 'contact', 'hero_image', 'image', 'asset/images/powerblink/contact-us-powerblink-fc-033.jpg', '2026-06-25 19:48:49', '2026-06-25 19:48:49');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (32, 'contact', 'map_image', 'image', 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg', '2026-06-25 19:48:49', '2026-06-25 19:48:49');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (33, 'faq', 'kicker', 'text', 'Need Help?', '2026-06-25 19:48:49', '2026-06-25 19:48:49');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (34, 'faq', 'heading', 'text', 'HELP CENTER', '2026-06-25 19:48:49', '2026-06-25 19:48:49');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (35, 'faq', 'intro', 'textarea', 'Common questions about registration, training, and academy policies at Powerblink FC.', '2026-06-25 19:48:50', '2026-06-25 19:48:50');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (36, 'faq', 'hero_image', 'image', 'asset/images/powerblink/home-powerblink-fc-044.jpg', '2026-06-25 19:48:50', '2026-06-25 19:48:50');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (37, 'faq', 'cat_1_title', 'text', 'Registration', '2026-06-25 19:48:50', '2026-06-25 19:48:50');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (38, 'faq', 'cat_1_icon', 'text', 'how_to_reg', '2026-06-25 19:48:50', '2026-06-25 19:48:50');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (39, 'faq', 'cat_1_faqs', 'json', '[{"q":"When can I pay the registration fee?","a":"Payment is only available after your application is approved. You will receive an email with a secure payment link."},{"q":"How long does review take?","a":"Our team typically reviews applications within a few business days."}]', '2026-06-25 19:48:50', '2026-06-25 19:48:50');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (40, 'faq', 'cat_2_title', 'text', 'Programs', '2026-06-25 19:48:50', '2026-06-25 19:48:50');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (41, 'faq', 'cat_2_icon', 'text', 'sports_soccer', '2026-06-25 19:48:51', '2026-06-25 19:48:51');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (42, 'faq', 'cat_2_faqs', 'json', '[{"q":"What age groups do you serve?","a":"We offer pathways from U7 through U15."},{"q":"How often do teams train?","a":"Frequency depends on the program \\u2014 typically 2\\u20134 sessions per week."}]', '2026-06-25 19:48:51', '2026-06-25 19:48:51');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (43, 'faq', 'cat_3_title', 'text', 'Training', '2026-06-25 19:48:51', '2026-06-25 19:48:51');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (44, 'faq', 'cat_3_icon', 'text', 'calendar_month', '2026-06-25 19:48:51', '2026-06-25 19:48:51');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (45, 'faq', 'cat_3_faqs', 'json', '[{"q":"What should my child bring to training?","a":"Boots, shin guards, training kit, and a water bottle."}]', '2026-06-25 19:48:51', '2026-06-25 19:48:51');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (46, 'faq', 'cat_4_title', 'text', 'Medical', '2026-06-25 19:48:52', '2026-06-25 19:48:52');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (47, 'faq', 'cat_4_icon', 'text', 'health_and_safety', '2026-06-25 19:48:52', '2026-06-25 19:48:52');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (48, 'faq', 'cat_4_faqs', 'json', '[{"q":"What medical information is required?","a":"Please disclose allergies, relevant medical history, and fitness clearance during registration."}]', '2026-06-25 19:48:52', '2026-06-25 19:48:52');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (49, 'faq', 'cta_title', 'text', 'STILL HAVE QUESTIONS?', '2026-06-25 19:48:52', '2026-06-25 19:48:52');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (50, 'faq', 'cta_body', 'textarea', 'Contact our academy office Monday through Saturday for registration and program support.', '2026-06-25 19:48:52', '2026-06-25 19:48:52');
INSERT INTO `page_sections` (`id`, `page`, `section_key`, `content_type`, `content`, `created_at`, `updated_at`) VALUES (51, 'faq', 'cta_image', 'image', 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg', '2026-06-25 19:48:52', '2026-06-25 19:48:52');

INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (1, 1, 1, 1, 82, 78, 80, 85, 88, 90, 83.8, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-01 00:00:00', '2026-06-25 19:48:27', '2026-06-25 19:48:27');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (2, 1, 2, 1, 75, 80, 77, 82, 85, 86, 80.8, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-02 00:00:00', '2026-06-25 19:48:27', '2026-06-25 19:48:27');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (3, 1, 3, 1, 88, 84, 83, 87, 90, 92, 87.3, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-03 00:00:00', '2026-06-25 19:48:28', '2026-06-25 19:48:28');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (4, 1, 4, 1, 70, 72, 74, 78, 80, 82, 76, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-04 00:00:00', '2026-06-25 19:48:28', '2026-06-25 19:48:28');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (5, 1, 5, 1, 82, 78, 80, 85, 88, 90, 83.8, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-05 00:00:00', '2026-06-25 19:48:28', '2026-06-25 19:48:28');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (6, 1, 6, 1, 75, 80, 77, 82, 85, 86, 80.8, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-06 00:00:00', '2026-06-25 19:48:28', '2026-06-25 19:48:28');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (7, 1, 7, 1, 88, 84, 83, 87, 90, 92, 87.3, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-07 00:00:00', '2026-06-25 19:48:28', '2026-06-25 19:48:28');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (8, 1, 8, 1, 70, 72, 74, 78, 80, 82, 76, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-08 00:00:00', '2026-06-25 19:48:28', '2026-06-25 19:48:28');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (9, 1, 9, 1, 82, 78, 80, 85, 88, 90, 83.8, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-09 00:00:00', '2026-06-25 19:48:29', '2026-06-25 19:48:29');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (10, 1, 10, 1, 75, 80, 77, 82, 85, 86, 80.8, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-10 00:00:00', '2026-06-25 19:48:29', '2026-06-25 19:48:29');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (11, 1, 11, 1, 88, 84, 83, 87, 90, 92, 87.3, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-11 00:00:00', '2026-06-25 19:48:29', '2026-06-25 19:48:29');
INSERT INTO `performance_reports` (`id`, `season_id`, `player_id`, `coach_id`, `passing`, `dribbling`, `speed`, `fitness`, `discipline`, `teamwork`, `overall_score`, `comments`, `reported_at`, `created_at`, `updated_at`) VALUES (12, 1, 12, 1, 70, 72, 74, 78, 80, 82, 76, 'Showcase performance report — steady progress across technical and tactical blocks.', '2026-06-12 00:00:00', '2026-06-25 19:48:29', '2026-06-25 19:48:29');

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (1, 'dashboard.view', 'web', '2026-06-25 19:46:54', '2026-06-25 19:46:54');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (2, 'analytics.view', 'web', '2026-06-25 19:46:55', '2026-06-25 19:46:55');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (3, 'players.view', 'web', '2026-06-25 19:46:55', '2026-06-25 19:46:55');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (4, 'players.create', 'web', '2026-06-25 19:46:55', '2026-06-25 19:46:55');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (5, 'players.update', 'web', '2026-06-25 19:46:56', '2026-06-25 19:46:56');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (6, 'players.delete', 'web', '2026-06-25 19:46:56', '2026-06-25 19:46:56');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (7, 'registrations.view', 'web', '2026-06-25 19:46:56', '2026-06-25 19:46:56');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (8, 'registrations.create', 'web', '2026-06-25 19:46:56', '2026-06-25 19:46:56');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (9, 'registrations.update', 'web', '2026-06-25 19:46:57', '2026-06-25 19:46:57');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (10, 'registrations.approve', 'web', '2026-06-25 19:46:57', '2026-06-25 19:46:57');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (11, 'registrations.reject', 'web', '2026-06-25 19:46:57', '2026-06-25 19:46:57');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (12, 'registrations.delete', 'web', '2026-06-25 19:46:57', '2026-06-25 19:46:57');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (13, 'programs.view', 'web', '2026-06-25 19:46:57', '2026-06-25 19:46:57');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (14, 'programs.manage', 'web', '2026-06-25 19:46:57', '2026-06-25 19:46:57');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (15, 'seasons.view', 'web', '2026-06-25 19:46:58', '2026-06-25 19:46:58');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (16, 'seasons.manage', 'web', '2026-06-25 19:46:58', '2026-06-25 19:46:58');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (17, 'training_sessions.view', 'web', '2026-06-25 19:46:58', '2026-06-25 19:46:58');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (18, 'training_sessions.manage', 'web', '2026-06-25 19:46:58', '2026-06-25 19:46:58');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (19, 'attendance.view', 'web', '2026-06-25 19:46:58', '2026-06-25 19:46:58');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (20, 'attendance.manage', 'web', '2026-06-25 19:46:59', '2026-06-25 19:46:59');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (21, 'performance.view', 'web', '2026-06-25 19:46:59', '2026-06-25 19:46:59');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (22, 'performance.manage', 'web', '2026-06-25 19:46:59', '2026-06-25 19:46:59');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (23, 'payments.view', 'web', '2026-06-25 19:47:00', '2026-06-25 19:47:00');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (24, 'payments.manage', 'web', '2026-06-25 19:47:00', '2026-06-25 19:47:00');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (25, 'payments.pay', 'web', '2026-06-25 19:47:00', '2026-06-25 19:47:00');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (26, 'coaches.view', 'web', '2026-06-25 19:47:00', '2026-06-25 19:47:00');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (27, 'coaches.manage', 'web', '2026-06-25 19:47:00', '2026-06-25 19:47:00');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (28, 'tournaments.view', 'web', '2026-06-25 19:47:01', '2026-06-25 19:47:01');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (29, 'tournaments.manage', 'web', '2026-06-25 19:47:01', '2026-06-25 19:47:01');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (30, 'tournaments.squads', 'web', '2026-06-25 19:47:01', '2026-06-25 19:47:01');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (31, 'announcements.view', 'web', '2026-06-25 19:47:01', '2026-06-25 19:47:01');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (32, 'announcements.manage', 'web', '2026-06-25 19:47:01', '2026-06-25 19:47:01');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (33, 'communications.receive', 'web', '2026-06-25 19:47:01', '2026-06-25 19:47:01');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (34, 'pages.manage', 'web', '2026-06-25 19:47:02', '2026-06-25 19:47:02');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (35, 'gallery.manage', 'web', '2026-06-25 19:47:02', '2026-06-25 19:47:02');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (36, 'media.manage', 'web', '2026-06-25 19:47:02', '2026-06-25 19:47:02');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (37, 'media.upload', 'web', '2026-06-25 19:47:02', '2026-06-25 19:47:02');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (38, 'documents.view', 'web', '2026-06-25 19:47:02', '2026-06-25 19:47:02');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (39, 'documents.upload', 'web', '2026-06-25 19:47:02', '2026-06-25 19:47:02');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (40, 'documents.verify', 'web', '2026-06-25 19:47:03', '2026-06-25 19:47:03');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (41, 'documents.manage', 'web', '2026-06-25 19:47:03', '2026-06-25 19:47:03');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (42, 'settings.manage', 'web', '2026-06-25 19:47:03', '2026-06-25 19:47:03');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (43, 'staff.manage', 'web', '2026-06-25 19:47:03', '2026-06-25 19:47:03');
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (44, 'audit.view', 'web', '2026-06-25 19:47:03', '2026-06-25 19:47:03');

INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (1, 1, 1, 'birth_certificate', 30, 'verified', 1, '2026-01-14 10:00:00', '2026-06-25 19:48:39', '2026-06-25 19:48:39');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (2, 1, 1, 'medical_form', 31, 'pending', NULL, NULL, '2026-06-25 19:48:39', '2026-06-25 19:48:39');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (3, 1, 1, 'passport_photo', 10, 'pending', NULL, NULL, '2026-06-25 19:48:39', '2026-06-25 19:48:39');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (4, 2, NULL, 'birth_certificate', 31, 'verified', 1, '2026-01-14 10:00:00', '2026-06-25 19:48:39', '2026-06-25 19:48:39');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (5, 2, NULL, 'medical_form', 10, 'pending', NULL, NULL, '2026-06-25 19:48:40', '2026-06-25 19:48:40');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (6, 2, NULL, 'passport_photo', 30, 'pending', NULL, NULL, '2026-06-25 19:48:40', '2026-06-25 19:48:40');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (7, 3, NULL, 'birth_certificate', 10, 'verified', 1, '2026-01-14 10:00:00', '2026-06-25 19:48:40', '2026-06-25 19:48:40');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (8, 3, NULL, 'medical_form', 30, 'pending', NULL, NULL, '2026-06-25 19:48:40', '2026-06-25 19:48:40');
INSERT INTO `player_documents` (`id`, `player_id`, `registration_id`, `document_type`, `media_id`, `status`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES (9, 3, NULL, 'passport_photo', 31, 'pending', NULL, NULL, '2026-06-25 19:48:40', '2026-06-25 19:48:40');

INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, 4, 1, 3, 1, 'PB-PLY-2026-001', 12, 'Tobi Okonkwo', '2013-04-18 00:00:00', 'Nigerian', 'Midfielder', NULL, 3, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:22', '2026-06-25 19:47:22', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, NULL, NULL, 2, 4, 1, 'PB-PLY-2026-002', 11, 'Daniel Eze', '2011-11-05 00:00:00', 'Nigerian', 'Defender', NULL, 3, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:23', '2026-06-25 19:47:23', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, NULL, NULL, 1, 2, 1, 'PB-PLY-2026-003', 13, 'Zainab Bello', '2016-03-22 00:00:00', 'Nigerian', 'Goalkeeper', NULL, 3, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:23', '2026-06-25 19:47:23', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, NULL, NULL, 1, 1, 1, 'PB-PLY-2026-004', 14, 'Emeka Nwachukwu', '2019-05-14 00:00:00', 'Nigerian', 'Forward', NULL, 3, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:23', '2026-06-25 19:47:23', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, NULL, NULL, 1, 1, 1, 'PB-PLY-2026-005', 15, 'Amina Hassan', '2019-02-10 00:00:00', 'Nigerian', 'Midfielder', NULL, 2, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:24', '2026-06-25 19:47:24', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, NULL, NULL, 2, 1, 1, 'PB-PLY-2026-006', 16, 'David Chukwu', '2018-11-28 00:00:00', 'Nigerian', 'Forward', NULL, 3, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:24', '2026-06-25 19:47:24', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, NULL, NULL, 3, 2, 1, 'PB-PLY-2026-007', 17, 'Grace Etim', '2016-07-14 00:00:00', 'Nigerian', 'Winger', NULL, 4, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:24', '2026-06-25 19:47:24', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (8, NULL, NULL, 4, 2, 1, 'PB-PLY-2026-008', 18, 'Ibrahim Musa', '2016-01-05 00:00:00', 'Nigerian', 'Defender', NULL, 5, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:25', '2026-06-25 19:47:25', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (9, NULL, NULL, 5, 3, 1, 'PB-PLY-2026-009', 19, 'Lara Adekunle', '2013-09-20 00:00:00', 'Nigerian', 'Midfielder', NULL, 2, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:25', '2026-06-25 19:47:25', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (10, NULL, NULL, 6, 3, 1, 'PB-PLY-2026-010', 20, 'Michael Obi', '2013-12-01 00:00:00', 'Nigerian', 'Forward', NULL, 3, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:26', '2026-06-25 19:47:26', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (11, NULL, NULL, 1, 4, 1, 'PB-PLY-2026-011', 21, 'Ngozi Akpan', '2011-06-18 00:00:00', 'Nigerian', 'Goalkeeper', NULL, 4, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:26', '2026-06-25 19:47:26', NULL);
INSERT INTO `players` (`id`, `registration_id`, `user_id`, `guardian_id`, `program_id`, `season_id`, `player_code`, `photo_media_id`, `name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (12, NULL, NULL, 2, 4, 1, 'PB-PLY-2026-012', 22, 'Samuel Adeyemi', '2011-03-09 00:00:00', 'Nigerian', 'Defender', NULL, 5, 'Committed, coachable, team-oriented', NULL, NULL, 'active', '2026-06-25 19:47:26', '2026-06-25 19:47:26', NULL);

INSERT INTO `programs` (`id`, `season_id`, `name`, `age_group`, `description`, `monthly_fee`, `registration_fee`, `max_capacity`, `sessions_per_week`, `is_active`, `hero_image_media_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, 'U7 Grassroots', 'U7', 'Fun-first introduction to football for ages 5–7 with ball mastery and coordination.', 4500000, 2500000, 24, 2, 1, 1, 1, '2026-06-25 19:47:17', '2026-06-25 19:47:17', NULL);
INSERT INTO `programs` (`id`, `season_id`, `name`, `age_group`, `description`, `monthly_fee`, `registration_fee`, `max_capacity`, `sessions_per_week`, `is_active`, `hero_image_media_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, 'U10 Foundation', 'U10', 'Technical foundations, small-sided games, and disciplined training habits.', 5500000, 3000000, 22, 3, 1, 2, 2, '2026-06-25 19:47:17', '2026-06-25 19:47:17', NULL);
INSERT INTO `programs` (`id`, `season_id`, `name`, `age_group`, `description`, `monthly_fee`, `registration_fee`, `max_capacity`, `sessions_per_week`, `is_active`, `hero_image_media_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 1, 'U13 Development', 'U13', 'Tactical awareness, positional play, and competitive match preparation.', 6500000, 3500000, 20, 3, 1, 3, 3, '2026-06-25 19:47:18', '2026-06-25 19:47:18', NULL);
INSERT INTO `programs` (`id`, `season_id`, `name`, `age_group`, `description`, `monthly_fee`, `registration_fee`, `max_capacity`, `sessions_per_week`, `is_active`, `hero_image_media_id`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 1, 'U15 Elite', 'U15', 'High-performance pathway with strength conditioning, scouting exposure, and tournament play.', 7500000, 4000000, 18, 4, 1, 4, 4, '2026-06-25 19:47:18', '2026-06-25 19:47:18', NULL);

INSERT INTO `registration_payments` (`id`, `registration_id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (1, 1, 1, 1, 'registration_fee', 'paystack', 'PB-PAY-REG-2026-001', 'success', 3500000, 'NGN', NULL, '2026-01-13 10:45:00', '2026-06-25 19:47:27', '2026-06-25 19:47:27');
INSERT INTO `registration_payments` (`id`, `registration_id`, `player_id`, `season_id`, `type`, `provider`, `reference`, `status`, `amount`, `currency`, `gateway_payload`, `paid_at`, `created_at`, `updated_at`) VALUES (2, 3, NULL, 1, 'registration_fee', 'paystack', 'PB-PAY-REG-2026-PENDING', 'pending', 4000000, 'NGN', NULL, NULL, '2026-06-25 19:47:27', '2026-06-25 19:47:27');

INSERT INTO `registrations` (`id`, `reference_code`, `season_id`, `program_id`, `guardian_id`, `status`, `payment_plan`, `payment_token`, `payment_token_expires_at`, `payment_token_used_at`, `player_name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `fitness_certified`, `profile_photo_media_id`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `approved_by`, `approved_at`, `rejected_reason`, `rejected_at`, `submitted_at`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 'PB-REG-2026-001', 1, 3, 1, 'activated', 'lump_sum', NULL, NULL, NULL, 'Tobi Okonkwo', '2013-04-18 00:00:00', 'Nigerian', 'Midfielder', 'Winger', 4, 'Vision, first touch, work rate', NULL, NULL, 1, 9, 'Chidi Okonkwo', '+234 806 789 0123', 'Father', 1, '2026-01-12 14:00:00', NULL, NULL, '2026-01-10 09:30:00', '2026-06-25 19:47:21', '2026-06-25 19:47:21', NULL);
INSERT INTO `registrations` (`id`, `reference_code`, `season_id`, `program_id`, `guardian_id`, `status`, `payment_plan`, `payment_token`, `payment_token_expires_at`, `payment_token_used_at`, `player_name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `fitness_certified`, `profile_photo_media_id`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `approved_by`, `approved_at`, `rejected_reason`, `rejected_at`, `submitted_at`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 'PB-REG-2026-002', 1, 2, 1, 'pending_review', 'lump_sum', NULL, NULL, NULL, 'Kemi Adebayo', '2016-08-02 00:00:00', 'Nigerian', 'Forward', 'Attacking Midfielder', 2, 'Pace, finishing, confidence on the ball', NULL, NULL, 1, 10, 'Chidi Okonkwo', '+234 806 789 0123', 'Father', NULL, NULL, NULL, NULL, '2026-06-20 11:15:00', '2026-06-25 19:47:22', '2026-06-25 19:47:22', NULL);
INSERT INTO `registrations` (`id`, `reference_code`, `season_id`, `program_id`, `guardian_id`, `status`, `payment_plan`, `payment_token`, `payment_token_expires_at`, `payment_token_used_at`, `player_name`, `date_of_birth`, `nationality`, `primary_position`, `secondary_position`, `years_experience`, `technical_strengths`, `allergies`, `medical_history`, `fitness_certified`, `profile_photo_media_id`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`, `approved_by`, `approved_at`, `rejected_reason`, `rejected_at`, `submitted_at`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 'PB-REG-2026-003', 1, 4, 2, 'awaiting_payment', 'installments', '12d2552d-684b-4d02-a631-0fba144f83dd', '2026-07-15 23:59:59', NULL, 'Daniel Eze', '2011-11-05 00:00:00', 'Nigerian', 'Defender', 'Centre Back', 5, 'Tackling, aerial ability, leadership', NULL, NULL, 1, 11, 'Adaeze Okonkwo', '+234 805 678 9012', 'Mother', 1, '2026-06-19 10:00:00', NULL, NULL, '2026-06-18 16:40:00', '2026-06-25 19:47:22', '2026-06-25 19:47:22', NULL);

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (2, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (32, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (31, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (20, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (19, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (44, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (27, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (26, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (33, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (1, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (41, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (39, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (40, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (38, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (35, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (36, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (37, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (34, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (24, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (25, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (23, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (22, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (21, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (4, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (6, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (5, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (3, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (14, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (13, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (10, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (8, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (12, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (11, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (9, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (7, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (16, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (15, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (42, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (43, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (29, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (30, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (28, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (18, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (17, 1);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (2, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (31, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (20, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (19, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (26, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (33, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (1, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (38, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (37, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (22, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (21, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (3, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (13, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (7, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (15, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (30, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (28, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (18, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (17, 2);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (31, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (19, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (26, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (33, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (1, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (39, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (38, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (25, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (23, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (21, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (3, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (13, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (7, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (15, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (28, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (17, 3);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (31, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (19, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (26, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (33, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (1, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (38, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (23, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (21, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (3, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (13, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (15, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (28, 4);
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES (17, 4);

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (1, 'admin', 'web', '2026-06-25 19:46:49', '2026-06-25 19:46:49');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (2, 'coach', 'web', '2026-06-25 19:46:50', '2026-06-25 19:46:50');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (3, 'parent', 'web', '2026-06-25 19:46:51', '2026-06-25 19:46:51');
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (4, 'player', 'web', '2026-06-25 19:46:52', '2026-06-25 19:46:52');

INSERT INTO `seasons` (`id`, `name`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, '2026 Season', '2026-01-01 00:00:00', '2026-12-31 00:00:00', 1, '2026-06-25 19:47:17', '2026-06-25 19:47:17', NULL);

INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (1, 1, 1, 'present', NULL, '2026-06-25 19:48:04', '2026-06-25 19:48:04');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (2, 1, 2, 'present', NULL, '2026-06-25 19:48:04', '2026-06-25 19:48:04');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (3, 1, 3, 'absent', 'Reported unavailable', '2026-06-25 19:48:04', '2026-06-25 19:48:04');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (4, 1, 4, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:04', '2026-06-25 19:48:04');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (5, 1, 5, 'present', NULL, '2026-06-25 19:48:04', '2026-06-25 19:48:04');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (6, 1, 6, 'present', NULL, '2026-06-25 19:48:04', '2026-06-25 19:48:04');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (7, 1, 7, 'present', NULL, '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (8, 1, 8, 'absent', 'Reported unavailable', '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (9, 1, 9, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (10, 1, 10, 'present', NULL, '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (11, 1, 11, 'present', NULL, '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (12, 1, 12, 'present', NULL, '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (13, 2, 1, 'present', NULL, '2026-06-25 19:48:05', '2026-06-25 19:48:05');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (14, 2, 2, 'absent', 'Reported unavailable', '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (15, 2, 3, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (16, 2, 4, 'present', NULL, '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (17, 2, 5, 'present', NULL, '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (18, 2, 6, 'present', NULL, '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (19, 2, 7, 'absent', 'Reported unavailable', '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (20, 2, 8, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:06', '2026-06-25 19:48:06');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (21, 2, 9, 'present', NULL, '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (22, 2, 10, 'present', NULL, '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (23, 2, 11, 'present', NULL, '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (24, 2, 12, 'absent', 'Reported unavailable', '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (25, 3, 1, 'absent', 'Reported unavailable', '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (26, 3, 2, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (27, 3, 3, 'present', NULL, '2026-06-25 19:48:07', '2026-06-25 19:48:07');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (28, 3, 4, 'present', NULL, '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (29, 3, 5, 'present', NULL, '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (30, 3, 6, 'absent', 'Reported unavailable', '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (31, 3, 7, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (32, 3, 8, 'present', NULL, '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (33, 3, 9, 'present', NULL, '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (34, 3, 10, 'present', NULL, '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (35, 3, 11, 'absent', 'Reported unavailable', '2026-06-25 19:48:08', '2026-06-25 19:48:08');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (36, 3, 12, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:09', '2026-06-25 19:48:09');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (37, 4, 1, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:09', '2026-06-25 19:48:09');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (38, 4, 2, 'present', NULL, '2026-06-25 19:48:09', '2026-06-25 19:48:09');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (39, 4, 3, 'present', NULL, '2026-06-25 19:48:09', '2026-06-25 19:48:09');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (40, 4, 4, 'present', NULL, '2026-06-25 19:48:09', '2026-06-25 19:48:09');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (41, 4, 5, 'absent', 'Reported unavailable', '2026-06-25 19:48:09', '2026-06-25 19:48:09');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (42, 4, 6, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:10', '2026-06-25 19:48:10');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (43, 4, 7, 'present', NULL, '2026-06-25 19:48:10', '2026-06-25 19:48:10');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (44, 4, 8, 'present', NULL, '2026-06-25 19:48:10', '2026-06-25 19:48:10');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (45, 4, 9, 'present', NULL, '2026-06-25 19:48:10', '2026-06-25 19:48:10');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (46, 4, 10, 'absent', 'Reported unavailable', '2026-06-25 19:48:10', '2026-06-25 19:48:10');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (47, 4, 11, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:10', '2026-06-25 19:48:10');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (48, 4, 12, 'present', NULL, '2026-06-25 19:48:11', '2026-06-25 19:48:11');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (49, 5, 1, 'present', NULL, '2026-06-25 19:48:11', '2026-06-25 19:48:11');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (50, 5, 2, 'present', NULL, '2026-06-25 19:48:11', '2026-06-25 19:48:11');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (51, 5, 3, 'present', NULL, '2026-06-25 19:48:11', '2026-06-25 19:48:11');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (52, 5, 4, 'absent', 'Reported unavailable', '2026-06-25 19:48:11', '2026-06-25 19:48:11');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (53, 5, 5, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:11', '2026-06-25 19:48:11');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (54, 5, 6, 'present', NULL, '2026-06-25 19:48:12', '2026-06-25 19:48:12');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (55, 5, 7, 'present', NULL, '2026-06-25 19:48:12', '2026-06-25 19:48:12');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (56, 5, 8, 'present', NULL, '2026-06-25 19:48:12', '2026-06-25 19:48:12');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (57, 5, 9, 'absent', 'Reported unavailable', '2026-06-25 19:48:12', '2026-06-25 19:48:12');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (58, 5, 10, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:12', '2026-06-25 19:48:12');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (59, 5, 11, 'present', NULL, '2026-06-25 19:48:12', '2026-06-25 19:48:12');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (60, 5, 12, 'present', NULL, '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (61, 6, 1, 'present', NULL, '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (62, 6, 2, 'present', NULL, '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (63, 6, 3, 'absent', 'Reported unavailable', '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (64, 6, 4, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (65, 6, 5, 'present', NULL, '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (66, 6, 6, 'present', NULL, '2026-06-25 19:48:13', '2026-06-25 19:48:13');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (67, 6, 7, 'present', NULL, '2026-06-25 19:48:14', '2026-06-25 19:48:14');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (68, 6, 8, 'absent', 'Reported unavailable', '2026-06-25 19:48:14', '2026-06-25 19:48:14');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (69, 6, 9, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:14', '2026-06-25 19:48:14');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (70, 6, 10, 'present', NULL, '2026-06-25 19:48:14', '2026-06-25 19:48:14');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (71, 6, 11, 'present', NULL, '2026-06-25 19:48:14', '2026-06-25 19:48:14');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (72, 6, 12, 'present', NULL, '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (73, 7, 1, 'present', NULL, '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (74, 7, 2, 'absent', 'Reported unavailable', '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (75, 7, 3, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (76, 7, 4, 'present', NULL, '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (77, 7, 5, 'present', NULL, '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (78, 7, 6, 'present', NULL, '2026-06-25 19:48:15', '2026-06-25 19:48:15');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (79, 7, 7, 'absent', 'Reported unavailable', '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (80, 7, 8, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (81, 7, 9, 'present', NULL, '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (82, 7, 10, 'present', NULL, '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (83, 7, 11, 'present', NULL, '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (84, 7, 12, 'absent', 'Reported unavailable', '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (85, 8, 1, 'absent', 'Reported unavailable', '2026-06-25 19:48:16', '2026-06-25 19:48:16');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (86, 8, 2, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:17', '2026-06-25 19:48:17');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (87, 8, 3, 'present', NULL, '2026-06-25 19:48:17', '2026-06-25 19:48:17');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (88, 8, 4, 'present', NULL, '2026-06-25 19:48:17', '2026-06-25 19:48:17');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (89, 8, 5, 'present', NULL, '2026-06-25 19:48:17', '2026-06-25 19:48:17');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (90, 8, 6, 'absent', 'Reported unavailable', '2026-06-25 19:48:17', '2026-06-25 19:48:17');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (91, 8, 7, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:18', '2026-06-25 19:48:18');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (92, 8, 8, 'present', NULL, '2026-06-25 19:48:18', '2026-06-25 19:48:18');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (93, 8, 9, 'present', NULL, '2026-06-25 19:48:18', '2026-06-25 19:48:18');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (94, 8, 10, 'present', NULL, '2026-06-25 19:48:18', '2026-06-25 19:48:18');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (95, 8, 11, 'absent', 'Reported unavailable', '2026-06-25 19:48:18', '2026-06-25 19:48:18');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (96, 8, 12, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:18', '2026-06-25 19:48:18');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (97, 9, 1, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (98, 9, 2, 'present', NULL, '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (99, 9, 3, 'present', NULL, '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (100, 9, 4, 'present', NULL, '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (101, 9, 5, 'absent', 'Reported unavailable', '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (102, 9, 6, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (103, 9, 7, 'present', NULL, '2026-06-25 19:48:19', '2026-06-25 19:48:19');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (104, 9, 8, 'present', NULL, '2026-06-25 19:48:20', '2026-06-25 19:48:20');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (105, 9, 9, 'present', NULL, '2026-06-25 19:48:20', '2026-06-25 19:48:20');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (106, 9, 10, 'absent', 'Reported unavailable', '2026-06-25 19:48:20', '2026-06-25 19:48:20');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (107, 9, 11, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:20', '2026-06-25 19:48:20');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (108, 9, 12, 'present', NULL, '2026-06-25 19:48:20', '2026-06-25 19:48:20');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (109, 10, 1, 'present', NULL, '2026-06-25 19:48:21', '2026-06-25 19:48:21');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (110, 10, 2, 'present', NULL, '2026-06-25 19:48:21', '2026-06-25 19:48:21');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (111, 10, 3, 'present', NULL, '2026-06-25 19:48:21', '2026-06-25 19:48:21');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (112, 10, 4, 'absent', 'Reported unavailable', '2026-06-25 19:48:21', '2026-06-25 19:48:21');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (113, 10, 5, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:21', '2026-06-25 19:48:21');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (114, 10, 6, 'present', NULL, '2026-06-25 19:48:22', '2026-06-25 19:48:22');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (115, 10, 7, 'present', NULL, '2026-06-25 19:48:22', '2026-06-25 19:48:22');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (116, 10, 8, 'present', NULL, '2026-06-25 19:48:22', '2026-06-25 19:48:22');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (117, 10, 9, 'absent', 'Reported unavailable', '2026-06-25 19:48:22', '2026-06-25 19:48:22');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (118, 10, 10, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:22', '2026-06-25 19:48:22');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (119, 10, 11, 'present', NULL, '2026-06-25 19:48:22', '2026-06-25 19:48:22');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (120, 10, 12, 'present', NULL, '2026-06-25 19:48:23', '2026-06-25 19:48:23');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (121, 11, 1, 'present', NULL, '2026-06-25 19:48:23', '2026-06-25 19:48:23');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (122, 11, 2, 'present', NULL, '2026-06-25 19:48:23', '2026-06-25 19:48:23');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (123, 11, 3, 'absent', 'Reported unavailable', '2026-06-25 19:48:23', '2026-06-25 19:48:23');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (124, 11, 4, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:23', '2026-06-25 19:48:23');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (125, 11, 5, 'present', NULL, '2026-06-25 19:48:24', '2026-06-25 19:48:24');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (126, 11, 6, 'present', NULL, '2026-06-25 19:48:24', '2026-06-25 19:48:24');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (127, 11, 7, 'present', NULL, '2026-06-25 19:48:24', '2026-06-25 19:48:24');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (128, 11, 8, 'absent', 'Reported unavailable', '2026-06-25 19:48:24', '2026-06-25 19:48:24');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (129, 11, 9, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:24', '2026-06-25 19:48:24');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (130, 11, 10, 'present', NULL, '2026-06-25 19:48:24', '2026-06-25 19:48:24');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (131, 11, 11, 'present', NULL, '2026-06-25 19:48:25', '2026-06-25 19:48:25');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (132, 11, 12, 'present', NULL, '2026-06-25 19:48:25', '2026-06-25 19:48:25');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (133, 12, 1, 'present', NULL, '2026-06-25 19:48:25', '2026-06-25 19:48:25');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (134, 12, 2, 'absent', 'Reported unavailable', '2026-06-25 19:48:25', '2026-06-25 19:48:25');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (135, 12, 3, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:25', '2026-06-25 19:48:25');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (136, 12, 4, 'present', NULL, '2026-06-25 19:48:25', '2026-06-25 19:48:25');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (137, 12, 5, 'present', NULL, '2026-06-25 19:48:26', '2026-06-25 19:48:26');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (138, 12, 6, 'present', NULL, '2026-06-25 19:48:26', '2026-06-25 19:48:26');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (139, 12, 7, 'absent', 'Reported unavailable', '2026-06-25 19:48:26', '2026-06-25 19:48:26');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (140, 12, 8, 'late', 'Arrived 10 minutes late', '2026-06-25 19:48:26', '2026-06-25 19:48:26');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (141, 12, 9, 'present', NULL, '2026-06-25 19:48:26', '2026-06-25 19:48:26');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (142, 12, 10, 'present', NULL, '2026-06-25 19:48:27', '2026-06-25 19:48:27');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (143, 12, 11, 'present', NULL, '2026-06-25 19:48:27', '2026-06-25 19:48:27');
INSERT INTO `session_attendance` (`id`, `training_session_id`, `player_id`, `status`, `remarks`, `created_at`, `updated_at`) VALUES (144, 12, 12, 'absent', 'Reported unavailable', '2026-06-25 19:48:27', '2026-06-25 19:48:27');

INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (1, 'site_display_name', 'Powerblink FC', '2026-06-25 19:47:04', '2026-06-25 19:47:04');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (2, 'logo_path', 'asset/images/powerblink/about-us-powerblink-fc-001.jpg', '2026-06-25 19:47:04', '2026-06-25 19:47:04');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (3, 'logo_light_path', 'asset/images/powerblink/about-us-powerblink-fc-001.jpg', '2026-06-25 19:47:04', '2026-06-25 19:47:04');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (4, 'favicon_path', 'asset/images/powerblink/about-us-powerblink-fc-001.jpg', '2026-06-25 19:47:05', '2026-06-25 19:47:05');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (5, 'auth_panel_image_path', 'asset/images/powerblink/home-powerblink-fc-044.jpg', '2026-06-25 19:47:05', '2026-06-25 19:47:05');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (6, 'dealer_phone', '+234 800 POWERBLINK', '2026-06-25 19:47:05', '2026-06-25 19:47:05');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (7, 'dealer_sales_phone', '+234 800 POWERBLINK', '2026-06-25 19:47:05', '2026-06-25 19:47:05');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (8, 'dealer_public_email', 'info@powerblinkfc.com', '2026-06-25 19:47:05', '2026-06-25 19:47:05');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (9, 'contact_notify_email', 'info@powerblinkfc.com', '2026-06-25 19:47:05', '2026-06-25 19:47:05');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (10, 'contact_from_name', 'Powerblink FC', '2026-06-25 19:47:06', '2026-06-25 19:47:06');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (11, 'dealer_address', 'Plot 42, Powerblink Avenue, Coastal Way, Ibeju Lekki, Lagos, Nigeria', '2026-06-25 19:47:06', '2026-06-25 19:47:06');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (12, 'dealer_hours_label', 'Academy Hours', '2026-06-25 19:47:06', '2026-06-25 19:47:06');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (13, 'dealer_sales_hours', 'Monday – Friday: 08:00AM – 06:00PM
Saturday: 08:00AM – 02:00PM
Sunday: Closed', '2026-06-25 19:47:06', '2026-06-25 19:47:06');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (14, 'dealer_service_hours', 'Monday – Friday: 08:00AM – 06:00PM
Saturday: 08:00AM – 02:00PM
Sunday: Closed', '2026-06-25 19:47:06', '2026-06-25 19:47:06');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (15, 'dealer_parts_hours', 'Monday – Friday: 08:00AM – 06:00PM
Saturday: 08:00AM – 02:00PM
Sunday: Closed', '2026-06-25 19:47:06', '2026-06-25 19:47:06');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (16, 'footer_tagline', 'Elite Excellence in Ibeju Lekki. Shaping the future of football, one star at a time.', '2026-06-25 19:47:07', '2026-06-25 19:47:07');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (17, 'footer_about', 'Powerblink Football Club Limited is a world-class youth academy in Ibeju Lekki, Lagos. We develop disciplined athletes through elite coaching, structured programs, and a safe environment that bridges grassroots talent with professional standards.', '2026-06-25 19:47:07', '2026-06-25 19:47:07');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (18, 'copyright_line', 'Powerblink FC', '2026-06-25 19:47:07', '2026-06-25 19:47:07');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (19, 'social_facebook', 'https://www.facebook.com/powerblinkfc', '2026-06-25 19:47:07', '2026-06-25 19:47:07');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (20, 'social_instagram', 'https://www.instagram.com/powerblinkfc', '2026-06-25 19:47:07', '2026-06-25 19:47:07');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (21, 'social_linkedin', 'https://www.linkedin.com/company/powerblinkfc', '2026-06-25 19:47:07', '2026-06-25 19:47:07');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (22, 'social_youtube', 'https://www.youtube.com/@powerblinkfc', '2026-06-25 19:47:08', '2026-06-25 19:47:08');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (23, 'newsletter_enabled', '1', '2026-06-25 19:47:08', '2026-06-25 19:47:08');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (24, 'newsletter_note', 'Get academy updates, tournament news, and registration openings.', '2026-06-25 19:47:08', '2026-06-25 19:47:08');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (25, 'payment_paystack_enabled', '1', '2026-06-25 19:47:08', '2026-06-25 19:47:08');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (26, 'payment_bank_transfer_enabled', '0', '2026-06-25 19:47:08', '2026-06-25 19:47:08');
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES (27, 'payment_pay_on_delivery_enabled', '0', '2026-06-25 19:47:08', '2026-06-25 19:47:08');

INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (1, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:30', '2026-06-25 19:47:30');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (2, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:47:30', '2026-06-25 19:47:30');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (3, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:47:30', '2026-06-25 19:47:30');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (4, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:47:30', '2026-06-25 19:47:30');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (5, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:47:31', '2026-06-25 19:47:31');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (6, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:47:31', '2026-06-25 19:47:31');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (7, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:47:31', '2026-06-25 19:47:31');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (8, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:47:31', '2026-06-25 19:47:31');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (9, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:47:31', '2026-06-25 19:47:31');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (10, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:47:31', '2026-06-25 19:47:31');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (11, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:47:32', '2026-06-25 19:47:32');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (12, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-06 22:47:30', '2026-06-25 19:47:32', '2026-06-25 19:47:32');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (13, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-07 23:47:30', '2026-06-25 19:47:32', '2026-06-25 19:47:32');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (14, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-09 00:47:30', '2026-06-25 19:47:32', '2026-06-25 19:47:32');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (15, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-10 01:47:30', '2026-06-25 19:47:32', '2026-06-25 19:47:32');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (16, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-11 02:47:30', '2026-06-25 19:47:33', '2026-06-25 19:47:33');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (17, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-06-11 19:47:30', '2026-06-25 19:47:33', '2026-06-25 19:47:33');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (18, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-12 20:47:30', '2026-06-25 19:47:33', '2026-06-25 19:47:33');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (19, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-13 21:47:30', '2026-06-25 19:47:33', '2026-06-25 19:47:33');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (20, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-14 22:47:30', '2026-06-25 19:47:33', '2026-06-25 19:47:33');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (21, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-15 23:47:30', '2026-06-25 19:47:34', '2026-06-25 19:47:34');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (22, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-17 00:47:30', '2026-06-25 19:47:34', '2026-06-25 19:47:34');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (23, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-18 01:47:30', '2026-06-25 19:47:34', '2026-06-25 19:47:34');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (24, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-19 02:47:30', '2026-06-25 19:47:34', '2026-06-25 19:47:34');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (25, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-19 19:47:30', '2026-06-25 19:47:34', '2026-06-25 19:47:34');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (26, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-20 20:47:30', '2026-06-25 19:47:34', '2026-06-25 19:47:34');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (27, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-21 21:47:30', '2026-06-25 19:47:35', '2026-06-25 19:47:35');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (28, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-22 22:47:30', '2026-06-25 19:47:35', '2026-06-25 19:47:35');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (29, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-26 23:47:30', '2026-06-25 19:47:35', '2026-06-25 19:47:35');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (30, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-05-28 00:47:30', '2026-06-25 19:47:35', '2026-06-25 19:47:35');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (31, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-05-29 01:47:30', '2026-06-25 19:47:36', '2026-06-25 19:47:36');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (32, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-05-30 02:47:30', '2026-06-25 19:47:36', '2026-06-25 19:47:36');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (33, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-05-30 19:47:30', '2026-06-25 19:47:36', '2026-06-25 19:47:36');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (34, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-05-31 20:47:30', '2026-06-25 19:47:36', '2026-06-25 19:47:36');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (35, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-01 21:47:30', '2026-06-25 19:47:36', '2026-06-25 19:47:36');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (36, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-02 22:47:30', '2026-06-25 19:47:37', '2026-06-25 19:47:37');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (37, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-03 23:47:30', '2026-06-25 19:47:37', '2026-06-25 19:47:37');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (38, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-05 00:47:30', '2026-06-25 19:47:37', '2026-06-25 19:47:37');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (39, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-06 01:47:30', '2026-06-25 19:47:37', '2026-06-25 19:47:37');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (40, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-07 02:47:30', '2026-06-25 19:47:37', '2026-06-25 19:47:37');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (41, '/', 'home', 'https://powerblinkfc.com/', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-06-07 19:47:30', '2026-06-25 19:47:38', '2026-06-25 19:47:38');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (42, '/', 'home', 'https://powerblinkfc.com/', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-08 20:47:30', '2026-06-25 19:47:38', '2026-06-25 19:47:38');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (43, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:38', '2026-06-25 19:47:38');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (44, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:47:38', '2026-06-25 19:47:38');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (45, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:47:38', '2026-06-25 19:47:38');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (46, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:47:39', '2026-06-25 19:47:39');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (47, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:47:39', '2026-06-25 19:47:39');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (48, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:47:39', '2026-06-25 19:47:39');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (49, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:47:39', '2026-06-25 19:47:39');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (50, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:47:39', '2026-06-25 19:47:39');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (51, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:47:40', '2026-06-25 19:47:40');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (52, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:47:40', '2026-06-25 19:47:40');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (53, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:47:40', '2026-06-25 19:47:40');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (54, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-06 22:47:30', '2026-06-25 19:47:40', '2026-06-25 19:47:40');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (55, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-07 23:47:30', '2026-06-25 19:47:40', '2026-06-25 19:47:40');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (56, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-09 00:47:30', '2026-06-25 19:47:41', '2026-06-25 19:47:41');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (57, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-10 01:47:30', '2026-06-25 19:47:41', '2026-06-25 19:47:41');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (58, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-11 02:47:30', '2026-06-25 19:47:41', '2026-06-25 19:47:41');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (59, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-06-11 19:47:30', '2026-06-25 19:47:41', '2026-06-25 19:47:41');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (60, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-12 20:47:30', '2026-06-25 19:47:41', '2026-06-25 19:47:41');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (61, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-13 21:47:30', '2026-06-25 19:47:42', '2026-06-25 19:47:42');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (62, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-14 22:47:30', '2026-06-25 19:47:42', '2026-06-25 19:47:42');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (63, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-15 23:47:30', '2026-06-25 19:47:42', '2026-06-25 19:47:42');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (64, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-17 00:47:30', '2026-06-25 19:47:42', '2026-06-25 19:47:42');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (65, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-18 01:47:30', '2026-06-25 19:47:42', '2026-06-25 19:47:42');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (66, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-19 02:47:30', '2026-06-25 19:47:43', '2026-06-25 19:47:43');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (67, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-19 19:47:30', '2026-06-25 19:47:43', '2026-06-25 19:47:43');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (68, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-20 20:47:30', '2026-06-25 19:47:43', '2026-06-25 19:47:43');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (69, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-21 21:47:30', '2026-06-25 19:47:43', '2026-06-25 19:47:43');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (70, '/programs', 'programs', 'https://powerblinkfc.com/programs', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-22 22:47:30', '2026-06-25 19:47:43', '2026-06-25 19:47:43');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (71, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:44', '2026-06-25 19:47:44');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (72, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:47:44', '2026-06-25 19:47:44');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (73, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:47:44', '2026-06-25 19:47:44');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (74, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:47:44', '2026-06-25 19:47:44');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (75, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:47:44', '2026-06-25 19:47:44');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (76, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:47:44', '2026-06-25 19:47:44');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (77, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:47:45', '2026-06-25 19:47:45');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (78, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:47:45', '2026-06-25 19:47:45');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (79, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:47:45', '2026-06-25 19:47:45');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (80, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:47:45', '2026-06-25 19:47:45');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (81, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:47:45', '2026-06-25 19:47:45');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (82, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-06 22:47:30', '2026-06-25 19:47:45', '2026-06-25 19:47:45');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (83, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-07 23:47:30', '2026-06-25 19:47:46', '2026-06-25 19:47:46');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (84, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-09 00:47:30', '2026-06-25 19:47:46', '2026-06-25 19:47:46');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (85, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-10 01:47:30', '2026-06-25 19:47:46', '2026-06-25 19:47:46');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (86, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-11 02:47:30', '2026-06-25 19:47:46', '2026-06-25 19:47:46');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (87, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-06-11 19:47:30', '2026-06-25 19:47:46', '2026-06-25 19:47:46');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (88, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-12 20:47:30', '2026-06-25 19:47:47', '2026-06-25 19:47:47');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (89, '/about', 'about', 'https://powerblinkfc.com/about', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-13 21:47:30', '2026-06-25 19:47:47', '2026-06-25 19:47:47');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (90, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:47', '2026-06-25 19:47:47');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (91, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:47:47', '2026-06-25 19:47:47');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (92, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:47:47', '2026-06-25 19:47:47');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (93, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:47:48', '2026-06-25 19:47:48');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (94, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:47:48', '2026-06-25 19:47:48');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (95, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:47:48', '2026-06-25 19:47:48');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (96, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:47:48', '2026-06-25 19:47:48');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (97, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:47:48', '2026-06-25 19:47:48');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (98, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:47:49', '2026-06-25 19:47:49');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (99, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:47:49', '2026-06-25 19:47:49');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (100, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:47:49', '2026-06-25 19:47:49');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (101, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-06 22:47:30', '2026-06-25 19:47:49', '2026-06-25 19:47:49');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (102, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-07 23:47:30', '2026-06-25 19:47:50', '2026-06-25 19:47:50');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (103, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-09 00:47:30', '2026-06-25 19:47:50', '2026-06-25 19:47:50');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (104, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-10 01:47:30', '2026-06-25 19:47:50', '2026-06-25 19:47:50');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (105, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-11 02:47:30', '2026-06-25 19:47:50', '2026-06-25 19:47:50');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (106, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-06-11 19:47:30', '2026-06-25 19:47:50', '2026-06-25 19:47:50');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (107, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-12 20:47:30', '2026-06-25 19:47:51', '2026-06-25 19:47:51');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (108, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-13 21:47:30', '2026-06-25 19:47:51', '2026-06-25 19:47:51');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (109, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-14 22:47:30', '2026-06-25 19:47:51', '2026-06-25 19:47:51');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (110, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-15 23:47:30', '2026-06-25 19:47:51', '2026-06-25 19:47:51');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (111, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-17 00:47:30', '2026-06-25 19:47:52', '2026-06-25 19:47:52');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (112, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-18 01:47:30', '2026-06-25 19:47:52', '2026-06-25 19:47:52');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (113, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-19 02:47:30', '2026-06-25 19:47:52', '2026-06-25 19:47:52');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (114, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-19 19:47:30', '2026-06-25 19:47:52', '2026-06-25 19:47:52');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (115, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-20 20:47:30', '2026-06-25 19:47:52', '2026-06-25 19:47:52');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (116, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-21 21:47:30', '2026-06-25 19:47:53', '2026-06-25 19:47:53');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (117, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-06-22 22:47:30', '2026-06-25 19:47:53', '2026-06-25 19:47:53');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (118, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-26 23:47:30', '2026-06-25 19:47:53', '2026-06-25 19:47:53');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (119, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-05-28 00:47:30', '2026-06-25 19:47:53', '2026-06-25 19:47:53');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (120, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-05-29 01:47:30', '2026-06-25 19:47:53', '2026-06-25 19:47:53');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (121, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-05-30 02:47:30', '2026-06-25 19:47:54', '2026-06-25 19:47:54');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (122, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-05-30 19:47:30', '2026-06-25 19:47:54', '2026-06-25 19:47:54');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (123, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-05-31 20:47:30', '2026-06-25 19:47:54', '2026-06-25 19:47:54');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (124, '/register', 'registration.wizard', 'https://powerblinkfc.com/register', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-01 21:47:30', '2026-06-25 19:47:54', '2026-06-25 19:47:54');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (125, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:54', '2026-06-25 19:47:54');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (126, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:47:54', '2026-06-25 19:47:54');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (127, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:47:55', '2026-06-25 19:47:55');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (128, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:47:55', '2026-06-25 19:47:55');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (129, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:47:55', '2026-06-25 19:47:55');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (130, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:47:55', '2026-06-25 19:47:55');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (131, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:47:55', '2026-06-25 19:47:55');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (132, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:47:55', '2026-06-25 19:47:55');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (133, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:47:56', '2026-06-25 19:47:56');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (134, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:47:56', '2026-06-25 19:47:56');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (135, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:47:56', '2026-06-25 19:47:56');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (136, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-06 22:47:30', '2026-06-25 19:47:56', '2026-06-25 19:47:56');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (137, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-06-07 23:47:30', '2026-06-25 19:47:56', '2026-06-25 19:47:56');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (138, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-06-09 00:47:30', '2026-06-25 19:47:57', '2026-06-25 19:47:57');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (139, '/gallery', 'gallery', 'https://powerblinkfc.com/gallery', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-06-10 01:47:30', '2026-06-25 19:47:57', '2026-06-25 19:47:57');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (140, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:57', '2026-06-25 19:47:57');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (141, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:47:57', '2026-06-25 19:47:57');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (142, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:47:57', '2026-06-25 19:47:57');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (143, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:47:58', '2026-06-25 19:47:58');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (144, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:47:58', '2026-06-25 19:47:58');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (145, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:47:58', '2026-06-25 19:47:58');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (146, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:47:58', '2026-06-25 19:47:58');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (147, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:47:58', '2026-06-25 19:47:58');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (148, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:47:59', '2026-06-25 19:47:59');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (149, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:47:59', '2026-06-25 19:47:59');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (150, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:47:59', '2026-06-25 19:47:59');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (151, '/contact', 'contact', 'https://powerblinkfc.com/contact', 'GET', NULL, NULL, NULL, NULL, 'demo-session-11', '2026-06-06 22:47:30', '2026-06-25 19:47:59', '2026-06-25 19:47:59');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (152, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-0', '2026-05-26 19:47:30', '2026-06-25 19:47:59', '2026-06-25 19:47:59');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (153, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-1', '2026-05-27 20:47:30', '2026-06-25 19:48:00', '2026-06-25 19:48:00');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (154, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', NULL, NULL, NULL, NULL, 'demo-session-2', '2026-05-28 21:47:30', '2026-06-25 19:48:00', '2026-06-25 19:48:00');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (155, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-3', '2026-05-29 22:47:30', '2026-06-25 19:48:00', '2026-06-25 19:48:00');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (156, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-4', '2026-05-30 23:47:30', '2026-06-25 19:48:00', '2026-06-25 19:48:00');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (157, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', NULL, NULL, NULL, NULL, 'demo-session-5', '2026-06-01 00:47:30', '2026-06-25 19:48:00', '2026-06-25 19:48:00');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (158, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-6', '2026-06-02 01:47:30', '2026-06-25 19:48:01', '2026-06-25 19:48:01');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (159, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-7', '2026-06-03 02:47:30', '2026-06-25 19:48:01', '2026-06-25 19:48:01');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (160, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', NULL, NULL, NULL, NULL, 'demo-session-8', '2026-06-03 19:47:30', '2026-06-25 19:48:01', '2026-06-25 19:48:01');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (161, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'google.com', NULL, NULL, NULL, 'demo-session-9', '2026-06-04 20:47:30', '2026-06-25 19:48:01', '2026-06-25 19:48:01');
INSERT INTO `site_traffic_events` (`id`, `path`, `route_name`, `url`, `method`, `referrer_host`, `referrer_url`, `user_agent`, `ip_hash`, `session_id`, `viewed_at`, `created_at`, `updated_at`) VALUES (162, '/tournaments', 'tournaments', 'https://powerblinkfc.com/tournaments', 'GET', 'instagram.com', NULL, NULL, NULL, 'demo-session-10', '2026-06-05 21:47:30', '2026-06-25 19:48:01', '2026-06-25 19:48:01');

INSERT INTO `timeline_events` (`id`, `year`, `title`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES (1, 2018, 'Academy Founded', 'Powerblink FC launches grassroots training in Ibeju Lekki.', 1, '2026-06-25 19:48:43', '2026-06-25 19:48:43');
INSERT INTO `timeline_events` (`id`, `year`, `title`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES (2, 2021, 'Elite Performance Framework', 'Structured U7–U15 pathways and sports science integration introduced.', 2, '2026-06-25 19:48:43', '2026-06-25 19:48:43');
INSERT INTO `timeline_events` (`id`, `year`, `title`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES (3, 2024, 'Independence Day Tournament', 'Inaugural academy-wide tournament draws regional youth clubs.', 3, '2026-06-25 19:48:43', '2026-06-25 19:48:43');
INSERT INTO `timeline_events` (`id`, `year`, `title`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES (4, 2026, 'Digital Academy Platform', 'Parent, player, and coach portals launch for registrations, attendance, and payments.', 4, '2026-06-25 19:48:44', '2026-06-25 19:48:44');

INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (1, 1, 1, 'Midfielder', '2026-06-25 19:48:29', '2026-06-25 19:48:29');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (2, 1, 2, 'Defender', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (3, 1, 3, 'Goalkeeper', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (4, 1, 4, 'Forward', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (5, 1, 5, 'Midfielder', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (6, 1, 6, 'Forward', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (7, 1, 7, 'Winger', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (8, 1, 8, 'Defender', '2026-06-25 19:48:30', '2026-06-25 19:48:30');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (9, 2, 1, 'Midfielder', '2026-06-25 19:48:31', '2026-06-25 19:48:31');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (10, 2, 2, 'Defender', '2026-06-25 19:48:33', '2026-06-25 19:48:33');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (11, 2, 3, 'Goalkeeper', '2026-06-25 19:48:33', '2026-06-25 19:48:33');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (12, 2, 4, 'Forward', '2026-06-25 19:48:33', '2026-06-25 19:48:33');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (13, 2, 5, 'Midfielder', '2026-06-25 19:48:33', '2026-06-25 19:48:33');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (14, 2, 6, 'Forward', '2026-06-25 19:48:34', '2026-06-25 19:48:34');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (15, 2, 7, 'Winger', '2026-06-25 19:48:34', '2026-06-25 19:48:34');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (16, 2, 8, 'Defender', '2026-06-25 19:48:34', '2026-06-25 19:48:34');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (17, 3, 1, 'Midfielder', '2026-06-25 19:48:34', '2026-06-25 19:48:34');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (18, 3, 2, 'Defender', '2026-06-25 19:48:34', '2026-06-25 19:48:34');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (19, 3, 3, 'Goalkeeper', '2026-06-25 19:48:35', '2026-06-25 19:48:35');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (20, 3, 4, 'Forward', '2026-06-25 19:48:35', '2026-06-25 19:48:35');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (21, 3, 5, 'Midfielder', '2026-06-25 19:48:35', '2026-06-25 19:48:35');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (22, 3, 6, 'Forward', '2026-06-25 19:48:35', '2026-06-25 19:48:35');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (23, 3, 7, 'Winger', '2026-06-25 19:48:35', '2026-06-25 19:48:35');
INSERT INTO `tournament_squads` (`id`, `tournament_id`, `player_id`, `position`, `created_at`, `updated_at`) VALUES (24, 3, 8, 'Defender', '2026-06-25 19:48:35', '2026-06-25 19:48:35');

INSERT INTO `tournaments` (`id`, `season_id`, `title`, `category`, `start_date`, `end_date`, `location`, `description`, `status`, `max_teams`, `featured_image_media_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, 'Powerblink Independence Day Tournament', 'U13-U15', '2026-10-01 00:00:00', '2026-10-03 00:00:00', 'Powerblink Academy Grounds, Ibeju Lekki', 'Regional youth showcase featuring Powerblink FC squads and invited clubs.', 'upcoming', 12, 23, '2026-06-25 19:48:29', '2026-06-25 19:48:29', NULL);
INSERT INTO `tournaments` (`id`, `season_id`, `title`, `category`, `start_date`, `end_date`, `location`, `description`, `status`, `max_teams`, `featured_image_media_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, 'Lekki Youth Cup 2026', 'U10-U13', '2026-08-15 00:00:00', '2026-08-17 00:00:00', 'Powerblink Academy Grounds, Ibeju Lekki', 'Regional youth showcase featuring Powerblink FC squads and invited clubs.', 'upcoming', 12, 24, '2026-06-25 19:48:31', '2026-06-25 19:48:31', NULL);
INSERT INTO `tournaments` (`id`, `season_id`, `title`, `category`, `start_date`, `end_date`, `location`, `description`, `status`, `max_teams`, `featured_image_media_id`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 1, 'Spring Academy Showcase 2026', 'All Ages', '2026-03-20 00:00:00', '2026-03-22 00:00:00', 'Powerblink Academy Grounds, Ibeju Lekki', 'Regional youth showcase featuring Powerblink FC squads and invited clubs.', 'completed', 12, 25, '2026-06-25 19:48:34', '2026-06-25 19:48:34', NULL);

INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, 1, 1, 'U7 Session 1', 'technical', '2026-05-01 00:00:00', '08:00:00', '09:30:00', 'Pitch A, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:02', '2026-06-25 19:48:02', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 1, 2, 2, 'U10 Session 2', 'tactical', '2026-05-04 00:00:00', '09:00:00', '10:30:00', 'Pitch B, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:02', '2026-06-25 19:48:02', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 1, 3, 3, 'U13 Session 3', 'match_play', '2026-05-07 00:00:00', '10:00:00', '11:30:00', 'Pitch C, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:02', '2026-06-25 19:48:02', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 1, 4, 4, 'U15 Session 4', 'conditioning', '2026-05-10 00:00:00', '11:00:00', '12:30:00', 'Pitch A, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:02', '2026-06-25 19:48:02', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 1, 1, 1, 'U7 Session 5', 'technical', '2026-05-13 00:00:00', '12:00:00', '13:30:00', 'Pitch B, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:02', '2026-06-25 19:48:02', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, 1, 2, 2, 'U10 Session 6', 'tactical', '2026-05-16 00:00:00', '13:00:00', '14:30:00', 'Pitch C, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:02', '2026-06-25 19:48:02', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, 1, 3, 3, 'U13 Session 7', 'match_play', '2026-05-19 00:00:00', '08:00:00', '09:30:00', 'Pitch A, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:03', '2026-06-25 19:48:03', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (8, 1, 4, 4, 'U15 Session 8', 'conditioning', '2026-05-22 00:00:00', '09:00:00', '10:30:00', 'Pitch B, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:03', '2026-06-25 19:48:03', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (9, 1, 1, 1, 'U7 Session 9', 'technical', '2026-05-25 00:00:00', '10:00:00', '11:30:00', 'Pitch C, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:03', '2026-06-25 19:48:03', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (10, 1, 2, 2, 'U10 Session 10', 'tactical', '2026-05-28 00:00:00', '11:00:00', '12:30:00', 'Pitch A, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:03', '2026-06-25 19:48:03', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (11, 1, 3, 3, 'U13 Session 11', 'match_play', '2026-05-31 00:00:00', '12:00:00', '13:30:00', 'Pitch B, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:03', '2026-06-25 19:48:03', NULL);
INSERT INTO `training_sessions` (`id`, `season_id`, `program_id`, `coach_id`, `title`, `session_type`, `date`, `start_time`, `end_time`, `location`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES (12, 1, 4, 4, 'U15 Session 12', 'conditioning', '2026-06-03 00:00:00', '13:00:00', '14:30:00', 'Pitch C, Powerblink Academy', 'Showcase training session for Powerblink FC.', '2026-06-25 19:48:03', '2026-06-25 19:48:03', NULL);

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`, `email_login_otp_enabled`, `is_super_admin`) VALUES (1, 'PowerBlink Admin', 'info@powerblinkfc.com', '2026-06-25 19:47:12', '$2y$12$zNz4afsRq4xzxHPORvMc3ueiPRB1it6bkdqCrbDypuj6sOwEZXrdW', NULL, '2026-06-25 19:47:12', '2026-06-25 19:47:13', NULL, NULL, 0, 1);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`, `email_login_otp_enabled`, `is_super_admin`) VALUES (2, 'Coach Elijah Opetunde', 'coach@powerblinkfc.com', '2026-06-25 19:47:14', '$2y$12$/trMbd0r2xyPZKxQTvX7ZufOARdRrLCO1F7gEMyc37Jcuswrt5mS2', NULL, '2026-06-25 19:47:14', '2026-06-25 19:47:14', NULL, NULL, 0, 0);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`, `email_login_otp_enabled`, `is_super_admin`) VALUES (3, 'Adaeze Okonkwo', 'parent@powerblinkfc.com', '2026-06-25 19:47:15', '$2y$12$ulif4H8tHu8pUwkMX1TVaeK0QvDgK7Q0An9TKgRtnmuangjl5hpPC', NULL, '2026-06-25 19:47:15', '2026-06-25 19:47:15', NULL, NULL, 0, 0);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `google_id`, `avatar`, `email_login_otp_enabled`, `is_super_admin`) VALUES (4, 'Tobi Okonkwo', 'player@powerblinkfc.com', '2026-06-25 19:47:16', '$2y$12$qPwwK3CMT0drtN9Ds06zKuyI7nuR6as7MYe6cJhPiGUcZEbSCxlYu', NULL, '2026-06-25 19:47:16', '2026-06-25 19:47:16', NULL, NULL, 0, 0);

SET FOREIGN_KEY_CHECKS=1;
