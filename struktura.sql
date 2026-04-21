-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: admin1000
-- ------------------------------------------------------
-- Server version	8.0.32-0ubuntu0.20.04.2

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
-- Table structure for table `allegro_chat_threads`
--

DROP TABLE IF EXISTS `allegro_chat_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allegro_chat_threads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `allegro_thread_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allegro_msg_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `allegro_user_login` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_outgoing` tinyint(1) NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allegro_offer_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allegro_order_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_allegro_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `allegro_chat_threads_user_id_foreign` (`user_id`),
  CONSTRAINT `allegro_chat_threads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `allegro_chat_threads_chk_1` CHECK (json_valid(`attachments`))
) ENGINE=InnoDB AUTO_INCREMENT=30655 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allegro_disputes`
--

DROP TABLE IF EXISTS `allegro_disputes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allegro_disputes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dispute_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_login` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordered_date` datetime NOT NULL,
  `unseen_changes` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `is_pending` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `allegro_disputes_order_id_foreign` (`order_id`),
  KEY `allegro_disputes_user_id_foreign` (`user_id`),
  CONSTRAINT `allegro_disputes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `allegro_disputes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1038 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allegro_general_expenses`
--

DROP TABLE IF EXISTS `allegro_general_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allegro_general_expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date_of_commitment_creation` datetime DEFAULT NULL,
  `offer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offer_identification` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_details` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attached_value_parameter` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allegro_oauth`
--

DROP TABLE IF EXISTS `allegro_oauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allegro_oauth` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `access_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allegro_orders`
--

DROP TABLE IF EXISTS `allegro_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allegro_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_order_message_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `allegro_orders_new_order_message_sent_index` (`new_order_message_sent`),
  KEY `allegro_orders_order_id_index` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84528 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allegro_package`
--

DROP TABLE IF EXISTS `allegro_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allegro_package` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `allegro_operation_date` datetime NOT NULL COMMENT 'Data operacji Allegro pobierana z pliku CSV',
  `package_spedition_company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_delivery_company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `real_total_delivery_company_cost` decimal(9,2) NOT NULL,
  `real_delivery_company_cost` decimal(9,2) NOT NULL,
  `allegro_subscription_cost` decimal(9,2) NOT NULL,
  `ads_campaign_fee` decimal(9,2) NOT NULL COMMENT 'Opłata za kampanie ADS',
  `bill_correction` decimal(9,2) NOT NULL COMMENT 'Korekta rachunku',
  `preference_auction_fee` decimal(9,2) NOT NULL COMMENT 'Opłata za wyróżnienie aukcji Allegro',
  `booked_payment` decimal(9,2) NOT NULL COMMENT 'Wpłata zaksiegowana (za faktury ALLEGRO )',
  `month_summary` decimal(9,2) NOT NULL COMMENT 'Podsumowanie miesiąca ALLEGRO',
  `allegro_transaction_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `allegro_offer_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_of_commission_cost` decimal(8,2) NOT NULL,
  `package_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `allegro_package_package_id_foreign` (`package_id`),
  CONSTRAINT `allegro_package_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `order_packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archive_order_payments`
--

