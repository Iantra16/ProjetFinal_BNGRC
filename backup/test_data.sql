-- Données de test pour DistributoinDons_Proportionnelle_Avancee

USE bngrc;

-- === TEST 1 : Exemple simple (besoin: 1, 3, 5 | dons: 5) ===
-- Résultat attendu: 0, 1, 2 → après reste: 1, 2, 2

-- Ajouter le type de besoin s'il n'existe pas
INSERT IGNORE INTO type_besoin (id, libelle) VALUES 
(1, 'nourriture'),
(2, 'materiaux'),
(3, 'argent');

-- Ajouter les régions
INSERT IGNORE INTO region (id, nom) VALUES 
(1, 'Région Nord'),
(2, 'Région Sud');

-- TEST 1 : Villes pour test 1
INSERT IGNORE INTO ville (id, nom, id_region) VALUES 
(1, 'Ville A - Test 1', 1),
(2, 'Ville B - Test 1', 1),
(3, 'Ville C - Test 1', 1);

-- Article Riz pour test 1
INSERT IGNORE INTO article (id, nom, prix_unitaire, unite, id_type_besoin) VALUES 
(10, 'Riz Test 1', 2.50, 'kg', 1);

-- Besoin pour Test 1
INSERT INTO besoin (id, id_ville, date_saisie) VALUES 
(101, 1, NOW()),
(102, 2, NOW()),
(103, 3, NOW());

-- Articles du besoin
INSERT INTO besoin_article (id, id_besoin, id_article, quantite) VALUES 
(1001, 101, 10, 1),    -- Ville A besoin 1 kg
(1002, 102, 10, 3),    -- Ville B besoin 3 kg
(1003, 103, 10, 5);    -- Ville C besoin 5 kg

-- Don pour Test 1
INSERT INTO don (id, donateur, date_don) VALUES 
(201, 'Donateur Test 1', NOW());

-- Articles du don
INSERT INTO don_article (id, id_don, id_article, quantite) VALUES 
(2001, 201, 10, 5);    -- 5 kg disponibles

-- Les distributions seront calculées par l'algorithme


-- === TEST 2 : Dons >= besoins totaux ===
-- Résultat attendu: 5, 10 (distribution parfaite, pas de reste)

INSERT IGNORE INTO ville (id, nom, id_region) VALUES 
(4, 'Ville X - Test 2', 2),
(5, 'Ville Y - Test 2', 2);

INSERT IGNORE INTO article (id, nom, prix_unitaire, unite, id_type_besoin) VALUES 
(11, 'Farine Test 2', 1.80, 'kg', 1);

INSERT INTO besoin (id, id_ville, date_saisie) VALUES 
(201, 4, NOW()),
(202, 5, NOW());

INSERT INTO besoin_article (id, id_besoin, id_article, quantite) VALUES 
(2001, 201, 11, 5),    -- Ville X besoin 5 kg
(2002, 202, 11, 10);   -- Ville Y besoin 10 kg

INSERT INTO don (id, donateur, date_don) VALUES 
(202, 'Donateur Test 2', NOW());

INSERT INTO don_article (id, id_don, id_article, quantite) VALUES 
(2002, 202, 11, 20);   -- 20 kg disponibles


-- === TEST 3 : Besoins non divisibles équitablement ===
-- 10 dons / 3 villes = 3.33 chacun
-- Résultat attendu: 3, 3, 3 → reste 1 → 4, 3, 3 (ou 3, 4, 3 ou 3, 3, 4 selon décimales)

INSERT IGNORE INTO ville (id, nom, id_region) VALUES 
(6, 'Ville P - Test 3', 1),
(7, 'Ville Q - Test 3', 1),
(8, 'Ville R - Test 3', 1);

INSERT IGNORE INTO article (id, nom, prix_unitaire, unite, id_type_besoin) VALUES 
(12, 'Sucre Test 3', 3.50, 'kg', 1);

INSERT INTO besoin (id, id_ville, date_saisie) VALUES 
(301, 6, NOW()),
(302, 7, NOW()),
(303, 8, NOW());

INSERT INTO besoin_article (id, id_besoin, id_article, quantite) VALUES 
(3001, 301, 12, 3),    -- Ville P besoin 3 kg
(3002, 302, 12, 3),    -- Ville Q besoin 3 kg
(3003, 303, 12, 3);    -- Ville R besoin 3 kg

INSERT INTO don (id, donateur, date_don) VALUES 
(203, 'Donateur Test 3', NOW());

INSERT INTO don_article (id, id_don, id_article, quantite) VALUES 
(2003, 203, 12, 10);   -- 10 kg disponibles
-- 10/9 = 1.111 chacun → 1, 1, 1 → reste 1 → 2, 1, 1


-- === TEST 4 : Dons insuffisants ===
-- Résultat attendu: mauvaise distribution faute de dons

INSERT IGNORE INTO ville (id, nom, id_region) VALUES 
(9, 'Ville M - Test 4', 2),
(10, 'Ville N - Test 4', 2);

INSERT IGNORE INTO article (id, nom, prix_unitaire, unite, id_type_besoin) VALUES 
(13, 'Huile Test 4', 5.00, 'litre', 1);

INSERT INTO besoin (id, id_ville, date_saisie) VALUES 
(401, 9, NOW()),
(402, 10, NOW());

INSERT INTO besoin_article (id, id_besoin, id_article, quantite) VALUES 
(4001, 401, 13, 10),   -- Ville M besoin 10 litres
(4002, 402, 13, 10);   -- Ville N besoin 10 litres

INSERT INTO don (id, donateur, date_don) VALUES 
(204, 'Donateur Test 4', NOW());

INSERT INTO don_article (id, id_don, id_article, quantite) VALUES 
(2004, 204, 13, 3);    -- seulement 3 litres disponibles
-- 3/20 = 0.15 chacun → 0, 0 → reste 3 → 1, 1 (+ 1 undistributed)


-- === TEST 5 : Cas complexe avec décimales différentes ===
-- Dons: 7, Besoins: 2, 5, 4
-- 7/11 = 0.636...
-- 2 * 0.636 = 1.272 → 1 (décimale: 0.272)
-- 5 * 0.636 = 3.181 → 3 (décimale: 0.181)
-- 4 * 0.636 = 2.545 → 2 (décimale: 0.545)
-- Total: 6, Reste: 1
-- Distribution du reste : besoin 4 a la plus grande décimale (0.545)
-- Résultat final: 1, 3, 3

INSERT IGNORE INTO ville (id, nom, id_region) VALUES 
(11, 'Ville Test 5A', 1),
(12, 'Ville Test 5B', 1),
(13, 'Ville Test 5C', 1);

INSERT IGNORE INTO article (id, nom, prix_unitaire, unite, id_type_besoin) VALUES 
(14, 'Pates Test 5', 2.20, 'kg', 1);

INSERT INTO besoin (id, id_ville, date_saisie) VALUES 
(501, 11, NOW()),
(502, 12, NOW()),
(503, 13, NOW());

INSERT INTO besoin_article (id, id_besoin, id_article, quantite) VALUES 
(5001, 501, 14, 2),    -- besoin 2 kg
(5002, 502, 14, 5),    -- besoin 5 kg
(5003, 503, 14, 4);    -- besoin 4 kg

INSERT INTO don (id, donateur, date_don) VALUES 
(205, 'Donateur Test 5', NOW());

INSERT INTO don_article (id, id_don, id_article, quantite) VALUES 
(2005, 205, 14, 7);    -- 7 kg disponibles

