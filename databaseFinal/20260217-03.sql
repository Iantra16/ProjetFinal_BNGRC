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

-- Insertion des Villes (liées à des régions arbitraires basées sur la géographie de Madagascar)
INSERT INTO ville (nom, id_region) VALUES 
('Toamasina', 9),   -- Atsinanana
('Mananjary', 22),  -- Vatovavy
('Farafangana', 8),  -- Atsimo-Atsinanana
('Nosy Be', 13),    -- Diana
('Morondava', 18);  -- Menabe

-- Insertion des Articles (basé sur les catégories de l'image 1)
-- ID Type_besoin: 1 = Nature, 2 = Materiaux, 3 = Argent
INSERT INTO article (nom, prix_unitaire, unite, id_type_besoin) VALUES 
('Riz (kg)', 3000, 'kg', 1),
('Eau (L)', 1000, 'L', 1),
('Tôle', 25000, 'pièce', 2),
('Bâche', 15000, 'pièce', 2),
('Argent', 1, 'Ariary', 3),
('Huile (L)', 6000, 'L', 1),
('Clous (kg)', 8000, 'kg', 2),
('Bois', 10000, 'pièce', 2),
('Haricots', 4000, 'kg', 1),
('groupe', 6750000, 'unité', 2);

-- Création des besoins (Entêtes)
-- On utilise les IDs de ville créés précédemment
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(1, '2026-02-16'), (1, '2026-02-15'), -- Toamasina
(2, '2026-02-15'), (2, '2026-02-16'), -- Mananjary
(3, '2026-02-16'), (3, '2026-02-15'), -- Farafangana
(4, '2026-02-15'), (4, '2026-02-16'), -- Nosy Be
(5, '2026-02-16'), (5, '2026-02-15'); -- Morondava

-- Insertion des articles par besoin (Détails)
-- Note: Les IDs de besoin et d'article sont à adapter selon l'ordre d'insertion
INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) VALUES 
(1, 1, 800, 17),  -- Toamasina Riz
(2, 2, 1500, 4),  -- Toamasina Eau
(1, 3, 120, 23),  -- Toamasina Tôle
(2, 4, 200, 1),   -- Toamasina Bâche
(1, 5, 12000000, 12), -- Toamasina Argent
(3, 1, 500, 9),   -- Mananjary Riz
(4, 6, 120, 25),  -- Mananjary Huile
(3, 3, 80, 6),    -- Mananjary Tôle
(4, 7, 60, 19),   -- Mananjary Clous
(3, 5, 6000000, 3), -- Mananjary Argent
(5, 1, 600, 21),  -- Farafangana Riz
(6, 2, 1000, 14), -- Farafangana Eau
(5, 4, 150, 8),   -- Farafangana Bâche
(6, 8, 100, 26),  -- Farafangana Bois
(5, 5, 8000000, 10), -- Farafangana Argent
(7, 1, 300, 5),   -- Nosy Be Riz
(8, 9, 200, 18),  -- Nosy Be Haricots
(7, 3, 40, 2),    -- Nosy Be Tôle
(8, 7, 30, 24),   -- Nosy Be Clous
(7, 5, 4000000, 7), -- Nosy Be Argent
(9, 1, 700, 11),  -- Morondava Riz
(10, 2, 1200, 20), -- Morondava Eau
(9, 4, 180, 15),  -- Morondava Bâche
(10, 8, 150, 22), -- Morondava Bois
(9, 5, 10000000, 13), -- Morondava Argent
(2, 10, 3, 16);   -- Toamasina Groupe

-- Insertion des entêtes de Dons (Image 2)
INSERT INTO don (donateur, date_don) VALUES 
('Anonyme', '2026-02-16'), ('Anonyme', '2026-02-16'),
('Anonyme', '2026-02-17'), ('Anonyme', '2026-02-17'), ('Anonyme', '2026-02-17'),
('Anonyme', '2026-02-16'), ('Anonyme', '2026-02-16'),
('Anonyme', '2026-02-17'), ('Anonyme', '2026-02-17'), ('Anonyme', '2026-02-17'),
('Anonyme', '2026-02-18'), ('Anonyme', '2026-02-18'), ('Anonyme', '2026-02-18'),
('Anonyme', '2026-02-19'), ('Anonyme', '2026-02-19'), ('Anonyme', '2026-02-17');

-- Insertion des articles liés aux dons
INSERT INTO don_article (id_don, id_article, quantite) VALUES 
(1, 5, 5000000),   -- Argent
(2, 5, 3000000),   -- Argent
(3, 5, 4000000),   -- Argent
(4, 5, 1500000),   -- Argent
(5, 5, 6000000),   -- Argent
(6, 1, 400),       -- Riz
(7, 2, 600),       -- Eau
(8, 3, 50),        -- Tôle
(9, 4, 70),        -- Bâche
(10, 9, 100),      -- Haricots
(11, 1, 2000),     -- Riz
(12, 3, 300),      -- Tôle
(13, 2, 5000),     -- Eau
(14, 5, 20000000), -- Argent
(15, 4, 500),      -- Bâche
(16, 9, 88);       -- Haricots