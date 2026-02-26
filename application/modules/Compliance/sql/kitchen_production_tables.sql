-- Kitchen Production Module Tables
-- Date: 2026-02-26

-- Sites table for Kitchen Production
CREATE TABLE IF NOT EXISTS `Compliance_KitchenProductionsites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `location_id` int DEFAULT NULL,
  `emailNotify` tinyint(1) DEFAULT 0,
  `emailToNotify` varchar(255) DEFAULT NULL,
  `staff_comments` text DEFAULT NULL,
  `manager_comments` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prep Area table for Kitchen Production
CREATE TABLE IF NOT EXISTS `Compliance_KitchenProductionPrepArea` (
  `id` int NOT NULL AUTO_INCREMENT,
  `prep_name` varchar(255) NOT NULL,
  `site_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `location_id` int DEFAULT NULL,
  `sort_order` int DEFAULT 0,
  `created_at` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table for Kitchen Production
CREATE TABLE IF NOT EXISTS `Compliance_KitchenProductionproducts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `par_level` decimal(10,2) DEFAULT 0,
  `prep_id` int DEFAULT NULL,
  `site_id` int DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `location_id` int DEFAULT NULL,
  `sort_order` int DEFAULT 0,
  `created_at` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prep_id` (`prep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- History table for Kitchen Production (stores daily production entries)
CREATE TABLE IF NOT EXISTS `Compliance_KitchenProduction_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `prep_id` int DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `entered_by` varchar(255) DEFAULT NULL,
  `date_entered` date DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `prep_id` (`prep_id`),
  KEY `date_entered` (`date_entered`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
