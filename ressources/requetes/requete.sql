-- Liste des jeux :
SELECT g.id, g.label, g.price, g.description, g.date_release
FROM game g

-- Liste des jeux par plateforme :
SELECT g.id, g.label, g.price, g.description, g.date_release
FROM game g
INNER JOIN game_plateform gp ON gp.id_game = g.id
WHERE gp.id_platform = 1

-- Liste de jeux par plateforme et limité à 15 (pour le détail d'une plateforme) :
SELECT p.label AS 'plateforme', g.label AS 'game', g.price
FROM game g
INNER JOIN game_plateform gp ON gp.id_game = g.id
INNER JOIN plateform p ON gp.id_platform = p.id
WHERE gp.id_platform = 1
LIMIT 15

-- Liste de jeux qui sont pas encore sortie et disponible à la précommande :
SELECT g.label, g.price, g.date_release
FROM game g
WHERE g.date_release > DATE(NOW())

-- Liste des jeux par genre :

SELECT g.id, g.label, g.price, g.description, g.date_release
FROM game g
INNER JOIN game_genre gg ON g.id = gg.id_game
WHERE gg.id_genre = 9

-- Liste des stocks globale :
SELECT s.id, s.license_key, s.date_availability, s.is_available, s.id_order, s.id_game
FROM stock s

-- Liste des stocks par jeu :
SELECT s.license_key, s.date_availability, s.is_available, g.label, g.price
FROM stock s
INNER JOIN game g ON g.id = s.id_game
WHERE g.id = 3

-- Liste des commandes d'un client
SELECT u.username,
	     o.created_at AS dateOrder,
		 s.license_key,
		 s.date_availability,
		 s.id_order,
		 g.label
FROM stock s
INNER JOIN `order` o ON s.id_order = o.id
INNER JOIN `user` u ON o.id_user = u.id
INNER JOIN game g ON s.id_game = g.id
WHERE u.id = 2
order BY o.created_at DESC

-- Liste des genre :
SELECT g.label
FROM genre g

-- Liste des plateformes par catégorie
SELECT p.label
FROM plateform p
INNER JOIN category c ON p.id_category = c.id
WHERE p.id_category = 1

-- Nombre de jeux par plateforme (ordre décroissant)
SELECT p.label, COUNT(gp.id_game) AS NbJeux
FROM game_plateform gp
INNER JOIN plateform p ON gp.id_platform = p.id
GROUP BY p.label
order BY NbJeux DESC

-- Nombre de jeux par genre (ordre décroissant)
SELECT g.label, COUNT(gg.id_game) AS NbJeux
FROM game_genre gg
INNER JOIN genre g ON gg.id_genre = g.id
GROUP BY g.label
order BY NbJeux DESC