-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: ecoride
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `covoiturage`
--

DROP TABLE IF EXISTS `covoiturage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `covoiturage` (
  `covoiturage_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_depart` date NOT NULL,
  `heure_depart` time NOT NULL,
  `lieu_depart` varchar(50) NOT NULL,
  `depart_lat` decimal(10,7) NOT NULL,
  `depart_lng` decimal(10,7) NOT NULL,
  `date_arrive` date NOT NULL,
  `heure_arrive` time NOT NULL,
  `lieu_arrive` varchar(50) NOT NULL,
  `arrivee_lat` decimal(10,7) NOT NULL,
  `arrivee_lng` decimal(10,7) NOT NULL,
  `nb_place` varchar(50) NOT NULL,
  `prix_personne` float NOT NULL,
  `statut_id` int(11) NOT NULL,
  `voiture_id` int(11) NOT NULL,
  `utilisateur` int(11) NOT NULL,
  `ecologique` varchar(50) DEFAULT NULL,
  `accepts_smoker` tinyint(1) DEFAULT 0 COMMENT '1 = fumeur accepté',
  `accepts_animal` tinyint(1) DEFAULT 0 COMMENT '1 = animaux acceptés',
  PRIMARY KEY (`covoiturage_id`),
  KEY `statut` (`statut_id`),
  KEY `voiture` (`voiture_id`),
  KEY `utilisateur` (`utilisateur`),
  CONSTRAINT `covoiturage_ibfk_1` FOREIGN KEY (`statut_id`) REFERENCES `statuts` (`statut`),
  CONSTRAINT `covoiturage_ibfk_2` FOREIGN KEY (`voiture_id`) REFERENCES `voitures` (`voiture_id`),
  CONSTRAINT `covoiturage_ibfk_3` FOREIGN KEY (`utilisateur`) REFERENCES `utilisateurs` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `covoiturage`
--

LOCK TABLES `covoiturage` WRITE;
/*!40000 ALTER TABLE `covoiturage` DISABLE KEYS */;
INSERT INTO `covoiturage` VALUES (10,'2025-04-15','09:00:00','Millau',0.0000000,0.0000000,'2025-04-15','10:45:00','Montpellier',0.0000000,0.0000000,'4',4,2,7,21,'oui',0,0),(11,'2025-04-14','08:00:00','Millau',0.0000000,0.0000000,'2025-04-14','08:15:00','Servian',0.0000000,0.0000000,'0',5,2,7,21,'oui',0,0),(12,'2025-04-16','10:00:00','Caylar',0.0000000,0.0000000,'2025-04-16','11:00:00','Caux',0.0000000,0.0000000,'3',3,2,8,20,'oui',0,0),(13,'2025-04-19','10:00:00','Toulouse',0.0000000,0.0000000,'2025-05-19','22:00:00','Paris',0.0000000,0.0000000,'2',8,2,8,20,'oui',0,0),(14,'2025-04-18','20:20:00','Millau',0.0000000,0.0000000,'2025-04-18','21:20:00','Adissan',0.0000000,0.0000000,'1',4,2,8,20,'oui',0,0),(23,'2025-04-19','09:00:00','Caylar',0.0000000,0.0000000,'2025-04-19','09:45:00','Millau',0.0000000,0.0000000,'0',4,2,8,20,'oui',0,0),(24,'2025-04-19','10:00:00','Caylar',0.0000000,0.0000000,'2025-04-19','10:45:00','Millau',0.0000000,0.0000000,'1',4,2,8,20,'oui',0,0),(26,'2025-04-20','20:00:00','Millau',0.0000000,0.0000000,'2025-04-20','21:30:00','Servian',0.0000000,0.0000000,'4',5,2,9,21,'0',1,0),(27,'2025-04-20','22:00:00','Millau',0.0000000,0.0000000,'2025-04-20','23:30:00','Servian',0.0000000,0.0000000,'4',7,2,9,21,'0',1,0),(28,'2025-04-21','10:00:00','Millau',0.0000000,0.0000000,'2025-04-21','12:30:00','Perpignan',0.0000000,0.0000000,'4',5,2,8,20,'1',1,0),(29,'2025-04-21','12:00:00','Lyon',0.0000000,0.0000000,'2025-04-21','18:00:00','Paris',0.0000000,0.0000000,'4',3,2,8,20,'1',1,0),(30,'2025-04-21','14:00:00','Lyon',0.0000000,0.0000000,'2025-04-21','22:00:00','Tours',0.0000000,0.0000000,'4',3,2,8,20,'1',1,0),(31,'2025-04-22','10:00:00','Lyon',0.0000000,0.0000000,'2025-04-22','16:00:00','Paris',0.0000000,0.0000000,'2',3,2,7,21,'1',1,0),(32,'2025-04-22','10:00:00','Lyon',0.0000000,0.0000000,'2025-04-22','18:00:00','Tours',0.0000000,0.0000000,'3',3,2,7,21,'1',1,0),(33,'2025-04-21','20:00:00','Toulouse',0.0000000,0.0000000,'2025-04-21','20:30:00','Blagnac',0.0000000,0.0000000,'3',4,2,7,21,'1',1,0),(34,'2025-04-21','10:00:00','Millau',0.0000000,0.0000000,'2025-04-21','11:15:00','Tourbes',0.0000000,0.0000000,'3',4,2,8,20,'1',0,0),(35,'2025-04-21','11:00:00','Millau',0.0000000,0.0000000,'2025-04-21','11:30:00','Servian',0.0000000,0.0000000,'3',3,2,8,20,'1',1,0),(36,'2025-04-22','10:00:00','Toulouse',0.0000000,0.0000000,'2025-04-22','12:30:00','Millau',0.0000000,0.0000000,'3',4,2,7,21,'1',0,0),(37,'2025-04-25','12:00:00','Toulouse',0.0000000,0.0000000,'2025-04-25','14:30:00','Millau',0.0000000,0.0000000,'3',4,2,7,21,'1',1,0),(38,'2025-04-22','10:00:00','Lyon',0.0000000,0.0000000,'2025-04-22','13:00:00','Marseille',0.0000000,0.0000000,'3',3,2,7,21,'1',0,0),(39,'2025-04-22','15:00:00','Limoges',0.0000000,0.0000000,'2025-04-22','17:00:00','Bordeaux',0.0000000,0.0000000,'2',3,2,7,21,'1',0,0),(40,'2025-04-22','10:00:00','Millau',0.0000000,0.0000000,'2025-04-22','15:00:00','Bourges',0.0000000,0.0000000,'2',3,2,7,21,'1',1,0),(41,'2025-04-22','10:00:00','Adissan',0.0000000,0.0000000,'2025-04-22','10:20:00','Servian',0.0000000,0.0000000,'1',3,2,8,20,'1',1,0),(42,'2025-04-22','11:00:00','Limoges',0.0000000,0.0000000,'2025-04-22','17:00:00','Millau',0.0000000,0.0000000,'2',5,2,7,21,'1',0,0),(43,'2025-04-22','10:00:00','Millau',0.0000000,0.0000000,'2025-04-22','20:00:00','Paris',0.0000000,0.0000000,'2',5,2,7,21,'1',0,0),(44,'2025-04-23','10:00:00','Millau',0.0000000,0.0000000,'2025-04-23','11:00:00','Adissan',0.0000000,0.0000000,'1',3,2,7,21,'1',0,0),(45,'2025-04-23','10:00:00','Toulouse',0.0000000,0.0000000,'2025-04-23','13:00:00','Adissan',0.0000000,0.0000000,'0',3,2,7,21,'1',0,0),(46,'2025-04-24','10:00:00','Toulouse',0.0000000,0.0000000,'2025-04-24','12:30:00','Adissan',0.0000000,0.0000000,'2',3,2,7,21,'1',0,0),(47,'2025-04-24','11:00:00','Millau',0.0000000,0.0000000,'2025-04-24','12:30:00','Servian',0.0000000,0.0000000,'1',4,2,7,21,'1',1,0),(48,'2025-05-24','10:00:00','Paris',0.0000000,0.0000000,'2025-05-24','12:30:00','Bourges',0.0000000,0.0000000,'0',4,2,7,21,'1',0,0),(49,'2025-04-23','10:00:00','Adissan',0.0000000,0.0000000,'2025-04-23','11:00:00','Toulouse',0.0000000,0.0000000,'1',3,2,8,20,'1',0,0),(50,'2025-04-24','10:00:00','Adissan',0.0000000,0.0000000,'2025-04-24','10:25:00','Servian',0.0000000,0.0000000,'2',3,2,7,21,'1',0,0),(51,'2025-04-24','10:00:00','Marseille',0.0000000,0.0000000,'2025-04-24','12:00:00','Montpellier',0.0000000,0.0000000,'3',3,2,8,20,'1',0,0),(52,'2025-04-24','12:00:00','Caylar',0.0000000,0.0000000,'2025-04-24','12:45:00','Millau',0.0000000,0.0000000,'2',3,2,8,20,'1',0,0),(53,'2025-04-25','10:00:00','Millau',0.0000000,0.0000000,'2025-04-25','11:30:00','Servian',0.0000000,0.0000000,'1',4,2,7,21,'1',0,0),(54,'2025-04-24','11:00:00','Millau',0.0000000,0.0000000,'2025-04-24','12:30:00','Servian',0.0000000,0.0000000,'1',4,2,8,20,'1',1,0),(55,'2025-04-24','16:00:00','Millau',0.0000000,0.0000000,'2025-04-24','17:30:00','Servian',0.0000000,0.0000000,'2',4,2,7,21,'1',0,0),(56,'2025-04-25','08:00:00','Millau',0.0000000,0.0000000,'2025-04-25','09:30:00','Servian',0.0000000,0.0000000,'1',4,2,8,20,'1',0,0),(57,'2025-04-25','12:00:00','Millau',0.0000000,0.0000000,'2025-04-25','13:30:00','Servian',0.0000000,0.0000000,'3',4,3,7,21,'1',1,0),(58,'2025-04-25','10:00:00','Toulouse',0.0000000,0.0000000,'2025-04-25','13:00:00','Millau',0.0000000,0.0000000,'3',4,3,7,21,'1',1,0),(59,'2025-04-25','10:00:00','Lyon',0.0000000,0.0000000,'2025-04-25','13:00:00','Marseille',0.0000000,0.0000000,'2',3,9,8,20,'1',1,0),(60,'2025-04-25','06:00:00','Lyon',0.0000000,0.0000000,'2025-04-25','09:00:00','Marseille',0.0000000,0.0000000,'6',5,3,8,20,'1',0,0),(61,'2025-04-20','10:00:00','Lyon',0.0000000,0.0000000,'2025-04-20','10:00:00','Marseille',0.0000000,0.0000000,'2',1,3,7,21,'1',0,0),(62,'2025-04-25','10:00:00','Millau',0.0000000,0.0000000,'2025-04-25','11:00:00','Servian',0.0000000,0.0000000,'2',3,3,8,20,'0',1,0),(63,'2025-04-26','10:00:00','3 Rue des Fleurs, Millau, France',44.1038279,3.0692014,'2025-04-26','11:05:00','4 Impasse François Galut, Adissan, France',43.5312743,3.4319990,'0',3,2,8,20,'1',1,1),(64,'2025-04-26','10:00:00','3 Rue des Lilas, Paris, France',48.8778886,2.3951989,'2025-04-26','18:00:00','3 Rue des Fleurs, Mulhouse, France',47.7447883,7.3364842,'4',4,2,8,20,'1',1,1),(65,'2025-04-27','10:00:00','3 Rue des Fleurs, Millau, France',44.1038279,3.0692014,'2025-04-27','11:30:00','4 Impasse François Galut, Adissan, France',43.5312743,3.4319990,'2',3,2,7,21,'1',0,0),(66,'2025-04-30','10:00:00','9 Rue du Tunnel, Béziers, France',43.3371580,3.2232682,'2025-04-30','10:30:00','Chemin de Servian, Tourbes, France',43.4419330,3.3700280,'1',3,2,8,20,'1',0,0),(67,'2025-04-30','10:00:00','9 Rue du Tunnel, Béziers, France',43.3371580,3.2232682,'2025-04-30','12:00:00','4 Impasse François Galut, Adissan, France',43.5312743,3.4319990,'2',3,3,8,20,'1',0,0),(68,'2025-04-30','10:00:00','Rue de Paris, Brest, France',48.4067394,-4.4608033,'2025-04-30','20:00:00','Rue de Rivoli, Paris, France',48.8637441,2.3321960,'0',3,2,9,21,'0',0,0),(69,'2025-04-30','12:00:00','Rue de Rivoli, Paris, France',48.8637441,2.3321960,'2025-04-30','21:00:00','Rue de Paris, Brest, France',48.4067394,-4.4608033,'3',3,2,10,21,'1',0,0);
/*!40000 ALTER TABLE `covoiturage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `covoiturage_preferences`
--

