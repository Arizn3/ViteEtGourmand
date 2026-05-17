-- MySQL dump 10.13  Distrib 8.0.45, for macos15 (arm64)
--
-- Host: 127.0.0.1    Database: vitegourmand
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `allergene`
--

DROP TABLE IF EXISTS `allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allergene` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allergene`
--

LOCK TABLES `allergene` WRITE;
/*!40000 ALTER TABLE `allergene` DISABLE KEYS */;
INSERT INTO `allergene` VALUES (1,'œuf'),(2,'lait'),(3,'moutarde'),(4,'arachide'),(5,'mollusques et crustacés'),(6,'poissons'),(7,'graine de sésames'),(8,'soja'),(9,'sulfites'),(10,'noix'),(11,'blé et triticale');
/*!40000 ALTER TABLE `allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avis`
--

DROP TABLE IF EXISTS `avis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note` int NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `utilisateur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8F91ABF0FB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_8F91ABF0FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avis`
--

LOCK TABLES `avis` WRITE;
/*!40000 ALTER TABLE `avis` DISABLE KEYS */;
INSERT INTO `avis` VALUES (1,4,'Service rapide et repas délicieux, merci !','VALIDE',3),(2,5,'Très bon menu et livraison impeccable !','VALIDE',4),(3,4,'Repas savoureux et équipe très agréable !','VALIDE',5),(4,3,'Bon dans l’ensemble, livraison presque en retard.','VALIDE',6),(5,4,'Menu correct et portions satisfaisantes.','VALIDE',7),(6,4,'Très bonne expérience pour notre événement.','EN_ATTENTE',8);
/*!40000 ALTER TABLE `avis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_cmd` date NOT NULL,
  `date_prestation` date NOT NULL,
  `heure_livraison` time NOT NULL,
  `prix_menu` double NOT NULL,
  `nb_personne` int NOT NULL,
  `prix_livraison` double NOT NULL,
  `statut` varchar(50) NOT NULL,
  `pret_materiel` tinyint NOT NULL,
  `restitution_materiel` tinyint NOT NULL,
  `utilisateur_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `adresse_livraison` varchar(255) NOT NULL,
  `ville_livraison` varchar(50) NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6EEAA67DFB88E14F` (`utilisateur_id`),
  KEY `IDX_6EEAA67DCCD7E912` (`menu_id`),
  CONSTRAINT `FK_commande_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`),
  CONSTRAINT `FK_commande_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande`
--

LOCK TABLES `commande` WRITE;
/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
INSERT INTO `commande` VALUES (1,'2026-04-10','2026-04-23','11:00:00',537.692,20,39.692,'Terminer',1,0,3,1,'1 Rte de la Landette','Blaignan-Prignac','2026-04-25','2026-04-10'),(2,'2026-04-11','2026-04-20','18:00:00',1193.681,45,23.231,'Terminer',1,0,4,3,'44 Rue Fort Bayard','Branne','2026-04-23','2026-04-11'),(3,'2026-04-17','2026-04-27','16:30:30',481.279,20,51.079,'Terminer',1,0,5,5,'16 Rue des Maourelles','Grayan-et-l\'Hôpital','2026-04-29','2026-04-17'),(4,'2026-04-17','2026-04-25','15:00:00',1134.999,50,14.499,'Terminer',1,0,6,1,'43 Rte de la Providence','Ludon-Médoc','2026-04-27','2026-04-17'),(5,'2026-04-19','2026-04-28','18:00:00',785.322,30,32.022,'Terminer',1,0,7,4,'9 Av. de l\'Europe','Les Peintures','2026-04-29','2026-04-19'),(6,'2026-05-01','2026-05-10','16:00:00',541.94,20,43.94,'Terminer',1,0,3,1,'1371 Rte de St Nazaire','Saint-Avit-Saint-Nazaire','2026-05-12','2026-05-01'),(7,'2026-05-02','2026-05-11','16:00:00',1066.758,40,26.358,'Terminer',1,0,4,3,'12 Rue du Silberberg','Saint-Hippolyte','2026-05-12','2026-05-02'),(8,'2026-05-03','2026-05-14','16:00:00',878.31,35,30.96,'Terminer',1,0,5,2,'24 Muraille','Saint-Pardon-de-Conques','2026-05-15','2026-05-03'),(9,'2026-05-04','2026-05-15','14:00:00',906.624,35,27.774,'Terminer',1,0,8,10,'41 Rue des Primevères','Sainte-Florence','2026-05-16','2026-05-04'),(10,'2026-05-04','2026-05-17','15:00:00',715.153,30,26.653,'Annuler',1,0,9,8,'26 Chem. de Guillemin','Sainte-Terre','2026-05-05','2026-05-04'),(11,'2026-05-10','2026-05-25','12:00:00',577.243,20,39.043,'Votre commande est en préparation',1,0,10,7,'4 Tucos','Sauviac',NULL,'2026-05-10'),(12,'2026-05-17','2026-05-29','14:00:00',464.7,20,34.5,'Votre commande est en préparation',1,0,11,5,'8 rue des chataigniers','Vertheuil',NULL,'2026-05-17'),(13,'2026-05-17','2026-05-27','15:00:00',939.284,40,21.284,'Votre commande va être prise en compte',1,0,12,8,'ZA La Tranche','Villeneuve',NULL,'2026-05-17');
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commande_historique`
--

