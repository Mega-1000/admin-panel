-- MariaDB dump 10.17  Distrib 10.4.10-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: testmega
-- ------------------------------------------------------
-- Server version	10.4.10-MariaDB

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
-- Table structure for table `sel_adr__address`
--

DROP TABLE IF EXISTS `sel_adr__address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_adr__address` (
                                    `id` int(10) unsigned NOT NULL,
                                    `adr_TransId` int(10) unsigned DEFAULT NULL,
                                    `adr_PostBuy_TransId` int(10) unsigned DEFAULT NULL,
                                    `adr_Type` int(2) unsigned NOT NULL,
                                    `adr_Name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_Address1` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_Address2` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_ZipCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_City` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_Company` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_NIP` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_PhoneNumber` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `adr_Email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                                    `updated_at` timestamp NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`),
                                    KEY `sel_adr__Address_adr_TransId_foreign` (`adr_TransId`),
                                    KEY `sel_adr__Address_adr_PostBuy_TransId_foreign` (`adr_PostBuy_TransId`),
                                    CONSTRAINT `sel_adr__Address_adr_PostBuy_TransId_foreign` FOREIGN KEY (`adr_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE,
                                    CONSTRAINT `sel_adr__Address_adr_TransId_foreign` FOREIGN KEY (`adr_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_cs__customer`
--

DROP TABLE IF EXISTS `sel_cs__customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_cs__customer` (
                                    `id` int(10) unsigned NOT NULL,
                                    `cs_Symbol` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `cs_Name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `cs_Company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `cs_NIP` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                                    `updated_at` timestamp NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_cs_custemail`
--

DROP TABLE IF EXISTS `sel_cs_custemail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_cs_custemail` (
                                    `id` int(10) unsigned NOT NULL,
                                    `ce_CustomerId` int(10) unsigned NOT NULL,
                                    `ce_email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `ce_default` bit(1) NOT NULL,
                                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                                    `updated_at` timestamp NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_cs_custphone` (
                                    `id` int(10) unsigned NOT NULL,
                                    `cp_CustomerId` int(10) unsigned NOT NULL,
                                    `cp_Phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `cp_Default` bit(1) NOT NULL,
                                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                                    `updated_at` timestamp NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_it__item` (
                                `id` int(10) unsigned NOT NULL,
                                `it_Name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `it_Symbol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `created_at` timestamp NULL DEFAULT current_timestamp(),
                                `updated_at` timestamp NULL DEFAULT current_timestamp(),
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_nt_note`
--

DROP TABLE IF EXISTS `sel_nt_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_nt_note` (
                               `id` int(10) unsigned NOT NULL,
                               `ne_Content` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `ne_TransId` int(10) unsigned DEFAULT NULL,
                               `created_at` timestamp NULL DEFAULT current_timestamp(),
                               `updated_at` timestamp NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_reg__registration` (
                                         `id` int(10) unsigned NOT NULL,
                                         `reg_Username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_sl_deliverer`
--

DROP TABLE IF EXISTS `sel_sl_deliverer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_sl_deliverer` (
                                    `id` int(10) unsigned NOT NULL,
                                    `dr_Name` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `created_at` timestamp NULL DEFAULT current_timestamp(),
                                    `updated_at` timestamp NULL DEFAULT current_timestamp(),
                                    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sel_sl_delivery`
--

DROP TABLE IF EXISTS `sel_sl_delivery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_sl_delivery` (
                                   `id` int(10) unsigned NOT NULL,
                                   `dm_Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `dm_DelivererId` int(10) unsigned NOT NULL,
                                   `created_at` timestamp NULL DEFAULT current_timestamp(),
                                   `updated_at` timestamp NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_tr__transaction` (
                                       `id` int(10) unsigned NOT NULL,
                                       `tr_CreationDate` datetime DEFAULT NULL,
                                       `tr_RegId` int(10) unsigned NOT NULL,
                                       `tr_Source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                                       `tr_Paid` bit(1) NOT NULL,
                                       `tr_Remittance` decimal(9,2) DEFAULT NULL,
                                       `tr_RemittanceDate` datetime DEFAULT NULL,
                                       `tr_CustomerId` int(10) unsigned NOT NULL,
                                       `tr_PayOnDelivery` bit(1) NOT NULL,
                                       `tr_DeliveryCost` decimal(9,2) DEFAULT NULL,
                                       `tr_Payment` decimal(9,2) DEFAULT NULL,
                                       `tr_DelivererId` int(10) unsigned NOT NULL,
                                       `tr_DeliveryId` int(10) unsigned NOT NULL,
                                       `created_at` timestamp NULL DEFAULT current_timestamp(),
                                       `updated_at` timestamp NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sel_tr_item` (
                               `id` int(10) unsigned NOT NULL,
                               `tt_TransId` int(10) unsigned DEFAULT NULL,
                               `tt_ItemId` int(10) unsigned DEFAULT NULL,
                               `tt_Name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `tt_Quantity` int(10) unsigned NOT NULL,
                               `tt_Price` decimal(10,0) unsigned NOT NULL,
                               `tt_OrigTransId` int(10) unsigned DEFAULT NULL,
                               `created_at` timestamp NULL DEFAULT current_timestamp(),
                               `updated_at` timestamp NULL DEFAULT current_timestamp(),
                               PRIMARY KEY (`id`),
                               KEY `sel_tr_Item_tt_TransId_foreign` (`tt_TransId`),
                               KEY `sel_tr_Item_tt_OrigTransId` (`tt_OrigTransId`),
                               KEY `sel_tr_Item_tt_ItemId` (`tt_ItemId`),
                               CONSTRAINT `sel_tr_Item_tt_ItemId_foreign` FOREIGN KEY (`tt_ItemId`) REFERENCES `sel_it__item` (`id`) ON DELETE CASCADE,
                               CONSTRAINT `sel_tr_Item_tt_OrigTransId_foreign` FOREIGN KEY (`tt_OrigTransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE,
                               CONSTRAINT `sel_tr_Item_tt_TransId_foreign` FOREIGN KEY (`tt_TransId`) REFERENCES `sel_tr__transaction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-28 15:15:48
