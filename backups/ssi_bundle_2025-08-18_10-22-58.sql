-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: supercare_ssi
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_audit_logs`
--

DROP TABLE IF EXISTS `admin_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_audit_logs` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT current_timestamp(),
  `admin_user` varchar(100) NOT NULL,
  `action_type` enum('CREATE','UPDATE','DELETE','LOGIN','LOGOUT','BACKUP','RESTORE','EXPORT','IMPORT','PASSWORD_RESET','ACCOUNT_CREATE','ACCOUNT_DELETE','SETTINGS_CHANGE','DATA_ACCESS','SYSTEM_MAINTENANCE') NOT NULL,
  `entity_type` enum('NURSE','SURGEON','PATIENT','BACKUP','SYSTEM','SETTINGS','AUDIT_LOG') NOT NULL,
  `entity_id` varchar(50) DEFAULT NULL,
  `entity_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `details_before` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details_before`)),
  `details_after` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details_after`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `status` enum('SUCCESS','FAILED','PENDING') DEFAULT 'SUCCESS',
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_admin_user` (`admin_user`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_entity_type` (`entity_type`),
  KEY `idx_entity_id` (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_audit_logs`
--

LOCK TABLES `admin_audit_logs` WRITE;
/*!40000 ALTER TABLE `admin_audit_logs` DISABLE KEYS */;
INSERT INTO `admin_audit_logs` VALUES (81,'2025-08-14 11:19:08','admin','CREATE','SURGEON','13','Mao oga','Created new surgeon account: Mao oga',NULL,'{\"id\":\"13\",\"name\":\"Mao oga\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(82,'2025-08-14 11:19:13','admin','DELETE','SURGEON','13','Mao oga','Deleted surgeon account: Mao oga','{\"id\":13,\"name\":\"Mao oga\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(83,'2025-08-14 11:19:16','admin','CREATE','SURGEON','14','Mao oga','Created new surgeon account: Mao oga',NULL,'{\"id\":\"14\",\"name\":\"Mao oga\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(84,'2025-08-14 11:19:44','admin','UPDATE','NURSE','18','Nomooo','Updated nurse account: Nomooo','{\"id\":18,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"llominomo123@gmail.com\",\"role\":\"nurse\"}','{\"id\":\"18\",\"nurse_id\":\"1212\",\"name\":\"Nomooo\",\"email\":\"llominomo123@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(85,'2025-08-14 11:40:12','admin','DELETE','SURGEON','14','Mao oga','Deleted surgeon account: Mao oga','{\"id\":14,\"name\":\"Mao oga\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(86,'2025-08-14 11:40:17','admin','CREATE','SURGEON','15','Mao oga','Created new surgeon account: Mao oga',NULL,'{\"id\":\"15\",\"name\":\"Mao oga\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(87,'2025-08-14 11:40:22','admin','CREATE','SURGEON','16','nomo','Created new surgeon account: nomo',NULL,'{\"id\":\"16\",\"name\":\"nomo\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(88,'2025-08-14 11:40:31','admin','CREATE','SURGEON','17','Mao ogaiu','Created new surgeon account: Mao ogaiu',NULL,'{\"id\":\"17\",\"name\":\"Mao ogaiu\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(89,'2025-08-14 12:17:19','admin','DELETE','SURGEON','16','nomo','Deleted surgeon account: nomo','{\"id\":16,\"name\":\"nomo\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(90,'2025-08-14 13:37:56','SYSTEM','BACKUP','BACKUP','ssi_bundle_2025-08-14_08-07-56.sql','Database Backup','Database backup created: ssi_bundle_2025-08-14_08-07-56.sql (29.91 KB)',NULL,'{\"file\":\"ssi_bundle_2025-08-14_08-07-56.sql\",\"size\":\"29.91 KB\",\"path\":\"C:\\\\New Xampp\\\\htdocs\\\\supercareSSibundle\\/backups\\/ssi_bundle_2025-08-14_08-07-56.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(91,'2025-08-14 16:20:36','SUPER_ADMIN','DELETE','BACKUP','ssi_bundle_2025-08-11_22-03-16.sql','Database Backup','Database backup deleted: ssi_bundle_2025-08-11_22-03-16.sql (17.68 KB)','{\"file\":\"ssi_bundle_2025-08-11_22-03-16.sql\",\"size\":\"17.68 KB\",\"path\":\"C:\\\\New Xampp\\\\htdocs\\\\supercareSSibundle\\/backups\\/ssi_bundle_2025-08-11_22-03-16.sql\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','76faidchsdlidpim9tjtlqtkor','SUCCESS',NULL),(92,'2025-08-14 16:20:40','SUPER_ADMIN','DELETE','BACKUP','ssi_bundle_2025-08-11_22-05-08.sql','Database Backup','Database backup deleted: ssi_bundle_2025-08-11_22-05-08.sql (17.68 KB)','{\"file\":\"ssi_bundle_2025-08-11_22-05-08.sql\",\"size\":\"17.68 KB\",\"path\":\"C:\\\\New Xampp\\\\htdocs\\\\supercareSSibundle\\/backups\\/ssi_bundle_2025-08-11_22-05-08.sql\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','76faidchsdlidpim9tjtlqtkor','SUCCESS',NULL),(93,'2025-08-18 12:31:46','admin','DELETE','SURGEON','17','Mao ogaiu','Deleted surgeon account: Mao ogaiu','{\"id\":17,\"name\":\"Mao ogaiu\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL);
/*!40000 ALTER TABLE `admin_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_login_logs`
--

DROP TABLE IF EXISTS `admin_login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ip_address` (`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_login_logs`
--

LOCK TABLES `admin_login_logs` WRITE;
/*!40000 ALTER TABLE `admin_login_logs` DISABLE KEYS */;
INSERT INTO `admin_login_logs` VALUES (1,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 05:26:40'),(2,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 05:27:22'),(3,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 05:31:38'),(4,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 05:43:42'),(5,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 05:48:35'),(6,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 05:55:13'),(7,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:05:26'),(8,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:06:43'),(9,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:07:24'),(11,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:09:32'),(12,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:10:03'),(13,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:15:30'),(19,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:29:55'),(20,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:35:16'),(21,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1','2025-08-14 06:35:54'),(22,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:45:00'),(23,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 06:45:50'),(24,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:14:01'),(25,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:14:18'),(26,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:14:53'),(27,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:19:02'),(28,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:20:55'),(29,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1','2025-08-14 11:21:10'),(30,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:21:57'),(31,'supercareadmin@gmail.com','failed','Invalid password','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:25:46'),(32,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:25:50'),(33,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:27:23'),(34,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:28:32'),(35,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:32:50'),(36,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:35:26'),(37,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-14 11:37:18'),(38,'supercareadmin@gmail.com','failed','Invalid password','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:46:58'),(39,'supercareadmin@gmail.com','failed','Invalid password','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:47:00'),(40,'supercareadmin@gmail.com','failed','Invalid password','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:47:26'),(41,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:52:57'),(42,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:53:24'),(43,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:54:13'),(44,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:54:59'),(45,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:56:06'),(46,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 06:58:55'),(47,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:22:10'),(48,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:27:38'),(49,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:27:57'),(50,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:32:40'),(51,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:33:32'),(52,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1','2025-08-18 07:34:09'),(53,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:40:29'),(54,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:43:51'),(55,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:44:05'),(56,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:44:30'),(57,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:51:59'),(58,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 07:53:07'),(59,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 08:06:35'),(60,'weoidhj@gmail.com','success','Admin account created','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:02:02'),(61,'weoidhj@gmail.com','success','Admin account deleted','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:02:07'),(62,'supercareadmin@gmail.com','success','Admin account created','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:02:43'),(63,'supercareadmin@gmail.com','success','Admin account deleted','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:02:51'),(64,'admin@supercare.com','success','Admin account deleted','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:08:19'),(65,'supercareadmin@gmail.com','success','Admin account created','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:08:46'),(66,'supercareadmin@gmail.com','success','Admin account deleted','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:08:53'),(67,'supercareadmin@gmail.com','success','Admin account created','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:22:44'),(68,'supercareadmin@gmail.com','success','Admin account deleted','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-18 10:22:51');
/*!40000 ALTER TABLE `admin_login_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin_username` (`admin_username`),
  UNIQUE KEY `unique_admin_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `antibiotic_usage`
--

DROP TABLE IF EXISTS `antibiotic_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `antibiotic_usage` (
  `antibiotic_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `serial_no` int(11) DEFAULT NULL,
  `drug_name` text DEFAULT NULL,
  `dosage_route_frequency` text DEFAULT NULL,
  `started_on` date DEFAULT NULL,
  `stopped_on` date DEFAULT NULL,
  PRIMARY KEY (`antibiotic_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `antibiotic_usage_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `antibiotic_usage`
--

LOCK TABLES `antibiotic_usage` WRITE;
/*!40000 ALTER TABLE `antibiotic_usage` DISABLE KEYS */;
/*!40000 ALTER TABLE `antibiotic_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL,
  `entity` varchar(50) NOT NULL,
  `entity_id` varchar(50) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_action` (`action`),
  KEY `idx_entity` (`entity`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cultural_dressing`
--

DROP TABLE IF EXISTS `cultural_dressing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cultural_dressing` (
  `cultural_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `cultural_swap` text DEFAULT NULL,
  `dressing_finding` text DEFAULT NULL,
  PRIMARY KEY (`cultural_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `cultural_dressing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cultural_dressing`
--

LOCK TABLES `cultural_dressing` WRITE;
/*!40000 ALTER TABLE `cultural_dressing` DISABLE KEYS */;
INSERT INTO `cultural_dressing` VALUES (13,5,'',''),(16,10,'',''),(17,11,'',''),(21,12,'',''),(24,13,'',''),(25,6,'','');
/*!40000 ALTER TABLE `cultural_dressing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drains`
--

DROP TABLE IF EXISTS `drains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drains` (
  `drain_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `drain_used` enum('Yes','No') DEFAULT NULL,
  `drain_description` text DEFAULT NULL,
  `drain_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`drain_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `drains_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drains`
--

LOCK TABLES `drains` WRITE;
/*!40000 ALTER TABLE `drains` DISABLE KEYS */;
/*!40000 ALTER TABLE `drains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `implanted_materials`
--

DROP TABLE IF EXISTS `implanted_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `implanted_materials` (
  `implant_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `implanted_used` enum('Yes','No') DEFAULT NULL,
  `metal` text DEFAULT NULL,
  `graft` text DEFAULT NULL,
  `patch` text DEFAULT NULL,
  `shunt_stent` text DEFAULT NULL,
  PRIMARY KEY (`implant_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `implanted_materials_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `implanted_materials`
--

LOCK TABLES `implanted_materials` WRITE;
/*!40000 ALTER TABLE `implanted_materials` DISABLE KEYS */;
INSERT INTO `implanted_materials` VALUES (14,5,NULL,'','','',''),(17,10,NULL,'','','',''),(18,11,NULL,'','','',''),(22,12,NULL,'','','',''),(25,13,NULL,'','','',''),(26,6,NULL,'','','','');
/*!40000 ALTER TABLE `implanted_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nurses`
--

DROP TABLE IF EXISTS `nurses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nurses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nurse_id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('nurse','supervisor','admin') DEFAULT 'nurse',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nurse_id` (`nurse_id`),
  KEY `idx_reset_token` (`reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nurses`
--

LOCK TABLES `nurses` WRITE;
/*!40000 ALTER TABLE `nurses` DISABLE KEYS */;
INSERT INTO `nurses` VALUES (18,'1212','Nomooo','llominomo123@gmail.com','$2y$10$FxSvUqwgJWyRUCLd9BbtIu12CRA9ElyGxudibm2mK8Q5Xzim9jwYG','nurse','2025-08-13 10:46:41','2025-08-14 11:13:30','14d87991e1d856ebfd6d342d346b855e65fd593982fd3e9a8f6bd113bf017f72','2025-08-14 08:43:30');
/*!40000 ALTER TABLE `nurses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `uhid` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `bed_ward` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `primary_diagnosis` text DEFAULT NULL,
  `surgical_procedure` text DEFAULT NULL,
  `date_completed` date DEFAULT NULL,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (5,'nomo millo',22,'Male','1343535345','9383103664','23','wdew','dwedew','dwedwed','2025-08-11'),(6,'Ahensana rajkumar\r\n',24,'Male','475985354','8731042017','23','asds','asdc','asdc','2025-08-13'),(10,'Ogaaaaaaa',23,'Male','134353534512','9383103664','12','advffdsb','gfbgfd','fgb','2025-08-12'),(11,'aidhchsdhc',12,'Male','13435353451212','9383103664','23','efds','sdcsd','','2025-08-12'),(12,'chubaaaa',12,'Male','1343535345222','9383103664','23','efsdg','','','2025-08-12'),(13,'nomooooooooooooooooo',21,'Male','1343535345124','8731042017','12','dcvsfd','sfdv','dsfv','2025-08-12');
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_operative_monitoring`
--

DROP TABLE IF EXISTS `post_operative_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_operative_monitoring` (
  `post_op_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `monitoring_date` date DEFAULT NULL,
  `dosage` text DEFAULT NULL,
  `discharge_fluid` text DEFAULT NULL,
  `tenderness_pain` text DEFAULT NULL,
  `swelling` text DEFAULT NULL,
  `fever` text DEFAULT NULL,
  PRIMARY KEY (`post_op_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `post_operative_monitoring_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_operative_monitoring`
--

LOCK TABLES `post_operative_monitoring` WRITE;
/*!40000 ALTER TABLE `post_operative_monitoring` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_operative_monitoring` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_phone`
--

DROP TABLE IF EXISTS `review_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_phone` (
  `review_phone_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `patient_identification` enum('Yes','No') DEFAULT NULL,
  `pain` enum('Yes','No') DEFAULT NULL,
  `pus` enum('Yes','No') DEFAULT NULL,
  `bleeding` enum('Yes','No') DEFAULT NULL,
  `other` enum('Yes','No') DEFAULT NULL,
  PRIMARY KEY (`review_phone_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `review_phone_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_phone`
--

LOCK TABLES `review_phone` WRITE;
/*!40000 ALTER TABLE `review_phone` DISABLE KEYS */;
INSERT INTO `review_phone` VALUES (13,5,NULL,NULL,NULL,NULL,NULL,NULL),(16,10,NULL,NULL,NULL,NULL,NULL,NULL),(17,11,NULL,NULL,NULL,NULL,NULL,NULL),(21,12,NULL,NULL,NULL,NULL,NULL,NULL),(24,13,NULL,NULL,NULL,NULL,NULL,NULL),(25,6,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `review_phone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_sutures`
--

DROP TABLE IF EXISTS `review_sutures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_sutures` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `review_on` date DEFAULT NULL,
  `sutures_removed_on` date DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `review_sutures_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_sutures`
--

LOCK TABLES `review_sutures` WRITE;
/*!40000 ALTER TABLE `review_sutures` DISABLE KEYS */;
INSERT INTO `review_sutures` VALUES (13,5,NULL,NULL),(16,10,NULL,NULL),(17,11,NULL,NULL),(21,12,NULL,NULL),(24,13,NULL,NULL),(25,6,NULL,NULL);
/*!40000 ALTER TABLE `review_sutures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `super_admin_otp_logs`
--

DROP TABLE IF EXISTS `super_admin_otp_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `super_admin_otp_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_count` int(11) DEFAULT 1,
  `status` enum('pending','used','expired','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_admin_otp_logs`
--

LOCK TABLES `super_admin_otp_logs` WRITE;
/*!40000 ALTER TABLE `super_admin_otp_logs` DISABLE KEYS */;
INSERT INTO `super_admin_otp_logs` VALUES (12,'ahensananingthemcha@gmail.com','455536','::1',1,'expired','2025-08-18 05:19:29',NULL),(13,'ahensananingthemcha@gmail.com','409903','::1',1,'used','2025-08-18 05:21:15','2025-08-18 05:21:35'),(14,'ahensananingthemcha@gmail.com','654526','::1',1,'used','2025-08-18 05:23:53','2025-08-18 05:24:12'),(15,'ahensananingthemcha@gmail.com','696474','::1',1,'used','2025-08-18 05:43:01','2025-08-18 05:43:22'),(16,'ahensananingthemcha@gmail.com','137065','::1',1,'used','2025-08-18 05:53:52','2025-08-18 05:54:46'),(17,'ahensananingthemcha@gmail.com','301197','::1',1,'expired','2025-08-18 05:54:06',NULL),(18,'ahensananingthemcha@gmail.com','940255','::1',1,'used','2025-08-18 05:55:05','2025-08-18 05:56:32'),(19,'ahensananingthemcha@gmail.com','649350','::1',1,'expired','2025-08-18 05:55:59',NULL),(20,'superadmin@supercare.com','339270','127.0.0.1',1,'pending','2025-08-18 06:01:19',NULL),(21,'ahensananingthemcha@gmail.com','347266','::1',1,'expired','2025-08-18 06:09:12',NULL),(22,'ahensananingthemcha@gmail.com','127318','::1',1,'used','2025-08-18 06:09:28','2025-08-18 06:10:28'),(23,'ahensananingthemcha@gmail.com','235313','::1',1,'used','2025-08-18 06:51:17','2025-08-18 06:52:12'),(24,'ahensananingthemcha@gmail.com','752911','::1',1,'used','2025-08-18 09:45:24','2025-08-18 09:45:46'),(25,'ahensananingthemcha@gmail.com','675032','::1',1,'used','2025-08-18 09:52:41','2025-08-18 09:53:14');
/*!40000 ALTER TABLE `super_admin_otp_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `super_admin_users`
--

DROP TABLE IF EXISTS `super_admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `super_admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_admin_users`
--

LOCK TABLES `super_admin_users` WRITE;
/*!40000 ALTER TABLE `super_admin_users` DISABLE KEYS */;
INSERT INTO `super_admin_users` VALUES (2,'ahensananingthemcha@gmail.com','$2y$10$.kY3I4IWUbAJvOctxA1IQ.B3YV07ZpaAyhmbiqKYHVHmVG0EFItwq','SuperAdmin','active',NULL,NULL,'2025-08-18 09:53:14','2025-08-14 09:38:15','2025-08-18 09:53:14');
/*!40000 ALTER TABLE `super_admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surgeons`
--

DROP TABLE IF EXISTS `surgeons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surgeons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgeons`
--

LOCK TABLES `surgeons` WRITE;
/*!40000 ALTER TABLE `surgeons` DISABLE KEYS */;
INSERT INTO `surgeons` VALUES (15,'Mao oga','2025-08-14 06:10:17');
/*!40000 ALTER TABLE `surgeons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surgical_details`
--

DROP TABLE IF EXISTS `surgical_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surgical_details` (
  `surgical_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `doa` date DEFAULT NULL,
  `dos` date DEFAULT NULL,
  `dod` date DEFAULT NULL,
  `surgeon` varchar(255) DEFAULT NULL,
  `operation_duration` text DEFAULT NULL,
  PRIMARY KEY (`surgical_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `surgical_details_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_details`
--

LOCK TABLES `surgical_details` WRITE;
/*!40000 ALTER TABLE `surgical_details` DISABLE KEYS */;
INSERT INTO `surgical_details` VALUES (14,5,NULL,'2025-08-12',NULL,'Dr A N Mahesh Chengappa',''),(17,10,NULL,'2025-08-12',NULL,'nomo',''),(18,11,NULL,'2025-08-11',NULL,'nomo',''),(22,12,NULL,'2025-08-12',NULL,'Mao oga',''),(25,13,NULL,'2025-08-12',NULL,'Mao oga',''),(26,6,NULL,'2025-08-20',NULL,'Mao oga','');
/*!40000 ALTER TABLE `surgical_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `surgical_skin_preparation`
--

DROP TABLE IF EXISTS `surgical_skin_preparation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surgical_skin_preparation` (
  `preparation_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `pre_op_bath` enum('Yes','No') DEFAULT NULL,
  `pre_op_bath_reason` text DEFAULT NULL,
  `hair_removal` enum('Razor','Trimmer','Not Done') DEFAULT NULL,
  `hair_removal_reason` text DEFAULT NULL,
  `hair_removal_location` enum('Ward','ICU/HDU','OT/LR') DEFAULT NULL,
  PRIMARY KEY (`preparation_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `surgical_skin_preparation_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_skin_preparation`
--

LOCK TABLES `surgical_skin_preparation` WRITE;
/*!40000 ALTER TABLE `surgical_skin_preparation` DISABLE KEYS */;
INSERT INTO `surgical_skin_preparation` VALUES (7,5,NULL,'',NULL,'',NULL),(10,10,NULL,'',NULL,'',NULL),(11,11,NULL,'',NULL,'',NULL),(15,12,NULL,'',NULL,'',NULL),(18,13,NULL,'',NULL,'',NULL),(19,6,NULL,'',NULL,'',NULL);
/*!40000 ALTER TABLE `surgical_skin_preparation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wound_complications`
--

DROP TABLE IF EXISTS `wound_complications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wound_complications` (
  `complication_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `complication_date` date DEFAULT NULL,
  `wound_dehiscence` tinyint(1) DEFAULT NULL,
  `allergic_reaction` tinyint(1) DEFAULT NULL,
  `bleeding_haemorrhage` tinyint(1) DEFAULT NULL,
  `other_complication` tinyint(1) DEFAULT NULL,
  `other_specify` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `superficial_ssi` tinyint(1) DEFAULT NULL,
  `deep_si` tinyint(1) DEFAULT NULL,
  `organ_space_ssi` tinyint(1) DEFAULT NULL,
  `purulent_discharge_superficial` tinyint(1) DEFAULT NULL,
  `purulent_discharge_deep` tinyint(1) DEFAULT NULL,
  `purulent_discharge_organ` tinyint(1) DEFAULT NULL,
  `organism_identified_superficial` tinyint(1) DEFAULT NULL,
  `organism_identified_organ` tinyint(1) DEFAULT NULL,
  `clinical_diagnosis_ssi` tinyint(1) DEFAULT NULL,
  `deep_incision_reopening` tinyint(1) DEFAULT NULL,
  `abscess_evidence_organ` tinyint(1) DEFAULT NULL,
  `deliberate_opening_symptoms` tinyint(1) DEFAULT NULL,
  `abscess_evidence_deep` tinyint(1) DEFAULT NULL,
  `not_infected_conditions` tinyint(1) DEFAULT NULL,
  `surgeon_opinion_superficial` text DEFAULT NULL,
  `surgeon_opinion_deep` text DEFAULT NULL,
  `surgeon_opinion_organ` text DEFAULT NULL,
  PRIMARY KEY (`complication_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `wound_complications_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wound_complications`
--

LOCK TABLES `wound_complications` WRITE;
/*!40000 ALTER TABLE `wound_complications` DISABLE KEYS */;
INSERT INTO `wound_complications` VALUES (13,5,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,10,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,11,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,12,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,13,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,6,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `wound_complications` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 15:52:58
