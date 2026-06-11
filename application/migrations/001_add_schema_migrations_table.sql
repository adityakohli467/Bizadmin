-- Migration: 001_add_schema_migrations_table
-- Description: Creates the schema_migrations tracking table in each tenant DB
-- Date: 2026-06-11

CREATE TABLE IF NOT EXISTS `schema_migrations` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT(11) NOT NULL,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_migration` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
