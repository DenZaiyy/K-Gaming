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

-- Listage des données de la table kgaming.category : ~3 rows (environ)
DELETE FROM `category`;
INSERT INTO `category` (`id`, `label`) VALUES
	(1, 'PC'),
	(2, 'PlayStation'),
	(3, 'Xbox');

-- Listage des données de la table kgaming.doctrine_migration_versions : ~1 rows (environ)
DELETE FROM `doctrine_migration_versions`;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20230505133924', '2023-05-05 16:47:20', 388);

-- Listage des données de la table kgaming.game : ~6 rows (environ)
DELETE FROM `game`;
INSERT INTO `game` (`id`, `label`, `price`, `date_release`) VALUES
	(1, 'Rocket League', 19.99, '2015-07-06'),
	(2, 'Rust', 39.99, '2023-05-08'),
	(3, 'New World', 38.99, '2021-09-28'),
	(4, 'Raft', 19.99, '2022-06-20'),
	(6, 'Counter-Strike 2', 19.99, '2025-06-20'),
	(7, 'Diablo IV', 19.99, '2023-06-06');

-- Listage des données de la table kgaming.game_genre : ~7 rows (environ)
DELETE FROM `game_genre`;
INSERT INTO `game_genre` (`game_id`, `genre_id`) VALUES
	(1, 6),
	(1, 7),
	(1, 9),
	(1, 11),
	(2, 5),
	(3, 9),
	(4, 10);

-- Listage des données de la table kgaming.game_plateform : ~20 rows (environ)
DELETE FROM `game_plateform`;
INSERT INTO `game_plateform` (`game_id`, `plateform_id`) VALUES
	(1, 1),
	(1, 4),
	(1, 6),
	(1, 7),
	(1, 8),
	(1, 9),
	(2, 1),
	(2, 6),
	(2, 7),
	(2, 8),
	(2, 9),
	(3, 1),
	(3, 8),
	(4, 1),
	(6, 1),
	(7, 1),
	(7, 6),
	(7, 7),
	(7, 8),
	(7, 9);

-- Listage des données de la table kgaming.genre : ~10 rows (environ)
DELETE FROM `genre`;
INSERT INTO `genre` (`id`, `label`) VALUES
	(1, 'FPS'),
	(2, 'Aventure'),
	(3, 'Simulation'),
	(4, 'Stratégie'),
	(5, 'MMO'),
	(6, 'Sport'),
	(7, 'Action'),
	(8, 'PVP En ligne'),
	(9, 'Cross-Plateforme'),
	(10, 'Survie'),
	(11, 'Multijoueur');

-- Listage des données de la table kgaming.messenger_messages : ~0 rows (environ)
DELETE FROM `messenger_messages`;

-- Listage des données de la table kgaming.plateform : ~9 rows (environ)
DELETE FROM `plateform`;
INSERT INTO `plateform` (`id`, `category_id`, `label`, `logo`) VALUES
	(1, 1, 'Steam', 'https://www.svgrepo.com/show/452107/steam.svg'),
	(2, 1, 'Origin', 'https://www.svgrepo.com/show/354154/origin.svg'),
	(3, 1, 'Battle.net', 'https://eu.shop.battle.net/static/favicon-32x32.png'),
	(4, 1, 'Epic Games', 'https://www.svgrepo.com/show/341792/epic-games.svg'),
	(5, 1, 'Ubisoft', 'https://www.svgrepo.com/show/342320/ubisoft.svg'),
	(6, 2, 'PlayStation 4', 'https://www.svgrepo.com/show/473756/playstation4.svg'),
	(7, 3, 'Xbox Serie X/S', 'https://upload.wikimedia.org/wikipedia/commons/a/af/Xbox_Series_X_logo.svg'),
	(8, 2, 'PlayStation 5', 'https://www.svgrepo.com/show/473757/playstation5.svg'),
	(9, 3, 'Xbox One', 'https://www.svgrepo.com/show/303464/xbox-one-3-logo.svg');

-- Listage des données de la table kgaming.purchase : ~7 rows (environ)
DELETE FROM `purchase`;
INSERT INTO `purchase` (`id`, `user_id`, `firstname`, `lastname`, `billing_address`, `billing_cp`, `billing_city`, `created_at`) VALUES
	(1, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(2, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(3, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(4, 1, 'Kévin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse', '2023-05-08 15:49:58'),
	(5, 6, 'Test', 'BAHOUI', '123 rue du test', '68000', 'Colmar', '2023-05-08 15:50:36'),
	(6, 6, 'Test', 'BAHOUI', '123 rue du test', '68000', 'Colmar', '2023-05-08 15:50:36'),
	(7, 6, 'Test', 'BAHOUI', '123 rue du test', '68000', 'Colmar', '2023-05-08 15:50:36');

-- Listage des données de la table kgaming.reset_password_request : ~0 rows (environ)
DELETE FROM `reset_password_request`;

-- Listage des données de la table kgaming.stock : ~22 rows (environ)
DELETE FROM `stock`;
INSERT INTO `stock` (`id`, `game_id`, `purchase_id`, `plateform_id`, `license_key`, `date_availability`, `is_available`) VALUES
	(1, 1, 1, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(2, 1, 2, 4, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(3, 1, NULL, 6, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(4, 1, NULL, 8, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(5, 2, 5, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(6, 2, 3, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(7, 2, 4, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(8, 3, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(9, 3, 6, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 0),
	(10, 4, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(11, 1, NULL, 9, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(12, 1, NULL, 7, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(13, 1, NULL, 6, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(14, 1, NULL, 6, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(15, 1, NULL, 8, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(16, 1, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(17, 1, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(18, 1, NULL, 4, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(19, 1, NULL, 4, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(20, 2, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(21, 2, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1),
	(22, 2, NULL, 1, 'XXXX-XXXX-XXXX-XXXX', '2023-05-08 13:02:54', 1);

-- Listage des données de la table kgaming.user : ~2 rows (environ)
DELETE FROM `user`;
INSERT INTO `user` (`id`, `username`, `password`, `email`, `avatar`, `roles`, `is_verified`, `create_at`) VALUES
	(1, 'denz', '$2y$13$jjQF4rgjxHoO6gHwK/It/ekENtEV8mJnzqyQAWmsM58Ch.Xs9nVJS', 'admin@kgaming.com', '/img/default.png', '["ROLE_ADMIN"]', 1, '2023-05-14 20:43:59'),
	(6, 'testAvatar', '$2y$13$CNSyDNHYgqVttbFybY/WmeYUw0ok1Pi3UyfoIg4O/oUtsfXgHKd/6', 'testAvatar@testAvatar.com', '/img/default.png', '["ROLE_ADMIN"]', 1, '2023-05-14 20:43:59');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
