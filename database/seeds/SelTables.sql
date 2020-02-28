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
-- Dumping data for table `sel_adr__address`
--

LOCK TABLES `sel_adr__address` WRITE;
/*!40000 ALTER TABLE `sel_adr__address` DISABLE KEYS */;
INSERT INTO `sel_adr__address` VALUES (1275,1180,NULL,1,'Ewelina Wojsław','Reymonta 1/1','','18-400','Łomża','','','512612290','12xl7gux4t+c9baf223@allegromail.pl','2019-11-25 22:49:20','2019-11-25 22:49:20'),(1276,1180,NULL,3,'Hubert Wojsław','Święcienin 31','','19-213','Radziłów','','','+48 500 330 585','12xl7gux4t@allegromail.pl','2019-11-25 22:49:20','2019-11-25 22:49:20'),(1277,1181,NULL,1,'Ewelina Wojsław','Reymonta 1/1','','18-400','Łomża','','','512612290','12xl7gux4t+c9baf223@allegromail.pl','2019-11-25 22:49:20','2019-11-25 22:49:20'),(1278,1181,NULL,3,'Hubert Wojsław','Święcienin 31','','19-213','Radziłów','','','+48 500 330 585','12xl7gux4t@allegromail.pl','2019-11-25 22:49:21','2019-11-25 22:49:21'),(1282,1182,NULL,1,'Arkadiusz Choma','Traugutta 2d/3','','22-400','Zamość','','','509693216','gfyk7mb8xo+3044402e6@allegromail.pl','2019-11-25 22:49:21','2019-11-25 22:49:21'),(1283,1182,NULL,3,'','WólkaZłojecka64A','','22-413','Nielisz','DekorsArkadiuszChoma posadzki betonowe,przemysłowe,żywiczne i de','9222467861','+48 509 693 216','gfyk7mb8xo+3044402e6@allegromail.pl','2019-11-25 22:49:21','2019-11-25 22:49:21'),(1288,1183,NULL,1,'Mirosław Buława','Ogrodowa 4a','','89-410','Witunia','','','609860222','fn139bnmtp+5b9c35270@allegromail.pl','2019-11-25 22:49:21','2019-11-25 22:49:21'),(1289,1183,NULL,3,'Mirosław Buława','Ogrodowa 4a','','89-410','Witunia','','','+48 52 389 64 55','fn139bnmtp+5b9c35270@allegromail.pl','2019-11-25 22:49:22','2019-11-25 22:49:22'),(1290,NULL,1180,2,'Mirosław Buława','Ogrodowa 4a','','89-410','Witunia','','','609860222','fn139bnmtp+5b9c35270@allegromail.pl','2019-11-25 22:49:24','2019-11-25 22:49:24'),(1291,NULL,1180,3,'Mirosław Buława','Ogrodowa 4a','','89-410','Witunia','','','','fn139bnmtp+5b9c35270@allegromail.pl','2019-11-25 22:49:25','2019-11-25 22:49:25'),(1294,1184,NULL,1,'Mirosław Ogórek','Wielkie 97','','21-143','Abramów','','','502-232-759','5yny2a82cs+3eb1351d4@allegromail.pl','2019-11-25 22:49:22','2019-11-25 22:49:22'),(1295,1184,NULL,3,'','Wielkie 97','','21-143','Abramów','FHU DOMIR Mirosław Ogórek','955-120-51-97','+48 512 989 673','5yny2a82cs+3eb1351d4@allegromail.pl','2019-11-25 22:49:22','2019-11-25 22:49:22'),(1296,NULL,1181,2,'Mirosław Ogórek','Wielkie 97','','21-143','Abramów','','','502-232-759','5yny2a82cs+3eb1351d4@allegromail.pl','2019-11-25 22:49:25','2019-11-25 22:49:25'),(1297,NULL,1181,3,'','Wielkie 97','','21-143','Abramów','FHU DOMIR Mirosław Ogórek','955-120-51-97','','5yny2a82cs+3eb1351d4@allegromail.pl','2019-11-25 22:49:25','2019-11-25 22:49:25'),(1300,1185,NULL,1,'Tomek Choma','Pszeniczna14','','82-300','Elbląg','Firma Ogólnobudowlana Tom-Bud','','+48 55 232 35 74','ycgkmqm4qv@allegromail.pl','2019-11-25 22:49:22','2019-11-25 22:49:22'),(1301,1185,NULL,3,'Tomek Choma','Pszeniczna14','','82-300','Elbląg','Firma Ogólnobudowlana Tom-Bud','','+48 55 232 35 74','ycgkmqm4qv@allegromail.pl','2019-11-25 22:49:23','2019-11-25 22:49:23'),(1302,1186,NULL,1,'Tomasz Choma','Pszeniczna14/1','','82-300','Elbląg','Firma Budowlana Tom Bud s.c. Monika Choma Tomasz Choma','','606292574','ycgkmqm4qv+7196cac46@allegromail.pl','2019-11-25 22:49:23','2019-11-25 22:49:23'),(1303,1186,NULL,3,'','PSZENICZNA 14/1','','82-300','ELBLĄG','Firma Budowlana Tom Bud s.c.Monika Choma  Tomasz Choma','5783136415','+48 55 232 35 74','ycgkmqm4qv+7196cac46@allegromail.pl','2019-11-25 22:49:23','2019-11-25 22:49:23'),(1306,1187,NULL,1,'Dariusz Juszczak','kościelna 15','','62-406','Lądek','','','509333232','1m9xi06z3u+326caf731@allegromail.pl','2019-11-25 22:49:23','2019-11-25 22:49:23'),(1307,1187,NULL,3,'Dariusz Juszczak','Polna 3','','62-406','Lądek','','','+48 509 333 232','1m9xi06z3u@allegromail.pl','2019-11-25 22:49:23','2019-11-25 22:49:23'),(1312,NULL,1182,2,'Tomasz Choma','Pszeniczna14/1','','82-300','Elbląg','Firma Budowlana Tom Bud s.c. Monika Choma Tomasz Choma','','606292574','ycgkmqm4qv+7196cac46@allegromail.pl','2019-11-25 22:49:25','2019-11-25 22:49:25'),(1313,NULL,1182,3,'','PSZENICZNA 14/1','','82-300','ELBLĄG','Firma Budowlana Tom Bud s.c.Monika Choma  Tomasz Choma','5783136415','','ycgkmqm4qv+7196cac46@allegromail.pl','2019-11-25 22:49:25','2019-11-25 22:49:25'),(1314,NULL,1183,2,'Dariusz Juszczak','kościelna 15','','62-406','Lądek','','','509333232','1m9xi06z3u+326caf731@allegromail.pl','2019-11-25 22:49:26','2019-11-25 22:49:26'),(1315,1189,NULL,1,'Tomasz Choma','Pszeniczna14/1','','82-300','Elbląg','Firma Budowlana Tom Bud s.c. Monika Choma Tomasz Choma','','606292574','ycgkmqm4qv+7196cac46@allegromail.pl','2019-11-25 22:49:24','2019-11-25 22:49:24'),(1316,1189,NULL,3,'','PSZENICZNA 14/1','','82-300','ELBLĄG','Firma Budowlana Tom Bud s.c.Monika Choma  Tomasz Choma','5783136415','+48 55 232 35 74','ycgkmqm4qv+7196cac46@allegromail.pl','2019-11-25 22:49:24','2019-11-25 22:49:24'),(1333,NULL,1184,2,'Jarosław Siewier','Gembartówka 17','','97-512','Gembartówka','','','+48 880 419 186','lnxv7dx9d7+652f57e09@allegromail.pl','2019-11-25 22:49:26','2019-11-25 22:49:26'),(1334,NULL,1189,2,'Paweł Spyra','Laskowskiego 2/15','','40-749','Katowice','','','+48 603 354 545','a17kxdgo3l+295830305@allegromail.pl','2019-11-25 22:49:26','2019-11-25 22:49:26'),(1339,NULL,1189,2,'Ewa Budniak','Pułtuska 114T','','07-200','Wyszkow','Salon fryzjerski EB','','729361115','co248wryky+314972c67@allegromail.pl','2019-11-25 22:49:27','2019-11-25 22:49:27'),(1351,NULL,1187,2,'Damian Pieta','Dworcowa 16/8','','85-010','Bydgoszcz','','','600516621','gaa59eogsw+388899df1@allegromail.pl','2019-11-25 22:49:26','2019-11-25 22:49:26');
/*!40000 ALTER TABLE `sel_adr__address` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_cs__customer`
--

LOCK TABLES `sel_cs__customer` WRITE;
/*!40000 ALTER TABLE `sel_cs__customer` DISABLE KEYS */;
INSERT INTO `sel_cs__customer` VALUES (1016,'','Arkadiusz Choma','Dekores Arkadiusz Choma Posadzki betonowe,przemysłowe,żywiczne  i dekoracyjne','','2019-11-25 22:49:03','2019-11-25 22:49:03'),(1038,'','Hubert Wojsław','','','2019-11-25 22:49:02','2019-11-25 22:49:02'),(1039,'','Mirosław Buława','WITUNIA.pl','','2019-11-25 22:49:03','2019-11-25 22:49:03'),(1040,'','dorota seluk','Dorota Seluk-Ogórek','','2019-11-25 22:49:03','2019-11-25 22:49:03'),(1041,'','Tomek Choma','Firma Ogólnobudowlana Tom-Bud','','2019-11-25 22:49:03','2019-11-25 22:49:03'),(1042,'','Dariusz Juszczak','','','2019-11-25 22:49:04','2019-11-25 22:49:04');
/*!40000 ALTER TABLE `sel_cs__customer` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_cs_custemail`
--

LOCK TABLES `sel_cs_custemail` WRITE;
/*!40000 ALTER TABLE `sel_cs_custemail` DISABLE KEYS */;
INSERT INTO `sel_cs_custemail` VALUES (1017,1016,'gfyk7mb8xo@allegromail.pl','','2019-11-25 22:49:08','2019-11-25 22:49:08'),(1039,1038,'12xl7gux4t@allegromail.pl','','2019-11-25 22:49:07','2019-11-25 22:49:07'),(1040,1039,'fn139bnmtp@allegromail.pl','','2019-11-25 22:49:08','2019-11-25 22:49:08'),(1041,1040,'5yny2a82cs@allegromail.pl','','2019-11-25 22:49:08','2019-11-25 22:49:08'),(1042,1041,'ycgkmqm4qv@allegromail.pl','','2019-11-25 22:49:08','2019-11-25 22:49:08'),(1043,1042,'1m9xi06z3u@allegromail.pl','','2019-11-25 22:49:09','2019-11-25 22:49:09');
/*!40000 ALTER TABLE `sel_cs_custemail` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_cs_custphone`
--

LOCK TABLES `sel_cs_custphone` WRITE;
/*!40000 ALTER TABLE `sel_cs_custphone` DISABLE KEYS */;
INSERT INTO `sel_cs_custphone` VALUES (1010,1016,'+48 509 693 216','','2019-11-25 22:49:05','2019-11-25 22:49:05'),(1037,1038,'+48 500 330 585','','2019-11-25 22:49:04','2019-11-25 22:49:04'),(1038,1038,'+48 512 612 290','\0','2019-11-25 22:49:04','2019-11-25 22:49:04'),(1039,1039,'+48 52 389 64 55','','2019-11-25 22:49:05','2019-11-25 22:49:05'),(1040,1040,'+48 512 989 673','','2019-11-25 22:49:05','2019-11-25 22:49:05'),(1041,1040,'+48 502 232 759','\0','2019-11-25 22:49:06','2019-11-25 22:49:06'),(1042,1041,'+48 55 232 35 74','','2019-11-25 22:49:06','2019-11-25 22:49:06'),(1043,1041,'+48 606 292 574','\0','2019-11-25 22:49:06','2019-11-25 22:49:06'),(1044,1042,'+48 509 333 232','','2019-11-25 22:49:07','2019-11-25 22:49:07');
/*!40000 ALTER TABLE `sel_cs_custphone` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_it__item`
--

LOCK TABLES `sel_it__item` WRITE;
/*!40000 ALTER TABLE `sel_it__item` DISABLE KEYS */;
INSERT INTO `sel_it__item` VALUES (1005,'siatka podtynkowa lateksowa 170g/m2','SDDB178BWJ-320-W1','2019-11-25 22:49:10','2019-11-25 22:49:10'),(1217,'siatka podtynkowa lateksowa 170g/m2','SDDB178BWJ-320-10','2019-11-25 22:49:10','2019-11-25 22:49:10'),(1411,'siatka elewacyjna podtynkowa 155 g/m2 1x50m','SDD155G-320-26','2019-11-25 22:49:10','2019-11-25 22:49:10'),(1416,'siatka elewacyjna podtynkowa 155 g/m2 1x50m','SDD155G-320-31','2019-11-25 22:49:10','2019-11-25 22:49:10'),(1485,'podkladka dystansowa B5 100szt 35-50mm','PDB5-160-11','2019-11-25 22:49:09','2019-11-25 22:49:09');
/*!40000 ALTER TABLE `sel_it__item` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_nt_note`
--

LOCK TABLES `sel_nt_note` WRITE;
/*!40000 ALTER TABLE `sel_nt_note` DISABLE KEYS */;
INSERT INTO `sel_nt_note` VALUES (1002,'wiadomość od klienta:\nproszę o pilna wysyłkę, f-ra imienna',1183,'2019-11-25 22:49:27','2019-11-25 22:49:27'),(1003,'wiadomość od klienta:\nProszę o szybka wysyłkę',1182,'2019-11-25 22:49:27','2019-11-25 22:49:27');
/*!40000 ALTER TABLE `sel_nt_note` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_reg__registration`
--

LOCK TABLES `sel_reg__registration` WRITE;
/*!40000 ALTER TABLE `sel_reg__registration` DISABLE KEYS */;
INSERT INTO `sel_reg__registration` VALUES (1002,'goldmoney2');
/*!40000 ALTER TABLE `sel_reg__registration` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_sl_deliverer`
--

LOCK TABLES `sel_sl_deliverer` WRITE;
/*!40000 ALTER TABLE `sel_sl_deliverer` DISABLE KEYS */;
INSERT INTO `sel_sl_deliverer` VALUES (1,'Poczta Polska','2019-11-22 19:15:56','2019-11-22 19:15:56'),(2,'Ruch','2019-11-22 19:15:56','2019-11-22 19:15:56'),(3,'InPost','2019-11-22 19:15:56','2019-11-22 19:15:56'),(4,'DHL','2019-11-22 19:15:56','2019-11-22 19:15:56'),(6,'Geis Parcel PL','2019-11-22 19:15:57','2019-11-22 19:15:57'),(100,'Odbiór osobisty','2019-11-22 19:15:57','2019-11-22 19:15:57'),(101,'Przesyłka elektroniczna','2019-11-22 19:15:57','2019-11-22 19:15:57');
/*!40000 ALTER TABLE `sel_sl_deliverer` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_sl_delivery`
--

LOCK TABLES `sel_sl_delivery` WRITE;
/*!40000 ALTER TABLE `sel_sl_delivery` DISABLE KEYS */;
INSERT INTO `sel_sl_delivery` VALUES (1,'Paczka pocztowa ekonomiczna',1,'2019-11-22 19:19:50','2019-11-22 19:19:50'),(2,'List ekonomiczny',1,'2019-11-22 19:19:50','2019-11-22 19:19:50'),(3,'Paczka pocztowa priorytetowa',1,'2019-11-22 19:19:50','2019-11-22 19:19:50'),(4,'List priorytetowy',1,'2019-11-22 19:19:51','2019-11-22 19:19:51'),(5,'Przesyłka pobraniowa',1,'2019-11-22 19:19:51','2019-11-22 19:19:51'),(6,'List polecony ekonomiczny',1,'2019-11-22 19:19:51','2019-11-22 19:19:51'),(7,'Przesyłka pobraniowa priorytetowa',1,'2019-11-22 19:19:51','2019-11-22 19:19:51'),(8,'List polecony priorytetowy',1,'2019-11-22 19:19:52','2019-11-22 19:19:52'),(9,'Kurier standard',3,'2019-11-22 19:19:52','2019-11-22 19:19:52'),(10,'Przesyłka kurierska',4,'2019-11-22 19:19:52','2019-11-22 19:19:52'),(11,'Kurier standard pobraniowy',3,'2019-11-22 19:19:52','2019-11-22 19:19:52'),(12,'Przesyłka kurierska pobraniowa',4,'2019-11-22 19:19:53','2019-11-22 19:19:53'),(13,'PACZKA w RUCHu',2,'2019-11-22 19:19:53','2019-11-22 19:19:53'),(14,'PACZKA w RUCHu po przedpłacie',2,'2019-11-22 19:19:53','2019-11-22 19:19:53'),(15,'Paczkomaty 24/7',3,'2019-11-22 19:19:53','2019-11-22 19:19:53'),(16,'Paczkomaty 24/7 po przedpłacie',3,'2019-11-22 19:19:53','2019-11-22 19:19:53'),(17,'SERVICE POINT',4,'2019-11-22 19:19:54','2019-11-22 19:19:54'),(18,'E-PRZESYŁKA',1,'2019-11-22 19:19:54','2019-11-22 19:19:54'),(19,'E-PRZESYŁKA po przedpłacie',1,'2019-11-22 19:19:54','2019-11-22 19:19:54'),(20,'Przesyłka biznesowa',1,'2019-11-22 19:19:54','2019-11-22 19:19:54'),(21,'Przesyłka biznesowa pobraniowa',1,'2019-11-22 19:19:55','2019-11-22 19:19:55'),(24,'Przesyłka',6,'2019-11-22 19:19:55','2019-11-22 19:19:55'),(25,'Przesyłka pobraniowa',6,'2019-11-22 19:19:55','2019-11-22 19:19:55'),(26,'Allegro Paczkomaty 24/7 InPost',3,'2019-11-22 19:19:55','2019-11-22 19:19:55'),(27,'Allegro Paczkomaty 24/7 InPost po przedpłacie',3,'2019-11-22 19:19:55','2019-11-22 19:19:55'),(28,'Allegro Kurier24 InPost',3,'2019-11-22 19:19:56','2019-11-22 19:19:56'),(29,'Allegro Kurier24 InPost po przedpłacie',3,'2019-11-22 19:19:56','2019-11-22 19:19:56'),(30,'Allegro miniKurier24 InPost',3,'2019-11-22 19:19:56','2019-11-22 19:19:56'),(31,'Allegro miniKurier24 InPost po przedpłacie',3,'2019-11-22 19:19:56','2019-11-22 19:19:56'),(100,'Odbiór osobisty',100,'2019-11-22 19:19:57','2019-11-22 19:19:57'),(101,'e-mail',101,'2019-11-22 19:19:57','2019-11-22 19:19:57');
/*!40000 ALTER TABLE `sel_sl_delivery` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `sel_tr__transaction`
--

LOCK TABLES `sel_tr__transaction` WRITE;
/*!40000 ALTER TABLE `sel_tr__transaction` DISABLE KEYS */;
INSERT INTO `sel_tr__transaction` VALUES (1180,'2019-10-27 23:17:56',1002,'PODKŁADKI DYSTANSE P','',210.84,'2019-10-28 00:00:00',1038,'\0',15.00,103.85,3,9,'2019-11-25 22:49:18','2019-11-25 22:49:18'),(1181,'2019-10-27 23:19:32',1002,'siatka elewacyjna po','',210.84,'2019-10-29 00:00:00',1038,'\0',15.00,121.99,3,9,'2019-11-25 22:49:18','2019-11-25 22:49:18'),(1182,'2019-10-28 13:47:04',1002,'siatka elewacyjna po','',265.92,'2019-10-28 22:36:24',1016,'\0',15.00,265.92,3,9,'2019-11-25 22:49:18','2019-11-25 22:49:18'),(1183,'2019-10-28 15:38:24',1002,'siatka elewacyjna po','',265.92,'2019-10-28 00:00:00',1039,'\0',15.00,265.92,3,9,'2019-11-25 22:49:19','2019-11-25 22:49:19'),(1184,'2019-10-28 19:50:49',1002,'siatka podtynkowa el','',900.92,'2019-10-28 00:00:00',1040,'\0',45.00,900.92,3,9,'2019-11-25 22:49:19','2019-11-25 22:49:19'),(1185,'2019-10-28 21:29:58',1002,'siatka elewacyjna po','\0',NULL,NULL,1041,'\0',15.00,851.40,1,2,'2019-11-25 22:49:19','2019-11-25 22:49:19'),(1186,'2019-10-28 21:29:58',1002,'kolki kolek szybkieg','\0',920.38,'2019-10-28 22:35:05',1041,'\0',60.00,83.98,3,9,'2019-11-25 22:49:19','2019-11-25 22:49:19'),(1187,'2019-10-28 22:00:31',1002,'siatka elewacyjna po','',182.28,'2019-10-28 22:34:41',1042,'\0',15.00,182.28,3,9,'2019-11-25 22:49:19','2019-11-25 22:49:19'),(1189,'2019-10-28 21:29:58',1002,'Grupa transakcji 1/2','',920.38,'2019-10-29 15:35:10',1041,'\0',60.00,920.38,3,9,'2019-11-25 22:49:20','2019-11-25 22:49:20');
/*!40000 ALTER TABLE `sel_tr__transaction` ENABLE KEYS */;
UNLOCK TABLES;

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

