-- ============================================
-- Thermometer Calibration Tables for Compliance Module
-- ============================================

-- Sites table
CREATE TABLE IF NOT EXISTS `Compliance_ThermometerCalibSites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `staff_comments` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `location_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prep Areas table
CREATE TABLE IF NOT EXISTS `Compliance_ThermometerCalibPrepArea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prep_name` varchar(255) NOT NULL,
  `site_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `location_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_thermcalib_prep_site` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products (Equipment) table
CREATE TABLE IF NOT EXISTS `Compliance_ThermometerCalibProducts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `prep_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `location_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` date DEFAULT NULL,
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_thermcalib_product_prep` (`prep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- History (Records) table
CREATE TABLE IF NOT EXISTS `Compliance_ThermometerCalib_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `prep_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `date_entered` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_thermcalib_history_product` (`product_id`),
  KEY `idx_thermcalib_history_date` (`date_entered`),
  KEY `idx_thermcalib_history_location` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
