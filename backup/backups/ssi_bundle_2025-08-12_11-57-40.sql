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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_audit_logs`
--

LOCK TABLES `admin_audit_logs` WRITE;
/*!40000 ALTER TABLE `admin_audit_logs` DISABLE KEYS */;
INSERT INTO `admin_audit_logs` VALUES (1,'2025-08-12 10:42:23','SYSTEM','SYSTEM_MAINTENANCE','SYSTEM',NULL,'Audit System','Audit log table created and initialized',NULL,'{\"table\": \"admin_audit_logs\", \"columns\": [\"audit_id\", \"timestamp\", \"admin_user\", \"action_type\", \"entity_type\", \"entity_id\", \"entity_name\", \"description\", \"details_before\", \"details_after\", \"ip_address\", \"user_agent\", \"session_id\", \"status\", \"error_message\"]}',NULL,NULL,NULL,'SUCCESS',NULL),(2,'2025-08-12 10:53:00','SYSTEM','BACKUP','BACKUP','ssi_bundle_2025-08-12_07-22-59.sql','Database Backup','Database backup created: ssi_bundle_2025-08-12_07-22-59.sql (20.56 KB)',NULL,'{\"file\":\"ssi_bundle_2025-08-12_07-22-59.sql\",\"size\":\"20.56 KB\",\"path\":\"C:\\\\New Xampp\\\\htdocs\\\\supercareSSibundle\\/backups\\/ssi_bundle_2025-08-12_07-22-59.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(3,'2025-08-12 12:05:27','admin','DELETE','NURSE','10','Nomo','Deleted nurse account: Nomo','{\"id\":10,\"nurse_id\":\"12345\",\"name\":\"Nomo\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(5,'2025-08-12 12:14:40','admin','DELETE','SURGEON','1','Mao oga','Deleted surgeon account: Mao oga','{\"id\":1,\"name\":\"Mao oga\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(6,'2025-08-12 12:14:44','admin','CREATE','SURGEON','4','Mao oga','Created new surgeon account: Mao oga',NULL,'{\"id\":\"4\",\"name\":\"Mao oga\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(7,'2025-08-12 12:16:34','admin','DELETE','SURGEON','3','nomo','Deleted surgeon account: nomo','{\"id\":3,\"name\":\"nomo\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(8,'2025-08-12 12:18:17','admin','UPDATE','NURSE','11','NomooMillo','Updated nurse account: NomooMillo','{\"id\":11,\"nurse_id\":\"12345\",\"name\":\"Nomoo\",\"email\":\"ahensananingthemcha@gmail.com\",\"role\":\"nurse\"}','{\"id\":\"11\",\"nurse_id\":\"12345\",\"name\":\"NomooMillo\",\"email\":\"ahensananingthemcha@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(9,'2025-08-12 12:27:59','system','LOGIN','NURSE','11','NomooMillo','Nurse login successful',NULL,'{\"id\":11,\"nurse_id\":\"12345\",\"name\":\"NomooMillo\",\"email\":\"ahensananingthemcha@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(10,'2025-08-12 12:57:32','admin','CREATE','NURSE','12','Ahen','Created new nurse account: Ahen',NULL,'{\"id\":\"12\",\"nurse_id\":\"1212\",\"name\":\"Ahen\",\"email\":\"llominomo123@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(11,'2025-08-12 13:00:08','admin','DELETE','NURSE','11','NomooMillo','Deleted nurse account: NomooMillo','{\"id\":11,\"nurse_id\":\"12345\",\"name\":\"NomooMillo\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(12,'2025-08-12 13:05:32','12345','UPDATE','PATIENT','6','Ahensana ','Patient form updated: Ahensana ',NULL,'{\"patient_id\":\"6\",\"uhid\":\"475985354\",\"name\":\"Ahensana \",\"action_type\":\"UPDATE\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','7qd49u7p19ef9c1tqd0r3bbl1n','SUCCESS',NULL),(13,'2025-08-12 13:06:25','12345','CREATE','PATIENT','12','chubaaaa','Patient form created: chubaaaa',NULL,'{\"patient_id\":\"12\",\"uhid\":\"1343535345222\",\"name\":\"chubaaaa\",\"action_type\":\"CREATE\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','7qd49u7p19ef9c1tqd0r3bbl1n','SUCCESS',NULL),(22,'2025-08-12 15:20:03','admin','DELETE','NURSE','12','Ahen','Deleted nurse account: Ahen','{\"id\":12,\"nurse_id\":\"1212\",\"name\":\"Ahen\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(23,'2025-08-12 15:20:37','admin','DELETE','SURGEON','4','Mao oga','Deleted surgeon account: Mao oga','{\"id\":4,\"name\":\"Mao oga\"}',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(24,'2025-08-12 15:20:57','admin','CREATE','SURGEON','5','Mao oga','Created new surgeon account: Mao oga',NULL,'{\"id\":\"5\",\"name\":\"Mao oga\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(25,'2025-08-12 15:25:05','admin','CREATE','NURSE','13','Nomo','Created new nurse account: Nomo',NULL,'{\"id\":\"13\",\"nurse_id\":\"12345\",\"name\":\"Nomo\",\"email\":\"ahensananingthemcha@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','','SUCCESS',NULL),(26,'2025-08-12 15:26:41','system','LOGIN','NURSE','13','Nomo','Nurse login successful',NULL,'{\"id\":13,\"nurse_id\":\"12345\",\"name\":\"Nomo\",\"email\":\"ahensananingthemcha@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','epf4ee9d975ubf6etpl1f6ee4p','SUCCESS',NULL);
/*!40000 ALTER TABLE `admin_audit_logs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cultural_dressing`
--

LOCK TABLES `cultural_dressing` WRITE;
/*!40000 ALTER TABLE `cultural_dressing` DISABLE KEYS */;
INSERT INTO `cultural_dressing` VALUES (13,5,'',''),(16,10,'',''),(17,11,'',''),(21,12,'',''),(23,6,'',''),(24,13,'','');
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `implanted_materials`
--

LOCK TABLES `implanted_materials` WRITE;
/*!40000 ALTER TABLE `implanted_materials` DISABLE KEYS */;
INSERT INTO `implanted_materials` VALUES (14,5,NULL,'','','',''),(17,10,NULL,'','','',''),(18,11,NULL,'','','',''),(22,12,NULL,'','','',''),(24,6,NULL,'','','',''),(25,13,NULL,'','','','');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nurses`
--

