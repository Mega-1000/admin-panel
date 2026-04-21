-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: mega1000.pro-linuxpl.com    Database: mega1000_produkcja
-- ------------------------------------------------------
-- Server version	5.7.27-30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `gt_addresses_to_check`
--

DROP TABLE IF EXISTS `gt_addresses_to_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gt_addresses_to_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gt_invoices_id` int(10) unsigned DEFAULT NULL,
  `gt_payments_id` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firmname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flat_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gt_addresses_to_check_gt_invoices_id_foreign` (`gt_invoices_id`),
  CONSTRAINT `gt_addresses_to_check_gt_invoices_id_foreign` FOREIGN KEY (`gt_invoices_id`) REFERENCES `gt_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_invoices`
--

DROP TABLE IF EXISTS `gt_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gt_invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `order_labels_id` int(10) unsigned NOT NULL,
  `gt_invoice_status_id` int(10) unsigned NOT NULL,
  `gt_invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gt_stock_status_id` int(10) unsigned NOT NULL,
  `ftp_invoice_filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ftp_status_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gt_invoices_order_id_foreign` (`order_id`),
  KEY `gt_invoices_order_labels_id_foreign` (`order_labels_id`),
  KEY `gt_invoices_gt_invoice_status_id_foreign` (`gt_invoice_status_id`),
  KEY `gt_invoices_ftp_status_id_foreign` (`ftp_status_id`),
  KEY `gt_invoices_gt_stock_status_id_foreign` (`gt_stock_status_id`),
  CONSTRAINT `gt_invoices_ftp_status_id_foreign` FOREIGN KEY (`ftp_status_id`) REFERENCES `gt_status` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gt_invoices_gt_invoice_status_id_foreign` FOREIGN KEY (`gt_invoice_status_id`) REFERENCES `gt_status` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gt_invoices_gt_stock_status_id_foreign` FOREIGN KEY (`gt_stock_status_id`) REFERENCES `gt_status` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gt_invoices_order_labels_id_foreign` FOREIGN KEY (`order_labels_id`) REFERENCES `order_labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=654 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_payments`
--

DROP TABLE IF EXISTS `gt_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gt_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `order_payments_id` int(10) unsigned NOT NULL,
  `gt_payment_status_id` int(10) unsigned NOT NULL,
  `gt_payment_address_status_id` int(10) unsigned NOT NULL,
  `gt_payment_date` datetime DEFAULT NULL,
  `gt_invoice_for_order_value` decimal(9,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gt_payments_order_id_foreign` (`order_id`),
  KEY `gt_payments_order_payments_id_foreign` (`order_payments_id`),
  KEY `gt_payments_gt_payment_status_id_foreign` (`gt_payment_status_id`),
  CONSTRAINT `gt_payments_gt_payment_address_status_id_foreign` FOREIGN KEY (`gt_payment_status_id`) REFERENCES `gt_status` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gt_payments_gt_payment_status_id_foreign` FOREIGN KEY (`gt_payment_status_id`) REFERENCES `gt_status` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gt_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gt_payments_order_payments_id_foreign` FOREIGN KEY (`order_payments_id`) REFERENCES `order_payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_products_to_check`
--

DROP TABLE IF EXISTS `gt_products_to_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gt_products_to_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gt_invoices_id` int(10) unsigned NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gt_products_to_check_gt_invoices_id_foreign` (`gt_invoices_id`),
  CONSTRAINT `gt_products_to_check_gt_invoices_id_foreign` FOREIGN KEY (`gt_invoices_id`) REFERENCES `gt_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_status`
--

DROP TABLE IF EXISTS `gt_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gt_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gt_type_id` int(10) unsigned NOT NULL,
  `gt_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gt_status_gt_type_id_foreign` (`gt_type_id`),
  CONSTRAINT `gt_status_gt_type_id_foreign` FOREIGN KEY (`gt_type_id`) REFERENCES `gt_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_type`
--

DROP TABLE IF EXISTS `gt_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gt_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gt_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-11-19 14:08:45