DROP TABLE IF EXISTS `covoiturage_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `covoiturage_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `covoiturage_id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `valeur` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `covoiturage_id` (`covoiturage_id`),
  CONSTRAINT `fk_covoit_pref` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `covoiturage_preferences`
--

LOCK TABLES `covoiturage_preferences` WRITE;
/*!40000 ALTER TABLE `covoiturage_preferences` DISABLE KEYS */;
INSERT INTO `covoiturage_preferences` VALUES (1,26,'une valise par personne',NULL),(2,27,'un sac par personne',NULL),(3,62,'1 VALISE PAR PERSONNE',NULL),(4,63,'1 valise par personne',NULL),(5,64,'1 sac par personne',NULL);
/*!40000 ALTER TABLE `covoiturage_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `energies`
--

DROP TABLE IF EXISTS `energies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `energies` (
  `energie_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`energie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `energies`
--

LOCK TABLES `energies` WRITE;
/*!40000 ALTER TABLE `energies` DISABLE KEYS */;
INSERT INTO `energies` VALUES (1,'Electrique'),(2,'Essence'),(3,'Diesel'),(4,'GPL');
/*!40000 ALTER TABLE `energies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marques`
--

DROP TABLE IF EXISTS `marques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marques` (
  `marque_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`marque_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marques`
