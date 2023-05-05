-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.32 - MySQL Community Server - GPL
-- SE du serveur:                macos13
-- HeidiSQL Version:             12.4.0.6659
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour kg-test
CREATE DATABASE IF NOT EXISTS `kg-test` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `kg-test`;

-- Listage de la structure de la table kg-test. category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.category : ~3 rows (environ)
INSERT IGNORE INTO `category` (`id`, `label`) VALUES
	(1, 'PC'),
	(2, 'PlayStation'),
	(3, 'Xbox');

-- Listage de la structure de la table kg-test. game
CREATE TABLE IF NOT EXISTS `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `price` float NOT NULL,
  `description` longtext NOT NULL,
  `date_release` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.game : ~6 rows (environ)
INSERT IGNORE INTO `game` (`id`, `label`, `price`, `description`, `date_release`) VALUES
	(1, 'Rocket League', 19.99, 'Jeu de football, avec une voiture, et le but c\'est de marquer conte dans le but de l\'adversaire tout en utilisant sa voiture!', '2015-04-27'),
	(2, 'New World', 38.99, 'Explore a thrilling, open-world MMO filled with danger and opportunity where you\'ll forge a new destiny on the supernatural island of Aeternum.', '2021-09-28'),
	(3, 'Rust', 39.99, 'The only aim in Rust is to survive. Everything wants you to die - the island’s wildlife and other inhabitants, the environment, other survivors. Do whatever it takes to last another night.', '2018-02-08'),
	(4, 'Raft', 19.99, 'Raft throws you and your friends into an epic oceanic adventure! Alone or together, players battle to survive a perilous voyage across a vast sea! Gather debris, scavenge reefs and build your own floating home, but be wary of the man-eating sharks!', '2022-06-20'),
	(5, 'Test', 30.99, 'test ', '2023-06-04'),
	(6, 'Call of duty : POOP', 80, 'Jeu nulle', '2024-11-01');

-- Listage de la structure de la table kg-test. game_genre
CREATE TABLE IF NOT EXISTS `game_genre` (
  `id_genre` int NOT NULL,
  `id_game` int NOT NULL,
  PRIMARY KEY (`id_genre`,`id_game`),
  KEY `FK__gameFGDG` (`id_game`),
  CONSTRAINT `FK__gameFGDG` FOREIGN KEY (`id_game`) REFERENCES `game` (`id`),
  CONSTRAINT `FK__genreGDFGDF` FOREIGN KEY (`id_genre`) REFERENCES `genre` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.game_genre : ~6 rows (environ)
INSERT IGNORE INTO `game_genre` (`id_genre`, `id_game`) VALUES
	(6, 1),
	(7, 1),
	(9, 1),
	(5, 2),
	(9, 3),
	(10, 4);

-- Listage de la structure de la table kg-test. game_plateform
CREATE TABLE IF NOT EXISTS `game_plateform` (
  `id_platform` int NOT NULL,
  `id_game` int NOT NULL,
  PRIMARY KEY (`id_platform`,`id_game`),
  KEY `FK__game` (`id_game`),
  CONSTRAINT `FK__game` FOREIGN KEY (`id_game`) REFERENCES `game` (`id`),
  CONSTRAINT `FK__plateform` FOREIGN KEY (`id_platform`) REFERENCES `plateform` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.game_plateform : ~13 rows (environ)
INSERT IGNORE INTO `game_plateform` (`id_platform`, `id_game`) VALUES
	(1, 1),
	(4, 1),
	(6, 1),
	(7, 1),
	(8, 1),
	(9, 1),
	(1, 2),
	(1, 3),
	(6, 3),
	(7, 3),
	(8, 3),
	(9, 3),
	(1, 4);

-- Listage de la structure de la table kg-test. genre
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.genre : ~10 rows (environ)
INSERT IGNORE INTO `genre` (`id`, `label`) VALUES
	(1, 'FPS'),
	(2, 'Aventure'),
	(3, 'Simulation'),
	(4, 'Stratégie'),
	(5, 'MMO'),
	(6, 'Sport'),
	(7, 'Action'),
	(8, 'PVP En ligne'),
	(9, 'Cross-Plateforme'),
	(10, 'Survie');

-- Listage de la structure de la table kg-test. plateform
CREATE TABLE IF NOT EXISTS `plateform` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `id_category` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `plateform_category_FK` (`id_category`),
  CONSTRAINT `plateform_category_FK` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.plateform : ~9 rows (environ)
INSERT IGNORE INTO `plateform` (`id`, `label`, `id_category`) VALUES
	(1, 'Steam', 1),
	(2, 'Origin', 1),
	(3, 'Battle.net', 1),
	(4, 'Epic Games', 1),
	(5, 'Ubisoft', 1),
	(6, 'PlayStation 4', 2),
	(7, 'Xbox Serie X/S', 3),
	(8, 'PlayStation 5', 2),
	(9, 'Xbox One', 3);

-- Listage de la structure de la table kg-test. purchase
CREATE TABLE IF NOT EXISTS `purchase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `billing_address` varchar(255) NOT NULL,
  `billing_cp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `billing_city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_order_user` (`id_user`),
  CONSTRAINT `FK_order_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.purchase : ~6 rows (environ)
