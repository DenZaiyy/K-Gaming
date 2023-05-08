-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour kgaming
CREATE DATABASE IF NOT EXISTS `kgaming` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `kgaming`;

-- Listage de la structure de table kgaming. category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.category : ~0 rows (environ)
INSERT IGNORE INTO `category` (`id`, `label`) VALUES
	(1, 'PC'),
	(2, 'PlayStation'),
	(3, 'Xbox');

-- Listage de la structure de table kgaming. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Listage des données de la table kgaming.doctrine_migration_versions : ~0 rows (environ)
INSERT IGNORE INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20230505133924', '2023-05-05 16:47:20', 388);

-- Listage de la structure de table kgaming. game
CREATE TABLE IF NOT EXISTS `game` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_release` datetime NOT NULL,
  `cover` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.game : ~4 rows (environ)
INSERT IGNORE INTO `game` (`id`, `label`, `price`, `description`, `date_release`, `cover`) VALUES
	(1, 'Rocket League', 19.99, 'test', '2023-05-05 21:56:31', 'https://images.igdb.com/igdb/image/upload/t_1080p/co5w0w.jpg'),
	(2, 'Rust', 39.99, 'test', '2023-05-08 10:57:06', 'https://store-speedtree-com.exactdn.com/site-assets/uploads/Rust-cover.jpg?strip=all&lossy=1&quality=73&ssl=1'),
	(3, 'New World', 38.99, 'Explore a thrilling, open-world MMO filled with danger and opportunity where you\'ll forge a new destiny on the supernatural island of Aeternum.', '2021-09-28 00:00:00', 'https://images.ctfassets.net/j95d1p8hsuun/5tad2SDo5WucQxXTcS8L7V/e04ed6cddc797efcf66638f64a7b3ca9/Standard_edition-boxart.jpg'),
	(4, 'Raft', 19.99, 'Raft throws you and your friends into an epic oceanic adventure! Alone or together, players battle to survive a perilous voyage across a vast sea! Gather debris, scavenge reefs and build your own floating home, but be wary of the man-eating sharks!', '2022-06-20 00:00:00', 'https://cdn.cdkeys.com/700x700/media/catalog/product/i/n/inquisitor_artwork_cover_6_.jpg');

-- Listage de la structure de table kgaming. game_genre
CREATE TABLE IF NOT EXISTS `game_genre` (
  `game_id` int NOT NULL,
  `genre_id` int NOT NULL,
  PRIMARY KEY (`game_id`,`genre_id`),
  KEY `IDX_B1634A77E48FD905` (`game_id`),
  KEY `IDX_B1634A774296D31F` (`genre_id`),
  CONSTRAINT `FK_B1634A774296D31F` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B1634A77E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.game_genre : ~0 rows (environ)

-- Listage de la structure de table kgaming. game_plateform
CREATE TABLE IF NOT EXISTS `game_plateform` (
  `game_id` int NOT NULL,
  `plateform_id` int NOT NULL,
  PRIMARY KEY (`game_id`,`plateform_id`),
  KEY `IDX_DC247165E48FD905` (`game_id`),
  KEY `IDX_DC247165CCAA542F` (`plateform_id`),
  CONSTRAINT `FK_DC247165CCAA542F` FOREIGN KEY (`plateform_id`) REFERENCES `plateform` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DC247165E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.game_plateform : ~13 rows (environ)
INSERT IGNORE INTO `game_plateform` (`game_id`, `plateform_id`) VALUES
	(1, 1),
	(1, 4),
	(1, 6),
	(1, 7),
	(1, 8),
	(1, 9),
	(2, 1),
	(3, 1),
	(3, 6),
	(3, 7),
	(3, 8),
	(3, 9),
	(4, 1);

-- Listage de la structure de table kgaming. genre
CREATE TABLE IF NOT EXISTS `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.genre : ~0 rows (environ)

-- Listage de la structure de table kgaming. messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.messenger_messages : ~0 rows (environ)

-- Listage de la structure de table kgaming. plateform
CREATE TABLE IF NOT EXISTS `plateform` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E82B06712469DE2` (`category_id`),
  CONSTRAINT `FK_E82B06712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.plateform : ~9 rows (environ)
INSERT IGNORE INTO `plateform` (`id`, `category_id`, `label`) VALUES
	(1, 1, 'Steam'),
	(2, 1, 'Origin'),
	(3, 1, 'Battle.net'),
	(4, 1, 'Epic Games'),
	(5, 1, 'Ubisoft'),
	(6, 2, 'PlayStation 4'),
	(7, 3, 'Xbox Serie X/S'),
	(8, 2, 'PlayStation 5'),
	(9, 3, 'Xbox One');

-- Listage de la structure de table kgaming. purchase
CREATE TABLE IF NOT EXISTS `purchase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_cp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `billing_city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6117D13BA76ED395` (`user_id`),
  CONSTRAINT `FK_6117D13BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.purchase : ~6 rows (environ)
INSERT IGNORE INTO `purchase` (`id`, `user_id`, `firstname`, `lastname`, `billing_address`, `billing_cp`, `billing_city`, `created_at`) VALUES
	(1, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(2, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(3, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(4, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(5, 2, 'Test', 'BAHOUI', '123 rue du test', '68000', 'Colmar', '2023-05-08 15:50:36'),
	(6, 2, 'Test', 'BAHOUI', '123 rue du test', '68000', 'Colmar', '2023-05-08 15:50:36'),
	(7, 2, 'Test', 'BAHOUI', '123 rue du test', '68000', 'Colmar', '2023-05-08 15:50:36');

-- Listage de la structure de table kgaming. reset_password_request
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.reset_password_request : ~0 rows (environ)

-- Listage de la structure de table kgaming. stock
CREATE TABLE IF NOT EXISTS `stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `purchase_id` int DEFAULT NULL,
  `license_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_availability` datetime NOT NULL,
  `is_available` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4B365660E48FD905` (`game_id`),
  KEY `IDX_4B365660558FBEB9` (`purchase_id`),
  CONSTRAINT `FK_4B365660558FBEB9` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`id`),
  CONSTRAINT `FK_4B365660E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.stock : ~10 rows (environ)
INSERT IGNORE INTO `stock` (`id`, `game_id`, `purchase_id`, `license_key`, `date_availability`, `is_available`) VALUES
	(1, 1, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(2, 1, 2, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(3, 1, NULL, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(4, 1, NULL, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(5, 2, 5, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(6, 2, 3, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(7, 2, 4, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(8, 3, NULL, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(9, 3, 6, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(10, 4, NULL, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1);

-- Listage de la structure de table kgaming. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` longtext COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table kgaming.user : ~2 rows (environ)
INSERT IGNORE INTO `user` (`id`, `username`, `roles`, `password`, `email`, `avatar`, `is_verified`) VALUES
	(1, 'denz', '["ROLE_ADMIN"]', '$2y$13$jjQF4rgjxHoO6gHwK/It/ekENtEV8mJnzqyQAWmsM58Ch.Xs9nVJS', 'admin@kgaming.com', NULL, 1),
	(2, 'test', '[]', '$2y$13$99yvYFVngTHU0WmhOTP/dO0qzPJn80PfYjANek6PvFG9xw7YpxkMK', 'test@test.com', NULL, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
