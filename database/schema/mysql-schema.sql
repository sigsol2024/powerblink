SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `academy_payments`;
CREATE TABLE `academy_payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `player_id` BIGINT UNSIGNED NOT NULL,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `provider` VARCHAR(255) NOT NULL DEFAULT 'paystack',
  `reference` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `amount` BIGINT UNSIGNED NOT NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'NGN',
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
  `method` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `route_name` VARCHAR(255),
  `status_code` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
  `user_agent` VARCHAR(255),
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
  `title` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `audience` VARCHAR(255) NOT NULL DEFAULT 'all',
  `channel` VARCHAR(255) NOT NULL DEFAULT 'in_app',
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
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `meta_description` TEXT,
  `content_html` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `coaches`;
CREATE TABLE `coaches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED,
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255),
  `bio` TEXT,
  `specialization` VARCHAR(255),
  `certifications` TEXT,
  `experience_years` BIGINT UNSIGNED,
  `license_level` VARCHAR(255),
  `email` VARCHAR(255),
  `phone` VARCHAR(255),
  `photo_media_id` BIGINT UNSIGNED,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
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
  `uuid` VARCHAR(255) NOT NULL,
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
  `title` VARCHAR(255),
  `category` VARCHAR(255),
  `sort_order` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `is_published` TINYINT(1) NOT NULL DEFAULT '1',
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
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255),
  `email` VARCHAR(255) NOT NULL,
  `address` TEXT,
  `relationship` VARCHAR(255),
  `emergency_contact_name` VARCHAR(255),
  `emergency_contact_phone` VARCHAR(255),
  `emergency_contact_relationship` VARCHAR(255),
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
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
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
  `id` VARCHAR(255),
  `name` VARCHAR(255) NOT NULL,
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
  `queue` VARCHAR(255) NOT NULL,
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
  `name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255),
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
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255),
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(255),
  `file_size` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `uploaded_by` BIGINT UNSIGNED,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `category` VARCHAR(255),
  `alt_text` VARCHAR(255),
  PRIMARY KEY (`id`),
  CONSTRAINT `media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  KEY `media_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_type`, `model_id`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT NOT NULL,
  `model_type` VARCHAR(255) NOT NULL,
  `model_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_type`, `model_id`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  KEY `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` VARCHAR(255),
  `type` VARCHAR(255) NOT NULL,
  `notifiable_type` VARCHAR(255) NOT NULL,
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
  `page` VARCHAR(255) NOT NULL,
  `section_key` VARCHAR(255) NOT NULL,
  `content_type` VARCHAR(255) NOT NULL DEFAULT 'text',
  `content` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  KEY `page_sections_page_index` (`page`),
  UNIQUE KEY `page_sections_page_section_key_unique` (`page`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
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
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
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
  `document_type` VARCHAR(255) NOT NULL,
  `media_id` BIGINT UNSIGNED NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
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
  `player_code` VARCHAR(255) NOT NULL,
  `photo_media_id` BIGINT UNSIGNED,
  `name` VARCHAR(255) NOT NULL,
  `date_of_birth` DATE,
  `nationality` VARCHAR(255),
  `primary_position` VARCHAR(255),
  `secondary_position` VARCHAR(255),
  `years_experience` BIGINT UNSIGNED,
  `technical_strengths` TEXT,
  `allergies` TEXT,
  `medical_history` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
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
  `name` VARCHAR(255) NOT NULL,
  `age_group` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `monthly_fee` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `registration_fee` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `max_capacity` BIGINT UNSIGNED,
  `sessions_per_week` BIGINT UNSIGNED,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
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
  `type` VARCHAR(255) NOT NULL,
  `provider` VARCHAR(255) NOT NULL DEFAULT 'paystack',
  `reference` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
  `amount` BIGINT UNSIGNED NOT NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'NGN',
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
  `reference_code` VARCHAR(255) NOT NULL,
  `season_id` BIGINT UNSIGNED NOT NULL,
  `program_id` BIGINT UNSIGNED NOT NULL,
  `guardian_id` BIGINT UNSIGNED NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'pending_review',
  `payment_plan` VARCHAR(255) NOT NULL DEFAULT 'lump_sum',
  `payment_token` VARCHAR(255),
  `payment_token_expires_at` DATETIME,
  `payment_token_used_at` DATETIME,
  `player_name` VARCHAR(255) NOT NULL,
  `date_of_birth` DATE,
  `nationality` VARCHAR(255),
  `primary_position` VARCHAR(255),
  `secondary_position` VARCHAR(255),
  `years_experience` BIGINT UNSIGNED,
  `technical_strengths` TEXT,
  `allergies` TEXT,
  `medical_history` TEXT,
  `fitness_certified` TINYINT(1) NOT NULL DEFAULT '0',
  `profile_photo_media_id` BIGINT UNSIGNED,
  `emergency_contact_name` VARCHAR(255),
  `emergency_contact_phone` VARCHAR(255),
  `emergency_contact_relationship` VARCHAR(255),
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
  `permission_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`),
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `guard_name` VARCHAR(255) NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `seasons`;
CREATE TABLE `seasons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT '0',
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
  `status` VARCHAR(255) NOT NULL DEFAULT 'present',
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
  `id` VARCHAR(255),
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(255),
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
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `site_traffic_events`;
CREATE TABLE `site_traffic_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(255) NOT NULL,
  `route_name` VARCHAR(255),
  `url` TEXT,
  `method` VARCHAR(255) NOT NULL DEFAULT 'GET',
  `referrer_host` VARCHAR(255),
  `referrer_url` TEXT,
  `user_agent` VARCHAR(255),
  `ip_hash` VARCHAR(255),
  `session_id` VARCHAR(255),
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
  `title` VARCHAR(255) NOT NULL,
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
  `position` VARCHAR(255),
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
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(255),
  `start_date` DATE,
  `end_date` DATE,
  `location` VARCHAR(255),
  `description` TEXT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'upcoming',
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
  `title` VARCHAR(255) NOT NULL,
  `session_type` VARCHAR(255),
  `date` DATE NOT NULL,
  `start_time` VARCHAR(255) NOT NULL,
  `end_time` VARCHAR(255) NOT NULL,
  `location` VARCHAR(255),
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
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` DATETIME,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100),
  `created_at` DATETIME,
  `updated_at` DATETIME,
  `google_id` VARCHAR(255),
  `avatar` VARCHAR(255),
  `email_login_otp_enabled` TINYINT(1) NOT NULL DEFAULT '0',
  `is_super_admin` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `users_is_super_admin_index` (`is_super_admin`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

