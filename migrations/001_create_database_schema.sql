/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.4.12-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: plantera
-- ------------------------------------------------------
-- Server version	11.4.12-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Current Database: `plantera`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `plantera` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `plantera`;

--
-- Table structure for table `ATTENDANCE`
--

DROP TABLE IF EXISTS `ATTENDANCE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ATTENDANCE` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `STATUS` tinyint(4) NOT NULL,
  `STUDENT_ID` bigint(20) NOT NULL,
  `LESSON_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_lesson_student` (`LESSON_ID`,`STUDENT_ID`),
  KEY `fk_attendance_student` (`STUDENT_ID`),
  CONSTRAINT `fk_attendance_lesson` FOREIGN KEY (`LESSON_ID`) REFERENCES `LESSON` (`ID`),
  CONSTRAINT `fk_attendance_student` FOREIGN KEY (`STUDENT_ID`) REFERENCES `STUDENT` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CLASS`
--

DROP TABLE IF EXISTS `CLASS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CLASS` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) NOT NULL,
  `YEAR` varchar(9) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CLASS_SCHEDULE`
--

DROP TABLE IF EXISTS `CLASS_SCHEDULE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CLASS_SCHEDULE` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `WEEKDAY` tinyint(4) NOT NULL,
  `START_TIME` time NOT NULL,
  `END_TIME` time NOT NULL,
  `CLASS_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_schedule_class` (`CLASS_ID`),
  CONSTRAINT `fk_schedule_class` FOREIGN KEY (`CLASS_ID`) REFERENCES `CLASS` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `LESSON`
--

DROP TABLE IF EXISTS `LESSON`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `LESSON` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `LESSON_DATE` date NOT NULL,
  `PLANNED_CONTENT` varchar(255) DEFAULT NULL,
  `CONTENT` varchar(255) DEFAULT NULL,
  `STATUS` tinyint(4) NOT NULL,
  `CLASS_ID` bigint(20) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_lesson_class` (`CLASS_ID`),
  CONSTRAINT `fk_lesson_class` FOREIGN KEY (`CLASS_ID`) REFERENCES `CLASS` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `STUDENT`
--

DROP TABLE IF EXISTS `STUDENT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `STUDENT` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(150) NOT NULL,
  `BIRTH_DATE` date DEFAULT NULL,
  `REGISTRATION` varchar(45) NOT NULL,
  `CLASS_ID` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_student_registration` (`REGISTRATION`),
  KEY `fk_student_class` (`CLASS_ID`),
  CONSTRAINT `fk_student_class` FOREIGN KEY (`CLASS_ID`) REFERENCES `CLASS` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `STUDENT_has_USER`
--

DROP TABLE IF EXISTS `STUDENT_has_USER`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `STUDENT_has_USER` (
  `STUDENT_ID` bigint(20) NOT NULL,
  `USER_ID` bigint(20) NOT NULL,
  `RELATIONSHIP` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`STUDENT_ID`,`USER_ID`),
  KEY `fk_shu_user` (`USER_ID`),
  CONSTRAINT `fk_shu_student` FOREIGN KEY (`STUDENT_ID`) REFERENCES `STUDENT` (`ID`),
  CONSTRAINT `fk_shu_user` FOREIGN KEY (`USER_ID`) REFERENCES `USER` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `USER`
--

DROP TABLE IF EXISTS `USER`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `USER` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(30) NOT NULL,
  `NAME` varchar(150) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `CELLPHONE` varchar(15) DEFAULT NULL,
  `CPF` char(11) DEFAULT NULL,
  `UF` char(2) DEFAULT NULL,
  `TYPE` tinyint(4) NOT NULL,
  `PASSWORD_HASH` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `uq_user_username` (`USERNAME`),
  UNIQUE KEY `uq_user_email` (`EMAIL`),
  CONSTRAINT `chk_user_type` CHECK (`TYPE` in (1,2,3))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'plantera'
--

--
-- Dumping routines for database 'plantera'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-09-21 19:44:17
