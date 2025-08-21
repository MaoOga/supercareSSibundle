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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_audit_logs`
--

LOCK TABLES `admin_audit_logs` WRITE;
/*!40000 ALTER TABLE `admin_audit_logs` DISABLE KEYS */;
INSERT INTO `admin_audit_logs` VALUES (45,'2025-08-19 17:23:04','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"30\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"Full Metal Alchemist\",\"graft\":\"He is already Full Metal\",\"Patch\":\"Bruh wants to make him Terminator Part 4\",\"Shunt\":\"No need\",\"drain-used\":\"Yes\",\"drain_1\":\"sdfghjkl;\",\"drain_2\":\"dcfvgbhnmjk,.\",\"drain_3\":\"dfghjkl;\'\",\"drain_4\":\"8765re\",\"drain_5\":\"poiuytre\",\"drug-name_1\":\"Cocaine\",\"dosage_1\":\"Every minute\",\"antibiotic_usage\":{\"startedon\":\"01\\/08\\/2025\",\"stoppeon\":\"31\\/08\\/2035\"},\"drug-name_2\":\"Brown Sugar\",\"dosage_2\":\"Every Time he needs\",\"drug-name_3\":\"Heroine\",\"dosage_3\":\"Every Second\",\"drug-name_4\":\"Angel Dust\",\"dosage_4\":\"Every Milisecond just give him a drip of angel dust\",\"post-operative\":{\"date\":\"13\\/08\\/2025\"},\"post-dosage_1\":\"no need\",\"type-ofdischarge_1\":\"45\",\"tenderness-pain_1\":\"y\",\"swelling_1\":\"y\",\"Fever_1\":\"n\",\"post-dosage_2\":\"safsdg\",\"type-ofdischarge_2\":\"23\",\"tenderness-pain_2\":\"n\",\"swelling_2\":\"y\",\"Fever_2\":\"n\",\"post-dosage_3\":\"no need\",\"type-ofdischarge_3\":\"67\",\"tenderness-pain_3\":\"y\",\"swelling_3\":\"n\",\"Fever_3\":\"y\",\"post-dosage_4\":\"safdg\",\"type-ofdischarge_4\":\"213\",\"tenderness-pain_4\":\"b\",\"swelling_4\":\"n\",\"Fever_4\":\"n\",\"Cultural-Swap\":\"awsdzfghjiouytjhrgewdsaDEFRGTYHJFTDRESWAFEGRHJFDRSEAFEGSAFGSFGSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"Dressing-Finding\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"Wound-Complication_Date\":\"07\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"WAESDRGTYUITDGSFSDFSD\",\"WoundD-Notes\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SurgeonOpinion2\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SurgeonOpinion3\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"ReviewOn\":\"21\\/08\\/2025\",\"SuturesROn\":\"29\\/08\\/2025\",\"RevieworPhoneDate\":\"04\\/08\\/2025\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"No\",\"reviewbleed\":\"Yes\",\"reviewother\":\"No\",\"date_completed\":\"19\\/08\\/2025\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','76f5m7qrtdl0lb2oq8dnd6pvbd','SUCCESS',NULL),(46,'2025-08-19 17:23:47','1212','LOGOUT','NURSE','1','Nomo','Nurse logged out: Nomo (1212)',NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','76f5m7qrtdl0lb2oq8dnd6pvbd','SUCCESS',NULL),(47,'2025-08-19 17:23:55','system','LOGIN','NURSE','3','SuperAdmin','Nurse login successful',NULL,'{\"id\":3,\"nurse_id\":\"123456\",\"name\":\"SuperAdmin\",\"email\":\"ahensananingthemcha@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','iehkmfhh6begtgntbl5cl5tkdo','SUCCESS',NULL),(48,'2025-08-19 17:24:03','123456','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"Full Metal Alchemist\",\"graft\":\"He is already Full Metal\",\"Patch\":\"Bruh wants to make him Terminator Part 4\",\"Shunt\":\"No need\",\"drain-used\":\"Yes\",\"drain_1\":\"sdfghjkl;\",\"drain_2\":\"dcfvgbhnmjk,.\",\"drain_3\":\"dfghjkl;\'\",\"drain_4\":\"8765re\",\"drain_5\":\"poiuytre\",\"drug-name_1\":\"Cocaine\",\"dosage_1\":\"Every minute\",\"antibiotic_usage\":{\"startedon\":\"01\\/08\\/2025\",\"stoppeon\":\"31\\/08\\/2035\"},\"drug-name_2\":\"Brown Sugar\",\"dosage_2\":\"Every Time he needs\",\"drug-name_3\":\"Heroine\",\"dosage_3\":\"Every Second\",\"drug-name_4\":\"Angel Dust\",\"dosage_4\":\"Every Milisecond just give him a drip of angel dust\",\"post-operative\":{\"date\":\"13\\/08\\/2025\"},\"post-dosage_1\":\"no need\",\"type-ofdischarge_1\":\"45\",\"tenderness-pain_1\":\"y\",\"swelling_1\":\"y\",\"Fever_1\":\"n\",\"post-dosage_2\":\"safsdg\",\"type-ofdischarge_2\":\"23\",\"tenderness-pain_2\":\"n\",\"swelling_2\":\"y\",\"Fever_2\":\"n\",\"post-dosage_3\":\"no need\",\"type-ofdischarge_3\":\"67\",\"tenderness-pain_3\":\"y\",\"swelling_3\":\"n\",\"Fever_3\":\"y\",\"post-dosage_4\":\"safdg\",\"type-ofdischarge_4\":\"213\",\"tenderness-pain_4\":\"b\",\"swelling_4\":\"n\",\"Fever_4\":\"n\",\"Cultural-Swap\":\"awsdzfghjiouytjhrgewdsaDEFRGTYHJFTDRESWAFEGRHJFDRSEAFEGSAFGSFGSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"Dressing-Finding\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"Wound-Complication_Date\":\"07\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"WAESDRGTYUITDGSFSDFSD\",\"WoundD-Notes\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SurgeonOpinion2\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SurgeonOpinion3\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"ReviewOn\":\"21\\/08\\/2025\",\"SuturesROn\":\"29\\/08\\/2025\",\"RevieworPhoneDate\":\"04\\/08\\/2025\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"No\",\"reviewbleed\":\"Yes\",\"reviewother\":\"No\",\"date_completed\":\"19\\/08\\/2025\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','iehkmfhh6begtgntbl5cl5tkdo','SUCCESS',NULL),(49,'2025-08-19 17:26:38','123456','LOGOUT','NURSE','3','SuperAdmin','Nurse logged out: SuperAdmin (123456)',NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','iehkmfhh6begtgntbl5cl5tkdo','SUCCESS',NULL),(50,'2025-08-20 10:31:52','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','atad70jq39t6fso62dogl0h8ba','SUCCESS',NULL),(51,'2025-08-20 10:32:36','1212','LOGOUT','NURSE','1','Nomo','Nurse logged out: Nomo (1212)',NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','atad70jq39t6fso62dogl0h8ba','SUCCESS',NULL),(52,'2025-08-20 11:50:43','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','lg2khsjcs26bnoumo82r6jevjg','SUCCESS',NULL),(53,'2025-08-20 12:18:52','SYSTEM','BACKUP','BACKUP','ssi_bundle_2025-08-20_06-48-52.sql','Database Backup','Database backup created: ssi_bundle_2025-08-20_06-48-52.sql (48.17 KB)',NULL,'{\"file\":\"ssi_bundle_2025-08-20_06-48-52.sql\",\"size\":\"48.17 KB\",\"path\":\"C:\\\\New Xampp\\\\htdocs\\\\supercareSSibundle\\/backups\\/ssi_bundle_2025-08-20_06-48-52.sql\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','','SUCCESS',NULL),(54,'2025-08-21 12:38:21','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','d2hash9d7j114dv6sot622mphs','SUCCESS',NULL),(55,'2025-08-21 12:50:28','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"100kg\",\"risk_factor_height\":\"5.5\",\"risk_factor_steroids\":\"yes\",\"risk_factor_tuberculosis\":\"no\",\"risk_factor_others\":\"laahhh\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"Full Metal Alchemist\",\"graft\":\"He is already Full Metal\",\"Patch\":\"Bruh wants to make him Terminator Part 4\",\"Shunt\":\"No need\",\"drain-used\":\"Yes\",\"drain_1\":\"sdfghjkl;\",\"drain_2\":\"dcfvgbhnmjk,.\",\"drain_3\":\"dfghjkl;\'\",\"drain_4\":\"8765re\",\"drain_5\":\"poiuytre\",\"drug-name_1\":\"Cocaine\",\"dosage_1\":\"Every minute\",\"antibiotic_usage\":{\"startedon\":\"01\\/08\\/2025\",\"stoppeon\":\"31\\/08\\/2035\"},\"drug-name_2\":\"Brown Sugar\",\"dosage_2\":\"Every Time he needs\",\"drug-name_3\":\"Heroine\",\"dosage_3\":\"Every Second\",\"drug-name_4\":\"Angel Dust\",\"dosage_4\":\"Every Milisecond just give him a drip of angel dust\",\"post-operative\":{\"date\":\"13\\/08\\/2025\"},\"post-dosage_1\":\"no need\",\"type-ofdischarge_1\":\"45\",\"tenderness-pain_1\":\"y\",\"swelling_1\":\"y\",\"Fever_1\":\"n\",\"post-dosage_2\":\"safsdg\",\"type-ofdischarge_2\":\"23\",\"tenderness-pain_2\":\"n\",\"swelling_2\":\"y\",\"Fever_2\":\"n\",\"post-dosage_3\":\"no need\",\"type-ofdischarge_3\":\"67\",\"tenderness-pain_3\":\"y\",\"swelling_3\":\"n\",\"Fever_3\":\"y\",\"post-dosage_4\":\"safdg\",\"type-ofdischarge_4\":\"213\",\"tenderness-pain_4\":\"b\",\"swelling_4\":\"n\",\"Fever_4\":\"n\",\"Cultural-Swap\":\"awsdzfghjiouytjhrgewdsaDEFRGTYHJFTDRESWAFEGRHJFDRSEAFEGSAFGSFGSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"Dressing-Finding\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"Wound-Complication_Date\":\"07\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"WAESDRGTYUITDGSFSDFSD\",\"WoundD-Notes\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-1b\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SurgeonOpinion2\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"SurgeonOpinion3\":\"SDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGFSDAFGDHJKJGFSDGHJFDSFGSGGDSGSDFGF\",\"ReviewOn\":\"21\\/08\\/2025\",\"SuturesROn\":\"29\\/08\\/2025\",\"infection_prevention_notes\":\"iuhfvdhvsdvdfuuh\\r\\nzxjfvhkjxd\",\"RevieworPhoneDate\":\"04\\/08\\/2025\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"No\",\"reviewbleed\":\"Yes\",\"reviewother\":\"No\",\"date_completed\":\"19\\/08\\/2025\",\"signature\":\"ogaa\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','d2hash9d7j114dv6sot622mphs','SUCCESS',NULL),(56,'2025-08-21 13:13:59','1212','LOGOUT','NURSE','1','Nomo','Nurse logged out: Nomo (1212)',NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','d2hash9d7j114dv6sot622mphs','SUCCESS',NULL),(57,'2025-08-21 13:14:09','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(58,'2025-08-21 13:17:39','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"tgdf\",\"risk_factor_height\":\"dsfgd\",\"risk_factor_steroids\":\"ffgdf\",\"risk_factor_tuberculosis\":\"dfg\",\"risk_factor_others\":\"ddfg\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"metal\":\"\",\"graft\":\"\",\"Patch\":\"\",\"Shunt\":\"\",\"drain_1\":\"\",\"drain_2\":\"\",\"drain_3\":\"\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(59,'2025-08-21 13:19:54','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"\",\"risk_factor_height\":\"\",\"risk_factor_steroids\":\"\",\"risk_factor_tuberculosis\":\"\",\"risk_factor_others\":\"\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"metal\":\"\",\"graft\":\"\",\"Patch\":\"\",\"Shunt\":\"\",\"drain_1\":\"\",\"drain_2\":\"\",\"drain_3\":\"\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(60,'2025-08-21 13:21:49','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"\",\"risk_factor_height\":\"\",\"risk_factor_steroids\":\"\",\"risk_factor_tuberculosis\":\"\",\"risk_factor_others\":\"\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"Yes\",\"metal\":\"dfv\",\"graft\":\"svdfv\",\"Patch\":\"dcv\",\"Shunt\":\"fvd\",\"drain-used\":\"Yes\",\"drain_1\":\"dfvdf\",\"drain_2\":\"vdf\",\"drain_3\":\"dfvd\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(61,'2025-08-21 13:22:45','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"dcs\",\"risk_factor_height\":\"csd\",\"risk_factor_steroids\":\"csdc\",\"risk_factor_tuberculosis\":\"sdcs\",\"risk_factor_others\":\"dc\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"metal\":\"\",\"graft\":\"\",\"Patch\":\"\",\"Shunt\":\"\",\"drain_1\":\"\",\"drain_2\":\"\",\"drain_3\":\"\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(62,'2025-08-21 13:23:16','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"\",\"risk_factor_height\":\"\",\"risk_factor_steroids\":\"\",\"risk_factor_tuberculosis\":\"\",\"risk_factor_others\":\"\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"metal\":\"\",\"graft\":\"\",\"Patch\":\"\",\"Shunt\":\"\",\"drain_1\":\"\",\"drain_2\":\"\",\"drain_3\":\"\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(63,'2025-08-21 13:23:44','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"\",\"risk_factor_height\":\"\",\"risk_factor_steroids\":\"\",\"risk_factor_tuberculosis\":\"\",\"risk_factor_others\":\"\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"metal\":\"\",\"graft\":\"\",\"Patch\":\"\",\"Shunt\":\"\",\"drain_1\":\"\",\"drain_2\":\"\",\"drain_3\":\"\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"ogaa\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(64,'2025-08-21 13:28:20','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"dsfgvd\",\"risk_factor_height\":\"fdvd\",\"risk_factor_steroids\":\"fv\",\"risk_factor_tuberculosis\":\"dfv\",\"risk_factor_others\":\"vdf\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"fvd\",\"graft\":\"dfv\",\"Patch\":\"dfv\",\"Shunt\":\"dfv\",\"drain-used\":\"No\",\"drain_1\":\"dfv\",\"drain_2\":\"dfv\",\"drain_3\":\"vdf\",\"drain_4\":\"dvf\",\"drug-name_1\":\"dfv\",\"dosage_1\":\"dfv\",\"antibiotic_usage\":{\"startedon\":\"12\\/08\\/2025\",\"stoppeon\":\"05\\/08\\/2025\"},\"drug-name_2\":\"dfv\",\"dosage_2\":\"dfv\",\"drug-name_3\":\"dvf\",\"dosage_3\":\"dfv\",\"post-operative\":{\"date\":\"05\\/08\\/2025\"},\"post-dosage_1\":\"dvf\",\"type-ofdischarge_1\":\"fdvv\",\"tenderness-pain_1\":\" cvb \",\"swelling_1\":\"bcv\",\"Fever_1\":\"vcb\",\"post-dosage_2\":\"fdv\",\"type-ofdischarge_2\":\"fdv\",\"tenderness-pain_2\":\"bvc\",\"swelling_2\":\"b\",\"Fever_2\":\"b\",\"post-dosage_3\":\"bcbc\",\"type-ofdischarge_3\":\"cvbb\",\"tenderness-pain_3\":\"cvbcv\",\"swelling_3\":\"b\",\"Fever_3\":\"bcvv\",\"post-dosage_4\":\"cvbc\",\"type-ofdischarge_4\":\"bcv\",\"tenderness-pain_4\":\"bcb\",\"swelling_4\":\"cbc\",\"Fever_4\":\"bcb\",\"Cultural-Swap\":\"bcvbcvbcxvbcv\\r\\ncvbcvb\\r\\ncv\\r\\nbcv\\r\\nb\",\"Dressing-Finding\":\"cvbxcvb\\r\\ncvb\\r\\ncvb\\r\\ncvb\\r\\nvcb\\r\\n\",\"Wound-Complication_Date\":\"06\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"cvb xcv cv \\r\\nv \",\"WoundD-Notes\":\"cv xcvb cvxb\\r\\ncvbcv\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-1b\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"bngfbnvn\",\"SurgeonOpinion2\":\"cvbxcfgbfg\",\"SurgeonOpinion3\":\" cv cxvb xgcv\",\"ReviewOn\":\"05\\/08\\/2025\",\"SuturesROn\":\"13\\/08\\/2025\",\"infection_prevention_notes\":\"vb nv vb vb  \",\"RevieworPhoneDate\":\"\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"Yes\",\"reviewbleed\":\"Yes\",\"reviewother\":\"Yes\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"ogaa\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','tcrje948slnleqgrr6dbosr081','SUCCESS',NULL),(65,'2025-08-21 14:41:42','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','mubpa2gdaj8pvrt33nr307ka5n','SUCCESS',NULL),(66,'2025-08-21 14:41:54','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','mubpa2gdaj8pvrt33nr307ka5n','SUCCESS',NULL),(67,'2025-08-21 14:46:39','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"dsfgvd\",\"risk_factor_height\":\"fdvd\",\"risk_factor_steroids\":\"fv\",\"risk_factor_tuberculosis\":\"dfv\",\"risk_factor_others\":\"vdf\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"fvd\",\"graft\":\"dfv\",\"Patch\":\"dfv\",\"Shunt\":\"dfv\",\"drain-used\":\"Yes\",\"drain_1\":\"dfv\",\"drain_2\":\"dfv\",\"drain_3\":\"vdf\",\"drain_4\":\"dvf\",\"drug-name_1\":\"dfv\",\"dosage_1\":\"dfv\",\"antibiotic_usage\":{\"startedon\":\"12\\/08\\/2025\",\"stoppeon\":\"05\\/08\\/2025\"},\"drug-name_2\":\"dfv\",\"dosage_2\":\"dfv\",\"drug-name_3\":\"dvf\",\"dosage_3\":\"dfv\",\"post-operative\":{\"date\":\"05\\/08\\/2025\"},\"post-dosage_1\":\"dvf\",\"type-ofdischarge_1\":\"fdvv\",\"tenderness-pain_1\":\" cvb \",\"swelling_1\":\"bcv\",\"Fever_1\":\"vcb\",\"post-dosage_2\":\"fdv\",\"type-ofdischarge_2\":\"fdv\",\"tenderness-pain_2\":\"bvc\",\"swelling_2\":\"b\",\"Fever_2\":\"b\",\"post-dosage_3\":\"bcbc\",\"type-ofdischarge_3\":\"cvbb\",\"tenderness-pain_3\":\"cvbcv\",\"swelling_3\":\"b\",\"Fever_3\":\"bcvv\",\"post-dosage_4\":\"cvbc\",\"type-ofdischarge_4\":\"bcv\",\"tenderness-pain_4\":\"bcb\",\"swelling_4\":\"cbc\",\"Fever_4\":\"bcb\",\"Cultural-Swap\":\"bcvbcvbcxvbcv\\r\\ncvbcvb\\r\\ncv\\r\\nbcv\\r\\nb\",\"Dressing-Finding\":\"cvbxcvb\\r\\ncvb\\r\\ncvb\\r\\ncvb\\r\\nvcb\\r\\n\",\"Wound-Complication_Date\":\"06\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"cvb xcv cv \\r\\nv \",\"WoundD-Notes\":\"cv xcvb cvxb\\r\\ncvbcv\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-1b\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"bngfbnvn\",\"SurgeonOpinion2\":\"cvbxcfgbfg\",\"SurgeonOpinion3\":\" cv cxvb xgcv\",\"ReviewOn\":\"05\\/08\\/2025\",\"SuturesROn\":\"13\\/08\\/2025\",\"infection_prevention_notes\":\"vb nv vb vb  \",\"RevieworPhoneDate\":\"04\\/08\\/2025\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"Yes\",\"reviewbleed\":\"Yes\",\"reviewother\":\"Yes\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"ogaa\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','mubpa2gdaj8pvrt33nr307ka5n','SUCCESS',NULL),(68,'2025-08-21 21:53:10','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','jb5bnv1en1ep79va2m5crhiib2','SUCCESS',NULL),(69,'2025-08-21 21:54:11','1212','CREATE','PATIENT','21','AHen','Created new patient record: AHen (UHID: 12345)',NULL,'{\"patient_id\":\"\",\"name\":\"AHen\",\"age\":\"\",\"Sex\":\"Male\",\"uhid\":\"12345\",\"phone\":\"\",\"bed\":\"\",\"address\":\"\",\"diagnosis\":\"\",\"surgical_procedure\":\"\",\"patient_info\":{\"doa\":\"\",\"dos\":\"\",\"dod\":\"\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"\",\"risk_factor_weight\":\"\",\"risk_factor_height\":\"\",\"risk_factor_steroids\":\"\",\"risk_factor_tuberculosis\":\"\",\"risk_factor_others\":\"\",\"pre-notdone\":\"\",\"hair-notdone\":\"\",\"metal\":\"\",\"graft\":\"\",\"Patch\":\"\",\"Shunt\":\"\",\"drain_1\":\"\",\"drain_2\":\"\",\"drain_3\":\"\",\"drug-name_1\":\"\",\"dosage_1\":\"\",\"antibiotic_usage\":{\"startedon\":\"\",\"stoppeon\":\"\"},\"drug-name_2\":\"\",\"dosage_2\":\"\",\"drug-name_3\":\"\",\"dosage_3\":\"\",\"post-operative\":{\"date\":\"\"},\"post-dosage_1\":\"\",\"type-ofdischarge_1\":\"\",\"tenderness-pain_1\":\"\",\"swelling_1\":\"\",\"Fever_1\":\"\",\"post-dosage_2\":\"\",\"type-ofdischarge_2\":\"\",\"tenderness-pain_2\":\"\",\"swelling_2\":\"\",\"Fever_2\":\"\",\"post-dosage_3\":\"\",\"type-ofdischarge_3\":\"\",\"tenderness-pain_3\":\"\",\"swelling_3\":\"\",\"Fever_3\":\"\",\"Cultural-Swap\":\"\",\"Dressing-Finding\":\"\",\"Wound-Complication_Date\":\"\",\"WoundD-Specify\":\"\",\"WoundD-Notes\":\"\",\"SurgeonOpinion1\":\"\",\"SurgeonOpinion2\":\"\",\"SurgeonOpinion3\":\"\",\"ReviewOn\":\"\",\"SuturesROn\":\"\",\"infection_prevention_notes\":\"\",\"RevieworPhoneDate\":\"\",\"date_completed\":\"\",\"signature\":\"\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','jb5bnv1en1ep79va2m5crhiib2','SUCCESS',NULL),(70,'2025-08-21 21:56:14','1212','LOGOUT','NURSE','1','Nomo','Nurse logged out: Nomo (1212)',NULL,NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','jb5bnv1en1ep79va2m5crhiib2','SUCCESS',NULL),(71,'2025-08-21 21:59:25','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','ipeg3qnl6ct0jvlostd5767kod','SUCCESS',NULL),(72,'2025-08-21 22:59:12','system','LOGIN','NURSE','1','Nomo','Nurse login successful',NULL,'{\"id\":1,\"nurse_id\":\"1212\",\"name\":\"Nomo\",\"email\":\"supercareadmin@gmail.com\",\"role\":\"nurse\"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','ostkqu15ip5nk364oq4p4t7ac1','SUCCESS',NULL),(73,'2025-08-21 23:07:06','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"dsfgvd\",\"risk_factor_height\":\"fdvd\",\"risk_factor_steroids\":\"fv\",\"risk_factor_tuberculosis\":\"dfv\",\"risk_factor_others\":\"vdf\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"fvd\",\"graft\":\"dfv\",\"Patch\":\"dfv\",\"Shunt\":\"dfv\",\"drain-used\":\"Yes\",\"drain_1\":\"dfv\",\"drain_2\":\"dfv\",\"drain_3\":\"vdf\",\"drain_4\":\"dvf\",\"drug-name_1\":\"dfv\",\"dosage_1\":\"dfv\",\"antibiotic_usage\":{\"startedon\":\"12\\/08\\/2025\",\"stoppeon\":\"05\\/08\\/2025\"},\"drug-name_2\":\"dfv\",\"dosage_2\":\"dfv\",\"drug-name_3\":\"dvf\",\"dosage_3\":\"dfv\",\"post-operative\":{\"date\":\"05\\/08\\/2025\"},\"post-dosage_1\":\"dvf\",\"type-ofdischarge_1\":\"fdvv\",\"tenderness-pain_1\":\" cvb \",\"swelling_1\":\"bcv\",\"Fever_1\":\"vcb\",\"post-dosage_2\":\"fdv\",\"type-ofdischarge_2\":\"fdv\",\"tenderness-pain_2\":\"bvc\",\"swelling_2\":\"b\",\"Fever_2\":\"b\",\"post-dosage_3\":\"bcbc\",\"type-ofdischarge_3\":\"cvbb\",\"tenderness-pain_3\":\"cvbcv\",\"swelling_3\":\"b\",\"Fever_3\":\"bcvv\",\"post-dosage_4\":\"cvbc\",\"type-ofdischarge_4\":\"bcv\",\"tenderness-pain_4\":\"bcb\",\"swelling_4\":\"cbc\",\"Fever_4\":\"bcb\",\"Cultural-Swap\":\"bcvbcvbcxvbcv\\r\\ncvbcvb\\r\\ncv\\r\\nbcv\\r\\nb\",\"Dressing-Finding\":\"cvbxcvb\\r\\ncvb\\r\\ncvb\\r\\ncvb\\r\\nvcb\\r\\n\",\"Wound-Complication_Date\":\"06\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"cvb xcv cv \\r\\nv \",\"WoundD-Notes\":\"cv xcvb cvxb\\r\\ncvbcv\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-1b\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"bngfbnvn\",\"SurgeonOpinion2\":\"cvbxcfgbfg\",\"SurgeonOpinion3\":\" cv cxvb xgcv\",\"ReviewOn\":\"05\\/08\\/2025\",\"SuturesROn\":\"13\\/08\\/2025\",\"infection_prevention_notes\":\"vb nv vb vb  \",\"RevieworPhoneDate\":\"04\\/08\\/2025\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"Yes\",\"reviewbleed\":\"Yes\",\"reviewother\":\"Yes\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"oga \"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','ostkqu15ip5nk364oq4p4t7ac1','SUCCESS',NULL),(74,'2025-08-21 23:07:49','1212','UPDATE','PATIENT','20','Malsawma Mao Oga Taijin','Updated patient record: Malsawma Mao Oga Taijin (UHID: 8055)',NULL,'{\"patient_id\":\"20\",\"name\":\"Malsawma Mao Oga Taijin\",\"age\":\"32\",\"Sex\":\"Female\",\"uhid\":\"8055\",\"phone\":\"986312209\",\"bed\":\"23\",\"address\":\"Street-05, Osaka, Japan\",\"diagnosis\":\"Too cool\",\"surgical_procedure\":\"Impossible\",\"patient_info\":{\"doa\":\"19\\/08\\/2025\",\"dos\":\"20\\/08\\/2025\",\"dod\":\"22\\/08\\/2025\",\"surgeon\":\"Mao oga\"},\"operation_duration\":\"Forever\",\"risk_factor_weight\":\"dsfgvd\",\"risk_factor_height\":\"fdvd\",\"risk_factor_steroids\":\"fv\",\"risk_factor_tuberculosis\":\"dfv\",\"risk_factor_others\":\"vdf\",\"surgical_skin_preparation\":{\"pre_op_bath\":\"No\",\"hair-removal\":\"Not Done\",\"removal-done\":\"Ward\"},\"pre-notdone\":\"Too Fresh that doesn\'t need to bath\",\"hair-notdone\":\"Not done cause he can do it himself\",\"implanted_used\":\"No\",\"metal\":\"fvd\",\"graft\":\"dfv\",\"Patch\":\"dfv\",\"Shunt\":\"dfv\",\"drain-used\":\"Yes\",\"drain_1\":\"dfv\",\"drain_2\":\"dfv\",\"drain_3\":\"vdf\",\"drain_4\":\"dvf\",\"drug-name_1\":\"dfv\",\"dosage_1\":\"dfv\",\"antibiotic_usage\":{\"startedon\":\"12\\/08\\/2025\",\"stoppeon\":\"05\\/08\\/2025\"},\"drug-name_2\":\"dfv\",\"dosage_2\":\"dfv\",\"drug-name_3\":\"dvf\",\"dosage_3\":\"dfv\",\"post-operative\":{\"date\":\"05\\/08\\/2025\"},\"post-dosage_1\":\"dvf\",\"type-ofdischarge_1\":\"fdvv\",\"tenderness-pain_1\":\" cvb \",\"swelling_1\":\"bcv\",\"Fever_1\":\"vcb\",\"post-dosage_2\":\"fdv\",\"type-ofdischarge_2\":\"fdv\",\"tenderness-pain_2\":\"bvc\",\"swelling_2\":\"b\",\"Fever_2\":\"b\",\"post-dosage_3\":\"bcbc\",\"type-ofdischarge_3\":\"cvbb\",\"tenderness-pain_3\":\"cvbcv\",\"swelling_3\":\"b\",\"Fever_3\":\"bcvv\",\"post-dosage_4\":\"cvbc\",\"type-ofdischarge_4\":\"bcv\",\"tenderness-pain_4\":\"bcb\",\"swelling_4\":\"cbc\",\"Fever_4\":\"bcb\",\"Cultural-Swap\":\"bcvbcvbcxvbcv\\r\\ncvbcvb\\r\\ncv\\r\\nbcv\\r\\nb\",\"Dressing-Finding\":\"cvbxcvb\\r\\ncvb\\r\\ncvb\\r\\ncvb\\r\\nvcb\\r\\n\",\"Wound-Complication_Date\":\"06\\/08\\/2025\",\"wounddehiscene\":\"Yes\",\"AllergicR\":\"Yes\",\"BleedingH\":\"Yes\",\"Other\":\"Yes\",\"WoundD-Specify\":\"cvb xcv cv \\r\\nv \",\"WoundD-Notes\":\"cv xcvb cvxb\\r\\ncvbcv\",\"SuperficialSSI\":\"Yes\",\"DeepSI\":\"Yes\",\"OrganSI\":\"Yes\",\"wtd1-1\":\"Yes\",\"wtd1-2\":\"Yes\",\"wtd1-3\":\"Yes\",\"wtd2-1\":\"Yes\",\"wtd2-1b\":\"Yes\",\"wtd2-2\":\"Yes\",\"wtd3-1\":\"Yes\",\"wtd3-2\":\"Yes\",\"wtd3-3\":\"Yes\",\"wtd4-1\":\"Yes\",\"wtd4-2\":\"Yes\",\"wtd4-3\":\"Yes\",\"SurgeonOpinion1\":\"bngfbnvn\",\"SurgeonOpinion2\":\"cvbxcfgbfg\",\"SurgeonOpinion3\":\" cv cxvb xgcv\",\"ReviewOn\":\"05\\/08\\/2025\",\"SuturesROn\":\"13\\/08\\/2025\",\"infection_prevention_notes\":\"vb nv vb vb  \",\"RevieworPhoneDate\":\"04\\/08\\/2025\",\"reviewp\":\"Yes\",\"reviewppain\":\"Yes\",\"reviewpus\":\"Yes\",\"reviewbleed\":\"Yes\",\"reviewother\":\"No\",\"date_completed\":\"21\\/08\\/2025\",\"signature\":\"oga \"}','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','ostkqu15ip5nk364oq4p4t7ac1','SUCCESS',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_login_logs`
--

