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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cultural_dressing`
--

LOCK TABLES `cultural_dressing` WRITE;
/*!40000 ALTER TABLE `cultural_dressing` DISABLE KEYS */;
INSERT INTO `cultural_dressing` VALUES (11,6,'',''),(13,5,'','');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `implanted_materials`
--

LOCK TABLES `implanted_materials` WRITE;
/*!40000 ALTER TABLE `implanted_materials` DISABLE KEYS */;
INSERT INTO `implanted_materials` VALUES (12,6,NULL,'','','',''),(14,5,NULL,'','','','');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nurses`
--

LOCK TABLES `nurses` WRITE;
/*!40000 ALTER TABLE `nurses` DISABLE KEYS */;
INSERT INTO `nurses` VALUES (5,'12345','Nomo','ahensananingthemcha@gmail.com','$2y$10$ar9RNnMKSrAFzzFC6f7wsummcU4wwSGlEVl6psu8nR/OVeS9W0uHS','nurse','2025-08-11 20:21:27','2025-08-11 20:46:30','d485ade75ee0b8c58efd7de3eed84063181e93bc41b9c2e27f49c6bc2d6e79a0','2025-08-11 18:16:30'),(6,'123456','Ahen','ahensanark8323@gmail.com','$2y$10$CuBnmDtI5YqVlD6nFGArg.vUmhtKuLWlwUFGJFCSjTAZHQ9Th2Qt2','nurse','2025-08-11 20:39:56','2025-08-11 20:44:04',NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (5,'nomo millo',22,'Male','1343535345','9383103664','23','wdew','dwedew','dwedwed','2025-08-11'),(6,'Ahensana rk',24,'Male','475985354','8731042017','23','asds','asdc','asdc','2025-08-11');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_phone`
--

LOCK TABLES `review_phone` WRITE;
/*!40000 ALTER TABLE `review_phone` DISABLE KEYS */;
INSERT INTO `review_phone` VALUES (11,6,NULL,NULL,NULL,NULL,NULL,NULL),(13,5,NULL,NULL,NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_sutures`
--

LOCK TABLES `review_sutures` WRITE;
/*!40000 ALTER TABLE `review_sutures` DISABLE KEYS */;
INSERT INTO `review_sutures` VALUES (11,6,NULL,NULL),(13,5,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgeons`
--

LOCK TABLES `surgeons` WRITE;
/*!40000 ALTER TABLE `surgeons` DISABLE KEYS */;
INSERT INTO `surgeons` VALUES (1,'Mao oga','2025-08-11 19:30:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_details`
--

LOCK TABLES `surgical_details` WRITE;
/*!40000 ALTER TABLE `surgical_details` DISABLE KEYS */;
INSERT INTO `surgical_details` VALUES (12,6,NULL,'2025-08-20',NULL,'Dr Sangeeta Kar',''),(14,5,NULL,'2025-08-12',NULL,'Dr A N Mahesh Chengappa','');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_skin_preparation`
--

LOCK TABLES `surgical_skin_preparation` WRITE;
/*!40000 ALTER TABLE `surgical_skin_preparation` DISABLE KEYS */;
INSERT INTO `surgical_skin_preparation` VALUES (5,6,NULL,'',NULL,'',NULL),(7,5,NULL,'',NULL,'',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wound_complications`
--

LOCK TABLES `wound_complications` WRITE;
/*!40000 ALTER TABLE `wound_complications` DISABLE KEYS */;
INSERT INTO `wound_complications` VALUES (11,6,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,5,NULL,0,0,0,0,'',NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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

-- Dump completed on 2025-08-12 10:20:45
