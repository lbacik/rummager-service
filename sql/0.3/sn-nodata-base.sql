-- MySQL dump 10.13  Distrib 5.6.19, for debian-linux-gnu (x86_64)
--
-- Host: serwer1448974.home.pl    Database: 16046184_sn2
-- ------------------------------------------------------
-- Server version	5.5.44-37.3-log

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
) ENGINE=InnoDB AUTO_INCREMENT=192176 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `smtp`
--

DROP TABLE IF EXISTS `smtp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `checkid` int(11) NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `port` int(11) NOT NULL DEFAULT '25',
  `helo` text COLLATE utf8_polish_ci,
  `helo-code` int(11) DEFAULT NULL,
  `ehlo` text COLLATE utf8_polish_ci,
  `ehlo-code` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  `greetings-code` int(11) DEFAULT NULL,
  `greetings-text` varchar(45) COLLATE utf8_polish_ci DEFAULT NULL,
  `checkTime` datetime DEFAULT NULL,
  `tstart` time DEFAULT NULL,
  `tcon` time DEFAULT NULL,
  `tend` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_check_idx` (`checkid`),
  KEY `checkTime` (`checkTime`) USING BTREE,
  KEY `ip` (`ip`) USING BTREE,
  CONSTRAINT `fk_check` FOREIGN KEY (`checkid`) REFERENCES `check` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=124028757 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci; -- ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `node_last_check`
--

DROP TABLE IF EXISTS `node_last_check`;
/*!50001 DROP VIEW IF EXISTS `node_last_check`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `node_last_check` AS SELECT 
 1 AS `host`,
 1 AS `nodeid`,
 1 AS `stime`,
 1 AS `ip`,
 1 AS `mask`,
 1 AS `lastcheck`,
 1 AS `minutes ago`*/;
SET character_set_client = @saved_cs_client;

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
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_node_idx` (`node`),
  KEY `fk_module_idx` (`module`),
  KEY `fk_net_idx` (`net`),
  CONSTRAINT `fk_module` FOREIGN KEY (`module`) REFERENCES `module` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_net` FOREIGN KEY (`net`) REFERENCES `ipv4class` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_node` FOREIGN KEY (`node`) REFERENCES `node` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=48463 DEFAULT CHARSET=latin1;
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
  `n` int(11) NOT NULL DEFAULT '1' COMMENT 'maksymalna ilość nodów',
  `t` int(11) NOT NULL DEFAULT '1' COMMENT 'threads',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin2;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ipv4_reserved`
--

DROP TABLE IF EXISTS `ipv4_reserved`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipv4_reserved` (
  `ip` varchar(15) NOT NULL,
  `mask` int(11) NOT NULL,
  PRIMARY KEY (`ip`,`mask`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2;
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

--
-- Table structure for table `dw_f_ip`
--

DROP TABLE IF EXISTS `dw_f_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dw_f_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `hostid` varchar(45) NOT NULL,
  `hosts` int(11) NOT NULL,
  `smtp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_date_idx` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=697 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `node_last_check`
--

/*!50001 DROP VIEW IF EXISTS `node_last_check`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `node_last_check` AS select `n`.`hid` AS `host`,`n`.`id` AS `nodeid`,`n`.`stime` AS `stime`,`i`.`ip` AS `ip`,`i`.`mask` AS `mask`,(case when (max(`s`.`checkTime`) is not null) then max(`s`.`checkTime`) else `n`.`stime` end) AS `lastcheck`,(case when (max(`s`.`checkTime`) is not null) then timestampdiff(MINUTE,max(`s`.`checkTime`),now()) else timestampdiff(MINUTE,`n`.`stime`,now()) end) AS `minutes ago` from (((`node` `n` join `check` `c` on((`n`.`id` = `c`.`node`))) join `ipv4class` `i` on((`c`.`net` = `i`.`id`))) left join `smtp` `s` on((`c`.`id` = `s`.`checkid`))) where (`n`.`status` = 'running') group by `n`.`hid`,`n`.`id`,`n`.`stime`,`i`.`ip`,`i`.`mask` order by (case when (max(`s`.`checkTime`) is not null) then max(`s`.`checkTime`) else `n`.`stime` end) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Dumping routines for database '16046184_sn2'
--
/*!50003 DROP PROCEDURE IF EXISTS `etl_f_ip` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_UNSIGNED_SUBTRACTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `etl_f_ip`()
BEGIN

	declare lastcheck date default 0;
	declare difference int default 0;

	select `date` into lastcheck from dw_f_ip order by id desc limit 1;
	select datediff(lastcheck, curdate()) into difference;

	IF difference < -1 THEN
		INSERT INTO dw_f_ip (`date`, `hostid`, `hosts`, `smtp`)
		SELECT cast( s.checktime AS date ) AS `date`
			,h.id as hostid
			,count( s.id ) AS `hosts`
			,count( s.tcon ) AS smtp
		FROM smtp s
			INNER JOIN `check` c ON s.checkid = c.id
			INNER JOIN node n ON c.node = n.id
			INNER JOIN host h ON n.hid = h.id
		WHERE s.checktime >= date_add(lastcheck, INTERVAL 1 DAY) 
			AND s.checktime < curdate()
		GROUP BY cast( s.checktime AS date ), h.id;	
	END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `node_status_update` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_UNSIGNED_SUBTRACTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `node_status_update`()
BEGIN

-- usuniecie juz nieaktywnych nodow
update node 
	set status = 'done' 
where id in (select nodeid 
                from node_last_check
                where `minutes ago` > 30);

-- usuniecie bledow 
/*
UPDATE node 
	SET STATUS = 'incomplete' 
WHERE STATUS = 'running' 
	AND id NOT IN (SELECT nodeid FROM `rnodes`);
*/

-- tymczasowo tutaj
/*
UPDATE smtp 
	SET netint = inet_aton( substring_index( `ip` , '.', 2 )) 
WHERE netint = 0;
*/
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-09-14 13:25:02