--

LOCK TABLES `marques` WRITE;
/*!40000 ALTER TABLE `marques` DISABLE KEYS */;
INSERT INTO `marques` VALUES (1,'Kia'),(2,'Fiat'),(3,'Tesla'),(4,'Mercedes'),(5,'BMW'),(6,'Peugeot'),(7,'Audi'),(8,'Ford'),(9,'Citroen'),(10,'Volkswagen'),(11,'Toyota'),(12,'Renault');
/*!40000 ALTER TABLE `marques` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) DEFAULT NULL,
  `utilisateur` int(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `content` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_message_chat_id` (`chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,NULL,20,'','','2025-04-16 12:51:04'),(2,NULL,20,'','','2025-04-16 12:51:12'),(3,NULL,20,'','','2025-04-16 12:51:35'),(4,NULL,20,'','','2025-04-16 12:51:37'),(5,NULL,20,'','','2025-04-16 12:59:42'),(6,NULL,20,'','','2025-04-16 13:06:10'),(7,NULL,20,'','','2025-04-16 13:09:42'),(8,NULL,20,'','','2025-04-16 13:30:22'),(9,NULL,20,'','','2025-04-16 13:47:14'),(10,NULL,20,'','','2025-04-16 13:47:15'),(11,NULL,20,'','','2025-04-16 13:47:16'),(12,NULL,20,'','','2025-04-16 13:47:17'),(13,NULL,20,'','','2025-04-16 13:47:20'),(14,NULL,20,'','','2025-04-16 13:47:38'),(15,NULL,20,'','','2025-04-16 13:47:51'),(16,NULL,20,'','','2025-04-16 13:54:35'),(17,NULL,20,'','','2025-04-16 13:54:37'),(18,NULL,20,'','','2025-04-16 13:54:46'),(19,NULL,20,'','','2025-04-16 13:57:15'),(20,NULL,20,'','','2025-04-16 13:57:17'),(21,NULL,20,'','','2025-04-16 13:57:27'),(22,NULL,20,'','','2025-04-16 14:17:43'),(23,NULL,20,'','','2025-04-16 14:17:44'),(24,NULL,20,'','','2025-04-16 14:18:00'),(25,NULL,20,'','','2025-04-16 14:18:01'),(26,NULL,20,'','','2025-04-16 14:18:02'),(27,NULL,20,'','','2025-04-16 14:18:23'),(28,NULL,20,'','','2025-04-16 14:18:29'),(29,NULL,20,'','','2025-04-16 14:20:23'),(30,NULL,20,'','','2025-04-16 14:20:25'),(31,NULL,20,'','','2025-04-16 14:24:27'),(32,NULL,20,'','','2025-04-16 14:24:33'),(33,NULL,20,'','','2025-04-16 14:35:34'),(34,NULL,20,'','','2025-04-16 14:35:37'),(35,NULL,20,'','','2025-04-16 14:37:58'),(36,NULL,20,'','','2025-04-16 14:38:00'),(37,NULL,20,'','','2025-04-16 14:38:25'),(38,NULL,20,'','','2025-04-16 14:38:26'),(39,NULL,20,'','','2025-04-16 14:38:39'),(40,NULL,20,'','','2025-04-16 14:38:40'),(41,NULL,20,'','','2025-04-16 14:39:42'),(42,NULL,20,'','','2025-04-16 14:39:43'),(43,NULL,20,'','','2025-04-16 14:39:45'),(44,NULL,20,'','','2025-04-16 14:42:21'),(45,NULL,20,'','','2025-04-16 14:42:24'),(46,NULL,20,'','','2025-04-16 14:56:59'),(47,NULL,20,'','','2025-04-16 14:57:02'),(48,NULL,20,'','','2025-04-16 15:03:58'),(49,NULL,20,'','','2025-04-16 15:03:59'),(50,NULL,20,'','','2025-04-16 15:04:04'),(51,NULL,20,'','','2025-04-16 15:05:19'),(52,NULL,20,'','','2025-04-16 15:05:20'),(53,NULL,20,'','','2025-04-16 15:05:41'),(54,NULL,20,'','','2025-04-16 15:05:43'),(55,NULL,20,'','','2025-04-16 15:06:47'),(56,NULL,20,'','','2025-04-16 15:06:47'),(57,NULL,20,'','','2025-04-16 15:06:50'),(58,NULL,20,'','','2025-04-16 15:06:52'),(59,NULL,20,'','','2025-04-16 15:06:57'),(60,NULL,20,'','','2025-04-16 15:20:53'),(61,NULL,20,'','','2025-04-16 15:20:54'),(62,NULL,20,'','','2025-04-16 15:20:58'),(63,NULL,20,'','','2025-04-16 15:21:00'),(64,NULL,20,'','','2025-04-16 15:21:27'),(65,NULL,20,'','','2025-04-16 15:23:46'),(66,NULL,20,'','','2025-04-16 15:23:47'),(67,NULL,20,'','','2025-04-16 15:23:48'),(68,NULL,20,'','','2025-04-16 15:23:53'),(69,NULL,20,'','','2025-04-16 15:25:19'),(70,NULL,20,'','','2025-04-16 15:27:47'),(71,NULL,20,'','','2025-04-16 15:30:50'),(72,NULL,20,'','','2025-04-16 15:36:34'),(73,NULL,20,'','','2025-04-17 10:21:58'),(74,NULL,20,'','','2025-04-17 13:01:13'),(75,NULL,20,'','','2025-04-18 10:29:23'),(76,NULL,20,'','','2025-04-18 11:41:18'),(77,NULL,20,'','','2025-04-18 11:49:20'),(78,NULL,20,'','','2025-04-18 11:49:20'),(79,NULL,20,'','','2025-04-18 11:49:23'),(80,NULL,20,'','','2025-04-18 11:49:59'),(81,NULL,20,'','','2025-04-18 11:50:05'),(82,81,20,'user','Bonjour, test n !','2025-04-18 11:50:18'),(83,NULL,20,'','','2025-04-18 11:50:24'),(84,NULL,20,'','','2025-04-18 11:50:32'),(85,NULL,20,'','','2025-04-18 11:50:33'),(86,NULL,20,'','','2025-04-18 11:54:19'),(87,NULL,20,'','','2025-04-18 11:54:23'),(88,NULL,20,'','','2025-04-18 11:57:32'),(89,NULL,20,'','','2025-04-18 11:57:38'),(90,NULL,21,'','','2025-04-18 14:39:27'),(91,NULL,20,'','','2025-04-18 19:00:41'),(92,91,20,'user','bonjour test 11','2025-04-18 19:00:50'),(93,NULL,20,'','','2025-04-18 19:44:03'),(94,NULL,20,'','','2025-04-18 19:50:51'),(95,NULL,20,'','','2025-04-18 19:50:52'),(96,NULL,20,'','','2025-04-18 19:50:54'),(97,NULL,20,'','','2025-04-18 19:50:55'),(98,NULL,20,'','','2025-04-18 19:50:56'),(99,NULL,20,'','','2025-04-18 19:50:58'),(100,NULL,20,'','','2025-04-18 20:04:07'),(101,NULL,20,'','','2025-04-18 20:18:22'),(102,NULL,20,'','','2025-04-18 20:18:29'),(103,NULL,20,'','','2025-04-18 20:47:13'),(104,NULL,20,'','','2025-04-19 08:04:30'),(105,NULL,21,'','','2025-04-19 09:55:52'),(106,NULL,NULL,'','','2025-04-19 13:19:19'),(107,NULL,20,'','','2025-04-19 13:59:23'),(108,NULL,20,'','','2025-04-19 17:28:41'),(109,NULL,20,'','','2025-04-19 18:00:32'),(110,NULL,20,'','','2025-04-19 18:05:38'),(111,NULL,20,'','','2025-04-19 20:10:34'),(112,NULL,20,'','','2025-04-20 18:12:16'),(113,NULL,20,'','','2025-04-20 18:15:19'),(114,NULL,21,'','','2025-04-21 08:24:27'),(115,NULL,21,'','','2025-04-21 11:35:19'),(116,NULL,20,'','','2025-04-21 14:13:41'),(117,NULL,21,'','','2025-04-22 16:12:14'),(118,117,21,'user','bonjour test2','2025-04-22 16:12:23'),(119,NULL,21,'','','2025-04-22 16:13:07'),(120,0,0,'contact','Titre : info\n\ntest5','2025-04-22 16:28:04'),(121,NULL,21,'','','2025-04-24 16:04:15'),(122,0,0,'contact','Titre : info\n\ntest 11','2025-04-24 16:13:12'),(123,0,0,'contact','Titre : info\n\nvoila2','2025-04-24 17:01:40'),(124,0,0,'contact','Titre : test3\n\nvoila3','2025-04-24 17:02:43'),(125,0,0,'contact','Titre : test444\n\n444','2025-04-24 17:44:33'),(126,0,0,'contact','Titre : test444555\n\n555','2025-04-27 08:50:58'),(127,0,0,'contact','Titre : gr\n\ngr','2025-04-27 11:23:27'),(128,0,0,'contact','Titre : gr\n\nfdg','2025-04-27 12:11:45');
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
  `note_id` int(11) NOT NULL AUTO_INCREMENT,
  `note` tinyint(11) NOT NULL,
  `commentaire` varchar(500) DEFAULT NULL,
  `chauffeur_id` int(11) NOT NULL,
  `covoiturage_id` int(11) NOT NULL,
  `passager_id` int(11) NOT NULL,
  `statut_id` tinyint(4) NOT NULL DEFAULT 4,
  PRIMARY KEY (`note_id`),
  KEY `chauffeur_id` (`chauffeur_id`),
  KEY `covoiturage_id` (`covoiturage_id`),
  KEY `fk_passager_notes` (`passager_id`),
  CONSTRAINT `fk_passager_notes` FOREIGN KEY (`passager_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (25,4,'top2',20,24,21,5),(26,3,'top3',20,24,21,5),(27,5,'top top',20,24,21,5),(28,5,'top / top',20,24,21,5),(29,4,'top',20,24,21,5),(30,3,'top',20,24,21,5),(31,2,'top',20,23,21,5),(32,5,'super1',20,29,21,5),(33,4,'ok1',21,11,20,5),(34,4,'ok2',21,31,20,5),(35,3,'ok',21,39,20,5),(36,5,'top7',21,40,20,5),(37,5,'top5',21,43,20,5),(38,5,'ok9',20,51,21,5),(39,5,'super',21,53,20,5),(41,3,'oui top',21,65,20,6),(42,4,'ok 300425',21,68,20,5);
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamations`
--

DROP TABLE IF EXISTS `reclamations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamations` (
  `reclamation_id` int(11) NOT NULL AUTO_INCREMENT,
  `mongo_id` varchar(24) DEFAULT NULL,
  `commentaire` varchar(50) DEFAULT NULL,
  `statut_id` varchar(50) NOT NULL DEFAULT '0',
  `utilisateur_id` int(11) NOT NULL,
  `utilisateur_concerne` int(11) NOT NULL,
  `covoiturage_id` int(11) NOT NULL,
  `date_signal` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`reclamation_id`),
  UNIQUE KEY `mongo_id` (`mongo_id`),
  KEY `utilisateur` (`utilisateur_id`),
  KEY `utilisateur_concerne` (`utilisateur_concerne`),
  CONSTRAINT `reclamations_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`),
  CONSTRAINT `reclamations_ibfk_2` FOREIGN KEY (`utilisateur_concerne`) REFERENCES `utilisateurs` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamations`
--

LOCK TABLES `reclamations` WRITE;
/*!40000 ALTER TABLE `reclamations` DISABLE KEYS */;
INSERT INTO `reclamations` VALUES (7,NULL,'1h de retard','8',20,21,38,'2025-04-21 23:01:38'),(8,NULL,'probleme2','8',21,20,41,'2025-04-21 23:16:26'),(9,NULL,'probleme8','7',20,21,42,'2025-04-21 23:24:46'),(10,NULL,'arrivé en retard de 2h','7',20,21,44,'2025-04-22 22:13:31'),(11,NULL,'probleme retard 5h','7',20,21,45,'2025-04-23 19:56:33'),(12,NULL,'probleme retard 6h','7',20,21,48,'2025-04-23 20:37:22'),(13,NULL,'probleme 30 min de retard','7',21,20,49,'2025-04-23 21:45:59'),(14,NULL,'probleme pas d\'animaux acceptés','8',20,21,50,'2025-04-24 09:08:02'),(15,NULL,'probleme pas de valise','7',21,20,52,'2025-04-24 09:55:16'),(16,NULL,'probleme mail test9','8',20,21,47,'2025-04-24 10:21:17'),(17,NULL,'probleme 10','7',21,20,54,'2025-04-24 10:26:30'),(18,NULL,'probleme tout','7',20,21,55,'2025-04-24 10:52:15'),(19,NULL,'PROBLEME !!!!!','7',21,20,56,'2025-04-24 10:59:13'),(20,NULL,'absent','7',21,20,64,'2025-04-27 12:01:29'),(22,'681259bfbe65d198d503b5b6','test 987654321','8',21,20,63,'2025-04-30 19:11:27'),(23,'68128b47be65d198d503b5ba','absent 30042025 100h','7',20,21,69,'2025-04-30 22:42:47'),(24,'68131f8dbe65d198d503b5bb','test du jour 01052025','8',21,20,66,'2025-05-01 09:15:25');
/*!40000 ALTER TABLE `reclamations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
  `covoiturage_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `prix` decimal(8,2) NOT NULL,
  `date_reservation` datetime NOT NULL,
  `valide` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`reservation_id`),
  KEY `covoiturage_id` (`covoiturage_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`),
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,24,21,4.00,'2025-04-19 12:26:58',0),(2,24,21,4.00,'2025-04-19 12:27:14',0),(3,24,21,4.00,'2025-04-19 12:56:30',0),(4,23,21,4.00,'2025-04-21 12:28:56',0),(5,23,21,4.00,'2025-04-21 12:52:58',0),(6,11,20,5.00,'2025-04-21 13:01:21',0),(7,11,20,5.00,'2025-04-21 13:10:23',0),(8,23,21,4.00,'2025-04-21 13:22:31',0),(9,31,20,3.00,'2025-04-21 15:49:52',0),(10,29,21,3.00,'2025-04-21 16:00:14',0),(11,33,20,4.00,'2025-04-21 16:16:10',0),(12,36,20,4.00,'2025-04-21 16:58:31',0),(13,38,20,3.00,'2025-04-21 22:16:55',0),(14,39,20,3.00,'2025-04-21 22:17:17',0),(15,40,20,3.00,'2025-04-21 22:47:26',0),(16,40,20,3.00,'2025-04-21 22:50:23',0),(17,41,21,3.00,'2025-04-21 23:03:46',0),(18,41,21,3.00,'2025-04-21 23:09:55',0),(19,42,20,5.00,'2025-04-21 23:23:44',0),(20,43,20,5.00,'2025-04-22 00:38:20',0),(21,44,20,3.00,'2025-04-22 22:12:39',0),(22,45,20,3.00,'2025-04-23 19:49:38',0),(23,45,20,3.00,'2025-04-23 20:19:36',0),(24,45,20,3.00,'2025-04-23 20:21:10',0),(25,45,20,3.00,'2025-04-23 20:21:17',0),(26,48,20,4.00,'2025-04-23 20:28:48',0),(27,48,20,4.00,'2025-04-23 20:28:54',0),(28,48,20,4.00,'2025-04-23 20:29:00',0),(29,49,21,3.00,'2025-04-23 21:44:37',0),(30,49,21,3.00,'2025-04-23 21:44:48',0),(31,50,20,3.00,'2025-04-24 09:06:05',0),(32,51,21,3.00,'2025-04-24 09:12:25',0),(33,52,21,3.00,'2025-04-24 09:53:41',0),(34,47,20,4.00,'2025-04-24 10:19:59',0),(35,54,21,4.00,'2025-04-24 10:25:33',0),(36,55,20,4.00,'2025-04-24 10:51:22',0),(37,56,21,4.00,'2025-04-24 10:57:44',0),(38,53,20,4.00,'2025-04-24 12:57:09',0),(42,64,21,4.00,'2025-04-27 11:58:18',0),(43,65,20,3.00,'2025-04-27 22:50:42',0),(44,63,21,3.00,'2025-04-29 18:00:51',0),(45,63,21,3.00,'2025-04-29 18:01:18',0),(46,63,21,3.00,'2025-04-29 18:01:23',0),(47,66,21,3.00,'2025-04-30 18:34:26',0),(48,66,21,3.00,'2025-04-30 18:34:31',0),(49,66,21,3.00,'2025-04-30 18:34:37',0),(50,68,20,3.00,'2025-04-30 22:37:31',0),(51,68,20,3.00,'2025-04-30 22:37:35',0),(52,68,20,3.00,'2025-04-30 22:37:38',0),(53,68,20,3.00,'2025-04-30 22:37:41',0),(54,69,20,3.00,'2025-04-30 22:39:48',0);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrateur'),(2,'Employé'),(3,'Utilisateur'),(4,'Suspendu');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuts`
--

DROP TABLE IF EXISTS `statuts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuts` (
  `statut` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  PRIMARY KEY (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuts`
--

LOCK TABLES `statuts` WRITE;
/*!40000 ALTER TABLE `statuts` DISABLE KEYS */;
INSERT INTO `statuts` VALUES (1,'En cours'),(2,'Terminer'),(3,'En attente'),(4,'A Valider'),(5,'validé'),(6,'refusé'),(7,'Prise en charge en cours'),(8,'Résolu'),(9,'Annulé');
/*!40000 ALTER TABLE `statuts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `covoiturage_id` int(11) NOT NULL COMMENT 'Réf. covoiturage',
  `emetteur_id` int(11) NOT NULL COMMENT 'Utilisateur payeur',
  `recepteur_id` int(11) NOT NULL COMMENT 'Utilisateur bénéficiaire',
  `montant` int(11) NOT NULL COMMENT 'Crédits transférés',
  `type_transaction` enum('paiement','commission') NOT NULL COMMENT 'Nature de la transaction',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date d’enregistrement',
  PRIMARY KEY (`id`),
  KEY `covoiturage_id` (`covoiturage_id`),
  KEY `emetteur_id` (`emetteur_id`),
  KEY `recepteur_id` (`recepteur_id`),
  CONSTRAINT `fk_tx_covoiturage` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tx_emetteur` FOREIGN KEY (`emetteur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tx_recepteur` FOREIGN KEY (`recepteur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,43,20,21,3,'paiement','2025-04-21 22:38:20'),(2,43,20,10,2,'commission','2025-04-21 22:38:20'),(3,44,20,21,1,'paiement','2025-04-22 20:12:39'),(4,44,20,10,2,'commission','2025-04-22 20:12:39'),(5,45,20,21,1,'paiement','2025-04-23 17:49:38'),(6,45,20,10,2,'commission','2025-04-23 17:49:38'),(7,45,20,21,1,'paiement','2025-04-23 18:19:36'),(8,45,20,10,2,'commission','2025-04-23 18:19:36'),(9,45,20,21,1,'paiement','2025-04-23 18:21:10'),(10,45,20,10,2,'commission','2025-04-23 18:21:10'),(11,45,20,21,1,'paiement','2025-04-23 18:21:17'),(12,45,20,10,2,'commission','2025-04-23 18:21:17'),(13,48,20,21,2,'paiement','2025-04-23 18:28:48'),(14,48,20,10,2,'commission','2025-04-23 18:28:48'),(15,48,20,21,2,'paiement','2025-04-23 18:28:54'),(16,48,20,10,2,'commission','2025-04-23 18:28:54'),(17,48,20,21,2,'paiement','2025-04-23 18:29:00'),(18,48,20,10,2,'commission','2025-04-23 18:29:00'),(19,49,21,20,1,'paiement','2025-04-23 19:44:37'),(20,49,21,10,2,'commission','2025-04-23 19:44:37'),(21,49,21,20,1,'paiement','2025-04-23 19:44:48'),(22,49,21,10,2,'commission','2025-04-23 19:44:48'),(23,50,20,21,1,'paiement','2025-04-24 07:06:05'),(24,50,20,10,2,'commission','2025-04-24 07:06:05'),(25,51,21,20,1,'paiement','2025-04-24 07:12:25'),(26,51,21,10,2,'commission','2025-04-24 07:12:25'),(27,52,21,20,1,'paiement','2025-04-24 07:53:41'),(28,52,21,10,2,'commission','2025-04-24 07:53:41'),(29,47,20,21,2,'paiement','2025-04-24 08:19:59'),(30,47,20,10,2,'commission','2025-04-24 08:19:59'),(31,54,21,20,2,'paiement','2025-04-24 08:25:33'),(32,54,21,10,2,'commission','2025-04-24 08:25:33'),(33,55,20,21,2,'paiement','2025-04-24 08:51:22'),(34,55,20,10,2,'commission','2025-04-24 08:51:22'),(35,56,21,20,2,'paiement','2025-04-24 08:57:44'),(36,56,21,10,2,'commission','2025-04-24 08:57:44'),(37,53,20,21,2,'paiement','2025-04-24 10:57:09'),(38,53,20,10,2,'commission','2025-04-24 10:57:09'),(39,57,20,21,2,'paiement','2025-04-24 10:57:15'),(40,57,20,10,2,'commission','2025-04-24 10:57:15'),(41,59,21,20,1,'paiement','2025-04-24 11:07:43'),(42,59,21,10,2,'commission','2025-04-24 11:07:43'),(43,60,21,20,3,'paiement','2025-04-24 12:11:39'),(44,60,21,10,2,'commission','2025-04-24 12:11:39'),(45,64,21,20,2,'paiement','2025-04-27 09:58:18'),(46,64,21,10,2,'commission','2025-04-27 09:58:18'),(47,65,20,21,1,'paiement','2025-04-27 20:50:42'),(48,65,20,10,2,'commission','2025-04-27 20:50:42'),(49,63,21,20,1,'paiement','2025-04-29 16:00:51'),(50,63,21,10,2,'commission','2025-04-29 16:00:51'),(51,63,21,20,1,'paiement','2025-04-29 16:01:18'),(52,63,21,10,2,'commission','2025-04-29 16:01:18'),(53,63,21,20,1,'paiement','2025-04-29 16:01:23'),(54,63,21,10,2,'commission','2025-04-29 16:01:23'),(55,66,21,20,1,'paiement','2025-04-30 16:34:26'),(56,66,21,10,2,'commission','2025-04-30 16:34:26'),(57,66,21,20,1,'paiement','2025-04-30 16:34:31'),(58,66,21,10,2,'commission','2025-04-30 16:34:31'),(59,66,21,20,1,'paiement','2025-04-30 16:34:37'),(60,66,21,10,2,'commission','2025-04-30 16:34:37'),(61,68,20,21,1,'paiement','2025-04-30 20:37:31'),(62,68,20,10,2,'commission','2025-04-30 20:37:31'),(63,68,20,21,1,'paiement','2025-04-30 20:37:35'),(64,68,20,10,2,'commission','2025-04-30 20:37:35'),(65,68,20,21,1,'paiement','2025-04-30 20:37:38'),(66,68,20,10,2,'commission','2025-04-30 20:37:38'),(67,68,20,21,1,'paiement','2025-04-30 20:37:41'),(68,68,20,10,2,'commission','2025-04-30 20:37:41'),(69,69,20,21,1,'paiement','2025-04-30 20:39:48'),(70,69,20,10,2,'commission','2025-04-30 20:39:48');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateurs` (
  `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `adresse` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `photo` varchar(255) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `credit` int(11) DEFAULT NULL,
  `role` int(11) NOT NULL,
  `is_chauffeur` tinyint(1) NOT NULL DEFAULT 0,
  `is_passager` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`utilisateur_id`),
  KEY `role` (`role`),
  CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (10,'Lune','Pierre','admin@mail.com','$2y$10$6MZOsimKtfwG76q7tMYZeObLv608q/cUhk/GFCPHo0c34iiOu5d.q','0612345678','','1988-03-16','admin.png','Admin',82,1,0,0),(20,'Calon','Emma','emma@mail.com','$2y$10$gd02BrFa0EaD.sImLzQSn.W7osb5LM5dm0otKZUa/4qyJxYUvH9z.','01 02 03 04 05','','1988-03-15','femme.png','Emma',4,3,1,1),(21,'Talon','Clem','clem@mail.com','$2y$10$DNGTLICSjvB/H47nDepq4O/ekDj7SizwTKq3WlRlNOBTdEJhDAfPS','06 01 02 03 04','','1980-02-25','homme.png','Clem',11,3,1,1),(22,'Talon','Stan','stan@mail.com','$2y$10$ICgX6Y02H.2kD.XEFCzvDuAZzU/d/3L2g.2UeXGFg/Zuwt6hPYmj6','06 11 22 33 44','','2000-06-08','homme.png','Stan',20,2,0,0),(23,'Palon','Cathy','cathy@mail.com','$2y$10$V9VMcXfEk77H17DFMNRXAuIT0v/zhLRRw1BPWWybqPpgSahKm9zGi','06 22 33 44 55','','1964-10-26','employeF.png','Cathy1',0,2,0,0),(25,'aaa','aaa','aaa@mail.com','$2y$10$4R/SsMXpSmT/dBX2qkV9Pu2SyqLsbigXpO3K0N70GnWVoClPJYvg.','01 01 01 01 01','','2000-01-01','employe.png','aaa',0,4,0,0),(26,'bbb','bbb','bbb@mail.com','$2y$10$g754AkSpo.F2IQ8Zt4.iIuUl5Q3r4verU21sFBBydyT6UwZjVWCFS','01 01 01 01 01','','2000-01-01','employe.png','bbb',0,2,0,0);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voitures`
--

DROP TABLE IF EXISTS `voitures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voitures` (
  `voiture_id` int(11) NOT NULL AUTO_INCREMENT,
  `modele` varchar(50) DEFAULT NULL,
  `immatriculation` varchar(50) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT NULL,
  `date_premiere_immat` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `marque_id` int(11) NOT NULL,
  `energie` int(11) NOT NULL,
  `proprietaire_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`voiture_id`),
  KEY `marque` (`marque_id`),
  KEY `energie` (`energie`),
  KEY `fk_voitures_utilisateurs` (`proprietaire_id`),
  CONSTRAINT `fk_voitures_utilisateurs` FOREIGN KEY (`proprietaire_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `voitures_ibfk_1` FOREIGN KEY (`marque_id`) REFERENCES `marques` (`marque_id`),
  CONSTRAINT `voitures_ibfk_2` FOREIGN KEY (`energie`) REFERENCES `energies` (`energie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voitures`
--

LOCK TABLES `voitures` WRITE;
/*!40000 ALTER TABLE `voitures` DISABLE KEYS */;
INSERT INTO `voitures` VALUES (7,'Partner','AB-123-CV','Vert','2015-01-01',NULL,6,1,21,NULL,NULL),(8,'Carens','AB-456-XD','Blanc','2018-02-02',NULL,1,1,20,NULL,NULL),(9,'Puma','AB-999-AB','Rouge','2011-11-11',NULL,8,3,21,NULL,NULL),(10,'Rio','CC-111-CC','Orange','2022-12-12',NULL,1,1,21,NULL,NULL),(11,'ceed','BB-999-CC','Rouge','2023-10-10',NULL,1,1,20,'2025-04-27 11:11:57',NULL),(12,'Punto','AA-236-AA','BLANC','2020-01-01','2025-04-27 11:25:05',2,1,20,'2025-04-27 11:47:50','2025-04-27 11:47:40');
/*!40000 ALTER TABLE `voitures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ecoride'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-01 14:03:37