INSERT IGNORE INTO `purchase` (`id`, `firstname`, `lastname`, `billing_address`, `billing_cp`, `billing_city`, `created_at`, `id_user`) VALUES
	(1, 'Kevin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-04 13:43:20', 2),
	(2, 'Abdel', 'Test', '123 rue du test', '68000', 'Colmar', '2023-05-05 11:51:58', 3),
	(3, 'Kevin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-05 11:54:19', 2),
	(4, 'Gerard', 'Gerard', '123 rue du test', '68000', 'Colmar', '2023-05-05 11:55:17', 4),
	(5, 'Gerard', 'Gerard', '123 rue du test', '68000', 'Colmar', '2023-05-05 11:55:17', 4),
	(6, 'Gerard', 'Gerard', '123 rue du test', '68000', 'Colmar', '2023-05-05 11:55:17', 4),
	(7, 'Gerard', 'Gerard', '123 rue du test', '68000', 'Colmar', '2023-05-05 11:55:17', 4);

-- Listage de la structure de la table kg-test. stock
CREATE TABLE IF NOT EXISTS `stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `license_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_availability` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `id_purchase` int DEFAULT NULL,
  `id_game` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_stock_game` (`id_game`),
  KEY `FK_stock_order` (`id_purchase`) USING BTREE,
  CONSTRAINT `FK_stock_game` FOREIGN KEY (`id_game`) REFERENCES `game` (`id`),
  CONSTRAINT `FK_stock_order` FOREIGN KEY (`id_purchase`) REFERENCES `purchase` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.stock : ~5 rows (environ)
INSERT IGNORE INTO `stock` (`id`, `license_key`, `date_availability`, `is_available`, `id_purchase`, `id_game`) VALUES
	(1, 'gpVLwyvNnXwwBCD9', '2023-05-04 00:00:00', 0, 1, 3),
	(2, 'EhNN1XhxvTG3aW5u', '2023-05-04 00:00:00', 0, 7, 3),
	(3, 'swIrU5tyTDwNXJzA', '2023-05-04 00:00:00', 1, NULL, 3),
	(4, 'L9HdBMINi9vifxyz', '2023-05-04 00:00:00', 0, 1, 2),
	(5, 'vUO9yRnl3BU6NWvu', '2023-05-04 00:00:00', 0, 6, 2),
	(6, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 2, 2),
	(7, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 2, 2),
	(8, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 2, 2),
	(9, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 2, 2),
	(10, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 2, 2),
	(11, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 3, 4),
	(12, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 3, 4),
	(13, 'DFSFSFSFSFSDFSFDFS', '2023-05-05 14:16:56', 0, 3, 1);

-- Listage de la structure de la table kg-test. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `role` json NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table kg-test.user : ~1 rows (environ)
INSERT IGNORE INTO `user` (`id`, `username`, `password`, `email`, `avatar`, `role`, `is_verified`) VALUES
	(2, 'denz', 'denz', 'denz@denz.com', NULL, '["ROLE_USER"]', 0),
	(3, 'abdel', 'abdel', 'abdel@abdel.com', NULL, '["ROLE_USER"]', 0),
	(4, 'gerard', 'gerard', 'lea@lea.com', NULL, '["ROLE_USER"]', 0);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