LOCK TABLES `admin_login_logs` WRITE;
/*!40000 ALTER TABLE `admin_login_logs` DISABLE KEYS */;
INSERT INTO `admin_login_logs` VALUES (1,'weoidhj@gmail.com','failed','Invalid credentials','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-19 09:12:06'),(2,'supercareadmin@gmail.com','success','Admin account created','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-19 10:13:22'),(3,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-19 10:15:24'),(4,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-19 11:27:48'),(5,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-20 05:01:35'),(6,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-20 06:31:21'),(7,'supercareadmin@gmail.com','success','Login successful','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','2025-08-21 09:17:06');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'Supercare','hospital','supercareadmin@gmail.com','$2y$10$Z2lTBtjKXsuvIRU7R1J5zeAkPK7t37AXQoxilxtSZrXW0P4l3KWwO','active','2025-08-19 10:13:22','2025-08-21 09:17:06','2025-08-21 09:17:06',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `antibiotic_usage`
--

LOCK TABLES `antibiotic_usage` WRITE;
/*!40000 ALTER TABLE `antibiotic_usage` DISABLE KEYS */;
INSERT INTO `antibiotic_usage` VALUES (166,20,1,'dfv','dfv','2025-08-12','2025-08-05'),(167,20,2,'dfv','dfv','2025-08-12','2025-08-05'),(168,20,3,'dvf','dfv','2025-08-12','2025-08-05');
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
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cultural_dressing`
--

LOCK TABLES `cultural_dressing` WRITE;
/*!40000 ALTER TABLE `cultural_dressing` DISABLE KEYS */;
INSERT INTO `cultural_dressing` VALUES (57,21,'',''),(59,20,'bcvbcvbcxvbcv\r\ncvbcvb\r\ncv\r\nbcv\r\nb','cvbxcvb\r\ncvb\r\ncvb\r\ncvb\r\nvcb\r\n');
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
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drains`
--

