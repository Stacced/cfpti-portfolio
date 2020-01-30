-- --------------------------------------------------------
-- Hôte :                        127.0.0.1
-- Version du serveur:           10.3.18-MariaDB-0+deb10u1 - Debian 10
-- SE du serveur:                debian-linux-gnu
-- HeidiSQL Version:             10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Listage de la structure de la base pour cfpti_portfolio
CREATE DATABASE IF NOT EXISTS `cfpti_portfolio` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `cfpti_portfolio`;

-- Listage de la structure de la table cfpti_portfolio. medias
CREATE TABLE IF NOT EXISTS `medias` (
  `idMedia` int(11) NOT NULL AUTO_INCREMENT,
  `mediaType` text NOT NULL DEFAULT '',
  `mediaName` text NOT NULL DEFAULT '',
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `editDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `idPost` int(11) NOT NULL,
  PRIMARY KEY (`idMedia`),
  KEY `fk_idPost` (`idPost`),
  CONSTRAINT `fk_idPost` FOREIGN KEY (`idPost`) REFERENCES `posts` (`idPost`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table cfpti_portfolio. posts
CREATE TABLE IF NOT EXISTS `posts` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL DEFAULT '',
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `editDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idPost`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- Les données exportées n'étaient pas sélectionnées.

-- Ajout utilisateur pour la DB
CREATE USER 'cfptportfolio'@'localhost' IDENTIFIED BY 'cfpti2020';
GRANT ALL PRIVILEGES ON cfpti_portfolio.* TO 'cfptportfolio'@'localhost';
FLUSH PRIVILEGES;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
