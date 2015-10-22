CREATE DATABASE  IF NOT EXISTS `sn` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `sn`;
-- MySQL dump 10.13  Distrib 5.6.19, for debian-linux-gnu (x86_64)
--
-- Host: deb02    Database: sn
-- ------------------------------------------------------
-- Server version	5.6.21-1~dotdeb.1

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
-- Table structure for table `node`
--

DROP TABLE IF EXISTS `node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT 'cert id - identyfikator certyfikatu',
  `stime` datetime NOT NULL,
  `status` enum('running','done','incomplete') COLLATE utf8_polish_ci NOT NULL,
  `hid` int(11) NOT NULL COMMENT 'host id',
  PRIMARY KEY (`id`),
  KEY `fk_host_idx` (`hid`),
  CONSTRAINT `fk_host` FOREIGN KEY (`hid`) REFERENCES `host` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=144912 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `smtp`
--

DROP TABLE IF EXISTS `smtp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8_polish_ci NOT NULL,
  `ipint` int(10) unsigned NOT NULL,
  `netint` int(11) NOT NULL,
  `port` int(11) NOT NULL DEFAULT '25',
  `helo` text COLLATE utf8_polish_ci,
  `helo-code` int(11) DEFAULT NULL,
  `ehlo` text COLLATE utf8_polish_ci,
  `ehlo-code` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  `greetings-code` int(11) DEFAULT NULL,
  `greetings-text` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  `checkTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `netint` (`netint`),
  KEY `checkTime` (`checkTime`),
  KEY `nodechecktime` (`checkTime`)
) ENGINE=InnoDB AUTO_INCREMENT=18306203 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `results_tab` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `check`
--

DROP TABLE IF EXISTS `check`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `check` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node` int(11) NOT NULL,
  `net` int(11) NOT NULL,
  `module` int(11) NOT NULL,
  `result` int(11) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resultid` (`module`,`result`),
  KEY `fk_node_idx` (`node`),
  KEY `fk_module_idx` (`module`),
  KEY `fk_net_idx` (`net`),
  CONSTRAINT `fk_module` FOREIGN KEY (`module`) REFERENCES `module` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_net` FOREIGN KEY (`net`) REFERENCES `ipv4class` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_node` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `host`
--

DROP TABLE IF EXISTS `host`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `n` int(11) NOT NULL DEFAULT '2' COMMENT 'maksymalna ilość nodów',
  `t` int(11) NOT NULL DEFAULT '1' COMMENT 'threads',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin2;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ipv4class`
--

DROP TABLE IF EXISTS `ipv4class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv4class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `mask` varchar(2) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,
  `status` enum('TODO','FINISHED') CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL DEFAULT 'TODO' COMMENT 'do usuniecia - trzeba to przeniesc do innej tabeli',
  PRIMARY KEY (`id`),
  UNIQUE KEY `network` (`ip`,`mask`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=56516 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-01-08 23:40:39