DROP TABLE IF EXISTS `commande_historique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande_historique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `statut` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_757DF90A82EA2E54` (`commande_id`),
  CONSTRAINT `FK_757DF90A82EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commande_historique`
--

LOCK TABLES `commande_historique` WRITE;
/*!40000 ALTER TABLE `commande_historique` DISABLE KEYS */;
INSERT INTO `commande_historique` VALUES (1,1,'Votre commande a été prise en compte','2026-04-10 11:30:00'),(2,1,'Votre commande est en préparation','2026-04-10 16:00:00'),(3,1,'En cours de livraison','2026-04-23 11:00:00'),(4,1,'Commande livrée','2026-04-23 11:30:00'),(5,1,'En attente du retour de matériel','2026-04-24 12:00:00'),(6,1,'Terminer','2026-04-25 15:00:00'),(7,2,'Votre commande a été prise en compte','2026-04-11 17:00:00'),(8,2,'Votre commande est en préparation','2026-04-11 18:50:00'),(9,2,'En cours de livraison','2026-04-20 17:30:00'),(10,2,'Commande livrée','2026-04-20 18:00:00'),(11,2,'En attente du retour de matériel','2026-04-21 13:00:00'),(12,2,'Terminer','2026-04-23 14:15:00'),(13,3,'Votre commande a été prise en compte','2026-04-17 14:34:00'),(14,3,'Votre commande est en préparation','2026-04-17 17:30:00'),(15,3,'En cours de livraison','2026-04-27 15:39:00'),(16,3,'Commande livrée','2026-04-27 16:15:00'),(17,3,'En attente du retour de matériel','2026-04-28 12:15:00'),(18,3,'Terminer','2026-04-29 15:15:00'),(19,4,'Votre commande a été prise en compte','2026-04-17 11:04:00'),(20,4,'Votre commande est en préparation','2026-04-17 14:00:00'),(21,4,'En cours de livraison','2026-04-25 14:00:00'),(22,4,'Commande livrée','2026-04-25 14:50:00'),(23,4,'En attente du retour de matériel','2026-04-26 13:55:00'),(24,4,'Terminer','2026-04-27 14:30:00'),(25,5,'Votre commande a été prise en compte','2026-04-19 14:30:00'),(26,5,'Votre commande est en préparation','2026-04-20 14:00:00'),(27,5,'En cours de livraison','2026-04-28 16:30:00'),(28,5,'Commande livrée','2026-04-28 17:35:00'),(29,5,'En attente du retour de matériel','2026-04-29 14:10:00'),(30,5,'Terminer','2026-04-29 14:40:00'),(31,6,'Votre commande a été prise en compte','2026-05-01 11:00:00'),(32,6,'Votre commande est en préparation','2026-05-01 14:00:00'),(33,6,'En cours de livraison','2026-05-10 15:00:00'),(34,6,'Commande livrée','2026-05-10 16:00:00'),(35,6,'En attente du retour de matériel','2026-05-11 14:00:00'),(36,6,'Terminer','2026-05-12 16:00:00'),(37,7,'Votre commande a été prise en compte','2026-05-02 16:00:00'),(38,7,'Votre commande est en préparation','2026-05-02 18:00:00'),(39,7,'En cours de livraison','2026-05-11 15:00:00'),(40,7,'Commande livrée','2026-05-11 15:57:00'),(41,7,'En attente du retour de matériel','2026-05-12 14:57:40'),(42,7,'Terminer','2026-05-12 18:40:00'),(43,8,'Votre commande a été prise en compte','2026-05-03 14:00:00'),(44,8,'Votre commande est en préparation','2026-05-03 14:40:00'),(45,8,'En cours de livraison','2026-05-14 15:10:20'),(46,8,'En attente du retour de matériel','2026-05-14 18:58:00'),(47,8,'Terminer','2026-05-15 17:09:00'),(48,9,'Votre commande a été prise en compte','2026-05-04 17:30:00'),(49,9,'Votre commande est en préparation','2026-05-05 11:30:00'),(50,9,'En cours de livraison','2026-05-15 12:30:00'),(51,9,'Commande livrée','2026-05-15 13:15:00'),(52,9,'En attente du retour de matériel','2026-05-16 13:40:00'),(53,9,'Terminer','2026-05-16 17:30:00'),(54,10,'Votre commande a été prise en compte','2026-05-04 17:39:00'),(55,10,'Annuler','2026-05-05 12:45:00'),(56,11,'Votre commande a été prise en compte','2026-05-10 17:46:30'),(57,11,'Votre commande est en préparation','2026-05-11 12:46:00'),(58,12,'Votre commande est en préparation','2026-05-17 17:00:00');
/*!40000 ALTER TABLE `commande_historique` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaire`
--

DROP TABLE IF EXISTS `horaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jour` varchar(50) NOT NULL,
  `heure_ouverture` time NOT NULL,
  `heure_fermeture` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaire`
--

LOCK TABLES `horaire` WRITE;
/*!40000 ALTER TABLE `horaire` DISABLE KEYS */;
INSERT INTO `horaire` VALUES (1,'Lundi','11:00:00','19:00:00'),(2,'Mardi','11:00:00','19:00:00'),(3,'Mercredi','11:00:00','19:00:00'),(4,'Jeudi','11:00:00','19:00:00'),(5,'Vendredi','11:00:00','19:00:00'),(6,'Samedi','11:00:00','19:00:00'),(7,'Dimanche','11:00:00','19:00:00');
/*!40000 ALTER TABLE `horaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL,
  `personne_mini` int NOT NULL,
  `prix_personne` double NOT NULL,
  `description` longtext NOT NULL,
  `qtt_restante` int NOT NULL,
  `created_at` date NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `regime_id` int NOT NULL,
  `theme_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7D053A9335E7D534` (`regime_id`),
  KEY `IDX_7D053A9359027487` (`theme_id`),
  CONSTRAINT `FK_menu_regime` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`id`),
  CONSTRAINT `FK_menu_theme` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Tradition Bordelais',20,24.9,'Un menu généreux composé d’un avocat frais en entrée, d’un bœuf bourguignon traditionnel mijoté avec soin et d’un duo gourmand bordelais pour terminer sur une touche sucrée et régionale.',210,'2026-05-17',NULL,2,1),(2,'Menu Gourmand',10,26.9,'Un menu convivial composé d’un houmous gourmand en entrée, d’un bœuf bourguignon accompagné de sa purée maison, puis d’un authentique canelé bordelais en dessert.',165,'2026-05-17',NULL,2,3),(3,'Saveurs Gourmandes',8,28.9,'Un menu généreux avec des bruschettas gourmandes en entrée, un bœuf grenade aux saveurs raffinées accompagné de sa garniture, puis une tarte fondante au chocolat pour finir sur une note intense et sucrée.',115,'2026-05-17',NULL,2,3),(4,'Élégance Gourmande',6,27.9,'Un menu raffiné composé d’une assiette de foie gras en entrée, de lentilles au riz parfumé aux saveurs délicates, puis d’un carré de gourmandise au chocolat pour une touche finale intense et fondante.',70,'2026-05-17',NULL,2,2),(5,'Vegan Fraîcheur',8,23.9,'Un menu végétal et coloré composé d’une salade Caprese revisitée, d’un Buddha Bowl Vegan équilibré et gourmand, puis d’un cheesecake aux fruits rouges pour une touche sucrée et fruitée.',260,'2026-05-17',NULL,1,3),(6,'Saveur Maison',6,22.9,'Un menu convivial composé d’une quiche lorraine maison en entrée, de tagliatelles crémeuses aux saveurs réconfortantes, puis d’un fraisier délicat et fruité en dessert.',100,'2026-05-17',NULL,2,1),(7,'Fraicheur Marine',10,29.9,'Un menu léger et raffiné composé d’une salade colorée fraîcheur, d’un cabillaud fondant délicatement préparé, puis d’une tarte au citron aux notes fraîches et acidulées.',70,'2026-05-17',NULL,2,2),(8,'Tradition du chef',8,25.5,'Un menu authentique composé d’une roquette fraîche aux pignons, d’un poulet rôti du chef préparé avec soin, puis d’une tarte au flan vanillé onctueuse et réconfortante.',210,'2026-05-17',NULL,2,1),(9,'Escale Estivale',6,24.9,'Un menu frais et équilibré composé d’une salade estivale, d’un risotto au poulet aux saveurs délicates, puis d’une douceur aux pommes pour terminer sur une note légère et fruitée.',70,'2026-05-17',NULL,2,3),(10,'Héritage Bordelais',10,27.9,'Un menu de caractère composé d’une soupe crémeuse du chef, de tripes à la bordelaise préparées selon la tradition, puis d’une tarte Tatin délicatement caramélisée.',45,'2026-05-17',NULL,2,3);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_plat`
--

DROP TABLE IF EXISTS `menu_plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_plat` (
  `menu_id` int NOT NULL,
  `plat_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`plat_id`),
  KEY `IDX_E8775249D73DB560` (`plat_id`),
  CONSTRAINT `FK_E8775249CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`),
  CONSTRAINT `FK_E8775249D73DB560` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_plat`
--

LOCK TABLES `menu_plat` WRITE;
/*!40000 ALTER TABLE `menu_plat` DISABLE KEYS */;
INSERT INTO `menu_plat` VALUES (1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(1,11),(2,12),(3,13),(4,14),(6,15),(5,16),(7,17),(8,18),(9,19),(10,20),(1,21),(2,22),(3,23),(4,24),(5,25),(6,26),(7,27),(8,28),(9,29),(10,30);
/*!40000 ALTER TABLE `menu_plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plat`
--

DROP TABLE IF EXISTS `plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_plat` varchar(50) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plat`
--

LOCK TABLES `plat` WRITE;
/*!40000 ALTER TABLE `plat` DISABLE KEYS */;
INSERT INTO `plat` VALUES (1,'Boeuf bourguignon traditionnel','Boeuf-Bourguignon-2-6a07542f3def6.png',NULL,'2026-05-15'),(2,'Boeuf bourguignon Purée','Boeuf-Bourguignon-6a07676929781.png',NULL,'2026-05-15'),(3,'Boeuf grenade','Boeuf-Salade-6a07680ab3076.jpg',NULL,'2026-05-15'),(4,'Lentilles au riz parfumé','Bol-de-Lentilles-6a0768e1aaa97.png',NULL,'2026-05-15'),(5,'Buddha Bowl Vegan','Bol-Vegetarien-6a0769437309e.png',NULL,'2026-05-15'),(6,'Tagliatelles crémeuses','Pates-Creme-6a0769cc12dd1.png',NULL,'2026-05-15'),(7,'Cabillaud fondant','Poisson-6a076a3009177.png',NULL,'2026-05-15'),(8,'Poulet rôti du chef','Poulet-Patate-6a076a6998291.png',NULL,'2026-05-15'),(9,'Risotto au poulet','Poulet-Riz-6a076aad24ef3.png',NULL,'2026-05-15'),(10,'Tripes à la Bordelaise','Tripes-a-la-Bordelaise-6a076affd232f.jpg',NULL,'2026-05-15'),(11,'Avocat frais','Avocat-frais-6a076b3e25f5c.png',NULL,'2026-05-15'),(12,'Houmous gourmand','Bol-de-Houmous-6a0888884d22d.png',NULL,'2026-05-16'),(13,'Bruschettas','Bruschetta-6a088b2952ae8.png',NULL,'2026-05-16'),(14,'Assiete de foie gras','Foie-gras-6a088b7235f0a.png',NULL,'2026-05-16'),(15,'Quiche Lorraine maison','Quiche-Lorraine-6a088bcc749a7.jpg',NULL,'2026-05-16'),(16,'Salade Caprese','Salade-Caprese-6a088c18dd28e.png',NULL,'2026-05-16'),(17,'Salade colorée fraîcheur','Salade-Coloree-6a088ccdc515e.png',NULL,'2026-05-16'),(18,'Roquette fraiche aux pignons','Salade-de-roquette-6a088d28b2759.png',NULL,'2026-05-16'),(19,'Salade estivale','Salade-fraiche-6a088d67e1a02.png',NULL,'2026-05-16'),(20,'Soupe crémeuse du chef','Soupe-Cremeuse-6a088d9ae3c1c.png',NULL,'2026-05-16'),(21,'Duo gourmand bordelais','Ambiance-Rustique-6a089e10ebdac.png',NULL,'2026-05-16'),(22,'Canelé bordelais','Canele-et-fruits-6a089e4560076.png',NULL,'2026-05-16'),(23,'Tarte fondante au chocolat','Dessert-au-Chocolat-6a089e7cf06a6.png',NULL,'2026-05-16'),(24,'Carré de gourmandise','Dessert-Gourmand-6a089eb530008.png',NULL,'2026-05-16'),(25,'Cheesecake fruits rouges','Dome-Cheesecake-6a089ef428476.png',NULL,'2026-05-16'),(26,'Fraisier gourmand','Fraise-6a089f229a87f.png',NULL,'2026-05-16'),(27,'Tarte au citron','Tarte-Citron-6a089f4d49b0e.png',NULL,'2026-05-16'),(28,'Tarte au flan vanillé','Tarte-Flan-6a089f84f3e3b.png',NULL,'2026-05-16'),(29,'Douceur aux pomme','Tarte-Pomme-6a089fb976621.png',NULL,'2026-05-16'),(30,'Tarte Tatin caramélisée','Tarte-Tatin-6a089fe5e736d.png',NULL,'2026-05-16');
/*!40000 ALTER TABLE `plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plat_allergene`
--

DROP TABLE IF EXISTS `plat_allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat_allergene` (
  `plat_id` int NOT NULL,
  `allergene_id` int NOT NULL,
  PRIMARY KEY (`plat_id`,`allergene_id`),
  KEY `IDX_6FA44BBF4646AB2` (`allergene_id`),
  CONSTRAINT `FK_plat_allergene_allergene` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_plat_allergene_plat` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plat_allergene`
--

LOCK TABLES `plat_allergene` WRITE;
/*!40000 ALTER TABLE `plat_allergene` DISABLE KEYS */;
INSERT INTO `plat_allergene` VALUES (6,1),(14,1),(15,1),(17,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(1,2),(4,2),(6,2),(7,2),(8,2),(9,2),(10,2),(13,2),(14,2),(15,2),(16,2),(18,2),(20,2),(21,2),(22,2),(23,2),(24,2),(25,2),(26,2),(27,2),(28,2),(29,2),(30,2),(4,3),(10,3),(11,3),(17,3),(19,3),(7,6),(5,7),(12,7),(17,7),(4,8),(5,8),(12,8),(23,8),(1,9),(6,9),(7,9),(8,9),(9,9),(10,9),(11,9),(13,9),(14,9),(15,9),(16,9),(17,9),(18,9),(19,9),(30,11);
/*!40000 ALTER TABLE `plat_allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regime`
--

DROP TABLE IF EXISTS `regime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regime` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `created_at` date NOT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regime`
--

LOCK TABLES `regime` WRITE;
/*!40000 ALTER TABLE `regime` DISABLE KEYS */;
INSERT INTO `regime` VALUES (1,'Végan','2026-05-15',NULL),(2,'Classique','2026-05-15',NULL),(3,'Sans gluten','2026-05-15',NULL);
/*!40000 ALTER TABLE `regime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(100) NOT NULL,
  `requested_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `utilisateur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_7CE748AFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reset_password_request`
--

LOCK TABLES `reset_password_request` WRITE;
/*!40000 ALTER TABLE `reset_password_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `reset_password_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ROLE_ADMIN'),(2,'ROLE_EMPLOYE'),(3,'ROLE_USER');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `created_at` date NOT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (1,'Rustique','2026-05-15',NULL),(2,'Buffet','2026-05-15',NULL),(3,'Convivial','2026-05-15',NULL);
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `role_id` int NOT NULL,
  `created_at` date NOT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1D1C63B3D60322AC` (`role_id`),
  CONSTRAINT `FK_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'admin@email.com','$2y$13$6htmHjF8jtuteC3iAgB3dOwOBQWd29IWD8Kl4677WaasVVzThEBOW','adminUser','adminUser','0606060606','adminUser',1,'2026-05-01',NULL),(2,'emp@email.com','$2y$13$0n0Ob4g49CK8AwVq1WCFqOVgejWyNfmWnwiQlA.Ync7jM/q4j4gf.','Juan','Rodriguez','0000000000','UserEmp',2,'2026-05-15',NULL),(3,'utilisateur1@email.com','$2y$13$i02ip1v6EPQoAso43OScXOnMk4K1B6UKsqhx6V4zfDhmruBz3ZiVW','Lucas','CAPPELLO','0606060606','2 Rés Les Matines, 33680 Le Porge',3,'2026-05-15',NULL),(4,'utilisateur2@email.com','$2y$13$EhTDKqC/owkQkF4z4CQPs.23sygrsFqVxIWx0ig00KKmTraGxgyVu','Luciano','Meloni','0606060606','27 Av. de Bordeaux 31, 33680 Lacanau',3,'2026-05-15',NULL),(5,'utilisateur3@email.com','$2y$13$pJPtvKM7FQWovTHiG9BJT.1m7pIkg1H0qWnPgV8osIXwYWrpZOzoS','Virginie','Uhlen','0606060606','19 esplanade des Antilles, 33607 Pessac',3,'2026-05-15',NULL),(6,'utilisateur4@email.com','$2y$13$wm6cBwdv5A72uE0.FyqUIOEriQqHgqQZwhZkNrcJ.7g74NbNsAVUO','Aurore','Gomes','0606060606','Pl. de la Comédie, 33000 Bordeaux',3,'2026-05-15',NULL),(7,'utilisateur5@email.com','$2y$13$qHj94yhiv9gI7ctVDVkkBOjvRr8tBB36hM3WjKFFg63Z4TpPQyquO','Adam','Al-najjar','0606060606','La Sayé ROUTE DE, 33620 Cavignac',3,'2026-05-15',NULL),(8,'utilisateur6@email.com','$2y$13$q.zdX06KO1x6hvCa1dN5cOVcHUaZnMbX8nDS8VtGtUpYb0W3f4Rdy','Aurelien','Drutalos','0606060606','Rte du Stade, 33820 Braud-et-Saint-Louis',3,'2026-05-15',NULL),(9,'utilisateur7@email.com','$2y$13$A2gnLwEnB37ETzPN6npe2ei2qgnU43rtyCio23l2WyZVhkd56bYf6','Jérémie','Gachon','0606060606','5 Av. Paul Desfarges, 16000 Angoulême',3,'2026-05-15',NULL),(10,'utilisateur8@email.com','$2y$13$6usuf4L4UiIdEmG61Lspyu6i7gcX3B2V4bYBSZpRVRPmRqKnywCIm','David','Gauckler','0606060606','Le bourg, 49 Rte de Torsac, 16410 Dirac',3,'2026-05-15',NULL),(11,'utilisateur9@email.com','$2y$13$TeYYBQEi/5/g3Axgh3D6k.0apV7bwmdn5uB7PpZO4kXi2y39.udCm','Cédric','Wolf','0606060606','À Hustet, 32250 Montréal',3,'2026-05-15',NULL),(12,'utilisateur10@email.com','$2y$13$0M3RoSht9VGFWdLSw9Oxy.8quYzouIQoeNNG4zUaR123clJV6LKiq','Ludivine','Fournier','0606060606','87 Av. de Gascogne, 33114 Le Barp',3,'2026-05-15',NULL);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-17 19:59:02