DROP TABLE IF EXISTS `archive_order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archive_order_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `amount` decimal(9,2) DEFAULT NULL COMMENT 'Kwota wpłacona',
  `notices` text COLLATE utf8mb4_unicode_ci COMMENT 'Dodatkowe uwagi do wpłaty',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `promise` enum('1','') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promise_date` datetime DEFAULT NULL COMMENT 'data do ktorej klient zadeklarowal wplate kwoty obiecane',
  `master_payment_id` int unsigned DEFAULT NULL COMMENT 'Jeśli puste - wpłata do konkretnego zamówienia, jeśli wypełnione - płatność jest częścią większej wpłaty klineta',
  `type` enum('CLIENT','WAREHOUSE','SPEDITION') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of payment - who got payment from order client.',
  `status` enum('ACCEPTED','PENDING','DECLINED') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Payment status - depending on warehouse action.',
  `token` text COLLATE utf8mb4_unicode_ci COMMENT 'Order payment confirmation token - it is used to send unique link to warehouse.',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `transaction_id` int unsigned DEFAULT NULL,
  `external_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID zewnetrznych płatności ( np allegro , bank itp  - nieobligatoryjne',
  `payer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Platnik - obligaatoryjne',
  `operation_date` datetime DEFAULT NULL COMMENT 'Data dokonania operacji wplaty / wyplaty - obligatoryjne dla wpłat zaksięgowanych',
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numer listu przewozowego firmy spedycyjnej',
  `operation_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID platnosci - nie obligatoryjna format ciag dowolnych zanków',
  `declared_sum` double(8,2) DEFAULT NULL COMMENT 'Kwota deklarowana - nie obligatoryjne fomat liczba do 2 miejsc po przecinku wartosc liczba dodatnia posiada statusy : deklarowana rozliczona deklarowana',
  `posting_date` datetime DEFAULT NULL COMMENT 'Data księgowania - obligatoryjne dla wpłat zaksięgowanych',
  `operation_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `order_package_id` int unsigned DEFAULT NULL COMMENT 'ID paczki do której przypisana jest wpłata - obcjonalne',
  `created_by` enum('bank','manually','allegro','shipping') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manually' COMMENT 'Kto utworzył wpłatę - obligatoryjne',
  PRIMARY KEY (`id`),
  KEY `order_payments_order_id_foreign` (`order_id`),
  KEY `order_payments_master_payment_id_foreign` (`master_payment_id`),
  KEY `order_payments_transaction_id_foreign` (`transaction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_codes`
--

DROP TABLE IF EXISTS `auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_codes` (
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `auth_codes_token_unique` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bonus_and_penalties`
--

DROP TABLE IF EXISTS `bonus_and_penalties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bonus_and_penalties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `cause` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `chat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `points` int unsigned NOT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `bonus_and_penalties_order_id_foreign` (`order_id`),
  KEY `bonus_and_penalties_user_id_foreign` (`user_id`),
  CONSTRAINT `bonus_and_penalties_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bonus_and_penalties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bonus_and_penalties_chk_1` CHECK (json_valid(`chat`))
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `buying_invoices`
--

DROP TABLE IF EXISTS `buying_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buying_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `value` double(8,2) NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `analized_by_claute` tinyint(1) NOT NULL DEFAULT '0',
  `validated_by_nexo` tinyint(1) NOT NULL DEFAULT '0',
  `file_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4451 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rewrite` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_visible` tinyint(1) NOT NULL,
  `priority` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `save_name` tinyint(1) NOT NULL DEFAULT '1',
  `save_description` tinyint(1) NOT NULL DEFAULT '1',
  `save_image` tinyint(1) NOT NULL DEFAULT '1',
  `artificially_created` tinyint(1) NOT NULL DEFAULT '0',
  `youtube` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_auction_firms`
--

DROP TABLE IF EXISTS `chat_auction_firms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_auction_firms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_auction_id` int unsigned NOT NULL,
  `firm_id` int unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_of_employee` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_auction_firms_token_unique` (`token`),
  KEY `chat_auction_firms_firm_id_foreign` (`firm_id`),
  CONSTRAINT `chat_auction_firms_firm_id_foreign` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9525 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_auction_offers`
--

DROP TABLE IF EXISTS `chat_auction_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_auction_offers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commercial_price_net` double(8,2) NOT NULL,
  `basic_price_net` double(8,2) NOT NULL,
  `calculated_price_net` double(8,2) NOT NULL,
  `aggregate_price_net` double(8,2) NOT NULL,
  `commercial_price_gross` double(8,2) NOT NULL,
  `basic_price_gross` double(8,2) NOT NULL,
  `calculated_price_gross` double(8,2) NOT NULL,
  `aggregate_price_gross` double(8,2) NOT NULL,
  `order_item_id` int unsigned NOT NULL,
  `chat_auction_id` int unsigned NOT NULL,
  `firm_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `send_notification` tinyint(1) NOT NULL DEFAULT '1',
  `product_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64482 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_auctions`
--

DROP TABLE IF EXISTS `chat_auctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_auctions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `end_of_auction` datetime NOT NULL,
  `date_of_delivery` datetime NOT NULL,
  `price` int NOT NULL,
  `quality` int NOT NULL,
  `chat_id` bigint unsigned NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_delivery_from` date NOT NULL,
  `date_of_delivery_to` date NOT NULL,
  `end_info_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=748 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_statuses`
--

DROP TABLE IF EXISTS `chat_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_user`
--

DROP TABLE IF EXISTS `chat_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `last_read_time` datetime DEFAULT NULL,
  `last_notification_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `chat_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `employee_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `assigned_messages_ids` mediumtext COLLATE utf8mb4_unicode_ci,
  `is_online` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `chat_user_chat_id_foreign` (`chat_id`),
  KEY `chat_user_user_id_foreign` (`user_id`),
  KEY `chat_user_employee_id_foreign` (`employee_id`),
  KEY `chat_user_customer_id_foreign` (`customer_id`),
  CONSTRAINT `chat_user_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_user_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `chat_user_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `chat_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86009 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `need_intervention` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int unsigned DEFAULT NULL COMMENT 'Główny przypisany konsultant',
  `complaint_form` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `questions_tree` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `information_about_chat_inactiveness_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chats_product_id_foreign` (`product_id`),
  KEY `chats_order_id_foreign` (`order_id`),
  KEY `chats_user_id_foreign` (`user_id`),
  CONSTRAINT `chats_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chats_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `chats_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14633 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chimney_attribute_options`
--

DROP TABLE IF EXISTS `chimney_attribute_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chimney_attribute_options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chimney_attribute_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chimney_attribute_options_chimney_attribute_id_foreign` (`chimney_attribute_id`),
  CONSTRAINT `chimney_attribute_options_chimney_attribute_id_foreign` FOREIGN KEY (`chimney_attribute_id`) REFERENCES `chimney_attributes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=881 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chimney_attributes`
--

DROP TABLE IF EXISTS `chimney_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chimney_attributes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_number` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chimney_attributes_category_id_foreign` (`category_id`),
  CONSTRAINT `chimney_attributes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chimney_products`
--

DROP TABLE IF EXISTS `chimney_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chimney_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `product_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `formula` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_number` int NOT NULL DEFAULT '0',
  `optional` int NOT NULL DEFAULT '0',
  `replacement_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `replacement_img` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chimney_products_category_id_foreign` (`category_id`),
  CONSTRAINT `chimney_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=387 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chimney_replacements`
--

DROP TABLE IF EXISTS `chimney_replacements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chimney_replacements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chimney_product_id` int unsigned NOT NULL,
  `product` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chimney_replacements_chimney_product_id_foreign` (`chimney_product_id`),
  CONSTRAINT `chimney_replacements_chimney_product_id_foreign` FOREIGN KEY (`chimney_product_id`) REFERENCES `chimney_products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `column_visibilities`
--

DROP TABLE IF EXISTS `column_visibilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `column_visibilities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_id` int unsigned DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `hidden` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `show` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `column_visibilities_module_id_foreign` (`module_id`),
  KEY `column_visibilities_role_id_foreign` (`role_id`),
  CONSTRAINT `column_visibilities_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `column_visibilities_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `column_visibilities_chk_1` CHECK (json_valid(`hidden`)),
  CONSTRAINT `column_visibilities_chk_2` CHECK (json_valid(`show`))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `confirm_packages`
--

DROP TABLE IF EXISTS `confirm_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `confirm_packages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `confirm_packages_package_id_foreign` (`package_id`),
  CONSTRAINT `confirm_packages_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `order_packages` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13488 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_approaches`
--

DROP TABLE IF EXISTS `contact_approaches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_approaches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referred_by_user_id` int NOT NULL,
  `done` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `prospect_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `container_types`
--

DROP TABLE IF EXISTS `container_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `container_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_provider` char(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `additional_informations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `container_types_chk_1` CHECK (json_valid(`additional_informations`))
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_types`
--

DROP TABLE IF EXISTS `content_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso2` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courier`
--

DROP TABLE IF EXISTS `courier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courier` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `courier_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Courier name',
  `courier_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Courier key',
  `item_number` int NOT NULL DEFAULT '0' COMMENT 'order',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_page_categories`
--

DROP TABLE IF EXISTS `custom_page_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_page_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL,
  `parent_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_page_categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `custom_page_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `custom_page_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_page_content`
--

DROP TABLE IF EXISTS `custom_page_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_page_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int unsigned NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_page_content_category_id_foreign` (`category_id`),
  CONSTRAINT `custom_page_content_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `custom_page_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `type` enum('STANDARD_ADDRESS','INVOICE_ADDRESS','DELIVERY_ADDRESS') COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country_id` mediumint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_customer_id_foreign` (`customer_id`),
  KEY `customer_addresses_country_id_foreign` (`country_id`),
  CONSTRAINT `customer_addresses_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `customer_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_from_old_db` int unsigned DEFAULT NULL,
  `login` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nick_allegro` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_token_expires_at` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_of_parrent_referral` int DEFAULT NULL,
  `balance_of_addictional_discount_account` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60488 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_rows`
--

DROP TABLE IF EXISTS `data_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_rows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `data_type_id` int unsigned NOT NULL,
  `field` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `browse` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `add` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `details` text COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `data_rows_data_type_id_foreign` (`data_type_id`),
  CONSTRAINT `data_rows_data_type_id_foreign` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `data_types`
--

DROP TABLE IF EXISTS `data_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_singular` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_plural` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generate_permissions` tinyint(1) NOT NULL DEFAULT '0',
  `server_side` tinyint NOT NULL DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_types_name_unique` (`name`),
  UNIQUE KEY `data_types_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deliverer_import`
--

DROP TABLE IF EXISTS `deliverer_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deliverer_import` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `deliverer_id` int unsigned NOT NULL,
  `originalFileName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importFileName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliverer_import_deliverer_id_foreign` (`deliverer_id`),
  CONSTRAINT `deliverer_import_deliverer_id_foreign` FOREIGN KEY (`deliverer_id`) REFERENCES `deliverers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deliverer_import_rules`
--

DROP TABLE IF EXISTS `deliverer_import_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deliverer_import_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `deliverer_id` int unsigned NOT NULL,
  `action` enum('searchCompare','searchRegex','set','get','getAndReplace','getWithCondition') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_column_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `import_column_number` tinyint unsigned DEFAULT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_column_number` tinyint unsigned DEFAULT NULL,
  `condition_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` tinyint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliverer_import_rules_deliverer_id_foreign` (`deliverer_id`),
  CONSTRAINT `deliverer_import_rules_deliverer_id_foreign` FOREIGN KEY (`deliverer_id`) REFERENCES `deliverers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deliverers`
--

DROP TABLE IF EXISTS `deliverers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deliverers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_amount` int NOT NULL,
  `old_amount` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36581 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_sending`
--

DROP TABLE IF EXISTS `email_sending`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_sending` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `email_setting_id` int unsigned NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `scheduled_date` datetime NOT NULL COMMENT 'Data planownego wysłania emaila',
  `send_date` datetime DEFAULT NULL COMMENT 'Data wysłania emaila',
  `message_send` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_sending_order_id_foreign` (`order_id`),
  KEY `email_sending_email_setting_id_foreign` (`email_setting_id`),
  CONSTRAINT `email_sending_email_setting_id_foreign` FOREIGN KEY (`email_setting_id`) REFERENCES `email_settings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_sending_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70082 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_settings`
--

DROP TABLE IF EXISTS `email_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `time` int NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_allegro` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emails_messages`
--

DROP TABLE IF EXISTS `emails_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `emails_messages_order_id_foreign` (`order_id`),
  CONSTRAINT `emails_messages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_represents`
--

DROP TABLE IF EXISTS `employee_represents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_represents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` int NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_roles`
--

DROP TABLE IF EXISTS `employee_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_contact_displayed_in_fronted` decimal(1,0) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_warehouse`
--

DROP TABLE IF EXISTS `employee_warehouse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_warehouse` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int unsigned NOT NULL,
  `warehouse_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_warehouse_employee_id_foreign` (`employee_id`),
  KEY `employee_warehouse_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `employee_warehouse_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_warehouse_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2538 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employeerole_employee`
--

DROP TABLE IF EXISTS `employeerole_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employeerole_employee` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int unsigned NOT NULL,
  `employee_role_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employeerole_employee_employee_id_foreign` (`employee_id`),
  KEY `employeerole_employee_employee_role_id_foreign` (`employee_role_id`),
  CONSTRAINT `employeerole_employee_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employeerole_employee_employee_role_id_foreign` FOREIGN KEY (`employee_role_id`) REFERENCES `employee_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8093 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firm_id` int unsigned DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `additional_comments` text COLLATE utf8mb4_unicode_ci,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `latitude` decimal(12,10) DEFAULT NULL,
  `longitude` decimal(12,10) DEFAULT NULL,
  `person_number` int DEFAULT NULL,
  `radius` int NOT NULL,
  `firstname_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `lastname_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `phone_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `firm_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `comments_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `postal_code_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `email_visibility` decimal(1,0) NOT NULL DEFAULT '1',
  `faq` text COLLATE utf8mb4_unicode_ci,
  `zip_code_2` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code_3` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code_4` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code_5` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_performing_avization` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `employees_firm_id_foreign` (`firm_id`),
  CONSTRAINT `employees_firm_id_foreign` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_category_indexes`
--

DROP TABLE IF EXISTS `faq_category_indexes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faq_category_indexes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `faq_category_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `faq_category_index` int unsigned NOT NULL,
  `faq_id` int unsigned DEFAULT NULL,
  `faq_category_type` enum('question','category') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'category',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1311 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `faqs_chk_1` CHECK (json_valid(`questions`))
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fast_responses`
--

DROP TABLE IF EXISTS `fast_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fast_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firm_addresses`
--

DROP TABLE IF EXISTS `firm_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `firm_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firm_id` int unsigned NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(12,10) DEFAULT NULL,
  `longitude` decimal(12,10) DEFAULT NULL,
  `flat_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `firm_addresses_firm_id_foreign` (`firm_id`),
  CONSTRAINT `firm_addresses_firm_id_foreign` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1431468 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firm_represents`
--

DROP TABLE IF EXISTS `firm_represents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `firm_represents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contact_info` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firm_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email_of_employee` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firm_sources`
--

DROP TABLE IF EXISTS `firm_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `firm_sources` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firm_id` int unsigned NOT NULL,
  `order_source_id` int unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `firm_sources_firm_id_order_source_id_unique` (`firm_id`,`order_source_id`),
  KEY `firm_sources_order_source_id_foreign` (`order_source_id`),
  CONSTRAINT `firm_sources_firm_id_foreign` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `firm_sources_order_source_id_foreign` FOREIGN KEY (`order_source_id`) REFERENCES `order_sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firms`
--

DROP TABLE IF EXISTS `firms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `firms` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_warehouse` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secondary_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complaint_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `nip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notices` text COLLATE utf8mb4_unicode_ci,
  `secondary_notices` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_from_old_db` int DEFAULT NULL,
  `firm_type` enum('PRODUCTION','DELIVERY','OTHER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PRODUCTION',
  `send_request_to_update_data` tinyint(1) NOT NULL DEFAULT '0',
  `access_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `practices_representatives_policy` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1431469 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `form_elements`
--

DROP TABLE IF EXISTS `form_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_elements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_id` int NOT NULL,
  `order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `color` text COLLATE utf8mb4_unicode_ci,
  `size` text COLLATE utf8mb4_unicode_ci,
  `new_tab` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_addresses_to_check`
--

DROP TABLE IF EXISTS `gt_addresses_to_check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gt_addresses_to_check` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gt_invoices_id` int unsigned DEFAULT NULL,
  `gt_payments_id` int unsigned DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gt_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `order_labels_id` int unsigned NOT NULL,
  `gt_invoice_status_id` int unsigned NOT NULL,
  `gt_invoice_dok_id` int DEFAULT NULL,
  `gt_invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gt_stock_status_id` int unsigned NOT NULL,
  `ftp_invoice_filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ftp_status_id` int unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=15603 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_payments`
--

DROP TABLE IF EXISTS `gt_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gt_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `order_payments_id` int unsigned NOT NULL,
  `gt_payment_status_id` int unsigned NOT NULL,
  `gt_payment_address_status_id` int unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gt_products_to_check` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gt_invoices_id` int unsigned NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `gt_products_to_check_gt_invoices_id_foreign` (`gt_invoices_id`),
  CONSTRAINT `gt_products_to_check_gt_invoices_id_foreign` FOREIGN KEY (`gt_invoices_id`) REFERENCES `gt_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gt_status`
--

DROP TABLE IF EXISTS `gt_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gt_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gt_type_id` int unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gt_type` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gt_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `import`
--

DROP TABLE IF EXISTS `import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `import` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_import` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `processing` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice_requests`
--

DROP TABLE IF EXISTS `invoice_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `status` enum('SENT','MISSING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_requests_order_id_foreign` (`order_id`),
  CONSTRAINT `invoice_requests_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=882701 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jpg_data`
--

DROP TABLE IF EXISTS `jpg_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jpg_data` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `row` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `col` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subcol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=755 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_groups`
--

DROP TABLE IF EXISTS `label_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_labels_to_add_after_addition`
--

DROP TABLE IF EXISTS `label_labels_to_add_after_addition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_labels_to_add_after_addition` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `main_label_id` int unsigned NOT NULL,
  `label_to_add_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `label_labels_after_addition_main_label_id_foreign` (`main_label_id`),
  KEY `label_labels_after_addition_label_to_add_id_foreign` (`label_to_add_id`),
  CONSTRAINT `label_labels_after_addition_label_to_add_id_foreign` FOREIGN KEY (`label_to_add_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_labels_after_addition_main_label_id_foreign` FOREIGN KEY (`main_label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_labels_to_add_after_removal`
--

DROP TABLE IF EXISTS `label_labels_to_add_after_removal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_labels_to_add_after_removal` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `main_label_id` int unsigned NOT NULL,
  `label_to_add_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `label_labels_after_removal_main_label_id_foreign` (`main_label_id`),
  KEY `label_labels_after_removal_label_to_add_id_foreign` (`label_to_add_id`),
  CONSTRAINT `label_labels_after_removal_label_to_add_id_foreign` FOREIGN KEY (`label_to_add_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_labels_after_removal_main_label_id_foreign` FOREIGN KEY (`main_label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_labels_to_add_after_timed_label`
--

DROP TABLE IF EXISTS `label_labels_to_add_after_timed_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_labels_to_add_after_timed_label` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `main_label_id` int unsigned NOT NULL,
  `label_to_add_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `label_labels_to_add_after_timed_label_main_label_id_foreign` (`main_label_id`),
  KEY `label_labels_to_add_after_timed_label_label_to_add_id_foreign` (`label_to_add_id`),
  CONSTRAINT `label_labels_to_add_after_timed_label_label_to_add_id_foreign` FOREIGN KEY (`label_to_add_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_labels_to_add_after_timed_label_main_label_id_foreign` FOREIGN KEY (`main_label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_labels_to_remove_after_addition`
--

DROP TABLE IF EXISTS `label_labels_to_remove_after_addition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_labels_to_remove_after_addition` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `main_label_id` int unsigned NOT NULL,
  `label_to_add_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `label_labels_to_remove_after_addition_main_label_id_foreign` (`main_label_id`),
  KEY `label_labels_to_remove_after_addition_label_to_add_id_foreign` (`label_to_add_id`),
  CONSTRAINT `label_labels_to_remove_after_addition_label_to_add_id_foreign` FOREIGN KEY (`label_to_add_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_labels_to_remove_after_addition_main_label_id_foreign` FOREIGN KEY (`main_label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=214 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_labels_to_remove_after_removal`
--

DROP TABLE IF EXISTS `label_labels_to_remove_after_removal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_labels_to_remove_after_removal` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `main_label_id` int unsigned NOT NULL,
  `label_to_add_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `label_labels_to_remove_after_removal_main_label_id_foreign` (`main_label_id`),
  KEY `label_labels_to_remove_after_removal_label_to_add_id_foreign` (`label_to_add_id`),
  CONSTRAINT `label_labels_to_remove_after_removal_label_to_add_id_foreign` FOREIGN KEY (`label_to_add_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `label_labels_to_remove_after_removal_main_label_id_foreign` FOREIGN KEY (`main_label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `label_logs`
--

DROP TABLE IF EXISTS `label_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `label_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `type` enum('ATTACH','DETACH') COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_at` datetime DEFAULT NULL,
  `has_consequence` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `label_logs_label_id_foreign` (`label_id`),
  KEY `label_logs_order_id_foreign` (`order_id`),
  KEY `label_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `label_logs_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`),
  CONSTRAINT `label_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `label_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2956199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels`
--

DROP TABLE IF EXISTS `labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `label_group_id` int unsigned DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '1000000',
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `font_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_label_selection_to_add_after_removal` tinyint(1) DEFAULT '0',
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci COMMENT 'Wiadomość wysyłana przy nadaniu etykiety zamówieniu',
  `timed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `labels_label_group_id_foreign` (`label_group_id`),
  CONSTRAINT `labels_label_group_id_foreign` FOREIGN KEY (`label_group_id`) REFERENCES `label_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=310 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels_timed_after_addition`
--

DROP TABLE IF EXISTS `labels_timed_after_addition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labels_timed_after_addition` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `main_label_id` int unsigned NOT NULL,
  `label_to_handle_id` int unsigned NOT NULL,
  `to_add_type_a` text COLLATE utf8mb4_unicode_ci,
  `to_remove_type_a` text COLLATE utf8mb4_unicode_ci,
  `to_add_type_b` text COLLATE utf8mb4_unicode_ci,
  `to_remove_type_b` text COLLATE utf8mb4_unicode_ci,
  `to_add_type_c` tinyint(1) DEFAULT NULL,
  `to_remove_type_c` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `labels_timed_after_addition_main_label_id_foreign` (`main_label_id`),
  KEY `labels_timed_after_addition_label_to_handle_id_foreign` (`label_to_handle_id`),
  CONSTRAINT `labels_timed_after_addition_label_to_handle_id_foreign` FOREIGN KEY (`label_to_handle_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `labels_timed_after_addition_main_label_id_foreign` FOREIGN KEY (`main_label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `low_order_quantity_alert_messages`
--

DROP TABLE IF EXISTS `low_order_quantity_alert_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `low_order_quantity_alert_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attachment_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `delay_time` double DEFAULT NULL,
  `low_order_quantity_alert_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `label_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=548 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `low_order_quantity_alerts`
--

DROP TABLE IF EXISTS `low_order_quantity_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `low_order_quantity_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_names` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_quantity` int NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `php_code` longtext COLLATE utf8mb4_unicode_ci,
  `column_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `space` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_reports`
--

DROP TABLE IF EXISTS `mail_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154778 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `icon_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `order` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `chat_user_id` int unsigned NOT NULL,
  `chat_id` int unsigned NOT NULL,
  `area` tinyint unsigned NOT NULL DEFAULT '0',
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `attachment_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `users_visibility` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_sms` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `messages_chat_user_id_foreign` (`chat_user_id`),
  KEY `messages_chat_id_foreign` (`chat_id`),
  CONSTRAINT `messages_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`),
  CONSTRAINT `messages_chat_user_id_foreign` FOREIGN KEY (`chat_user_id`) REFERENCES `chat_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=507 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newletter_guides`
--

DROP TABLE IF EXISTS `newletter_guides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newletter_guides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_guides`
--

DROP TABLE IF EXISTS `newsletter_guides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_guides` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_messages`
--

DROP TABLE IF EXISTS `newsletter_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_packets`
--

DROP TABLE IF EXISTS `newsletter_packets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_packets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `newsletter_entries_ids` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletters`
--

DROP TABLE IF EXISTS `newsletters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `auction_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newspaper_campaigns`
--

DROP TABLE IF EXISTS `newspaper_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newspaper_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newspaper_tokens`
--

DROP TABLE IF EXISTS `newspaper_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newspaper_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `categories` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `client_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint NOT NULL,
  `client_id` int unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_personal_access_clients_client_id_index` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_addresses`
--

DROP TABLE IF EXISTS `order_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `type` enum('INVOICE_ADDRESS','DELIVERY_ADDRESS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firmname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flat_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country_id` mediumint unsigned DEFAULT NULL,
  `isAbroad` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_addresses_order_id_foreign` (`order_id`),
  KEY `order_addresses_country_id_foreign` (`country_id`),
  CONSTRAINT `order_addresses_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `order_addresses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121671 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_allegro_commissions`
--

DROP TABLE IF EXISTS `order_allegro_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_allegro_commissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_allegro_commissions_order_id_foreign` (`order_id`),
  CONSTRAINT `order_allegro_commissions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=93402 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_datatable_columns`
--

DROP TABLE IF EXISTS `order_datatable_columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_datatable_columns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order` int NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filter` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resetFilters` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3378 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_dates`
--

DROP TABLE IF EXISTS `order_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_dates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `customer_shipment_date_from` datetime DEFAULT NULL,
  `customer_shipment_date_to` datetime DEFAULT NULL,
  `customer_delivery_date_from` datetime DEFAULT NULL,
  `customer_delivery_date_to` datetime DEFAULT NULL,
  `consultant_shipment_date_from` datetime DEFAULT NULL,
  `consultant_shipment_date_to` datetime DEFAULT NULL,
  `consultant_delivery_date_from` datetime DEFAULT NULL,
  `consultant_delivery_date_to` datetime DEFAULT NULL,
  `warehouse_shipment_date_from` datetime DEFAULT NULL,
  `warehouse_shipment_date_to` datetime DEFAULT NULL,
  `warehouse_delivery_date_from` datetime DEFAULT NULL,
  `warehouse_delivery_date_to` datetime DEFAULT NULL,
  `customer_acceptance` tinyint(1) NOT NULL DEFAULT '0',
  `consultant_acceptance` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse_acceptance` tinyint(1) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_dates_order_id_foreign` (`order_id`),
  CONSTRAINT `order_dates_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50806 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_files`
--

DROP TABLE IF EXISTS `order_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_files_order_id_foreign` (`order_id`),
  CONSTRAINT `order_files_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1404 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_invoice_documents`
--

DROP TABLE IF EXISTS `order_invoice_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoice_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `preliminary_buying_document_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buying_document_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gross_value` decimal(10,2) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_invoice_values`
--

DROP TABLE IF EXISTS `order_invoice_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoice_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=186646 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_invoices`
--

DROP TABLE IF EXISTS `order_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'buy',
  `invoice_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_visible_for_client` tinyint(1) NOT NULL DEFAULT '1',
  `ai_analysis` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2305 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `net_purchase_price_commercial_unit` decimal(9,2) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki handlowej',
  `net_purchase_price_basic_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki podstawowej',
  `net_purchase_price_calculated_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki obliczeniowej',
  `net_purchase_price_aggregate_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki zbiorczej',
  `net_purchase_price_the_largest_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki największej',
  `net_selling_price_commercial_unit` decimal(9,2) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki handlowej',
  `net_selling_price_basic_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki podstawowej',
  `net_selling_price_calculated_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki obliczeniowej',
  `net_selling_price_aggregate_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki zbiorczej',
  `net_selling_price_the_largest_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki największej',
  `net_purchase_price_commercial_unit_after_discounts` decimal(9,2) DEFAULT NULL,
  `net_purchase_price_basic_unit_after_discounts` decimal(9,2) DEFAULT NULL,
  `net_purchase_price_calculated_unit_after_discounts` decimal(9,2) DEFAULT NULL,
  `net_purchase_price_aggregate_unit_after_discounts` decimal(9,2) DEFAULT NULL,
  `net_purchase_price_the_largest_unit_after_discounts` decimal(9,2) DEFAULT NULL,
  `gross_selling_price_commercial_unit` decimal(8,2) NOT NULL,
  `gross_selling_price_basic_unit` decimal(8,2) NOT NULL,
  `gross_selling_price_calculated_unit` decimal(8,2) NOT NULL,
  `gross_selling_price_aggregate_unit` decimal(8,2) NOT NULL,
  `gross_selling_price_the_largest_unit` decimal(8,2) NOT NULL,
  `product_stock_packet_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_product_stock_packet_id_foreign` (`product_stock_packet_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_stock_packet_id_foreign` FOREIGN KEY (`product_stock_packet_id`) REFERENCES `product_stock_packets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=269599 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_label_scheduler_awaits`
--

DROP TABLE IF EXISTS `order_label_scheduler_awaits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_label_scheduler_awaits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  `labels_timed_after_addition_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_label_scheduler_awaits_user_id_foreign` (`user_id`),
  KEY `order_label_scheduler_awaits_order_id_foreign` (`order_id`),
  KEY `labels_timed_after_addition_foreign_for_awaits` (`labels_timed_after_addition_id`),
  CONSTRAINT `labels_timed_after_addition_foreign_for_awaits` FOREIGN KEY (`labels_timed_after_addition_id`) REFERENCES `labels_timed_after_addition` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_label_scheduler_awaits_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_label_scheduler_awaits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_label_schedulers`
--

DROP TABLE IF EXISTS `order_label_schedulers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_label_schedulers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `label_id` int unsigned DEFAULT NULL,
  `label_id_to_handle` int unsigned DEFAULT NULL,
  `type` enum('A','B','C') COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_time` datetime NOT NULL,
  `action` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `triggered_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_label_schedulers_order_id_foreign` (`order_id`),
  KEY `order_label_schedulers_label_id_foreign` (`label_id`),
  KEY `order_label_schedulers_label_id_to_handle_foreign` (`label_id_to_handle`),
  CONSTRAINT `order_label_schedulers_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_label_schedulers_label_id_to_handle_foreign` FOREIGN KEY (`label_id_to_handle`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_label_schedulers_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83245 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_labels`
--

DROP TABLE IF EXISTS `order_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_labels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `label_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `added_type` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `order_labels_order_id_foreign` (`order_id`),
  KEY `order_labels_label_id_foreign` (`label_id`),
  CONSTRAINT `order_labels_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_labels_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6773052 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_message_attachments`
--

DROP TABLE IF EXISTS `order_message_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_message_attachments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_message_id` int unsigned NOT NULL,
  `file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_message_attachments_order_message_id_foreign` (`order_message_id`),
  CONSTRAINT `order_message_attachments_order_message_id_foreign` FOREIGN KEY (`order_message_id`) REFERENCES `order_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_messages`
--

DROP TABLE IF EXISTS `order_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Temat wiadomości',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Treść wiadomości',
  `additional_description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('GENERAL','SHIPPING','WAREHOUSE','COMPLAINT') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ogólne, spedycja, magazyn, reklamacja',
  `source` enum('MAIL','FORM') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FORM',
  `status` enum('OPEN','CLOSED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL COMMENT 'ID pracownika',
  `timestamp` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `order_messages_order_id_foreign` (`order_id`),
  KEY `order_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `order_messages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=810 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_monitor_notes`
--

DROP TABLE IF EXISTS `order_monitor_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_monitor_notes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_monitor_notes_order_id_foreign` (`order_id`),
  CONSTRAINT `order_monitor_notes_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_offers`
--

DROP TABLE IF EXISTS `order_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_offers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_offers_order_id_foreign` (`order_id`),
  CONSTRAINT `order_offers_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4597 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_order_invoices`
--

DROP TABLE IF EXISTS `order_order_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_order_invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_order_invoices_invoice_id_foreign` (`invoice_id`),
  KEY `order_order_invoices_order_id_foreign` (`order_id`),
  CONSTRAINT `order_order_invoices_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `order_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_order_invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2305 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_other_package_product`
--

DROP TABLE IF EXISTS `order_other_package_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_other_package_product` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `order_other_package_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=150767 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_other_packages`
--

DROP TABLE IF EXISTS `order_other_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_other_packages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `type` enum('not_calculable','from_factory') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44785 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_package_product`
--

DROP TABLE IF EXISTS `order_package_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_package_product` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_package_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142866 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_packages`
--

DROP TABLE IF EXISTS `order_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_packages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `number` int NOT NULL DEFAULT '1' COMMENT 'Numer porządkowy tworzonej paczki względem zamówienia',
  `size_a` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_b` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_c` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipment_date` date NOT NULL COMMENT 'Data wysyłki',
  `delivery_date` date DEFAULT NULL COMMENT 'Data dostarczenia przesyłki',
  `delivery_courier_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nazwa kuriera',
  `service_courier_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Spedycja obsługująca',
  `weight` double(8,2) NOT NULL,
  `quantity` decimal(8,2) DEFAULT NULL,
  `container_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cash_on_delivery` decimal(9,2) DEFAULT NULL COMMENT 'Kwota pobrania',
  `notices` text COLLATE utf8mb4_unicode_ci COMMENT 'Uwagi dla kuriera',
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sending_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numer nadania',
  `letter_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numer listu przewozowego',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cost_for_client` decimal(8,2) DEFAULT NULL COMMENT 'Koszt wysylki brutto dla klienta',
  `cost_for_company` decimal(8,2) DEFAULT NULL COMMENT 'Koszt wysylki brutto dla firmy',
  `inpost_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chosen_data_template` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Template na podstawie, którego wybrano autouzupełnienie danych',
  `content` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zawartość przesyłki',
  `send_protocol` tinyint(1) NOT NULL DEFAULT '0',
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packing_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number_sent_to_allegro` tinyint(1) NOT NULL DEFAULT '0',
  `cod_cost_for_us` decimal(8,2) NOT NULL,
  `delivery_cost_balance` double(8,2) NOT NULL DEFAULT '0.00',
  `services` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `protection_method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `shipment_group_id` int unsigned DEFAULT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sticker_has_been_printed` tinyint(1) NOT NULL DEFAULT '0',
  `real_cost_for_company_sum` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_packages_order_id_foreign` (`order_id`),
  KEY `order_packages_shipment_group_id_foreign` (`shipment_group_id`),
  CONSTRAINT `order_packages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_packages_shipment_group_id_foreign` FOREIGN KEY (`shipment_group_id`) REFERENCES `shipment_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=122121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_packages_real_cost_for_company`
--

DROP TABLE IF EXISTS `order_packages_real_cost_for_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_packages_real_cost_for_company` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_package_id` int unsigned NOT NULL,
  `deliverer_id` int unsigned DEFAULT NULL,
  `cost` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` enum('SOP','SOD') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_num` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_packages_real_cost_for_company_order_package_id_foreign` (`order_package_id`),
  KEY `deliverer_id_foreign` (`deliverer_id`),
  CONSTRAINT `deliverer_id_foreign` FOREIGN KEY (`deliverer_id`) REFERENCES `deliverers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_packages_real_cost_for_company_order_package_id_foreign` FOREIGN KEY (`order_package_id`) REFERENCES `order_packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=125325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_payment_confirmations`
--

DROP TABLE IF EXISTS `order_payment_confirmations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payment_confirmations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `file_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `order_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_payments`
--

DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `amount` decimal(9,2) DEFAULT NULL COMMENT 'Kwota wpłacona',
  `deletable` tinyint(1) NOT NULL DEFAULT '1',
  `notices` text COLLATE utf8mb4_unicode_ci COMMENT 'Dodatkowe uwagi do wpłaty',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `promise` enum('1','') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promise_date` datetime DEFAULT NULL COMMENT 'data do ktorej klient zadeklarowal wplate kwoty obiecane',
  `master_payment_id` int unsigned DEFAULT NULL COMMENT 'Jeśli puste - wpłata do konkretnego zamówienia, jeśli wypełnione - płatność jest częścią większej wpłaty klineta',
  `type` enum('CLIENT','WAREHOUSE','SPEDITION') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of payment - who got payment from order client.',
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Payment status - depending on warehouse action.',
  `token` text COLLATE utf8mb4_unicode_ci COMMENT 'Order payment confirmation token - it is used to send unique link to warehouse.',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `transaction_id` int unsigned DEFAULT NULL,
  `external_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID zewnetrznych płatności ( np allegro , bank itp  - nieobligatoryjne',
  `payer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Platnik - obligaatoryjne',
  `operation_date` datetime DEFAULT NULL COMMENT 'Data dokonania operacji wplaty / wyplaty - obligatoryjne dla wpłat zaksięgowanych',
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numer listu przewozowego firmy spedycyjnej',
  `operation_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID platnosci - nie obligatoryjna format ciag dowolnych zanków',
  `declared_sum` double(8,2) DEFAULT NULL COMMENT 'Kwota deklarowana - nie obligatoryjne fomat liczba do 2 miejsc po przecinku wartosc liczba dodatnia posiada statusy : deklarowana rozliczona deklarowana',
  `posting_date` datetime DEFAULT NULL COMMENT 'Data księgowania - obligatoryjne dla wpłat zaksięgowanych',
  `operation_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `order_package_id` int unsigned DEFAULT NULL COMMENT 'ID paczki do której przypisana jest wpłata - obcjonalne',
  `created_by` enum('bank','manually','allegro','shipping') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manually' COMMENT 'Kto utworzył wpłatę - obligatoryjne',
  `rebooked_order_payment_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_payments_order_id_foreign` (`order_id`),
  KEY `order_payments_master_payment_id_foreign` (`master_payment_id`),
  KEY `order_payments_transaction_id_foreign` (`transaction_id`),
  CONSTRAINT `order_payments_master_payment_id_foreign` FOREIGN KEY (`master_payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_payments_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=624182 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_payments_logs`
--

DROP TABLE IF EXISTS `order_payments_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booked_date` datetime DEFAULT NULL,
  `payment_type` enum('CLIENT_PAYMENT','ORDER_PAYMENT','RETURN_PAYMENT','REMOVE_PAYMENT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_payment_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  `employee_id` int unsigned NOT NULL,
  `payment_service_operator` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `payment_amount` decimal(8,2) DEFAULT NULL,
  `transfer_payment_amount` decimal(8,2) DEFAULT NULL,
  `client_return_payment_amount` decimal(8,2) DEFAULT NULL,
  `payment_sum_before_payment` decimal(8,2) DEFAULT NULL,
  `payment_sum_after_payment` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_payments_logs_order_payment_id_foreign` (`order_payment_id`),
  KEY `order_payments_logs_user_id_foreign` (`user_id`),
  KEY `order_payments_logs_employee_id_foreign` (`employee_id`),
  KEY `order_payments_logs_order_id_foreign` (`order_id`),
  CONSTRAINT `order_payments_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  CONSTRAINT `order_payments_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_payments_logs_order_payment_id_foreign` FOREIGN KEY (`order_payment_id`) REFERENCES `order_payments` (`id`),
  CONSTRAINT `order_payments_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3030 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_returns`
--

DROP TABLE IF EXISTS `order_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `product_stock_position_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `quantity_undamaged` int NOT NULL DEFAULT '0',
  `quantity_damaged` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_returns_order_id_foreign` (`order_id`),
  KEY `order_returns_product_id_foreign` (`product_id`),
  KEY `order_returns_product_stock_position_id_foreign` (`product_stock_position_id`),
  KEY `order_returns_user_id_foreign` (`user_id`),
  CONSTRAINT `order_returns_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_returns_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_returns_product_stock_position_id_foreign` FOREIGN KEY (`product_stock_position_id`) REFERENCES `product_stock_positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_returns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=367 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_selling_invoice_values`
--

DROP TABLE IF EXISTS `order_selling_invoice_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_selling_invoice_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_sources`
--

DROP TABLE IF EXISTS `order_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_sources` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiple` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_status_changed_labels_to_add`
--

DROP TABLE IF EXISTS `order_status_changed_labels_to_add`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_changed_labels_to_add` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `status_id` int unsigned NOT NULL,
  `label_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_status_changed_labels_to_add_status_id_foreign` (`status_id`),
  KEY `order_status_changed_labels_to_add_label_id_foreign` (`label_id`),
  CONSTRAINT `order_status_changed_labels_to_add_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_status_changed_labels_to_add_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_task_employees`
--

DROP TABLE IF EXISTS `order_task_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_task_employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int unsigned NOT NULL,
  `order_task_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_task_employees_order_task_id_foreign` (`order_task_id`),
  KEY `order_task_employees_employee_id_foreign` (`employee_id`),
  CONSTRAINT `order_task_employees_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_task_employees_order_task_id_foreign` FOREIGN KEY (`order_task_id`) REFERENCES `order_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_tasks`
--

DROP TABLE IF EXISTS `order_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `employee_id` int unsigned NOT NULL COMMENT 'Id pracownika który stworzył zadanie',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Treść zadania',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tytuł zadania',
  `show_label_at` datetime NOT NULL COMMENT 'Po upływie tej daty podawanej z godzinami, na liście zamówień wybranym do zadania pracownika pojawia się odpowiednia etykieta',
  `status` enum('OPEN','CLOSED') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_tasks_order_id_foreign` (`order_id`),
  KEY `order_tasks_employee_id_foreign` (`employee_id`),
  CONSTRAINT `order_tasks_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_tasks_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_warehouse_notifications`
--

DROP TABLE IF EXISTS `order_warehouse_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_warehouse_notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `warehouse_id` int unsigned NOT NULL,
  `realization_date` datetime DEFAULT NULL,
  `possible_delay_days` int DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_contact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waiting_for_response` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `delayed_to` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_warehouse_notifications_order_id_foreign` (`order_id`),
  KEY `order_warehouse_notifications_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `order_warehouse_notifications_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_warehouse_notifications_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37366 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_from_front_db` int unsigned DEFAULT NULL,
  `customer_id` int unsigned NOT NULL,
  `status_id` int unsigned DEFAULT NULL,
  `firm_source_id` int unsigned DEFAULT NULL,
  `last_status_update_date` datetime DEFAULT NULL COMMENT 'Status data zmiany',
  `total_price` decimal(9,2) DEFAULT NULL COMMENT 'Suma zamówienia',
  `weight` decimal(8,2) DEFAULT NULL COMMENT 'Waga zamówienia',
  `shipment_price_for_client` decimal(8,2) DEFAULT NULL COMMENT 'Koszt wysyłki',
  `shipment_price_for_us` decimal(8,2) DEFAULT NULL COMMENT 'Koszt wysyłki dla firmy',
  `customer_notices` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cash_on_delivery_amount` decimal(8,2) DEFAULT NULL COMMENT 'Kwota pobrania',
  `allegro_transaction_id` int DEFAULT NULL,
  `employee_id` int unsigned DEFAULT NULL COMMENT 'Przypisany pracownik do zamówienia, w praktyce jest nim konsultant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `warehouse_id` int unsigned DEFAULT NULL,
  `additional_service_cost` decimal(8,2) DEFAULT NULL,
  `invoice_warehouse_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_number` text COLLATE utf8mb4_unicode_ci,
  `consultant_earning` decimal(8,2) DEFAULT NULL,
  `warehouse_cost` decimal(8,2) DEFAULT NULL,
  `printed` enum('1','') COLLATE utf8mb4_unicode_ci DEFAULT '',
  `correction_description` text COLLATE utf8mb4_unicode_ci,
  `correction_amount` decimal(8,2) DEFAULT NULL,
  `packing_warehouse_cost` decimal(8,2) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `rating_message` text COLLATE utf8mb4_unicode_ci,
  `shipping_abroad` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Wysyłka za granicę',
  `proposed_payment` decimal(9,2) DEFAULT NULL COMMENT 'Proponowana zaliczka brutto',
  `additional_cash_on_delivery_cost` decimal(9,2) DEFAULT NULL COMMENT 'Dodatkowy koszt pobrania',
  `shipment_date` datetime DEFAULT NULL COMMENT 'Data rozpoczecia nadawania przesylki - globalna dla calego zamowienia',
  `shipment_start_days_variation` int DEFAULT NULL,
  `consultant_notices` text COLLATE utf8mb4_unicode_ci,
  `remainder_date` datetime DEFAULT NULL,
  `invoice_number` text COLLATE utf8mb4_unicode_ci COMMENT 'Numer faktury do zamówienia towaru (własnego)',
  `additional_info` text COLLATE utf8mb4_unicode_ci COMMENT 'Dodatkowe informacje do zamówienia towaru (własnego)',
  `print_order` tinyint(1) NOT NULL DEFAULT '0',
  `consultant_notice` text COLLATE utf8mb4_unicode_ci,
  `consultant_value` decimal(8,2) DEFAULT NULL,
  `warehouse_notice` text COLLATE utf8mb4_unicode_ci,
  `warehouse_value` decimal(8,2) DEFAULT NULL,
  `production_date` datetime DEFAULT NULL,
  `master_order_id` int unsigned DEFAULT NULL,
  `spedition_comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_deadline` date DEFAULT NULL,
  `sello_id` int DEFAULT NULL,
  `initial_sending_date_client` datetime DEFAULT NULL,
  `initial_sending_date_consultant` datetime DEFAULT NULL,
  `initial_sending_date_magazine` datetime DEFAULT NULL,
  `confirmed_sending_date_consultant` datetime DEFAULT NULL,
  `initial_pickup_date_client` datetime DEFAULT NULL,
  `confirmed_pickup_date_client` datetime DEFAULT NULL,
  `confirmed_pickup_date_consultant` datetime DEFAULT NULL,
  `initial_delivery_date_consultant` datetime DEFAULT NULL,
  `confirmed_delivery_date` datetime DEFAULT NULL,
  `proforma_filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `financial_comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_refund` decimal(8,2) DEFAULT NULL,
  `refunded` decimal(8,2) DEFAULT NULL,
  `refund_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allegro_form_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allegro_deposit_value` decimal(8,2) unsigned DEFAULT NULL,
  `allegro_operation_date` datetime DEFAULT NULL,
  `allegro_additional_service` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_channel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `labels_log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmed_sending_date_warehouse` datetime DEFAULT NULL,
  `confirmed_pickup_date_warehouse` datetime DEFAULT NULL,
  `initial_delivery_date_warehouse` datetime DEFAULT NULL,
  `data_verified_by_allegro_api` tinyint(1) NOT NULL DEFAULT '0',
  `allegro_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_invoice_date` datetime DEFAULT NULL COMMENT 'Preferowana data wystawienia systemu',
  `need_support` tinyint(1) NOT NULL DEFAULT '0',
  `reminder_date` datetime DEFAULT NULL,
  `proposed_cash_on_delivery` decimal(8,2) NOT NULL DEFAULT '20.00',
  `invoice_bilans` tinyint(1) DEFAULT NULL,
  `is_buying_admin_side` tinyint(1) NOT NULL DEFAULT '0',
  `preliminary_buying_document_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buying_document_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packages_values` json DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `send_auto_messages` tinyint(1) NOT NULL DEFAULT '1',
  `date_accepted` tinyint(1) DEFAULT NULL,
  `auction_order_placed` tinyint(1) NOT NULL,
  `start_of_spedition_period_sent` tinyint(1) NOT NULL DEFAULT '0',
  `near_end_of_spedition_period_sent` tinyint(1) NOT NULL DEFAULT '0',
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_confirmation` datetime NOT NULL,
  `special_data_filled` tinyint(1) NOT NULL,
  `driver_phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_of_spedition_period_sent` tinyint(1) NOT NULL DEFAULT '0',
  `customer_acceptation_date` datetime NOT NULL,
  `invoice_buying_warehouse_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `not_able_to_handle_users` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipped_at` date DEFAULT NULL,
  `calculated_shipping_invoices` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `allegro_form_id` (`allegro_form_id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_status_id_foreign` (`status_id`),
  KEY `orders_warehouse_id_foreign` (`warehouse_id`),
  KEY `orders_employee_id_foreign` (`employee_id`),
  KEY `orders_token_index` (`token`),
  KEY `orders_firm_source_id_foreign` (`firm_source_id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_firm_source_id_foreign` FOREIGN KEY (`firm_source_id`) REFERENCES `firm_sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=89177 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `package_templates`
--

DROP TABLE IF EXISTS `package_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sizeA` int DEFAULT NULL,
  `sizeB` int DEFAULT NULL,
  `sizeC` int DEFAULT NULL,
  `accept_time` time DEFAULT NULL,
  `accept_time_info` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_time` time DEFAULT NULL,
  `max_time_info` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_courier_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_courier_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `container_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shape` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice_max_lenght` int NOT NULL,
  `content` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cod_cost` decimal(8,2) DEFAULT NULL,
  `approx_cost_client` decimal(8,2) DEFAULT NULL,
  `approx_cost_firm` decimal(8,2) DEFAULT NULL,
  `max_weight` decimal(8,2) DEFAULT NULL,
  `volume` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `list_order` int NOT NULL DEFAULT '1000',
  `services` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `protection_method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `displayed_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sello_delivery_id` int DEFAULT NULL,
  `sello_deliverer_id` int DEFAULT NULL,
  `packing_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cod_cost_for_us` decimal(8,2) NOT NULL,
  `allegro_delivery_method` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `package_templates_chk_1` CHECK (json_valid(`allegro_delivery_method`))
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `packing_types`
--

DROP TABLE IF EXISTS `packing_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packing_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(9,2) DEFAULT NULL COMMENT 'Kwota wpłacona od kontrahenta',
  `amount_left` decimal(9,2) DEFAULT NULL COMMENT 'Kwota pozostała do rozdysponowania pomiędzy zleceniami',
  `title` text COLLATE utf8mb4_unicode_ci,
  `customer_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `notices` text COLLATE utf8mb4_unicode_ci COMMENT 'Dodatkowe uwagi do wpłaty',
  `promise` enum('1','') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promise_date` datetime DEFAULT NULL COMMENT 'data do ktorej klient zadeklarowal wplate kwoty obiecane',
  `type` enum('CLIENT','WAREHOUSE','SPEDITION') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of payment - who got payment from order client.',
  `warehouse_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  KEY `payments_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2654 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments_import`
--

DROP TABLE IF EXISTS `payments_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments_import` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=603 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_permission_id_index` (`permission_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_key_index` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `postal_code_lat_lon`
--

DROP TABLE IF EXISTS `postal_code_lat_lon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `postal_code_lat_lon` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` double(8,4) NOT NULL,
  `longitude` double(8,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22038 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_analyzers`
--

DROP TABLE IF EXISTS `product_analyzers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_analyzers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `analyze_date` datetime DEFAULT NULL,
  `parse_service` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parse_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_analyzers_product_id_foreign` (`product_id`),
  CONSTRAINT `product_analyzers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_media`
--

DROP TABLE IF EXISTS `product_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_media_product_id_foreign` (`product_id`),
  CONSTRAINT `product_media_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3041 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_opinions`
--

DROP TABLE IF EXISTS `product_opinions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_opinions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `rating` int NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_packets`
--

DROP TABLE IF EXISTS `product_packets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_packets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `packet_products_symbols` json NOT NULL,
  `packet_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_packings`
--

DROP TABLE IF EXISTS `product_packings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_packings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `calculation_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jednostka obliczeniowa',
  `unit_consumption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zużycie jednostki',
  `unit_commercial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jednostka miary handlowej',
  `unit_basic` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Podstawowa jednostka miary',
  `unit_of_collective` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jednostka zbiorcza miary',
  `unit_biggest` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Największa jednostka',
  `numbers_of_basic_commercial_units_in_pack` decimal(15,4) DEFAULT NULL COMMENT 'Ilość jednostek podstawowych w opakowaniu handlowym',
  `number_of_sale_units_in_the_pack` decimal(15,4) DEFAULT NULL COMMENT 'Ilość jednostek handlowych w opakowaniu zbiorczym',
  `number_of_trade_items_in_the_largest_unit` decimal(15,4) DEFAULT NULL COMMENT 'Ilość jednostek handlowych w jednostce największej',
  `ean_of_commercial_packing` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kod kreskowy opakowania handlowego',
  `ean_of_collective_packing` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kod kreskowy opakowania zbiorczego',
  `ean_of_biggest_packing` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kod kreskowy opakowania największego',
  `packing_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rodzaj opakowania',
  `number_of_pieces_in_total_volume` decimal(15,4) DEFAULT NULL COMMENT 'Ilość sztuk w całkowitej objętości',
  `recommended_courier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zalecany kurier',
  `max_pieces_in_one_package` decimal(15,4) DEFAULT NULL COMMENT 'Maksymalna ilość danego asortymentu w paczce',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `numbers_of_basic_commercial_units_in_transport_pack` decimal(15,4) NOT NULL,
  `warehouse` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `packing_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dimension_x` decimal(15,2) NOT NULL,
  `dimension_y` decimal(15,2) NOT NULL,
  `dimension_z` decimal(15,2) NOT NULL,
  `max_in_pallete_80` decimal(15,2) NOT NULL,
  `max_in_pallete_100` decimal(15,2) NOT NULL,
  `per_package_factor` decimal(15,2) NOT NULL,
  `warehouse_physical` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paczkomat_size_a` int DEFAULT NULL,
  `paczkomat_size_b` int DEFAULT NULL,
  `paczkomat_size_c` int DEFAULT NULL,
  `allegro_courier` int DEFAULT NULL,
  `number_of_layers_of_trade_units_in_vertical` int DEFAULT NULL,
  `number_of_trade_units_in_package_width` double(8,2) DEFAULT NULL,
  `number_of_trade_units_in_full_horizontal_layer_in_global_package` double(8,2) DEFAULT NULL,
  `number_of_layers_of_trade_units_in_height_in_global_package` double(8,2) DEFAULT NULL,
  `number_of_trade_units_in_length_in_global_package` double(8,2) DEFAULT NULL,
  `number_of_trade_units_in_width_in_global_package` double(8,2) DEFAULT NULL,
  `number_of_trade_items_in_p1` double(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_packings_product_id_foreign` (`product_id`),
  CONSTRAINT `product_packings_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_photos`
--

DROP TABLE IF EXISTS `product_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_photos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_photos_product_id_foreign` (`product_id`),
  CONSTRAINT `product_photos_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_prices`
--

DROP TABLE IF EXISTS `product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_prices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `net_purchase_price_commercial_unit` decimal(9,2) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki handlowej',
  `net_purchase_price_commercial_unit_after_discounts` decimal(9,2) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki handlowej po rabatach',
  `net_special_price_commercial_unit` decimal(9,2) DEFAULT NULL COMMENT 'Cena specjalna netto zakupu jednostki handlowej',
  `net_purchase_price_basic_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki podstawowej',
  `net_purchase_price_basic_unit_after_discounts` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto po rabatach',
  `net_special_price_basic_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena specjalna netto zakupu jednostki podstawowej',
  `net_purchase_price_calculated_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki obliczeniowej',
  `net_purchase_price_calculated_unit_after_discounts` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa netto jednostki obliczeniowej po rabatach',
  `net_special_price_calculated_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena specjalna netto jednostki obliczeniowej',
  `net_purchase_price_aggregate_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa brutto jednostki zbiorczej',
  `net_purchase_price_aggregate_unit_after_discounts` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa brutto jednostki zbiorczej po rabatach',
  `net_special_price_aggregate_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena specjalna brutto jednostki zbiorczej',
  `net_purchase_price_the_largest_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa brutto jednostki największej',
  `net_purchase_price_the_largest_unit_after_discounts` decimal(9,4) DEFAULT NULL COMMENT 'Cena zakupowa brutto jednostki największej po rabatach',
  `net_special_price_the_largest_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena specjalna brutto jednostki największej',
  `net_selling_price_commercial_unit` decimal(9,2) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki handlowej',
  `net_selling_price_basic_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki podstawowej',
  `net_selling_price_calculated_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki obliczeniowej',
  `net_selling_price_aggregate_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki zbiorczej',
  `net_selling_price_the_largest_unit` decimal(9,4) DEFAULT NULL COMMENT 'Cena sprzedaży netto jednostki największej',
  `discount1` decimal(9,2) DEFAULT NULL COMMENT 'Rabat 1',
  `discount2` decimal(9,2) DEFAULT NULL COMMENT 'Rabat 2',
  `discount3` decimal(9,2) DEFAULT NULL COMMENT 'Rabat 3',
  `bonus1` decimal(9,2) DEFAULT NULL,
  `bonus2` decimal(9,2) DEFAULT NULL,
  `bonus3` decimal(9,2) DEFAULT NULL,
  `gross_price_of_packing` decimal(9,2) DEFAULT NULL COMMENT 'Cena brutto opakowania',
  `table_price` decimal(9,2) DEFAULT NULL COMMENT 'Cena tabelaryczna',
  `vat` int NOT NULL,
  `additional_payment_for_milling` decimal(9,2) DEFAULT NULL COMMENT 'Dopłata za frezowanie',
  `coating` double(8,2) DEFAULT NULL COMMENT 'Narzut',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `euro_exchange` double(8,4) DEFAULT NULL,
  `gross_selling_price_basic_unit` double DEFAULT NULL,
  `gross_purchase_price_basic_unit_after_discounts` double(8,4) DEFAULT NULL,
  `gross_selling_price_commercial_unit` double DEFAULT NULL,
  `gross_purchase_price_commercial_unit_after_discounts` double(8,4) DEFAULT NULL,
  `gross_selling_price_calculated_unit` double DEFAULT NULL,
  `gross_purchase_price_calculated_unit_after_discounts` double(8,4) DEFAULT NULL,
  `gross_selling_price_aggregate_unit` double DEFAULT NULL,
  `gross_purchase_price_aggregate_unit_after_discounts` double(8,4) DEFAULT NULL,
  `gross_selling_price_the_largest_unit` double DEFAULT NULL,
  `gross_purchase_price_the_largest_unit_after_discounts` double(8,4) DEFAULT NULL,
  `solid_discount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `allegro_selling_gross_commercial_price` decimal(8,2) NOT NULL,
  `allegro_gross_selling_price_after_all_additional_costs` double(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_prices_product_id_foreign` (`product_id`),
  CONSTRAINT `product_prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143216 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_sets`
--

DROP TABLE IF EXISTS `product_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_sets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `set_id` int unsigned NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_sets_product_id_foreign` (`product_id`),
  KEY `product_sets_set_id_foreign` (`set_id`),
  CONSTRAINT `product_sets_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `product_sets_set_id_foreign` FOREIGN KEY (`set_id`) REFERENCES `sets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_stock_logs`
--

DROP TABLE IF EXISTS `product_stock_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stock_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` int unsigned NOT NULL,
  `product_stock_position_id` int unsigned NOT NULL,
  `action` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `user_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `stock_quantity_after_action` int DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `product_stock_logs_product_stock_id_foreign` (`product_stock_id`),
  KEY `product_stock_logs_user_id_foreign` (`user_id`),
  KEY `product_stock_logs_order_id_foreign` (`order_id`),
  CONSTRAINT `product_stock_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stock_logs_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_stock_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70644 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_stock_packet_items`
--

DROP TABLE IF EXISTS `product_stock_packet_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stock_packet_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `product_stock_packet_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_stock_packet_items_product_id_foreign` (`product_id`),
  KEY `product_stock_packet_items_product_stock_packet_id_foreign` (`product_stock_packet_id`),
  CONSTRAINT `product_stock_packet_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_stock_packet_items_product_stock_packet_id_foreign` FOREIGN KEY (`product_stock_packet_id`) REFERENCES `product_stock_packets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_stock_packets`
--

DROP TABLE IF EXISTS `product_stock_packets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stock_packets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `packet_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `packet_quantity` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_stock_positions`
--

DROP TABLE IF EXISTS `product_stock_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stock_positions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_stock_id` int unsigned NOT NULL,
  `lane` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Alejka',
  `bookstand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Regał',
  `shelf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Półka',
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pozycja',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position_quantity` int NOT NULL,
  `damaged` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_stock_positions_product_stock_id_foreign` (`product_stock_id`),
  CONSTRAINT `product_stock_positions_product_stock_id_foreign` FOREIGN KEY (`product_stock_id`) REFERENCES `product_stocks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1929 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_stocks`
--

DROP TABLE IF EXISTS `product_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_stocks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `quantity` int NOT NULL COMMENT 'Stan magazynowy',
  `min_quantity` int DEFAULT NULL COMMENT 'Minimalny stan magazynowy',
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jednostka w jakiej pokazujemy stan minimalny',
  `start_quantity` int DEFAULT NULL COMMENT 'Początkowy stan magazynowy',
  `number_on_a_layer` int DEFAULT NULL COMMENT 'Ilość na warstwie',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `product_stocks_product_id_foreign` (`product_id`),
  CONSTRAINT `product_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=744018 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_trade_groups`
--

DROP TABLE IF EXISTS `product_trade_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_trade_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('price','weight') COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int NOT NULL,
  `first_condition` decimal(12,2) NOT NULL,
  `first_price` decimal(12,2) NOT NULL,
  `second_condition` decimal(12,2) DEFAULT NULL,
  `second_price` decimal(12,2) DEFAULT NULL,
  `third_condition` decimal(12,2) DEFAULT NULL,
  `third_price` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9878 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiplier_of_the_number_of_pieces` int DEFAULT NULL COMMENT 'Mnożnik ilości sztuk',
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_trade_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki handlowej',
  `weight_collective_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki zbiorczej',
  `weight_biggest_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki największej',
  `weight_base_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki podstawowej',
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Film',
  `manufacturer_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link strony"',
  `priority` int DEFAULT NULL,
  `meta_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_photo_promoted` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie polecamy',
  `description_photo_table` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie tabela',
  `description_photo_contact` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie kontakt',
  `description_photo_details` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie szczegóły',
  `set_rule` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reguła kompletu',
  `manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'producent',
  `additional_info1` text COLLATE utf8mb4_unicode_ci COMMENT 'Uwagi 1',
  `additional_info2` text COLLATE utf8mb4_unicode_ci COMMENT 'Uwagi 2',
  `supplier_product_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Symbol produktu na opakowaniu zbiorczym',
  `product_name_on_collective_box` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa produktu na opakowaniu zbiorczym',
  `product_name_supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa dostawcy',
  `product_name_supplier_on_documents` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa produktu u dostawcy na dokumentach',
  `supplier_product_symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Symbol towaru u dostawcy na dokumentach',
  `product_name_manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa towaru producenta',
  `symbol_name_manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Symbol towaru producenta',
  `pricelist_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa cennika',
  `calculator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rodzaj kalkulatora na stronie',
  `product_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'url_kat1 + url_kat2 + url_kat3 + url_kat4 + url_kat5 ze starej bazy',
  `product_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Grupowanie produktów dla przestawienia oferty[wariacji]',
  `url_for_website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Url do zdjec po translacji na sciezke serwerowa a nie lokalna',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date_of_price_change` date DEFAULT NULL COMMENT 'Następna data zapytania o cenę',
  `date_of_the_new_prices` date DEFAULT NULL COMMENT 'Data od kiedy obowiązują nowe ceny',
  `product_group_for_change_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Grupa produktów',
  `products_related_to_the_automatic_price_change` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Produkty powiązane z automatyczną zmianą ceny',
  `text_price_change` text COLLATE utf8mb4_unicode_ci COMMENT 'Tekst zmiana ceny',
  `text_price_change_data_first` text COLLATE utf8mb4_unicode_ci COMMENT 'Tekst do kolumny Automatyczna zmiana cen dana 1',
  `text_price_change_data_second` text COLLATE utf8mb4_unicode_ci COMMENT 'Tekst do kolumny Automatyczna zmiana cen dana 2',
  `text_price_change_data_third` text COLLATE utf8mb4_unicode_ci COMMENT 'Tekst do kolumny Automatyczna zmiana cen dana 3',
  `text_price_change_data_fourth` text COLLATE utf8mb4_unicode_ci COMMENT 'Tekst do kolumny Automatyczna zmiana cen dana 4',
  `subject_to_price_change` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Czy podczas importu podlega automatycznej zmiany ceny',
  `value_of_price_change_data_first` text COLLATE utf8mb4_unicode_ci COMMENT 'Wartość pola Automatyczna zmiana cen dana 1',
  `value_of_price_change_data_second` text COLLATE utf8mb4_unicode_ci COMMENT 'Wartość pola Automatyczna zmiana cen dana 2',
  `value_of_price_change_data_third` text COLLATE utf8mb4_unicode_ci COMMENT 'Wartość pola Automatyczna zmiana cen dana 3',
  `value_of_price_change_data_fourth` text COLLATE utf8mb4_unicode_ci COMMENT 'Wartość pola Automatyczna zmiana cen dana 4',
  `pattern_to_set_the_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Wzór do ustalenia ceny za jednostkę wskazaną',
  `variation_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variation_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality_to_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_of_the_order_for_free_transport` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Wartość zamóienia u danego producenta aby otrzymać darmowy transport',
  `show_on_page` tinyint DEFAULT NULL,
  `token_prod_cat` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trade_group_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `displayed_group_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `producent_override` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_product` tinyint(1) DEFAULT NULL,
  `average_amount_of_product_in_package` int DEFAULT NULL,
  `employees_ids` text COLLATE utf8mb4_unicode_ci,
  `is_package` tinyint(1) NOT NULL DEFAULT '0',
  `save_name` tinyint(1) NOT NULL DEFAULT '1',
  `save_image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `assortment_quantity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `delivery_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `low_order_quantity_alert_text` text COLLATE utf8mb4_unicode_ci,
  `layers_in_package` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `automatic_email_messages_14_column` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `automatic_email_messages_15_column` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int DEFAULT NULL,
  `youtube` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_parent_id_index` (`parent_id`),
  KEY `products_token_prod_cat_index` (`token_prod_cat`),
  KEY `symbol` (`symbol`),
  KEY `products_product_name_supplier_index` (`product_name_supplier`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `name_2` (`name`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provider_transactions`
--

DROP TABLE IF EXISTS `provider_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waybill_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` int unsigned NOT NULL,
  `cash_on_delivery` double(8,2) DEFAULT NULL,
  `provider_balance` double(8,2) NOT NULL,
  `provider_balance_on_invoice` double(8,2) NOT NULL,
  `transaction_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_transactions_order_id_foreign` (`order_id`),
  KEY `provider_transactions_transaction_id_foreign` (`transaction_id`),
  CONSTRAINT `provider_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `provider_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `queue_monitor`
--

DROP TABLE IF EXISTS `queue_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `queue_monitor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `job_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `started_at_exact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `finished_at_exact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_elapsed` double(12,6) DEFAULT NULL,
  `failed` tinyint(1) NOT NULL DEFAULT '0',
  `attempt` int NOT NULL DEFAULT '0',
  `progress` int DEFAULT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci,
  `exception_message` text COLLATE utf8mb4_unicode_ci,
  `exception_class` text COLLATE utf8mb4_unicode_ci,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `queue_monitor_job_id_index` (`job_id`),
  KEY `queue_monitor_started_at_index` (`started_at`),
  KEY `queue_monitor_time_elapsed_index` (`time_elapsed`),
  KEY `queue_monitor_failed_index` (`failed`)
) ENGINE=InnoDB AUTO_INCREMENT=2212677 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_dailies`
--

DROP TABLE IF EXISTS `report_dailies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_dailies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `date` date NOT NULL,
  `price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_dailies_report_id_foreign` (`report_id`),
  KEY `report_dailies_user_id_foreign` (`user_id`),
  CONSTRAINT `report_dailies_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_dailies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_properties`
--

DROP TABLE IF EXISTS `report_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_properties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int unsigned NOT NULL,
  `task_id` int unsigned NOT NULL,
  `time_work` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `report_properties_report_id_foreign` (`report_id`),
  KEY `report_properties_task_id_foreign` (`task_id`),
  KEY `report_properties_user_id_foreign` (`user_id`),
  CONSTRAINT `report_properties_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_properties_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_user`
--

DROP TABLE IF EXISTS `report_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `report_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `report_user_user_id_foreign` (`user_id`),
  KEY `report_user_report_id_foreign` (`report_id`),
  CONSTRAINT `report_user_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` date NOT NULL,
  `to` date NOT NULL,
  `value` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_adr__address`
--

DROP TABLE IF EXISTS `sel_adr__address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_adr__address` (
  `id` int unsigned NOT NULL,
  `adr_TransId` int unsigned DEFAULT NULL,
  `adr_PostBuy_TransId` int unsigned DEFAULT NULL,
  `adr_Type` int unsigned NOT NULL,
  `adr_Name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_Address1` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_Address2` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_ZipCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_City` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_Company` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_NIP` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_PhoneNumber` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adr_Email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_adr__Address_adr_TransId_foreign` (`adr_TransId`),
  KEY `sel_adr__Address_adr_PostBuy_TransId_foreign` (`adr_PostBuy_TransId`),
  CONSTRAINT `sel_adr__Address_adr_PostBuy_TransId_foreign` FOREIGN KEY (`adr_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sel_adr__Address_adr_TransId_foreign` FOREIGN KEY (`adr_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_au_Note`
--

DROP TABLE IF EXISTS `sel_au_Note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_au_Note` (
  `id` int unsigned NOT NULL,
  `an_Description` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_TransactionId` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_au_Note_tr_TransactionId_foreign` (`tr_TransactionId`),
  CONSTRAINT `sel_au_Note_tr_TransactionId_foreign` FOREIGN KEY (`tr_TransactionId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_cs__customer`
--

DROP TABLE IF EXISTS `sel_cs__customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_cs__customer` (
  `id` int unsigned NOT NULL,
  `cs_Symbol` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_Name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_Nick` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_ExternalCustomerId` int DEFAULT NULL,
  `cs_Company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_NIP` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_cs_custemail`
--

DROP TABLE IF EXISTS `sel_cs_custemail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_cs_custemail` (
  `id` int unsigned NOT NULL,
  `ce_CustomerId` int unsigned NOT NULL,
  `ce_email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ce_default` bit(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_cs_CustEmail_ce_CustomerId_foreign` (`ce_CustomerId`),
  CONSTRAINT `ce_CustomerId_ce_CustomerId_foreign` FOREIGN KEY (`ce_CustomerId`) REFERENCES `sel_cs__customer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_cs_custphone`
--

DROP TABLE IF EXISTS `sel_cs_custphone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_cs_custphone` (
  `id` int unsigned NOT NULL,
  `cp_CustomerId` int unsigned NOT NULL,
  `cp_Phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cp_Default` bit(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_cs_CustPhone_cp_CustomerId_foreign` (`cp_CustomerId`),
  CONSTRAINT `sel_cs_CustPhone_cp_CustomerId_foreign` FOREIGN KEY (`cp_CustomerId`) REFERENCES `sel_cs__customer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_it__item`
--

DROP TABLE IF EXISTS `sel_it__item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_it__item` (
  `id` int unsigned NOT NULL,
  `it_Name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `it_Symbol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_nt_note`
--

DROP TABLE IF EXISTS `sel_nt_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_nt_note` (
  `id` int unsigned NOT NULL,
  `ne_Content` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ne_TransId` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_nt_Note_ne_TransId_foreign` (`ne_TransId`),
  CONSTRAINT `sel_nt_Note_ne_TransId_foreign` FOREIGN KEY (`ne_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_reg__registration`
--

DROP TABLE IF EXISTS `sel_reg__registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_reg__registration` (
  `id` int unsigned NOT NULL,
  `reg_Username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_sl_deliverer`
--

DROP TABLE IF EXISTS `sel_sl_deliverer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_sl_deliverer` (
  `id` int unsigned NOT NULL,
  `dr_Name` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_sl_delivery`
--

DROP TABLE IF EXISTS `sel_sl_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_sl_delivery` (
  `id` int unsigned NOT NULL,
  `dm_Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dm_DelivererId` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_sl_Delivery_dm_DelivererId_foreign` (`dm_DelivererId`),
  CONSTRAINT `sel_sl_Delivery_dm_DelivererId_foreign` FOREIGN KEY (`dm_DelivererId`) REFERENCES `sel_sl_deliverer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_tr__transaction`
--

DROP TABLE IF EXISTS `sel_tr__transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_tr__transaction` (
  `id` int unsigned NOT NULL,
  `tr_CreationDate` datetime DEFAULT NULL,
  `tr_Grouped` bit(1) NOT NULL,
  `tr_Group` bit(1) NOT NULL,
  `cs_Nick` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tr_RegId` int unsigned NOT NULL,
  `tr_Source` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_Paid` bit(1) NOT NULL,
  `tr_Remittance` decimal(9,2) DEFAULT NULL,
  `tr_RemittanceDate` datetime DEFAULT NULL,
  `tr_CustomerId` int unsigned NOT NULL,
  `tr_PayOnDelivery` bit(1) NOT NULL,
  `tr_DeliveryCost` decimal(9,2) DEFAULT NULL,
  `tr_Payment` decimal(9,2) DEFAULT NULL,
  `tr_DelivererId` int unsigned NOT NULL,
  `tr_DeliveryId` int unsigned NOT NULL,
  `tr_CheckoutFormId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_CheckoutFormPaymentId` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_CheckoutFormCalculatedNumberOfPackages` int NOT NULL,
  `au_Number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `au_Note` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tw_Id` int DEFAULT NULL,
  `tw_Symbol` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tw_Pole2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pt_ExternalDealId` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_tr__Transaction_tr_RegId_foreign` (`tr_RegId`),
  KEY `sel_tr__Transaction_tr_CustomerId_foreign` (`tr_CustomerId`),
  KEY `sel_tr__Transaction_tr_DelivererId_foreign` (`tr_DelivererId`),
  KEY `sel_tr__Transaction_tr_DeliveryId_foreign` (`tr_DeliveryId`),
  CONSTRAINT `sel_tr__Transaction_tr_CustomerId_foreign` FOREIGN KEY (`tr_CustomerId`) REFERENCES `sel_cs__customer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sel_tr__Transaction_tr_DelivererId_foreign` FOREIGN KEY (`tr_DelivererId`) REFERENCES `sel_sl_deliverer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sel_tr__Transaction_tr_DeliveryId_foreign` FOREIGN KEY (`tr_DeliveryId`) REFERENCES `sel_sl_delivery` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sel_tr__Transaction_tr_RegId_foreign` FOREIGN KEY (`tr_RegId`) REFERENCES `sel_reg__registration` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_tr_item`
--

DROP TABLE IF EXISTS `sel_tr_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sel_tr_item` (
  `id` int unsigned NOT NULL,
  `tt_TransId` int unsigned DEFAULT NULL,
  `tt_ItemId` int unsigned DEFAULT NULL,
  `tt_Name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tt_Quantity` int unsigned NOT NULL,
  `tt_Price` decimal(10,2) unsigned NOT NULL,
  `tt_OrigTransId` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sel_tr_Item_tt_TransId_foreign` (`tt_TransId`),
  KEY `sel_tr_Item_tt_OrigTransId` (`tt_OrigTransId`),
  KEY `sel_tr_Item_tt_ItemId` (`tt_ItemId`),
  CONSTRAINT `sel_tr_Item_tt_ItemId_foreign` FOREIGN KEY (`tt_ItemId`) REFERENCES `sel_it__item` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sel_tr_Item_tt_TransId_foreign` FOREIGN KEY (`tt_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sets`
--

DROP TABLE IF EXISTS `sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `product_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sets_product_id_foreign` (`product_id`),
  CONSTRAINT `sets_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `details` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '1',
  `group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipment_groups`
--

DROP TABLE IF EXISTS `shipment_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipment_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `courier_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Courier name',
  `package_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Package group type',
  `lp` int NOT NULL COMMENT 'Package group number of the day.',
  `shipment_date` date NOT NULL COMMENT 'Group shipment date',
  `sent` tinyint(1) NOT NULL COMMENT 'If package group was send',
  `closed` tinyint(1) NOT NULL COMMENT 'If package group was closed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipping_pay_in_reports`
--

DROP TABLE IF EXISTS `shipping_pay_in_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_pay_in_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `symbol_spedytora` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numer_listu` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nr_faktury_do_ktorej_dany_lp_zostal_przydzielony` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_nadania_otrzymania` date NOT NULL,
  `nr_i_d` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rzeczywisty_koszt_transportu_brutto` decimal(10,2) NOT NULL,
  `wartosc_pobrania` decimal(10,2) NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reszta` blob NOT NULL,
  `rodzaj` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `surcharge` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `found` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spedition_exchange_items`
--

DROP TABLE IF EXISTS `spedition_exchange_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spedition_exchange_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoiced` tinyint(1) NOT NULL,
  `spedition_exchange_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `spedition_exchange_items_spedition_exchange_id_foreign` (`spedition_exchange_id`),
  KEY `spedition_exchange_items_order_id_foreign` (`order_id`),
  CONSTRAINT `spedition_exchange_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `spedition_exchange_items_spedition_exchange_id_foreign` FOREIGN KEY (`spedition_exchange_id`) REFERENCES `spedition_exchanges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spedition_exchange_offers`
--

DROP TABLE IF EXISTS `spedition_exchange_offers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spedition_exchange_offers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `spedition_exchange_id` int unsigned NOT NULL,
  `firm_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `driver_first_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_last_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_phone_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_document_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_car_registration_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_arrival_date` date NOT NULL,
  `driver_approx_arrival_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spedition_exchange_offers_spedition_exchange_id_foreign` (`spedition_exchange_id`),
  CONSTRAINT `spedition_exchange_offers_spedition_exchange_id_foreign` FOREIGN KEY (`spedition_exchange_id`) REFERENCES `spedition_exchanges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spedition_exchanges`
--

DROP TABLE IF EXISTS `spedition_exchanges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spedition_exchanges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `chosen_spedition_offer_id` int unsigned DEFAULT NULL,
  `hash` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `spedition_exchanges_chosen_spedition_offer_id_foreign` (`chosen_spedition_offer_id`),
  CONSTRAINT `spedition_exchanges_chosen_spedition_offer_id_foreign` FOREIGN KEY (`chosen_spedition_offer_id`) REFERENCES `spedition_exchange_offers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `generate_order_offer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `styro_lead_mails`
--

DROP TABLE IF EXISTS `styro_lead_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `styro_lead_mails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email_sent` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `email_read` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `on_website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `made_inquiry` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `number_of_emails_sent` int NOT NULL DEFAULT '0',
  `styro_lead_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `styro_leads`
--

DROP TABLE IF EXISTS `styro_leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `styro_leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firm_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_sent` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `email_read` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `on_website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `made_inquiry` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `number_of_emails_sent` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5780 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `handler` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_salary_details`
--

DROP TABLE IF EXISTS `task_salary_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_salary_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `consultant_notice` text COLLATE utf8mb4_unicode_ci,
  `consultant_value` decimal(8,2) DEFAULT NULL,
  `warehouse_notice` text COLLATE utf8mb4_unicode_ci,
  `warehouse_value` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_salary_details_task_id_foreign` (`task_id`),
  CONSTRAINT `task_salary_details_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51593 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task_times`
--

DROP TABLE IF EXISTS `task_times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_times` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `transfer_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_times_task_id_foreign` (`task_id`),
  CONSTRAINT `task_times_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=285979 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('WAITING_FOR_ACCEPT','REJECTED','TO_DO','FINISHED','IN_PROGRESS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rendering` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_user_id_foreign` (`user_id`),
  KEY `tasks_created_by_foreign` (`created_by`),
  KEY `tasks_warehouse_id_foreign` (`warehouse_id`),
  KEY `tasks_order_id_foreign` (`order_id`),
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=289318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timed_labels`
--

DROP TABLE IF EXISTS `timed_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timed_labels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `execution_time` datetime NOT NULL COMMENT 'Kolumna ta zawiera datę oraz czas w którym zostanie dodana etykieta interwencja',
  `order_id` int unsigned DEFAULT NULL,
  `label_id` int unsigned DEFAULT NULL,
  `is_executed` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timed_labels_order_id_foreign` (`order_id`),
  KEY `timed_labels_label_id_foreign` (`label_id`),
  CONSTRAINT `timed_labels_label_id_foreign` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`),
  CONSTRAINT `timed_labels_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tracker_logs`
--

DROP TABLE IF EXISTS `tracker_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tracker_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracker_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `tracker_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71070 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `posted_in_system_date` datetime DEFAULT NULL COMMENT 'Data zaksięgowania w systemie',
  `posted_in_bank_date` datetime DEFAULT NULL COMMENT 'Data zaksięgowania w banku',
  `payment_id` text COLLATE utf8mb4_unicode_ci COMMENT 'Identyfikator płatności z sello',
  `kind_of_operation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rodzaj operacji',
  `order_id` int unsigned DEFAULT NULL COMMENT 'Identyfikator zamówienia',
  `operator` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Operator płatności',
  `operation_value` double(8,2) DEFAULT NULL COMMENT 'Wartość operacji',
  `balance` double(8,2) DEFAULT NULL COMMENT 'Saldo',
  `accounting_notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uwagi księgowe',
  `transaction_notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uwagi dotyczace transakcji',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_order_id_foreign` (`order_id`),
  CONSTRAINT `transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `translations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_key` int unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translations_table_name_column_name_foreign_key_locale_unique` (`table_name`,`column_name`,`foreign_key`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_emails`
--

DROP TABLE IF EXISTS `user_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_emails` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `host` text COLLATE utf8mb4_unicode_ci,
  `port` text COLLATE utf8mb4_unicode_ci,
  `username` text COLLATE utf8mb4_unicode_ci,
  `password` text COLLATE utf8mb4_unicode_ci,
  `encryption` enum('SSL','TLS','NONE') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_emails_user_id_foreign` (`user_id`),
  CONSTRAINT `user_emails_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `user_id` int unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_roles_user_id_index` (`user_id`),
  KEY `user_roles_role_id_index` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` int unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'users/default.png',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rate_hour` double DEFAULT NULL,
  `can_decline` tinyint(1) NOT NULL DEFAULT '0',
  `grid_settings` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_surplus_payments`
--

DROP TABLE IF EXISTS `users_surplus_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_surplus_payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `surplus_amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_surplus_payments_user_id_foreign` (`user_id`),
  KEY `users_surplus_payments_order_id_foreign` (`order_id`),
  CONSTRAINT `users_surplus_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `users_surplus_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_surplus_payments_history`
--

DROP TABLE IF EXISTS `users_surplus_payments_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_surplus_payments_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `surplus_amount` decimal(8,2) NOT NULL,
  `operation` enum('INCREASE','DECREASE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_surplus_payment` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_surplus_payments_history_user_id_foreign` (`user_id`),
  KEY `users_surplus_payments_history_order_id_foreign` (`order_id`),
  KEY `users_surplus_payments_history_user_surplus_payment_foreign` (`user_surplus_payment`),
  CONSTRAINT `users_surplus_payments_history_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `users_surplus_payments_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `users_surplus_payments_history_user_surplus_payment_foreign` FOREIGN KEY (`user_surplus_payment`) REFERENCES `users_surplus_payments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_works`
--

DROP TABLE IF EXISTS `users_works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_works` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `date_of_work` date NOT NULL,
  `start` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_works_user_id_foreign` (`user_id`),
  CONSTRAINT `users_works_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117740 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouse_addresses`
--

DROP TABLE IF EXISTS `warehouse_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int unsigned NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `latitude` decimal(12,10) NOT NULL,
  `longitude` decimal(12,10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_addresses_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_addresses_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1431486 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouse_orders`
--

DROP TABLE IF EXISTS `warehouse_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `symbol` text COLLATE utf8mb4_unicode_ci,
  `shipment_date` datetime DEFAULT NULL,
  `confirmation_date` datetime DEFAULT NULL,
  `company` text COLLATE utf8mb4_unicode_ci,
  `email` text COLLATE utf8mb4_unicode_ci,
  `confirmation` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `arrival_date` datetime DEFAULT NULL,
  `status` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` int unsigned DEFAULT NULL,
  `consultant_comment_date` datetime DEFAULT NULL,
  `consultant_comment` text COLLATE utf8mb4_unicode_ci,
  `warehouse_comment` text COLLATE utf8mb4_unicode_ci,
  `comments_for_warehouse` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_orders_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouse_orders_items`
--

DROP TABLE IF EXISTS `warehouse_orders_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_orders_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `warehouse_order_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_orders_items_warehouse_order_id_foreign` (`warehouse_order_id`),
  KEY `warehouse_orders_items_product_id_foreign` (`product_id`),
  CONSTRAINT `warehouse_orders_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouse_orders_items_warehouse_order_id_foreign` FOREIGN KEY (`warehouse_order_id`) REFERENCES `warehouse_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouse_properties`
--

DROP TABLE IF EXISTS `warehouse_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse_properties` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int unsigned NOT NULL,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `additional_comments` text COLLATE utf8mb4_unicode_ci,
  `open_days` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_properties_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `warehouse_properties_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1431477 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firm_id` int unsigned NOT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `radius` double(8,2) NOT NULL,
  `warehouse_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Warehouse email - every warehouse has email. It should not be unique - every company can have many warehouses with same email address',
  `cordinates` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipment_after_pay_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipment_after_pay_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_firm_id_foreign` (`firm_id`),
  KEY `warehouses_symbol_index` (`symbol`),
  CONSTRAINT `warehouses_firm_id_foreign` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1431485 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `working_events`
--

DROP TABLE IF EXISTS `working_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `working_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `working_events_order_id_foreign` (`order_id`),
  KEY `working_events_user_id_foreign` (`user_id`),
  CONSTRAINT `working_events_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `working_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3604865 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-20 19:47:51