LOCK TABLES `drains` WRITE;
/*!40000 ALTER TABLE `drains` DISABLE KEYS */;
INSERT INTO `drains` VALUES (135,20,'Yes','dfv',1),(136,20,'Yes','dfv',2),(137,20,'Yes','vdf',3),(138,20,'Yes','dvf',4);
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
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `implanted_materials`
--

LOCK TABLES `implanted_materials` WRITE;
/*!40000 ALTER TABLE `implanted_materials` DISABLE KEYS */;
INSERT INTO `implanted_materials` VALUES (57,21,NULL,'','','',''),(59,20,'No','fvd','dfv','dfv','dfv');
/*!40000 ALTER TABLE `implanted_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infection_prevention_notes`
--

DROP TABLE IF EXISTS `infection_prevention_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infection_prevention_notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `infection_prevention_notes` text DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`note_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `infection_prevention_notes_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infection_prevention_notes`
--

LOCK TABLES `infection_prevention_notes` WRITE;
/*!40000 ALTER TABLE `infection_prevention_notes` DISABLE KEYS */;
INSERT INTO `infection_prevention_notes` VALUES (11,21,'',''),(13,20,'vb nv vb vb  ','oga ');
/*!40000 ALTER TABLE `infection_prevention_notes` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nurses`
--

LOCK TABLES `nurses` WRITE;
/*!40000 ALTER TABLE `nurses` DISABLE KEYS */;
INSERT INTO `nurses` VALUES (1,'1212','Nomo','supercareadmin@gmail.com','$2y$10$sp6O1pdP4cLafG2C8sDyV.ty4031Q/TvQDwValcAZ9dkAzV4Mb9NG','nurse','2025-08-19 10:15:41','2025-08-19 10:15:41',NULL,NULL),(3,'123456','SuperAdmin','ahensananingthemcha@gmail.com','$2y$10$XTd/kVayaXP0MMBLcu4cKe8qgM8uy61tOBSRcoL9Kcu98IDimrIf.','nurse','2025-08-19 10:58:12','2025-08-19 10:58:12',NULL,NULL);
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
  `signature` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (20,'Malsawma Mao Oga Taijin',32,'Female','8055','986312209','23','Street-05, Osaka, Japan','Too cool','Impossible','2025-08-21','oga '),(21,'AHen',0,'Male','12345','','','','','','2025-08-21','');
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
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_operative_monitoring`
--

LOCK TABLES `post_operative_monitoring` WRITE;
/*!40000 ALTER TABLE `post_operative_monitoring` DISABLE KEYS */;
INSERT INTO `post_operative_monitoring` VALUES (103,20,1,'2025-08-05','dvf','fdvv',' cvb ','bcv','vcb'),(104,20,2,'2025-08-05','fdv','fdv','bvc','b','b'),(105,20,3,'2025-08-05','bcbc','cvbb','cvbcv','b','bcvv'),(106,20,4,'2025-08-05','cvbc','bcv','bcb','cbc','bcb');
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
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_phone`
--