LOCK TABLES `nurses` WRITE;
/*!40000 ALTER TABLE `nurses` DISABLE KEYS */;
INSERT INTO `nurses` VALUES (13,'12345','Nomo','ahensananingthemcha@gmail.com','$2y$10$6nkLpKElqpJDuXDix7IwKeEqUMt6NiYocUbRy2ifo2bNVcuXnO6NS','nurse','2025-08-12 09:55:05','2025-08-12 09:55:05',NULL,NULL);
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
INSERT INTO `patients` VALUES (5,'nomo millo',22,'Male','1343535345','9383103664','23','wdew','dwedew','dwedwed','2025-08-11'),(6,'Ahensana rk\r\n',24,'Male','475985354','8731042017','23','asds','asdc','asdc','2025-08-12'),(10,'Ogaaaaaaa',23,'Male','134353534512','9383103664','12','advffdsb','gfbgfd','fgb','2025-08-12'),(11,'aidhchsdhc',12,'Male','13435353451212','9383103664','23','efds','sdcsd','','2025-08-12'),(12,'chubaaaa',12,'Male','1343535345222','9383103664','23','efsdg','','','2025-08-12'),(13,'nomooooooooooooooooo',21,'Male','1343535345124','8731042017','12','dcvsfd','sfdv','dsfv','2025-08-12');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_phone`
--

LOCK TABLES `review_phone` WRITE;
/*!40000 ALTER TABLE `review_phone` DISABLE KEYS */;
INSERT INTO `review_phone` VALUES (13,5,NULL,NULL,NULL,NULL,NULL,NULL),(16,10,NULL,NULL,NULL,NULL,NULL,NULL),(17,11,NULL,NULL,NULL,NULL,NULL,NULL),(21,12,NULL,NULL,NULL,NULL,NULL,NULL),(23,6,NULL,NULL,NULL,NULL,NULL,NULL),(24,13,NULL,NULL,NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_sutures`
--

LOCK TABLES `review_sutures` WRITE;
/*!40000 ALTER TABLE `review_sutures` DISABLE KEYS */;
INSERT INTO `review_sutures` VALUES (13,5,NULL,NULL),(16,10,NULL,NULL),(17,11,NULL,NULL),(21,12,NULL,NULL),(23,6,NULL,NULL),(24,13,NULL,NULL);
/*!40000 ALTER TABLE `review_sutures` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgeons`
--

LOCK TABLES `surgeons` WRITE;
/*!40000 ALTER TABLE `surgeons` DISABLE KEYS */;
INSERT INTO `surgeons` VALUES (5,'Mao oga','2025-08-12 09:50:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_details`
--

LOCK TABLES `surgical_details` WRITE;
/*!40000 ALTER TABLE `surgical_details` DISABLE KEYS */;
INSERT INTO `surgical_details` VALUES (14,5,NULL,'2025-08-12',NULL,'Dr A N Mahesh Chengappa',''),(17,10,NULL,'2025-08-12',NULL,'nomo',''),(18,11,NULL,'2025-08-11',NULL,'nomo',''),(22,12,NULL,'2025-08-12',NULL,'Mao oga',''),(24,6,NULL,'2025-08-20',NULL,'Mao oga',''),(25,13,NULL,'2025-08-12',NULL,'Mao oga','');
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_skin_preparation`
--

LOCK TABLES `surgical_skin_preparation` WRITE;
/*!40000 ALTER TABLE `surgical_skin_preparation` DISABLE KEYS */;
INSERT INTO `surgical_skin_preparation` VALUES (7,5,NULL,'',NULL,'',NULL),(10,10,NULL,'',NULL,'',NULL),(11,11,NULL,'',NULL,'',NULL),(15,12,NULL,'',NULL,'',NULL),(17,6,NULL,'',NULL,'',NULL),(18,13,NULL,'',NULL,'',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wound_complications`
--

LOCK TABLES `wound_complications` WRITE;
/*!40000 ALTER TABLE `wound_complications` DISABLE KEYS */;
INSERT INTO `wound_complications` VALUES (13,5,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,10,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,11,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,12,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,6,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,13,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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

-- Dump completed on 2025-08-12 15:27:40
