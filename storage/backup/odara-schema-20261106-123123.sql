-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: odara
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `charger_transactions`
--

DROP TABLE IF EXISTS `charger_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charger_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `pin_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `item_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_charger_date` (`transaction_date`),
  KEY `idx_charger_customer` (`customer_name`),
  KEY `idx_charger_room` (`room_id`),
  KEY `idx_charger_user` (`user_id`),
  KEY `idx_charger_pin` (`pin_number`),
  CONSTRAINT `fk_charger_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_charger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rooms_code` (`code`),
  KEY `idx_rooms_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopee_transactions`
--

DROP TABLE IF EXISTS `shopee_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shopee_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_date` date NOT NULL,
  `tracking_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` bigint unsigned DEFAULT NULL,
  `item_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `service_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shopee_date` (`transaction_date`),
  KEY `idx_shopee_customer` (`customer_name`),
  KEY `idx_shopee_room` (`room_id`),
  KEY `idx_shopee_user` (`user_id`),
  KEY `idx_shopee_tracking` (`tracking_number`),
  CONSTRAINT `fk_shopee_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_shopee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','petugas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'petugas',
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `v_charger_transactions`
--

DROP TABLE IF EXISTS `v_charger_transactions`;
/*!50001 DROP VIEW IF EXISTS `v_charger_transactions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_charger_transactions` AS SELECT 
 1 AS `id`,
 1 AS `transaction_date`,
 1 AS `pin_number`,
 1 AS `customer_name`,
 1 AS `room_id`,
 1 AS `room_code`,
 1 AS `room_name`,
 1 AS `item_type`,
 1 AS `qty`,
 1 AS `description`,
 1 AS `user_id`,
 1 AS `petugas_name`,
 1 AS `petugas_username`,
 1 AS `created_at`,
 1 AS `updated_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_shopee_transactions`
--

DROP TABLE IF EXISTS `v_shopee_transactions`;
/*!50001 DROP VIEW IF EXISTS `v_shopee_transactions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_shopee_transactions` AS SELECT 
 1 AS `id`,
 1 AS `transaction_date`,
 1 AS `tracking_number`,
 1 AS `customer_name`,
 1 AS `room_id`,
 1 AS `room_code`,
 1 AS `room_name`,
 1 AS `item_type`,
 1 AS `item_price`,
 1 AS `service_fee`,
 1 AS `total`,
 1 AS `notes`,
 1 AS `user_id`,
 1 AS `petugas_name`,
 1 AS `petugas_username`,
 1 AS `created_at`,
 1 AS `updated_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'odara'
--

--
-- Dumping routines for database 'odara'
--

--
-- Final view structure for view `v_charger_transactions`
--

/*!50001 DROP VIEW IF EXISTS `v_charger_transactions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_charger_transactions` AS select `c`.`id` AS `id`,`c`.`transaction_date` AS `transaction_date`,`c`.`pin_number` AS `pin_number`,`c`.`customer_name` AS `customer_name`,`c`.`room_id` AS `room_id`,`r`.`code` AS `room_code`,`r`.`name` AS `room_name`,`c`.`item_type` AS `item_type`,`c`.`qty` AS `qty`,`c`.`description` AS `description`,`c`.`user_id` AS `user_id`,`u`.`name` AS `petugas_name`,`u`.`username` AS `petugas_username`,`c`.`created_at` AS `created_at`,`c`.`updated_at` AS `updated_at` from ((`charger_transactions` `c` left join `rooms` `r` on((`r`.`id` = `c`.`room_id`))) left join `users` `u` on((`u`.`id` = `c`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_shopee_transactions`
--

/*!50001 DROP VIEW IF EXISTS `v_shopee_transactions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_shopee_transactions` AS select `s`.`id` AS `id`,`s`.`transaction_date` AS `transaction_date`,`s`.`tracking_number` AS `tracking_number`,`s`.`customer_name` AS `customer_name`,`s`.`room_id` AS `room_id`,`r`.`code` AS `room_code`,`r`.`name` AS `room_name`,`s`.`item_type` AS `item_type`,`s`.`item_price` AS `item_price`,`s`.`service_fee` AS `service_fee`,(`s`.`item_price` + `s`.`service_fee`) AS `total`,`s`.`notes` AS `notes`,`s`.`user_id` AS `user_id`,`u`.`name` AS `petugas_name`,`u`.`username` AS `petugas_username`,`s`.`created_at` AS `created_at`,`s`.`updated_at` AS `updated_at` from ((`shopee_transactions` `s` left join `rooms` `r` on((`r`.`id` = `s`.`room_id`))) left join `users` `u` on((`u`.`id` = `s`.`user_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-06 12:31:24
