/*
 * This file is part of the akAdmin package.
 * (c) 2010 Azat Khuzhin <dohardgopro@gmail.com>
 *
 * For the full copyright and license information, please view http://www.gnu.org/licenses/gpl-2.0.html
 */

-- MySQL dump 10.13  Distrib 5.1.48, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: akAdmin
-- ------------------------------------------------------
-- Server version	5.1.48-1

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
-- Table structure for table `test_akadmin_sections`
--

DROP TABLE IF EXISTS `test_akadmin_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_akadmin_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `createTime` bigint(20) unsigned NOT NULL,
  `editTime` bigint(20) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `tableName` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parentid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_akadmin_sections_fields`
--

DROP TABLE IF EXISTS `test_akadmin_sections_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_akadmin_sections_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `createTime` bigint(20) unsigned NOT NULL,
  `editTime` bigint(20) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `field` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  `sid` int(11) unsigned NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` int(2) NOT NULL,
  `type` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid` (`sid`,`field`),
  KEY `sid_5` (`sid`,`hidden`,`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_akadmin_users`
--

DROP TABLE IF EXISTS `test_akadmin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_akadmin_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `createTime` bigint(20) unsigned NOT NULL,
  `editTime` bigint(20) unsigned NOT NULL,
  `lastTime` bigint(20) unsigned NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_akadmin_users_grants`
--

DROP TABLE IF EXISTS `test_akadmin_users_grants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_akadmin_users_grants` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `grants` int(11) unsigned NOT NULL,
  `uid` int(11) unsigned NOT NULL,
  `createTime` bigint(20) unsigned NOT NULL,
  `editTime` bigint(20) unsigned NOT NULL,
  `sid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sid_2` (`sid`,`uid`),
  KEY `sid` (`sid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-08-03  1:49:31
