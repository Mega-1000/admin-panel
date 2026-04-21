-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: mega1000
-- ------------------------------------------------------
-- Server version	5.7.27-0ubuntu0.18.04.1

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
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `multiplier_of_the_number_of_pieces` int(11) DEFAULT NULL COMMENT 'Mnożnik ilości sztuk',
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_for_website` varchar(2083) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_trade_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki handlowej',
  `weight_collective_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki zbiorczej',
  `weight_biggest_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki największej',
  `weight_base_unit` double(8,2) DEFAULT NULL COMMENT 'Waga jednostki podstawowej',
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Film',
  `manufacturer_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link strony"',
  `priority` int(11) DEFAULT NULL,
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ACTIVE','PENDING') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_photo_promoted` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie polecamy',
  `description_photo_table` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie tabela',
  `description_photo_contact` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie kontakt',
  `description_photo_details` text COLLATE utf8mb4_unicode_ci COMMENT 'Opis zdjęcie szczegóły',
  `set_symbol` text COLLATE utf8mb4_unicode_ci COMMENT 'Symbol kompletu',
  `set_rule` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reguła kompletu',
  `manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'producent',
  `additional_info1` text COLLATE utf8mb4_unicode_ci COMMENT 'Uwagi 1',
  `additional_info2` text COLLATE utf8mb4_unicode_ci COMMENT 'Uwagi 2',
  `product_symbol_on_collective_box` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Symbol produktu na opakowaniu zbiorczym',
  `product_name_on_collective_box` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa produktu na opakowaniu zbiorczym',
  `product_name_supplier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa dostawcy',
  `product_name_supplier_on_documents` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa produktu u dostawcy na dokumentach',
  `product_symbol_on_supplier_documents` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Symbol towaru u dostawcy na dokumentach',
  `product_name_manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa towaru producenta',
  `symbol_name_manufacturer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Symbol towaru producenta',
  `pricelist_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nazwa cennika',
  `calculator_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rodzaj kalkulatora na stronie',
  `product_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'url_kat1 + url_kat2 + url_kat3 + url_kat4 + url_kat5 ze starej bazy',
  `product_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Grupowanie produktów dla przestawienia oferty[wariacji]',
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
  `show_on_page` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=322592 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-10-05 12:20:45
