-- Database Schema for Farm System (MySQL Compatible)
-- Generated for Davina and Rodgers Solutions LTD

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `weeks`
--

CREATE TABLE IF NOT EXISTS `weeks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_number` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `week_number` (`week_number`,`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `flock_status`
--

CREATE TABLE IF NOT EXISTS `flock_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `age_weeks` int(11) DEFAULT NULL,
  `opening_birds` int(11) DEFAULT NULL,
  `added_birds` int(11) DEFAULT 0,
  `sold_culls` int(11) DEFAULT 0,
  `dead_birds` int(11) DEFAULT 0,
  `cause_of_death` text DEFAULT NULL,
  `isolated_birds` int(11) DEFAULT 0,
  `closing_birds` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `flock_status_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `egg_production`
--

CREATE TABLE IF NOT EXISTS `egg_production` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `total_collected` int(11) DEFAULT NULL,
  `crates_produced` decimal(10,2) DEFAULT NULL,
  `cracked_broken` int(11) DEFAULT 0,
  `small_eggs` int(11) DEFAULT 0,
  `fed_to_dogs` int(11) DEFAULT 0,
  `consumed_home` int(11) DEFAULT 0,
  `discarded` int(11) DEFAULT 0,
  `saleable_eggs` int(11) DEFAULT NULL,
  `eggs_per_bird_day` decimal(10,2) DEFAULT NULL,
  `production_percent` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `egg_production_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `feed_records`
--

CREATE TABLE IF NOT EXISTS `feed_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `feed_type` text DEFAULT NULL,
  `stock_start` decimal(10,2) DEFAULT NULL,
  `purchased` decimal(10,2) DEFAULT NULL,
  `total_available` decimal(10,2) DEFAULT NULL,
  `used_bags` decimal(10,2) DEFAULT NULL,
  `stock_end` decimal(10,2) DEFAULT NULL,
  `cost_purchased` decimal(10,2) DEFAULT NULL,
  `avg_feed_per_bird` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `feed_records_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `water_records`
--

CREATE TABLE IF NOT EXISTS `water_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `water_source` text DEFAULT NULL,
  `treatments_given` text DEFAULT NULL,
  `cost_treatments` decimal(10,2) DEFAULT NULL,
  `repairs_done` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `water_records_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `health_records`
--

CREATE TABLE IF NOT EXISTS `health_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `vaccinations` text DEFAULT NULL,
  `diseases_observed` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `drugs_given` text DEFAULT NULL,
  `treatment_duration` text DEFAULT NULL,
  `withdrawal_period` text DEFAULT NULL,
  `vet_visits` text DEFAULT NULL,
  `vet_recommendations` text DEFAULT NULL,
  `deaths_linked_illness` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `health_records_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `biosecurity`
--

CREATE TABLE IF NOT EXISTS `biosecurity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `disinfectant_used` text DEFAULT NULL,
  `houses_cleaned` tinyint(1) DEFAULT 0,
  `footbath_maintained` tinyint(1) DEFAULT 0,
  `rodent_control` tinyint(1) DEFAULT 0,
  `visitors_recorded` text DEFAULT NULL,
  `protective_gear_used` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `biosecurity_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `labour_records`
--

CREATE TABLE IF NOT EXISTS `labour_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `num_workers` int(11) DEFAULT NULL,
  `days_worked_per_worker` decimal(10,2) DEFAULT NULL,
  `wages_paid` decimal(10,2) DEFAULT NULL,
  `casual_labour_cost` decimal(10,2) DEFAULT NULL,
  `meals_cost` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `labour_records_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `expenses`
--

CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `feed_cost` decimal(10,2) DEFAULT 0.00,
  `medicine_cost` decimal(10,2) DEFAULT 0.00,
  `fuel_cost` decimal(10,2) DEFAULT 0.00,
  `repairs_cost` decimal(10,2) DEFAULT 0.00,
  `electricity_cost` decimal(10,2) DEFAULT 0.00,
  `water_cost` decimal(10,2) DEFAULT 0.00,
  `labour_cost` decimal(10,2) DEFAULT 0.00,
  `transport_cost` decimal(10,2) DEFAULT 0.00,
  `supplies_cost` decimal(10,2) DEFAULT 0.00,
  `misc_cost` decimal(10,2) DEFAULT 0.00,
  `total_expenses` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `egg_sales`
--

CREATE TABLE IF NOT EXISTS `egg_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `grade1_sold` decimal(10,2) DEFAULT 0.00,
  `grade2_sold` decimal(10,2) DEFAULT 0.00,
  `grade3_sold` decimal(10,2) DEFAULT 0.00,
  `grade4_sold` decimal(10,2) DEFAULT 0.00,
  `price_per_crate` decimal(10,2) DEFAULT 0.00,
  `start_balance` decimal(10,2) DEFAULT 0.00,
  `total_crates_sold` decimal(10,2) DEFAULT NULL,
  `total_sales_value` decimal(10,2) DEFAULT NULL,
  `cash_received` decimal(10,2) DEFAULT NULL,
  `credit_sales` decimal(10,2) DEFAULT NULL,
  `outstanding_balance` decimal(10,2) DEFAULT NULL,
  `transport_cost_sales` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `egg_sales_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `inventory`
--

CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `feed_store_bags` decimal(10,2) DEFAULT NULL,
  `reorder_needed` tinyint(1) DEFAULT 0,
  `drug_store_low` text DEFAULT NULL,
  `expired_drugs_removed` tinyint(1) DEFAULT 0,
  `egg_trays_balance` decimal(10,2) DEFAULT NULL,
  `disinfectants_remaining` decimal(10,2) DEFAULT NULL,
  `supplies_restock` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `assets_equipment`
--

CREATE TABLE IF NOT EXISTS `assets_equipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `repair_needed` text DEFAULT NULL,
  `repairs_done` text DEFAULT NULL,
  `fuel_used_genset` decimal(10,2) DEFAULT NULL,
  `vehicle_condition` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `assets_equipment_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `performance_summary`
--

CREATE TABLE IF NOT EXISTS `performance_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `mortality_rate` decimal(10,2) DEFAULT NULL,
  `production_rate` decimal(10,2) DEFAULT NULL,
  `eggs_per_bird` decimal(10,2) DEFAULT NULL,
  `feed_per_bird` decimal(10,2) DEFAULT NULL,
  `feed_cost_per_egg` decimal(10,2) DEFAULT NULL,
  `cost_production_crate` decimal(10,2) DEFAULT NULL,
  `total_sales` decimal(10,2) DEFAULT NULL,
  `total_expenses` decimal(10,2) DEFAULT NULL,
  `gross_profit` decimal(10,2) DEFAULT NULL,
  `net_profit` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `performance_summary_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `manager_notes`
--

CREATE TABLE IF NOT EXISTS `manager_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `week_id` int(11) NOT NULL,
  `challenges` text DEFAULT NULL,
  `concerns` text DEFAULT NULL,
  `supplier_issues` text DEFAULT NULL,
  `market_changes` text DEFAULT NULL,
  `action_plan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  CONSTRAINT `manager_notes_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
