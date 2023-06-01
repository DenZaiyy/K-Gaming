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

-- Listage des données de la table kgaming.address : ~2 rows (environ)
DELETE FROM `address`;
INSERT INTO `address` (`id`, `user_id`, `label`, `firstname`, `lastname`, `address`, `cp`, `city`) VALUES
	(1, 1, 'Maison', 'Kevin', 'GRISCHKO', '29 rue du ban', '68200', 'Mulhouse'),
	(3, 1, 'Travail', 'Kevin', 'GRISCHKO', '16 rue de la rose', '68270', 'Wittenheim');

-- Listage des données de la table kgaming.category : ~3 rows (environ)
DELETE FROM `category`;
INSERT INTO `category` (`id`, `label`) VALUES
	(1, 'PC'),
	(2, 'PlayStation'),
	(3, 'Xbox');

-- Listage des données de la table kgaming.doctrine_migration_versions : ~1 rows (environ)
DELETE FROM `doctrine_migration_versions`;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20230523090825', '2023-05-05 16:47:20', 388);

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
	(4, 10),
	(6, 1),
	(7, 5);

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

-- Listage des données de la table kgaming.genre : ~11 rows (environ)
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

-- Listage des données de la table kgaming.purchase : ~3 rows (environ)
DELETE FROM `purchase`;
INSERT INTO `purchase` (`id`, `user_id`, `address_id`, `delivery`, `user_full_name`, `is_paid`, `method`, `reference`, `stripe_session_id`, `paypal_order_id`, `created_at`) VALUES
	(51, 1, 1, '29 rue du ban</br>68200 - Mulhouse', 'Kevin GRISCHKO', 1, 'stripe', '01062023-64789d1fc96ef', 'cs_test_a1EmaG4MXEKXrzLSxXKT4o6zBucUpLJokMB9cdUVhtYrOD26beBEoy2Tlc', NULL, '2023-06-01 13:29:03'),
	(52, 1, 1, '29 rue du ban</br>68200 - Mulhouse', 'Kevin GRISCHKO', 1, 'stripe', '01062023-64789de1496eb', 'cs_test_a1EmaG4MXEKXrzLSxXKT4o6zBucUpLJokMB9cdUVhtYrOD26beBEoy2Tlc', NULL, '2023-06-01 13:32:17'),
	(53, 1, 3, '16 rue de la rose</br>68270 - Wittenheim', 'Kevin GRISCHKO', 1, 'stripe', '01062023-6478a40591a84', 'cs_test_a1EmaG4MXEKXrzLSxXKT4o6zBucUpLJokMB9cdUVhtYrOD26beBEoy2Tlc', NULL, '2023-06-01 13:58:29');

-- Listage des données de la table kgaming.recap_details : ~5 rows (environ)
DELETE FROM `recap_details`;
INSERT INTO `recap_details` (`id`, `order_product_id`, `quantity`, `price`, `total_recap`, `game_label`, `platform_label`, `game_id`, `platform_id`) VALUES
	(1, 51, 1, 19.99, '19.99', 'Rocket League', 'Steam', 1, 1),
	(2, 52, 1, 39.99, '39.99', 'Rust', 'Steam', 2, 1),
	(3, 52, 1, 19.99, '19.99', 'Rocket League', 'Steam', 1, 1),
	(4, 52, 1, 38.99, '38.99', 'New World', 'Steam', 3, 1),
	(5, 53, 1, 19.99, '19.99', 'Rocket League', 'Steam', 1, 1);

-- Listage des données de la table kgaming.reset_password_request : ~0 rows (environ)
DELETE FROM `reset_password_request`;

