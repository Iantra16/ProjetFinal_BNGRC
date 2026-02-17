USE bngrc;

-- Fusion des données de :
-- - 20260216-02.sql
-- - 20260217-add-ordre-column.sql
-- Note: le fichier 20260217-add-ordre-column.sql ne contient pas d'INSERT/UPDATE de données,
-- uniquement des modifications de structure et de vues.

DELETE FROM distribution;
DELETE FROM don_article;
DELETE FROM don;
DELETE FROM besoin_article;
DELETE FROM besoin;
DELETE FROM ville;
DELETE FROM region;
DELETE FROM article;
DELETE FROM type_besoin;

ALTER TABLE distribution AUTO_INCREMENT = 1;
ALTER TABLE don_article AUTO_INCREMENT = 1;
ALTER TABLE don AUTO_INCREMENT = 1;
ALTER TABLE besoin_article AUTO_INCREMENT = 1;
ALTER TABLE besoin AUTO_INCREMENT = 1;
ALTER TABLE ville AUTO_INCREMENT = 1;
ALTER TABLE region AUTO_INCREMENT = 1;
ALTER TABLE article AUTO_INCREMENT = 1;
ALTER TABLE type_besoin AUTO_INCREMENT = 1;

INSERT INTO region (nom) VALUES
('Alaotra-Mangoro'),
('Amoron''i Mania'),
('Analamanga'),
('Analanjirofo'),
('Androy'),
('Anosy'),
('Atsimo-Andrefana'),
('Atsimo-Atsinanana'),
('Atsinanana'),
('Betsiboka'),
('Boeny'),
('Bongolava'),
('Diana'),
('Fitovinany'),
('Ihorombe'),
('Itasy'),
('Melaky'),
('Menabe'),
('Sava'),
('Sofia'),
('Vakinankaratra'),
('Vatovavy'),
('Vatovavy-Fitovinany');

INSERT INTO type_besoin (libelle) VALUES
('Nature'),
('Materiaux'),
('Argent');

-- ==========================================
-- Données additionnelles avec ordre de priorité
-- Source: 20260217-data-with-ordre.sql
-- ==========================================

-- Villes du jeu de données
INSERT INTO ville (nom, id_region) VALUES 
('Toamasina', 33),      -- Atsinanana
('Nosy Be', 37),        -- Diana
('Mananjary', 46),      -- Vatovavy
('Farafangana', 8),     -- Atsimo-Atsinanana
('Morondava', 42),      -- Menabe
('Toliara', 31);        -- Atsimo-Andrefana (si pas dans region, utiliser 1)

-- Articles du jeu de données
-- On garde les articles existants et on en ajoute si nécessaire
INSERT IGNORE INTO article (nom, prix_unitaire, unite, id_type_besoin) VALUES 
('Bâche', 15000.00, 'unité', 2),
('Tôle', 25000.00, 'unité', 2),
('Argent', 1.00, 'Ariary', 3),
('Eau (L)', 1000.00, 'litre', 1),
('Riz (kg)', 3000.00, 'kg', 1),
('Clous (kg)', 8000.00, 'kg', 2),
('Bols', 10000.00, 'lot', 2),
('Huile (L)', 6000.00, 'litre', 1),
('Groupe', 6750000.00, 'unité', 2);

-- Besoins avec ordre de priorité (basé sur colonne C du Google Sheets)
-- Toamasina (2026-02-15)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(1, '2026-02-15 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(1, (SELECT id FROM article WHERE nom = 'Bâche' LIMIT 1), 200, 1),
(1, (SELECT id FROM article WHERE nom = 'Tôle' LIMIT 1), 40, 2);

-- Nosy Be (2026-02-15)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(2, '2026-02-15 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(2, (SELECT id FROM article WHERE nom = 'Argent' LIMIT 1), 6000000, 3),
(2, (SELECT id FROM article WHERE nom = 'Eau (L)' LIMIT 1), 1500, 4),
(2, (SELECT id FROM article WHERE nom = 'Riz (kg)' LIMIT 1), 300, 5),
(2, (SELECT id FROM article WHERE nom = 'Tôle' LIMIT 1), 80, 6),
(2, (SELECT id FROM article WHERE nom = 'Argent' LIMIT 1), 4000000, 7);

-- Mananjary (2026-02-15, 2026-02-16)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(3, '2026-02-15 00:00:00'),
(3, '2026-02-16 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(3, (SELECT id FROM article WHERE nom = 'Bâche' LIMIT 1), 150, 8),
(3, (SELECT id FROM article WHERE nom = 'Riz (kg)' LIMIT 1), 500, 9),
(4, (SELECT id FROM article WHERE nom = 'Argent' LIMIT 1), 8000000, 10),
(4, (SELECT id FROM article WHERE nom = 'Argent' LIMIT 1), 12000000, 12),
(4, (SELECT id FROM article WHERE nom = 'Argent' LIMIT 1), 10000000, 13);

-- Farafangana (2026-02-15, 2026-02-16)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(4, '2026-02-15 00:00:00'),
(4, '2026-02-16 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(5, (SELECT id FROM article WHERE nom = 'Riz (kg)' LIMIT 1), 700, 11),
(5, (SELECT id FROM article WHERE nom = 'Eau (L)' LIMIT 1), 1000, 14),
(6, (SELECT id FROM article WHERE nom = 'Bâche' LIMIT 1), 180, 15);

-- Morondava (2026-02-15, 2026-02-16)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(5, '2026-02-15 00:00:00'),
(5, '2026-02-16 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(7, (SELECT id FROM article WHERE nom = 'Groupe' LIMIT 1), 3, 16),
(7, (SELECT id FROM article WHERE nom = 'Riz (kg)' LIMIT 1), 800, 17),
(8, (SELECT id FROM article WHERE nom = 'Eau (L)' LIMIT 1), 1200, 20),
(8, (SELECT id FROM article WHERE nom = 'Riz (kg)' LIMIT 1), 600, 21),
(8, (SELECT id FROM article WHERE nom = 'Bols' LIMIT 1), 150, 22);

-- Toamasina (2026-02-16)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(1, '2026-02-16 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(9, (SELECT id FROM article WHERE nom = 'Riz (kg)' LIMIT 1), 800, 18),
(9, (SELECT id FROM article WHERE nom = 'Eau (L)' LIMIT 1), 4000, 18);

-- Nosy Be (2026-02-16)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(2, '2026-02-16 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(10, (SELECT id FROM article WHERE nom = 'Clous (kg)' LIMIT 1), 60, 19),
(10, (SELECT id FROM article WHERE nom = 'Eau (L)' LIMIT 1), 3000, 23);

-- Mananjary (2026-02-16)
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(3, '2026-02-16 00:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES
(11, (SELECT id FROM article WHERE nom = 'Clous (kg)' LIMIT 1), 30, 24),
(11, (SELECT id FROM article WHERE nom = 'Huile (L)' LIMIT 1), 120, 25),
(11, (SELECT id FROM article WHERE nom = 'Bols' LIMIT 1), 100, 26);

-- Vérification des données insérées
SELECT 'Vérification des besoins avec ordre:' AS info;
SELECT 
	b.id AS besoin_id,
	v.nom AS ville,
	a.nom AS article,
	ba.quantite,
	ba.ordre,
	b.date_saisie
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN article a ON ba.id_article = a.id
ORDER BY ba.ordre ASC;