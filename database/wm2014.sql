-- MySQL dump 10.13  Distrib 5.1.57, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: a9254714_wm2014
-- ------------------------------------------------------
-- Server version	5.1.57
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `benutzergruppe`
--

DROP TABLE IF EXISTS `benutzergruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `benutzergruppe` (
  `gruppen_key` int(11) NOT NULL,
  `bezeichnung` varchar(50) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `benutzergruppe`
--

LOCK TABLES `benutzergruppe` WRITE;
/*!40000 ALTER TABLE `benutzergruppe` DISABLE KEYS */;
INSERT INTO `benutzergruppe` VALUES (1,'Fußball-Experte',1),(2,'Fußball-Kenner',2),(3,'Fußball-Laien',3),(4,'Mädchen',4),(0,'Gesamtwertung',0);
/*!40000 ALTER TABLE `benutzergruppe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blitz_ergebnis`
--

DROP TABLE IF EXISTS `blitz_ergebnis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blitz_ergebnis` (
  `key_spiel` int(11) DEFAULT NULL,
  `tore_ms1` int(11) DEFAULT NULL,
  `tore_ms2` int(11) DEFAULT NULL,
  `tendenz` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blitz_ergebnis`
--

LOCK TABLES `blitz_ergebnis` WRITE;
/*!40000 ALTER TABLE `blitz_ergebnis` DISABLE KEYS */;
/*!40000 ALTER TABLE `blitz_ergebnis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blitzrangliste`
--

DROP TABLE IF EXISTS `blitzrangliste`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blitzrangliste` (
  `spieler_key` int(11) NOT NULL DEFAULT '0',
  `spielername` varchar(20) DEFAULT NULL,
  `vorname` varchar(20) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `weltmeister` int(11) DEFAULT NULL,
  `bezahlt` varchar(5) DEFAULT NULL,
  `punkte` int(11) DEFAULT NULL,
  `exakte_tips` int(11) DEFAULT NULL,
  `richtige_Tendenz` int(11) DEFAULT NULL,
  `passwort` varchar(20) DEFAULT NULL,
  `Freigabe` tinyint(1) NOT NULL DEFAULT '0',
  `Newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `Rolle` varchar(15) NOT NULL DEFAULT '0',
  `login` varchar(15) NOT NULL DEFAULT '',
  `ICQ_NUMMER` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blitzrangliste`
--

LOCK TABLES `blitzrangliste` WRITE;
/*!40000 ALTER TABLE `blitzrangliste` DISABLE KEYS */;
/*!40000 ALTER TABLE `blitzrangliste` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gruppe`
--

DROP TABLE IF EXISTS `gruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gruppe` (
  `gruppe_key` smallint(6) NOT NULL DEFAULT '0',
  `Bezeichnung` varchar(30) NOT NULL DEFAULT '',
  `sortierung` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`gruppe_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gruppe`
--

LOCK TABLES `gruppe` WRITE;
/*!40000 ALTER TABLE `gruppe` DISABLE KEYS */;
INSERT INTO `gruppe` VALUES (1,'Gruppe A',1),(2,'Gruppe B',2),(3,'Gruppe C',3),(4,'Gruppe D',4),(5,'Gruppe E',5),(6,'Gruppe F',6),(7,'Gruppe G',7),(8,'Gruppe H',8);
/*!40000 ALTER TABLE `gruppe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gruppenspiel`
--

DROP TABLE IF EXISTS `gruppenspiel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gruppenspiel` (
  `spiel_key` int(11) NOT NULL AUTO_INCREMENT,
  `spiel_art` smallint(6) DEFAULT NULL,
  `gruppe` smallint(6) DEFAULT NULL,
  `key_ms1` int(11) DEFAULT NULL,
  `tore_ms1` int(11) DEFAULT NULL,
  `key_ms2` int(11) DEFAULT NULL,
  `tore_ms2` int(11) DEFAULT NULL,
  `tendenz` int(11) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  `Status` smallint(6) NOT NULL DEFAULT '0',
  `bezeichnung` varchar(100) DEFAULT NULL,
  `auto_ko_fk_ms1` int(11) DEFAULT NULL,
  `auto_ko_fk_ms2` int(11) DEFAULT NULL,
  `auto_gruppe_fk_ms1` int(11) DEFAULT NULL,
  `auto_gruppe_fk_ms2` int(11) DEFAULT NULL,
  `auto_platz_ms1` int(11) DEFAULT NULL,
  `auto_platz_ms2` int(11) DEFAULT NULL,
  `ticker_url` varchar(200) DEFAULT NULL,
  `ticker_spiel_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`spiel_key`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gruppenspiel`
--

LOCK TABLES `gruppenspiel` WRITE;
/*!40000 ALTER TABLE `gruppenspiel` DISABLE KEYS */;
INSERT INTO `gruppenspiel` VALUES (1,1,1,1,NULL,2,NULL,NULL,'2014-06-12','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2040&event=11484',''),(2,1,1,3,NULL,4,NULL,NULL,'2014-06-13','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2041&event=11485',''),(3,1,1,1,NULL,3,NULL,NULL,'2014-06-17','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(4,1,1,4,NULL,2,NULL,NULL,'2014-06-19','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2042&event=11487',''),(5,1,1,2,NULL,3,NULL,NULL,'2014-06-23','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2042&event=11488',''),(6,1,1,4,NULL,1,NULL,NULL,'2014-06-23','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2043&event=11489',''),(7,1,2,5,NULL,6,NULL,NULL,'2014-06-13','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2043&event=11490',''),(8,1,2,7,NULL,8,NULL,NULL,'2014-06-14','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2044&event=11491',''),(9,1,2,8,NULL,6,NULL,NULL,'2014-06-18','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2044&event=11492',''),(10,1,2,5,NULL,7,NULL,NULL,'2014-06-18','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2025&event=11469',''),(11,1,2,8,NULL,5,NULL,NULL,'2014-06-23','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2034&event=11478',''),(12,1,2,6,NULL,7,NULL,NULL,'2014-06-23','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2041&event=11486',''),(13,1,3,9,NULL,10,NULL,NULL,'2014-06-14','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2026&event=11470',''),(14,1,3,11,NULL,12,NULL,NULL,'2014-06-15','03:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2027&event=11471',''),(15,1,3,9,NULL,11,NULL,NULL,'2014-06-19','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2028&event=11472',''),(16,1,3,12,NULL,10,NULL,NULL,'2014-06-20','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2029&event=11473',''),(17,1,3,12,NULL,9,NULL,NULL,'2014-06-24','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2030&event=11474',''),(18,1,3,10,NULL,11,NULL,NULL,'2014-06-24','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2031&event=11475',''),(19,1,4,13,NULL,14,NULL,NULL,'2014-06-14','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2032&event=11476',''),(20,1,4,15,NULL,16,NULL,NULL,'2014-06-15','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2033&event=11477',''),(21,1,4,13,NULL,15,NULL,NULL,'2014-06-19','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(22,1,4,16,NULL,14,NULL,NULL,'2014-06-20','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2035&event=11479',''),(23,1,4,16,NULL,13,NULL,NULL,'2014-06-24','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2036&event=11480',''),(24,1,4,14,NULL,15,NULL,NULL,'2014-06-24','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2037&event=11481',''),(25,1,5,17,NULL,18,NULL,NULL,'2014-06-15','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2040&event=11484',''),(26,1,5,19,NULL,20,NULL,NULL,'2014-06-15','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2041&event=11485',''),(27,1,5,17,NULL,19,NULL,NULL,'2014-06-20','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(28,1,5,20,NULL,18,NULL,NULL,'2014-06-21','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2042&event=11487',''),(29,1,5,20,NULL,17,NULL,NULL,'2014-06-25','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2042&event=11488',''),(30,1,5,18,NULL,19,NULL,NULL,'2014-06-25','22:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2043&event=11489',''),(31,1,6,21,NULL,22,NULL,NULL,'2014-06-16','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2043&event=11490',''),(32,1,6,23,NULL,24,NULL,NULL,'2014-06-16','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2044&event=11491',''),(33,1,6,21,NULL,23,NULL,NULL,'2014-06-21','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2044&event=11492',''),(34,1,6,24,NULL,22,NULL,NULL,'2014-06-22','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2025&event=11469',''),(35,1,6,24,NULL,21,NULL,NULL,'2014-06-25','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2034&event=11478',''),(36,1,6,22,NULL,23,NULL,NULL,'2014-06-25','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2041&event=11486',''),(37,1,7,25,NULL,26,NULL,NULL,'2014-06-16','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2026&event=11470',''),(38,1,7,27,NULL,28,NULL,NULL,'2014-06-17','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2027&event=11471',''),(39,1,7,25,NULL,27,NULL,NULL,'2014-06-21','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2028&event=11472',''),(40,1,7,28,NULL,26,NULL,NULL,'2014-06-23','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2029&event=11473',''),(41,1,7,28,NULL,25,NULL,NULL,'2014-06-26','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2030&event=11474',''),(42,1,7,26,NULL,27,NULL,NULL,'2014-06-26','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2031&event=11475',''),(43,1,8,29,NULL,30,NULL,NULL,'2014-06-17','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2032&event=11476',''),(44,1,8,31,NULL,32,NULL,NULL,'2014-06-18','00:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2033&event=11477',''),(45,1,8,29,NULL,31,NULL,NULL,'2014-06-22','18:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(46,1,8,32,NULL,30,NULL,NULL,'2014-06-22','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2035&event=11479',''),(47,1,8,32,NULL,29,NULL,NULL,'2014-06-26','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2036&event=11480',''),(48,1,8,30,NULL,31,NULL,NULL,'2014-06-26','21:00:00',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'http://www1.sportschau.de/sportschau_specials/layout/php/ticker/index.phtml?tid=2037&event=11481',''),(49,2,NULL,50,NULL,50,NULL,NULL,'2014-06-28','18:00:00',0,'Achtelfinale 1',NULL,NULL,1,2,1,2,NULL,NULL),(50,2,NULL,50,NULL,50,NULL,NULL,'2014-06-28','22:00:00',0,'Achtelfinale 2',NULL,NULL,3,4,1,2,NULL,NULL),(51,2,NULL,50,NULL,50,NULL,NULL,'2014-06-29','18:00:00',0,'Achtelfinale 3',NULL,NULL,2,1,1,2,NULL,NULL),(52,3,NULL,50,NULL,50,NULL,NULL,'2014-06-29','22:00:00',0,'Achtelfinale 4',NULL,NULL,4,3,1,2,NULL,NULL),(53,2,NULL,50,NULL,50,NULL,NULL,'2014-06-30','18:00:00',0,'Achtelfinale 5',NULL,NULL,5,6,1,2,NULL,NULL),(54,2,NULL,50,NULL,50,NULL,NULL,'2014-06-30','22:00:00',0,'Achtelfinale 6',NULL,NULL,7,8,1,2,NULL,NULL),(55,2,NULL,50,NULL,50,NULL,NULL,'2014-07-01','18:00:00',0,'Achtelfinale 7',NULL,NULL,6,5,1,2,NULL,NULL),(56,2,NULL,50,NULL,50,NULL,NULL,'2014-07-01','22:00:00',0,'Achtelfinale 8',NULL,NULL,8,7,1,2,NULL,NULL),(57,3,NULL,50,NULL,50,NULL,NULL,'2014-07-04','18:00:00',0,'Viertelfinale 1',53,54,NULL,NULL,1,1,NULL,NULL),(58,3,NULL,50,NULL,50,NULL,NULL,'2014-07-04','22:00:00',0,'Viertelfinale 2',49,50,NULL,NULL,1,1,NULL,NULL),(59,3,NULL,50,NULL,50,NULL,NULL,'2014-07-05','18:00:00',0,'Viertelfinale 3',55,56,NULL,NULL,1,1,NULL,NULL),(60,3,NULL,50,NULL,50,NULL,NULL,'2014-07-05','22:00:00',0,'Viertelfinale 4',51,52,NULL,NULL,1,1,NULL,NULL),(61,4,NULL,50,NULL,50,NULL,NULL,'2014-07-08','22:00:00',0,'Halbfinale A',57,58,NULL,NULL,1,1,NULL,NULL),(62,4,NULL,50,NULL,50,NULL,NULL,'2014-07-09','22:00:00',0,'Halbfinale B',59,60,NULL,NULL,1,1,NULL,NULL),(63,5,NULL,50,NULL,50,NULL,NULL,'2014-07-12','22:00:00',0,'Kleines Finale',61,62,NULL,NULL,2,2,NULL,NULL),(64,6,NULL,50,NULL,50,NULL,NULL,'2014-07-13','21:00:00',0,'Finale',61,62,NULL,NULL,1,1,NULL,NULL);
/*!40000 ALTER TABLE `gruppenspiel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logging`
--

DROP TABLE IF EXISTS `logging`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logging` (
  `nummer` int(11) NOT NULL AUTO_INCREMENT,
  `spieler` smallint(6) NOT NULL DEFAULT '0',
  `datum` date NOT NULL DEFAULT '0000-00-00',
  `zeit` time NOT NULL DEFAULT '00:00:00',
  `source` varchar(30) NOT NULL DEFAULT '',
  `ip_adresse` varchar(20) DEFAULT NULL,
  `logtext` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`nummer`)
) ENGINE=MyISAM AUTO_INCREMENT=9720 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logging`
--


--
-- Table structure for table `mannschaft`
--

DROP TABLE IF EXISTS `mannschaft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mannschaft` (
  `mannschaft_key` int(11) NOT NULL AUTO_INCREMENT,
  `mannschaftsname` varchar(20) DEFAULT NULL,
  `gruppe` varchar(5) DEFAULT NULL,
  `anz_spiele` int(11) DEFAULT NULL,
  `punkte` int(11) DEFAULT NULL,
  `plusTore` int(11) DEFAULT NULL,
  `minusTore` int(11) DEFAULT NULL,
  `tordifferenz` int(11) DEFAULT NULL,
  `info_link` varchar(200) DEFAULT NULL,
  `STATUS` int(11) DEFAULT NULL,
  `GEWINNER` int(1) NOT NULL DEFAULT '0',
  `flagge` varchar(200) DEFAULT NULL,
  `IS_NULL` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mannschaft_key`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mannschaft`
--

LOCK TABLES `mannschaft` WRITE;
/*!40000 ALTER TABLE `mannschaft` DISABLE KEYS */;
INSERT INTO `mannschaft` VALUES (1,'Brasilien','1',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43924/index.html',1,0,'bra.png',0),(2,'Kroatien','1',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43938/index.html',1,0,'cro.png',0),(3,'Mexiko','1',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43911/index.html',1,0,'mex.png',0),(4,'Kamerun','1',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43849/index.html',1,0,'cmr.png',0),(5,'Spanien','2',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43969/index.html',1,0,'esp.png',0),(6,'Neiderlande','2',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43960/index.html',1,0,'ned.png',0),(7,'Chile','2',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43925/index.html',1,0,'chi.png',0),(8,'Australien','2',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43976/index.html',1,0,'aus.png',0),(9,'Kolumbien','3',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43926/index.html',1,0,'col.png',0),(10,'Griechenland','3',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43949/index.html',1,0,'gre.png',0),(11,'Elfenbeinküste','3',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43854/index.html',1,0,'civ.png',0),(12,'Japan','3',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43819/index.html',1,0,'jpn.png',0),(13,'Uruguay','4',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43930/index.html',1,0,'uru.png',0),(14,'Costa Rica','4',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43901/index.html',1,0,'crc.png',0),(15,'England','4',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43942/index.html',1,0,'eng.png',0),(16,'Italien','4',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43954/index.html',1,0,'ita.png',0),(17,'Schweiz','5',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43971/index.html',1,0,'sui.png',0),(18,'Equador','5',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43927/index.html',1,0,'ecu.png',0),(19,'Frankreich','5',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43946/index.html',1,0,'fra.png',0),(20,'Honduras','5',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43909/index.html',1,0,'hon.png',0),(21,'Argentinien','6',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43922/index.html',1,0,'arg.png',0),(22,'Bosnien Herzogowina','6',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=44037/index.html',1,0,'bih.png',0),(23,'Iran','6',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43817/index.html',1,0,'irn.png',0),(24,'Nigeria','6',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43876/index.html',1,0,'nga.png',0),(25,'Deutschland','7',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43948/index.html',1,0,'ger.png',0),(26,'Portugal','7',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43963/index.html',1,0,'por.png',0),(27,'Ghana','7',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43860/index.html',1,0,'gha.png',0),(28,'USA','7',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43921/index.html',1,0,'usa.png',0),(29,'Belgien','8',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43935/index.html',1,0,'bel.png',0),(30,'Algerien','8',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43843/index.html',1,0,'alg.png',0),(31,'Russland','8',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43965/index.html',1,0,'rus.png',0),(32,'Südkorea','8',0,0,0,0,0,'http://www.fifa.com/worldcup/teams/team=43822/index.html',1,0,'kor.png',0),(50,'NULL-Mannschaft',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,1);
/*!40000 ALTER TABLE `mannschaft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `user_von` int(11) NOT NULL DEFAULT '0',
  `user_nach` int(11) NOT NULL DEFAULT '0',
  `titel` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `datum` date NOT NULL DEFAULT '0000-00-00',
  `zeit` time NOT NULL DEFAULT '00:00:00',
  `gelesen` tinyint(1) NOT NULL DEFAULT '0',
  `message_key` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`message_key`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--


--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `news_key` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(80) DEFAULT NULL,
  `text` text,
  `datum` date DEFAULT NULL,
  `zeit` time DEFAULT NULL,
  PRIMARY KEY (`news_key`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--


--
-- Table structure for table `runde`
--

DROP TABLE IF EXISTS `runde`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `runde` (
  `runde_key` smallint(6) NOT NULL DEFAULT '0',
  `Bezeichnung` varchar(30) NOT NULL DEFAULT '',
  `freigabe` tinyint(4) DEFAULT NULL,
  `Sortierung` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`runde_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `runde`
--

LOCK TABLES `runde` WRITE;
/*!40000 ALTER TABLE `runde` DISABLE KEYS */;
INSERT INTO `runde` VALUES (1,'Vorrunde',1,1),(2,'Achtelfinale',1,2),(3,'Viertelfinale',1,3),(4,'Halbfinale',1,4),(5,'Spiel um Platz 3',1,5),(6,'Finale',1,6);
/*!40000 ALTER TABLE `runde` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spieler`
--

DROP TABLE IF EXISTS `spieler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spieler` (
  `spieler_key` int(11) NOT NULL AUTO_INCREMENT,
  `spielername` varchar(20) DEFAULT NULL,
  `vorname` varchar(20) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `weltmeister` int(11) DEFAULT NULL,
  `bezahlt` varchar(5) DEFAULT NULL,
  `punkte` int(11) DEFAULT NULL,
  `exakte_tips` int(11) DEFAULT NULL,
  `richtige_Tendenz` int(11) DEFAULT NULL,
  `passwort` varchar(20) DEFAULT NULL,
  `Freigabe` tinyint(1) NOT NULL DEFAULT '0',
  `Newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `Rolle` varchar(15) NOT NULL DEFAULT '0',
  `login` varchar(15) NOT NULL DEFAULT '',
  `ICQ_NUMMER` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`spieler_key`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spieler`
--

LOCK TABLES `spieler` WRITE;
/*!40000 ALTER TABLE `spieler` DISABLE KEYS */;
INSERT INTO `spieler` VALUES (1,'Reiners','Reiner','0190-696969','rreiner@gmail.com',1,'0',0,0,0,'reiners',0,0,'ADMIN','reiners','NULL');
/*!40000 ALTER TABLE `spieler` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spieler_benutzergruppe`
--

DROP TABLE IF EXISTS `spieler_benutzergruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spieler_benutzergruppe` (
  `key_spieler` int(11) NOT NULL,
  `key_gruppe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spieler_benutzergruppe`
--

LOCK TABLES `spieler_benutzergruppe` WRITE;
/*!40000 ALTER TABLE `spieler_benutzergruppe` DISABLE KEYS */;
/*!40000 ALTER TABLE `spieler_benutzergruppe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_badwords`
--

DROP TABLE IF EXISTS `st_badwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_badwords` (
  `badword` varchar(25) NOT NULL DEFAULT '',
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_badwords`
--

LOCK TABLES `st_badwords` WRITE;
/*!40000 ALTER TABLE `st_badwords` DISABLE KEYS */;
/*!40000 ALTER TABLE `st_badwords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_entries`
--

DROP TABLE IF EXISTS `st_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_entries` (
  `comment` text NOT NULL,
  `date` varchar(10) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `homepage` varchar(150) NOT NULL DEFAULT '',
  `icq` int(9) NOT NULL DEFAULT '0',
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(27) NOT NULL DEFAULT '',
  `activation_code` varchar(32) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `time` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=186 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_entries`
--

LOCK TABLES `st_entries` WRITE;
/*!40000 ALTER TABLE `st_entries` DISABLE KEYS */;
INSERT INTO `st_entries` VALUES ('','26.05.2014','','http://',0,184,'79.204.39.172',0,'Yankeeboy','285cb069e9b253bcb7566c2a382a16cc','Welcome to America :love:','21:44'),('','04.06.2014','','http://',0,185,'79.204.62.214',0,'Abels','f34015a18f1c76f3700718f133c31ed7','Hallo zusammen!\r\n\r\nDer Stammtisch darf genutzt werden - gerne und ausdrücklich auch zu Themen, die den reinen Fußball-Horizont überschreiten :)\r\n\r\nAbels','22:59');
/*!40000 ALTER TABLE `st_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_ip_ban`
--

DROP TABLE IF EXISTS `st_ip_ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_ip_ban` (
  `id` mediumint(7) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` int(15) NOT NULL DEFAULT '0',
  `type` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=795 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_ip_ban`
--

LOCK TABLES `st_ip_ban` WRITE;
/*!40000 ALTER TABLE `st_ip_ban` DISABLE KEYS */;
INSERT INTO `st_ip_ban` VALUES (794,'79.204.62.214',1401915554,'entry'),(793,'79.204.62.214',1401915337,'stats'),(792,'2.241.152.220',1401911333,'stats');
/*!40000 ALTER TABLE `st_ip_ban` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_properties`
--

DROP TABLE IF EXISTS `st_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_properties` (
  `admin_email` varchar(50) NOT NULL DEFAULT '',
  `antiflood_ban` smallint(5) NOT NULL DEFAULT '0',
  `bbcode` tinyint(1) NOT NULL DEFAULT '0',
  `captcha` tinyint(1) NOT NULL DEFAULT '0',
  `check_email` varchar(15) NOT NULL DEFAULT '',
  `check_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `check_icq` tinyint(1) NOT NULL DEFAULT '0',
  `deactivate_html` varchar(15) NOT NULL DEFAULT '',
  `default_style` tinyint(3) NOT NULL DEFAULT '0',
  `default_template` tinyint(3) NOT NULL DEFAULT '0',
  `entries_per_site` tinyint(3) NOT NULL DEFAULT '0',
  `entry_length_limit` tinyint(1) NOT NULL DEFAULT '0',
  `entry_length_maximum` mediumint(7) NOT NULL DEFAULT '0',
  `entry_length_minimum` tinyint(3) NOT NULL DEFAULT '0',
  `guestbook_status` tinyint(1) NOT NULL DEFAULT '0',
  `guestbook_title` varchar(50) NOT NULL DEFAULT '',
  `images_in_entries` tinyint(1) NOT NULL DEFAULT '0',
  `language` varchar(10) NOT NULL DEFAULT '',
  `links_in_sitefunction` tinyint(3) NOT NULL DEFAULT '0',
  `max_word_length` tinyint(3) NOT NULL DEFAULT '0',
  `notification_entries` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(32) NOT NULL DEFAULT '',
  `release_entries` tinyint(1) NOT NULL DEFAULT '0',
  `show_ip` tinyint(1) NOT NULL DEFAULT '0',
  `smilies` tinyint(1) NOT NULL DEFAULT '0',
  `thanks_email` tinyint(1) NOT NULL DEFAULT '0',
  `statistic` tinyint(1) NOT NULL DEFAULT '0',
  `statistic_ban` smallint(5) NOT NULL DEFAULT '0',
  `username` varchar(15) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_properties`
--

LOCK TABLES `st_properties` WRITE;
/*!40000 ALTER TABLE `st_properties` DISABLE KEYS */;
INSERT INTO `st_properties` VALUES ('cheffe@bei-abels.de',20,1,0,'',0,0,'1',2,1,20,0,0,0,1,'WM-Stammtisch',1,'German',10,100,0,'38cb5df00ba0a25a453a8bb6a07a9da2',0,0,1,0,1,7200,'abels');
/*!40000 ALTER TABLE `st_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_smilies`
--

DROP TABLE IF EXISTS `st_smilies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_smilies` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '',
  `bbcode` varchar(15) NOT NULL DEFAULT '',
  `filename` varchar(20) NOT NULL DEFAULT '',
  `width` tinyint(3) NOT NULL DEFAULT '0',
  `height` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_smilies`
--

LOCK TABLES `st_smilies` WRITE;
/*!40000 ALTER TABLE `st_smilies` DISABLE KEYS */;
INSERT INTO `st_smilies` VALUES (1,'smile',':)','smile.gif',15,15),(2,'sad',':(','sad.gif',15,15),(3,'angry',':angry:','angry.gif',15,15),(4,'big grin',':D','biggrin.gif',15,15),(5,'cool',':cool:','cool.gif',15,15),(6,'frown',':frown:','frown.gif',15,15),(7,'indifferent',':-|','indifferent.gif',15,15),(8,'oh',':O','oh.gif',15,15),(9,'oh well',':-/','ohwell.gif',15,15),(10,'tonque',':P','tongue.gif',15,15),(11,'wink',';)','wink.gif',15,15),(12,'glasses','8)','glasses.gif',15,15),(13,'love',':love:','love.gif',15,15),(14,'shoked',':shoked:','shoked.gif',15,15),(15,'devil',':devil:','devil.gif',15,15),(16,'kiss',':kiss:','kiss.gif',15,15),(17,'pirate',':pirate:','pirate.gif',15,15);
/*!40000 ALTER TABLE `st_smilies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_statistic`
--

DROP TABLE IF EXISTS `st_statistic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_statistic` (
  `date` varchar(15) NOT NULL DEFAULT '',
  `hits` mediumint(7) NOT NULL DEFAULT '0',
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `visits` mediumint(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_statistic`
--

LOCK TABLES `st_statistic` WRITE;
/*!40000 ALTER TABLE `st_statistic` DISABLE KEYS */;
INSERT INTO `st_statistic` VALUES ('24.05.2014',5,78,1),('25.05.2014',3,79,2),('26.05.2014',8,80,3),('28.05.2014',4,81,4),('29.05.2014',11,82,3),('30.05.2014',9,83,6),('31.05.2014',4,84,3),('02.06.2014',5,85,1),('03.06.2014',7,86,4),('04.06.2014',6,87,2);
/*!40000 ALTER TABLE `st_statistic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_style`
--

DROP TABLE IF EXISTS `st_style`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_style` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `style` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_style`
--

LOCK TABLES `st_style` WRITE;
/*!40000 ALTER TABLE `st_style` DISABLE KEYS */;
INSERT INTO `st_style` VALUES (1,'ygGS','body {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-attachment: fixed;\n            background-repeat: no-repeat;\n            background-position: center center;\n            }\n\n            .guestbook_table {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-color: #000000;\n            }\n\n            .guestbook_table2 {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            }\n\n            A:link {\n            color:#000000;\n            text-decoration: none;\n            }\n\n            A:visited {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:active {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:hover {\n            color: #808080;\n            text-decoration: underline;\n            }\n\n            textarea {\n            background-color: #e3e3e3;\n            border: 1px solid #000000;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            input {\n            background-color: #ffff00;\n            border: 1px solid #000000;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            #captcha {\n            border: 1px solid #000000;\n            }'),(2,'myPHP Guestbook 2.0.1','body {\n            font-family: Tahom,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-attachment: fixed;\n            background-repeat: no-repeat;\n            background-position: center center;\n            }\n\n            .guestbook_table {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            background-color: #808080;\n            }\n\n            .guestbook_table2 {\n            font-family: Tahoma,Verdana,Helvetica;\n            font-size: 11px;\n            color: #000000;\n            }\n\n            A:link {\n            color:#000000;\n            text-decoration: none;\n            }\n\n            A:visited {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:active {\n            color: #000000;\n            text-decoration: none;\n            }\n\n            A:hover {\n            color: #808080;\n            text-decoration: underline;\n            }\n\n            textarea {\n            background-color: #f2f2f2;\n            border: 1px solid #808080;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            input {\n            background-color: #f2f2f2;\n            border: 1px solid #808080;\n            color: #000000;\n            font-size: 11px;\n            font-family: Tahoma,Verdana,Helvetica;\n            }\n\n            #captcha {\n            border: 1px solid #808080;\n            }');
/*!40000 ALTER TABLE `st_style` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `st_template`
--

DROP TABLE IF EXISTS `st_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `st_template` (
  `bgcolor` varchar(15) NOT NULL DEFAULT '',
  `bgimage` varchar(100) NOT NULL DEFAULT '',
  `border` tinyint(2) NOT NULL DEFAULT '0',
  `cellpadding` tinyint(2) NOT NULL DEFAULT '0',
  `cellspacing` tinyint(2) NOT NULL DEFAULT '0',
  `html` text NOT NULL,
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `image_email` varchar(25) NOT NULL DEFAULT '',
  `image_homepage` varchar(25) NOT NULL DEFAULT '',
  `name` varchar(25) NOT NULL DEFAULT '',
  `tablealign` varchar(6) NOT NULL DEFAULT '',
  `tablewidth` smallint(4) NOT NULL DEFAULT '0',
  `tdcolor` varchar(15) NOT NULL DEFAULT '',
  `td2color` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `st_template`
--

LOCK TABLES `st_template` WRITE;
/*!40000 ALTER TABLE `st_template` DISABLE KEYS */;
INSERT INTO `st_template` VALUES ('#ffffff','',1,2,1,'<table style=\"table-layout: fixed\" cellpadding=\"<$cellpadding$>\" cellspacing=\"<$cellspacing$>\" border=\"<$border$>\" width=\"<$tablewidth$>\" class=\"guestbook_table\">\n <tr>\n  <td width=\"55%\" bgcolor=\"<$tdcolor$>\" align=\"left\"><b><$name$></b></td>\n  <td width=\"15%\" bgcolor=\"<$tdcolor$>\" align=\"center\"><$email_icon$> <$homepage_icon$> <$icq_icon$></td>\n  <td width=\"30%\" bgcolor=\"<$tdcolor$>\" align=\"center\"><$date$> | <$time$></td>\n </tr>\n <tr>\n  <td colspan=\"3\" bgcolor=\"<$td2color$>\" align=\"left\"><span style=\"font-size: 10px;\"><$ip$></span>\n  <$text$><$comment$></td>\n </tr>\n</table>\n<br />',1,'emailxp.gif','homepagexp.gif','myPHP Guestbook 2.0.1','center',700,'#f2f2f2','#eeeeff'),('#ffffff','',1,2,0,'<table border=\"<$border$>\" cellspacing=\"<$cellspacing$>\" cellpadding=\"<$cellpadding$>\" style=\"table-layout: fixed\" width=\"<$tablewidth$>\" class=\"guestbook_table2\">\n <tr>\n  <td width=\"85%\" bgcolor=\"<$tdcolor$>\" align=\"left\" style=\"border-top: 1px solid #000000\"><b><$name$></b>, schrieb am <$date$> um <$time$> Uhr</td>\n  <td width=\"15%\" bgcolor=\"<$tdcolor$>\" align=\"right\" style=\"border-top: 1px solid #000000\"><$email_icon$> <$homepage_icon$> <$icq_icon$></td>\n </tr>\n <tr>\n  <td colspan=\"2\" bgcolor=\"<$td2color$>\" align=\"left\"><span style=\"font-size: 10px;\"><$ip$></span><$text$><$comment$></td>\n </tr>\n</table>\n<br />',2,'emailmodern.png','homepagemodern.png','Light','center',600,'#DCDCDC','#ADD8E6'),('#ffffff','',1,3,1,'<table cellpadding=\"<$cellpadding$>\" cellspacing=\"<$cellspacing$>\" border=\"<$border$>\" width=\"<$tablewidth$>\" class=\"guestbook_table\">\n<tr>\n   <td width=\"22%\" bgcolor=\"<$td2color$>\" align=\"left\" valign=\"top\">\n     <b><$name$></b><br /><$date$> <$time$><br />\n      #<$id$><br />\n<$email_icon$> <$homepage_icon$> <$icq_icon$>\n     <br />\n     <$ip$>\n   </td>\n  <td width=\"78%\" bgcolor=\"<$td2color$>\" align=\"left\" valign=\"top\">\n    <$text$><$comment$>\n  </td>\n</tr>\n</table>',3,'emailmodern.png','homepagemodern.png','myPHP Guestbook 2.0.0','center',600,'#dcdcdc','#ADD8E6');
/*!40000 ALTER TABLE `st_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tips`
--

DROP TABLE IF EXISTS `tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tips` (
  `key_spieler` int(11) NOT NULL DEFAULT '0',
  `key_spiel` int(11) NOT NULL DEFAULT '0',
  `tore_ms1` int(11) DEFAULT NULL,
  `tore_ms2` int(11) DEFAULT NULL,
  `tendenz` int(11) DEFAULT NULL,
  `erreichte_punkte` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tips`
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-04 17:24:49