--
-- Dumping data for table `sel_tr_item`
--

LOCK TABLES `sel_tr_item` WRITE;
/*!40000 ALTER TABLE `sel_tr_item` DISABLE KEYS */;
INSERT INTO `sel_tr_item` VALUES (1117,1180,1485,'',1,88,NULL,'2019-11-25 22:49:28','2019-11-25 22:49:28'),(1118,1181,1217,'',1,106,NULL,'2019-11-25 22:49:28','2019-11-25 22:49:28'),(1119,1182,1416,'',3,83,NULL,'2019-11-25 22:49:28','2019-11-25 22:49:28'),(1120,1183,1416,'',3,83,NULL,'2019-11-25 22:49:28','2019-11-25 22:49:28'),(1121,1184,1005,'',8,106,NULL,'2019-11-25 22:49:29','2019-11-25 22:49:29'),(1122,1185,1411,'',10,83,NULL,'2019-11-25 22:49:29','2019-11-25 22:49:29'),(1123,1186,NULL,'kolki kolek szybkieg',2,11,NULL,'2019-11-25 22:49:29','2019-11-25 22:49:29'),(1124,1187,1416,'',2,83,NULL,'2019-11-25 22:49:29','2019-11-25 22:49:29'),(1126,1189,1411,'',10,83,1185,'2019-11-25 22:49:29','2019-11-25 22:49:29'),(1127,1189,NULL,'kolki kolek szybkieg',2,11,1186,'2019-11-25 22:49:30','2019-11-25 22:49:30');
/*!40000 ALTER TABLE `sel_tr_item` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-02-28 14:50:09