LOCK TABLES `review_phone` WRITE;
/*!40000 ALTER TABLE `review_phone` DISABLE KEYS */;
INSERT INTO `review_phone` VALUES (87,21,NULL,NULL,NULL,NULL,NULL,NULL),(89,20,'2025-08-04','Yes','Yes','Yes','Yes','No');
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
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_sutures`
--

LOCK TABLES `review_sutures` WRITE;
/*!40000 ALTER TABLE `review_sutures` DISABLE KEYS */;
INSERT INTO `review_sutures` VALUES (87,21,NULL,NULL),(89,20,'2025-08-05','2025-08-13');
/*!40000 ALTER TABLE `review_sutures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_factors`
--

DROP TABLE IF EXISTS `risk_factors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_factors` (
  `risk_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `weight` text DEFAULT NULL,
  `height` text DEFAULT NULL,
  `steroids` text DEFAULT NULL,
  `tuberculosis` text DEFAULT NULL,
  `others` text DEFAULT NULL,
  PRIMARY KEY (`risk_id`),
  KEY `patient_id` (`patient_id`),
  CONSTRAINT `risk_factors_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_factors`
--

LOCK TABLES `risk_factors` WRITE;
/*!40000 ALTER TABLE `risk_factors` DISABLE KEYS */;
INSERT INTO `risk_factors` VALUES (10,21,'','','','',''),(12,20,'dsfgvd','fdvd','fv','dfv','vdf');
/*!40000 ALTER TABLE `risk_factors` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_admin_otp_logs`
--

LOCK TABLES `super_admin_otp_logs` WRITE;
/*!40000 ALTER TABLE `super_admin_otp_logs` DISABLE KEYS */;
INSERT INTO `super_admin_otp_logs` VALUES (20,'superadmin@supercare.com','339270','127.0.0.1',1,'pending','2025-08-18 06:01:19',NULL),(28,'ahensananingthemcha@gmail.com','429546','::1',1,'used','2025-08-21 17:45:00','2025-08-21 17:45:25');
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
INSERT INTO `super_admin_users` VALUES (2,'ahensananingthemcha@gmail.com','$2y$10$.kY3I4IWUbAJvOctxA1IQ.B3YV07ZpaAyhmbiqKYHVHmVG0EFItwq','SuperAdmin','active',NULL,NULL,'2025-08-21 17:45:25','2025-08-14 09:38:15','2025-08-21 17:45:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgeons`
--

LOCK TABLES `surgeons` WRITE;
/*!40000 ALTER TABLE `surgeons` DISABLE KEYS */;
INSERT INTO `surgeons` VALUES (18,'Mao oga','2025-08-19 05:47:55');
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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_details`
--

LOCK TABLES `surgical_details` WRITE;
/*!40000 ALTER TABLE `surgical_details` DISABLE KEYS */;
INSERT INTO `surgical_details` VALUES (97,21,NULL,NULL,NULL,'Mao oga',''),(99,20,'2025-08-19','2025-08-20','2025-08-22','Mao oga','Forever');
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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `surgical_skin_preparation`
--

LOCK TABLES `surgical_skin_preparation` WRITE;
/*!40000 ALTER TABLE `surgical_skin_preparation` DISABLE KEYS */;
INSERT INTO `surgical_skin_preparation` VALUES (81,21,NULL,'',NULL,'',NULL),(83,20,'No','Too Fresh that doesn\'t need to bath','Not Done','Not done cause he can do it himself','Ward');
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
  `organism_identified_deep` tinyint(1) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wound_complications`
--

LOCK TABLES `wound_complications` WRITE;
/*!40000 ALTER TABLE `wound_complications` DISABLE KEYS */;
INSERT INTO `wound_complications` VALUES (88,21,NULL,0,0,0,0,'','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','',''),(90,20,'2025-08-06',1,1,1,1,'cvb xcv cv \r\nv ','cv xcvb cvxb\r\ncvbcv',1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'bngfbnvn','cvbxcfgbfg',' cv cxvb xgcv');
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

-- Dump completed on 2025-08-21 23:15:35