-- Listage des données de la table kgaming.stock : ~32 rows (environ)
DELETE FROM `stock`;
INSERT INTO `stock` (`id`, `game_id`, `purchase_id`, `plateform_id`, `license_key`, `date_availability`, `is_available`) VALUES
	(1, 2, NULL, 7, '66D2-A17E-F004-017A', '2023-06-01 09:39:45', 1),
	(2, 2, NULL, 5, '2FC0-92ED-02DB-919D', '2023-06-01 09:39:45', 1),
	(3, 3, 52, 1, 'D774-C59B-BA3A-1CF7', '2023-06-01 09:39:45', 0),
	(4, 4, NULL, 7, '0E2B-E431-4239-FA2A', '2023-06-01 09:39:45', 1),
	(5, 7, NULL, 8, '46DA-78B8-90D7-2B5A', '2023-06-01 09:39:45', 1),
	(6, 2, NULL, 9, 'CD8E-CE7C-14E7-F6A4', '2023-06-01 09:39:45', 1),
	(7, 2, NULL, 9, '3EA7-3BD4-F1EA-D759', '2023-06-01 09:39:45', 1),
	(8, 7, NULL, 9, 'E16C-88E7-2172-2DC9', '2023-06-01 09:39:45', 1),
	(9, 6, NULL, 9, '3CD0-EF5B-DE2D-1E9D', '2023-06-01 09:39:45', 1),
	(10, 2, NULL, 3, 'E071-0F75-866C-5FA9', '2023-06-01 09:39:45', 1),
	(11, 2, NULL, 4, '41D8-CC3B-0C9A-098B', '2023-06-01 09:39:45', 1),
	(12, 1, NULL, 2, 'FA4A-C086-56E1-16D8', '2023-06-01 09:39:45', 1),
	(13, 7, NULL, 6, '75C8-C0BF-987B-0855', '2023-06-01 09:39:45', 1),
	(14, 3, NULL, 5, '7BE3-230A-61F2-399E', '2023-06-01 09:39:45', 1),
	(15, 2, NULL, 8, '60FE-5441-32DB-5BEF', '2023-06-01 09:39:45', 1),
	(16, 2, NULL, 4, '7482-F23B-13DD-92C9', '2023-06-01 09:39:45', 1),
	(17, 1, NULL, 7, 'FB6E-E017-6953-2ABB', '2023-06-01 09:39:45', 1),
	(18, 3, NULL, 3, 'D88E-30E0-26D4-6D6B', '2023-06-01 09:39:45', 1),
	(19, 4, NULL, 2, '081F-A045-D1DB-CD6E', '2023-06-01 09:39:45', 1),
	(20, 1, 51, 1, '2CAB-FC78-6052-FE82', '2023-06-01 09:39:45', 0),
	(21, 1, NULL, 4, '57DF-FBC1-A448-5801', '2023-06-01 09:39:45', 1),
	(22, 2, NULL, 6, '8425-C2A8-6F2D-01CB', '2023-06-01 09:39:45', 1),
	(23, 2, NULL, 6, '9D86-E655-9677-988B', '2023-06-01 09:39:45', 1),
	(24, 2, NULL, 6, '397D-B0E0-A08B-BB01', '2023-06-01 09:39:45', 1),
	(25, 1, 52, 1, '8BC9-A00D-4A38-BACC', '2023-06-01 09:39:45', 0),
	(26, 6, NULL, 7, 'D299-F3AC-A9A8-2AD6', '2023-06-01 09:39:45', 1),
	(27, 3, NULL, 4, '8DDB-A419-D31A-EE0E', '2023-06-01 09:39:45', 1),
	(28, 6, NULL, 8, '42CA-FAEA-D212-8119', '2023-06-01 09:39:45', 1),
	(29, 4, NULL, 7, '8B7C-94E0-D128-99E4', '2023-06-01 09:39:45', 1),
	(30, 3, NULL, 1, 'DDEB-5F69-E958-2D01', '2023-06-01 09:39:45', 1),
	(31, 4, NULL, 3, '5597-0AED-D130-46AB', '2023-06-01 09:39:45', 1),
	(32, 1, NULL, 5, 'EEA5-16EA-7262-32B7', '2023-06-01 09:39:45', 1),
	(33, 3, NULL, 8, '90AF-97C3-1700-2BB5', '2023-06-01 09:39:45', 1),
	(34, 4, NULL, 2, '9F3C-BC07-5F8E-FFDD', '2023-06-01 09:39:45', 1),
	(35, 6, NULL, 2, 'C3DD-2A9A-4A8D-65A8', '2023-06-01 09:39:45', 1),
	(36, 6, NULL, 3, '85F1-28A7-FCAB-FF2C', '2023-06-01 09:39:45', 1),
	(37, 6, NULL, 6, '0766-5794-A016-3041', '2023-06-01 09:39:45', 1),
	(38, 2, NULL, 6, '7157-0D4D-8175-D6BB', '2023-06-01 09:39:45', 1),
	(39, 3, NULL, 3, '3030-23F1-AD09-418E', '2023-06-01 09:39:45', 1),
	(40, 6, NULL, 5, '7093-BB90-4AD8-3834', '2023-06-01 09:39:45', 1),
	(41, 7, NULL, 8, 'CB45-9CCE-3A4D-BB96', '2023-06-01 09:39:45', 1),
	(42, 1, NULL, 9, '697C-1142-999D-D5F1', '2023-06-01 09:39:45', 1),
	(43, 2, NULL, 6, '6CD5-6F55-33F9-C90F', '2023-06-01 09:39:45', 1),
	(44, 7, NULL, 7, 'A7F8-94C2-88E0-23EE', '2023-06-01 09:39:45', 1),
	(45, 3, NULL, 6, '70F6-81DB-C066-3236', '2023-06-01 09:39:45', 1),
	(46, 1, NULL, 7, 'F628-8A88-1505-71EE', '2023-06-01 09:39:45', 1),
	(47, 2, NULL, 3, '8805-3002-0BD8-BD48', '2023-06-01 09:39:45', 1),
	(48, 1, NULL, 2, 'D38C-5DA1-EF35-BABA', '2023-06-01 09:39:45', 1),
	(49, 6, NULL, 5, '8429-6A99-0C61-554D', '2023-06-01 09:39:45', 1),
	(50, 7, NULL, 1, 'B300-407F-00AD-D7BE', '2023-06-01 09:39:45', 1),
	(51, 6, NULL, 1, '7E8C-34F5-1704-2E78', '2023-06-01 09:39:45', 1),
	(52, 7, NULL, 1, '4E24-A8AB-9280-D719', '2023-06-01 09:39:45', 1),
	(53, 4, NULL, 5, '670F-1EB7-FAC8-E028', '2023-06-01 09:39:45', 1),
	(54, 6, NULL, 5, '2134-97E1-4805-47A9', '2023-06-01 09:39:45', 1),
	(55, 2, NULL, 4, '4611-1580-488D-7674', '2023-06-01 09:39:45', 1),
	(56, 6, NULL, 9, '261D-9C90-AD41-2483', '2023-06-01 09:39:45', 1),
	(57, 7, NULL, 8, '5FE5-5DA6-D149-AA5F', '2023-06-01 09:39:45', 1),
	(58, 7, NULL, 6, '1721-BCE2-CE29-00FE', '2023-06-01 09:39:45', 1),
	(59, 6, NULL, 3, '5C69-056A-0223-51CD', '2023-06-01 09:39:45', 1),
	(60, 3, NULL, 5, '4AC8-1A61-EF23-F850', '2023-06-01 09:39:45', 1),
	(61, 3, NULL, 2, 'F6E2-4524-B741-0917', '2023-06-01 09:39:45', 1),
	(62, 2, NULL, 5, '4F14-D8CD-9173-77C9', '2023-06-01 09:39:45', 1),
	(63, 3, NULL, 1, '4AC7-F14E-1960-5BBB', '2023-06-01 09:39:45', 1),
	(64, 7, NULL, 4, '32F5-F308-F0A8-E847', '2023-06-01 09:39:45', 1),
	(65, 4, NULL, 9, '566C-74CD-4DE9-8141', '2023-06-01 09:39:45', 1),
	(66, 6, NULL, 6, '9E82-76DF-7FDB-0271', '2023-06-01 09:39:45', 1),
	(67, 1, NULL, 3, '7787-BDD8-D646-8A1D', '2023-06-01 09:39:45', 1),
	(68, 7, NULL, 3, '5518-B4D2-636F-A7E1', '2023-06-01 09:39:45', 1),
	(69, 4, NULL, 9, '76FA-D2D1-B4B7-CAEC', '2023-06-01 09:39:45', 1),
	(70, 6, NULL, 5, 'A86A-E9DA-9F89-F318', '2023-06-01 09:39:45', 1),
	(71, 7, NULL, 6, 'E2B5-7A71-F542-738A', '2023-06-01 09:39:45', 1),
	(72, 3, NULL, 2, '5788-5E2E-F541-8B6F', '2023-06-01 09:39:45', 1),
	(73, 6, NULL, 6, '6208-8263-77AA-1354', '2023-06-01 09:39:45', 1),
	(74, 6, NULL, 1, 'E4B8-4B10-E975-0AFB', '2023-06-01 09:39:45', 1),
	(75, 4, NULL, 5, '886F-2BE4-66A1-72C1', '2023-06-01 09:39:45', 1),
	(76, 3, NULL, 4, '2784-E057-930A-4883', '2023-06-01 09:39:45', 1),
	(77, 1, NULL, 6, '07F9-49C8-BDD3-F83E', '2023-06-01 09:39:45', 1),
	(78, 6, NULL, 2, 'C4F6-6A6A-568C-BA56', '2023-06-01 09:39:45', 1),
	(79, 2, 52, 1, 'AC32-8A55-9589-F308', '2023-06-01 09:39:45', 0),
	(80, 3, NULL, 3, 'E7A6-E47B-6582-FBCC', '2023-06-01 09:39:45', 1),
	(81, 1, NULL, 2, '65FF-575D-EA70-9112', '2023-06-01 09:39:45', 1),
	(82, 4, NULL, 5, '4839-B9D4-6503-964E', '2023-06-01 09:39:45', 1),
	(83, 3, NULL, 2, '1319-C0B7-3F88-9367', '2023-06-01 09:39:45', 1),
	(84, 1, NULL, 7, '6132-A89A-4BEB-7A62', '2023-06-01 09:39:45', 1),
	(85, 2, NULL, 6, '9964-A28C-6BBA-C3B2', '2023-06-01 09:39:45', 1),
	(86, 3, NULL, 9, '19A5-7295-C309-1F4A', '2023-06-01 09:39:45', 1),
	(87, 2, NULL, 2, '50E2-5EAE-9D1A-8706', '2023-06-01 09:39:45', 1),
	(88, 3, NULL, 1, '12F7-4BB4-268D-0C0B', '2023-06-01 09:39:45', 1),
	(89, 6, NULL, 8, '8A7D-77F5-15BE-09BA', '2023-06-01 09:39:45', 1),
	(90, 7, NULL, 7, '2C61-7D4C-7C37-B519', '2023-06-01 09:39:45', 1),
	(91, 3, NULL, 8, 'D461-6F94-0C4A-5BDB', '2023-06-01 09:39:45', 1),
	(92, 7, NULL, 5, 'D3E2-4B76-2D6D-FC46', '2023-06-01 09:39:45', 1),
	(93, 1, NULL, 7, '0E8D-463D-9D0C-7AF3', '2023-06-01 09:39:45', 1),
	(94, 3, NULL, 8, '3259-75A2-E0FE-1DF0', '2023-06-01 09:39:45', 1),
	(95, 2, NULL, 3, '3B3F-579F-06BB-25DA', '2023-06-01 09:39:45', 1),
	(96, 2, NULL, 4, '1E80-1BE7-A161-A1D6', '2023-06-01 09:39:45', 1),
	(97, 7, NULL, 3, 'DB7D-1338-A76F-2124', '2023-06-01 09:39:45', 1),
	(98, 6, NULL, 9, '2027-5A08-C622-BBE3', '2023-06-01 09:39:45', 1),
	(99, 4, NULL, 8, 'B9F1-0A23-FECD-44D3', '2023-06-01 09:39:45', 1),
	(100, 2, NULL, 1, 'C15F-F8D3-9967-EB71', '2023-06-01 09:39:45', 1),
	(101, 6, NULL, 2, '7F91-3683-0C42-B597', '2023-06-01 09:39:45', 1),
	(102, 1, NULL, 2, '0E3D-11BA-6540-430E', '2023-06-01 13:55:57', 1),
	(103, 3, NULL, 6, '88F8-5408-5B8B-BF5A', '2023-06-01 13:55:57', 1),
	(104, 6, NULL, 1, '4015-A31A-029C-6ED1', '2023-06-01 13:55:57', 1),
	(105, 4, NULL, 6, 'A7B3-1003-CE97-6AFC', '2023-06-01 13:55:57', 1),
	(106, 6, NULL, 5, '0C2D-0E54-889B-159C', '2023-06-01 13:55:57', 1),
	(107, 7, NULL, 8, '69D5-4190-2044-8AF2', '2023-06-01 13:55:57', 1),
	(108, 3, NULL, 7, '1456-A8A4-0182-D61D', '2023-06-01 13:55:57', 1),
	(109, 6, NULL, 5, '33D0-BD6C-3B35-49F7', '2023-06-01 13:55:57', 1),
	(110, 1, NULL, 2, '06DA-7F68-0DBF-CCBB', '2023-06-01 13:55:57', 1),
	(111, 3, NULL, 2, 'E05D-EF3B-6A6B-228F', '2023-06-01 13:55:57', 1),
	(112, 3, NULL, 4, '84BF-4E11-0F4D-163C', '2023-06-01 13:55:57', 1),
	(113, 6, NULL, 9, '81F2-2F7A-F6D8-2A9D', '2023-06-01 13:55:57', 1),
	(114, 6, NULL, 2, '5D04-47EC-AB76-E2B7', '2023-06-01 13:55:57', 1),
	(115, 7, NULL, 5, '784F-5A1D-A5BC-2398', '2023-06-01 13:55:57', 1),
	(116, 2, NULL, 4, 'EE00-F08D-642E-8960', '2023-06-01 13:55:57', 1),
	(117, 1, NULL, 7, '9D22-1888-8135-C7AB', '2023-06-01 13:55:57', 1),
	(118, 7, NULL, 2, '7051-CE83-F932-3EFD', '2023-06-01 13:55:57', 1),
	(119, 7, NULL, 5, '29C4-4E2C-A0D5-1A89', '2023-06-01 13:55:57', 1),
	(120, 7, NULL, 9, 'B500-C83C-3154-D08E', '2023-06-01 13:55:57', 1),
	(121, 1, NULL, 5, '1CF5-3890-D373-CEEA', '2023-06-01 13:55:57', 1),
	(122, 4, NULL, 9, '0AB0-7540-655B-1F69', '2023-06-01 13:55:57', 1),
	(123, 3, NULL, 7, '9675-F9DC-FE68-A823', '2023-06-01 13:55:57', 1),
	(124, 6, NULL, 8, '29EC-F0F5-36DC-B754', '2023-06-01 13:55:57', 1),
	(125, 3, NULL, 1, '74F0-1FD4-4C88-882B', '2023-06-01 13:55:57', 1),
	(126, 1, NULL, 6, '86F5-B46E-2252-7538', '2023-06-01 13:55:57', 1),
	(127, 6, NULL, 4, 'EEDE-2D63-6E90-A0FA', '2023-06-01 13:55:57', 1),
	(128, 1, NULL, 5, '6D38-6DB4-FA0F-74D2', '2023-06-01 13:55:57', 1),
	(129, 6, NULL, 2, '3FEB-D6E6-382C-2128', '2023-06-01 13:55:57', 1),
	(130, 4, NULL, 8, '4F43-4AE5-939F-F9E2', '2023-06-01 13:55:57', 1),
	(131, 6, NULL, 1, 'D887-C76A-4EC1-5848', '2023-06-01 13:55:57', 1),
	(132, 4, NULL, 5, '7113-DEFF-C8D6-C9BE', '2023-06-01 13:55:57', 1),
	(133, 4, NULL, 3, '2B1D-7085-5FFD-3E0E', '2023-06-01 13:55:57', 1),
	(134, 3, NULL, 5, '895E-3DCD-B6D9-BA8A', '2023-06-01 13:55:57', 1),
	(135, 4, NULL, 3, 'B65C-B4F2-0915-5659', '2023-06-01 13:55:57', 1),
	(136, 3, NULL, 8, 'BEBC-F7F3-6B7F-451B', '2023-06-01 13:55:57', 1),
	(137, 3, NULL, 9, 'F1A9-FCF7-E802-0926', '2023-06-01 13:55:57', 1),
	(138, 3, NULL, 1, 'D41E-30D3-9804-02B6', '2023-06-01 13:55:57', 1),
	(139, 6, NULL, 6, '00A4-3237-69BC-E61D', '2023-06-01 13:55:57', 1),
	(140, 6, NULL, 5, '7219-31B7-FA71-8836', '2023-06-01 13:55:57', 1),
	(141, 1, NULL, 3, '857E-631F-F2F1-6468', '2023-06-01 13:55:57', 1),
	(142, 2, NULL, 9, 'DD10-E90F-2946-5749', '2023-06-01 13:55:57', 1),
	(143, 6, NULL, 5, 'C83C-30E6-4631-62A2', '2023-06-01 13:55:57', 1),
	(144, 7, NULL, 3, '84D8-49D6-9F86-DFAC', '2023-06-01 13:55:57', 1),
	(145, 3, NULL, 1, '2343-4CF6-0141-6875', '2023-06-01 13:55:57', 1),
	(146, 3, NULL, 3, 'F6DC-6A38-D70F-93ED', '2023-06-01 13:55:57', 1),
	(147, 2, NULL, 3, 'CC71-6059-1C2A-1023', '2023-06-01 13:55:57', 1),
	(148, 7, NULL, 2, '997E-2E96-260B-B7D1', '2023-06-01 13:55:57', 1),
	(149, 7, NULL, 4, '3D24-173D-7DB6-CDF5', '2023-06-01 13:55:57', 1),
	(150, 6, NULL, 4, '951C-B078-6D4F-9BD5', '2023-06-01 13:55:57', 1),
	(151, 2, NULL, 7, '8E12-BB1A-01F5-5965', '2023-06-01 13:55:57', 1),
	(152, 3, NULL, 5, '5416-BC84-2AFB-1C56', '2023-06-01 13:55:57', 1),
	(153, 7, NULL, 3, '2ED1-15ED-F346-0EF6', '2023-06-01 13:55:57', 1),
	(154, 2, NULL, 9, '7D24-663B-F280-CADA', '2023-06-01 13:55:57', 1),
	(155, 4, NULL, 4, '099D-8FEA-4127-7C3B', '2023-06-01 13:55:57', 1),
	(156, 2, NULL, 6, '5F43-33BC-5E9D-A740', '2023-06-01 13:55:57', 1),
	(157, 1, NULL, 3, '59B7-6B96-45C3-6A40', '2023-06-01 13:55:57', 1),
	(158, 6, NULL, 6, 'E6C0-3A81-3938-BBE4', '2023-06-01 13:55:57', 1),
	(159, 6, NULL, 8, '9F78-49E1-34B4-5F9B', '2023-06-01 13:55:57', 1),
	(160, 7, NULL, 9, '433A-CFFD-6605-6D1F', '2023-06-01 13:55:57', 1),
	(161, 3, NULL, 2, 'FB18-153A-11E3-5AC8', '2023-06-01 13:55:57', 1),
	(162, 1, NULL, 7, 'AE47-B9F0-4BA4-C601', '2023-06-01 13:55:57', 1),
	(163, 4, NULL, 3, 'AECA-798F-B769-C11B', '2023-06-01 13:55:57', 1),
	(164, 1, NULL, 4, '3F4A-AA12-E0A7-52BC', '2023-06-01 13:55:57', 1),
	(165, 4, NULL, 5, '5FF5-3064-A3E4-F4B4', '2023-06-01 13:55:57', 1),
	(166, 2, NULL, 2, 'D58B-4416-40F8-0F09', '2023-06-01 13:55:57', 1),
	(167, 4, NULL, 6, 'C0AB-8D7D-9EAF-168F', '2023-06-01 13:55:57', 1),
	(168, 6, NULL, 8, '3C4B-FE11-A79B-6809', '2023-06-01 13:55:57', 1),
	(169, 4, NULL, 3, '7DC8-2387-6514-536D', '2023-06-01 13:55:57', 1),
	(170, 4, NULL, 3, '0CC8-EC99-8DF6-2F41', '2023-06-01 13:55:57', 1),
	(171, 6, NULL, 6, '6906-11E4-11A8-031E', '2023-06-01 13:55:57', 1),
	(172, 7, NULL, 7, '5554-DD60-8D87-FC3B', '2023-06-01 13:55:57', 1),
	(173, 4, NULL, 9, 'B80D-E3D7-B9E5-2F73', '2023-06-01 13:55:57', 1),
	(174, 4, NULL, 8, '6FED-DEB1-6445-D599', '2023-06-01 13:55:57', 1),
	(175, 2, NULL, 6, 'DBBF-5669-B716-70FB', '2023-06-01 13:55:57', 1),
	(176, 3, NULL, 4, '0B35-2A45-4BE5-CDF9', '2023-06-01 13:55:57', 1),
	(177, 6, NULL, 3, '34DA-F5BA-458A-E4F8', '2023-06-01 13:55:57', 1),
	(178, 7, NULL, 9, '2DD4-CBD6-132B-0E55', '2023-06-01 13:55:57', 1),
	(179, 4, NULL, 2, '2308-CEA0-2D72-F469', '2023-06-01 13:55:57', 1),
	(180, 3, NULL, 6, '125E-E3BF-D9D0-5F38', '2023-06-01 13:55:57', 1),
	(181, 1, 53, 1, 'CFBC-D747-7CBD-CFE4', '2023-06-01 13:55:57', 0),
	(182, 3, NULL, 8, '12E0-D678-09EF-80D1', '2023-06-01 13:55:57', 1),
	(183, 6, NULL, 8, '94BE-FE00-A382-DA1A', '2023-06-01 13:55:57', 1),
	(184, 2, NULL, 3, 'E743-18D3-96A0-575E', '2023-06-01 13:55:57', 1),
	(185, 6, NULL, 3, '073A-C4E5-1EC2-DC4E', '2023-06-01 13:55:57', 1),
	(186, 4, NULL, 2, 'F079-3832-4AF0-09F2', '2023-06-01 13:55:57', 1),
	(187, 4, NULL, 3, 'A1CF-F15A-0DE7-E7DE', '2023-06-01 13:55:57', 1),
	(188, 6, NULL, 4, 'ECE9-9436-E329-CDAE', '2023-06-01 13:55:57', 1),
	(189, 6, NULL, 4, 'EBDF-454D-D966-5092', '2023-06-01 13:55:57', 1),
	(190, 2, NULL, 8, '6E55-407C-6D4B-1D7D', '2023-06-01 13:55:57', 1),
	(191, 3, NULL, 3, '4067-8815-FD86-CB7C', '2023-06-01 13:55:57', 1),
	(192, 1, NULL, 1, '7609-E740-793F-37A3', '2023-06-01 13:55:57', 1),
	(193, 2, NULL, 2, '7D86-AAF1-AC5D-E528', '2023-06-01 13:55:57', 1),
	(194, 1, NULL, 7, 'CA7E-6BB4-0D4B-2998', '2023-06-01 13:55:57', 1),
	(195, 6, NULL, 3, 'B6EC-87E3-76BF-33B1', '2023-06-01 13:55:57', 1),
	(196, 3, NULL, 2, 'C790-CE39-BA06-C7EF', '2023-06-01 13:55:57', 1),
	(197, 3, NULL, 2, 'E68E-889F-632F-ED7C', '2023-06-01 13:55:57', 1),
	(198, 1, NULL, 3, '85DC-55B8-D40B-DC78', '2023-06-01 13:55:57', 1),
	(199, 2, NULL, 4, '6F34-AD3E-670C-DF8D', '2023-06-01 13:55:57', 1),
	(200, 1, NULL, 7, '4CD1-CCCB-8912-9BF1', '2023-06-01 13:55:57', 1),
	(201, 4, NULL, 3, 'E2C9-A2A2-4219-CF9B', '2023-06-01 13:55:57', 1),
	(202, 4, NULL, 7, '3C48-C257-3B1C-D2FE', '2023-06-01 13:55:57', 1);

-- Listage des données de la table kgaming.user : ~1 rows (environ)
DELETE FROM `user`;
INSERT INTO `user` (`id`, `username`, `password`, `email`, `avatar`, `roles`, `is_verified`, `create_at`, `is_banned`, `ip_address`) VALUES
	(1, 'denz', '$2y$13$jjQF4rgjxHoO6gHwK/It/ekENtEV8mJnzqyQAWmsM58Ch.Xs9nVJS', 'denzoubiden@gmail.com', '/img/default.png', '["ROLE_ADMIN"]', 1, '2023-05-14 20:43:59', 0, '');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
