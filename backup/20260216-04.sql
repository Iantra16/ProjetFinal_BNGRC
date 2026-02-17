-- ==========================================
-- DONNÉES DE TEST POUR BESOINMODEL ET DONMODEL
-- ==========================================
-- Ce fichier contient des données de test complètes pour tester
-- les modèles BesoinModel.php et DonModel.php

-- USE bngrc;

-- Nettoyage des données existantes (dans le bon ordre pour respecter les clés étrangères)

-- ==========================================
-- 2. ARTICLES
-- ==========================================
INSERT INTO article (nom, prix_unitaire, unite, id_type_besoin) VALUES 
-- Articles nature (nourriture, eau)
('Riz', 2500.00, 'kg', 1),
('Eau potable', 1000.00, 'litre', 1),
('Huile', 3000.00, 'litre', 1),
('Haricots', 2000.00, 'kg', 1),
('Sucre', 1500.00, 'kg', 1),

-- Articles matériaux (construction, vêtements)
('Tôle', 15000.00, 'unité', 2),
('Ciment', 8000.00, 'sac', 2),
('Couverture', 5000.00, 'unité', 2),
('Bâche', 7000.00, 'mètre carré', 2),
('Vêtements', 3000.00, 'lot', 2),

-- Articles argent
('Don financier', 1.00, 'Ariary', 3);

-- ==========================================
-- 4. VILLES
-- ==========================================
INSERT INTO ville (nom, id_region) VALUES 
-- Analamanga
('Antananarivo', 3),
('Ambohidratrimo', 3),

-- Vakinankaratra
('Antsirabe', 21),
('Betafo', 21),

-- Diana
('Antsiranana', 13),
('Ambilobe', 13),

-- Atsimo-Atsinanana
('Farafangana', 8),
('Vangaindrano', 8);

-- ==========================================
-- 5. BESOINS (avec leurs articles)
-- ==========================================

-- Besoin 1: Antananarivo - Inondations
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(1, '2026-02-10 08:30:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite) VALUES
(1, 1, 500.00),   -- 500 kg de riz
(1, 2, 1000.00),  -- 1000 litres d'eau
(1, 8, 100.00);   -- 100 couvertures

-- Besoin 2: Antsirabe - Séisme
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(3, '2026-02-11 14:45:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite) VALUES
(2, 6, 200.00),   -- 200 tôles
(2, 7, 150.00),   -- 150 sacs de ciment
(2, 9, 500.00);   -- 500 m² de bâche

-- Besoin 3: Antsiranana - Cyclone
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(5, '2026-02-12 10:15:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite) VALUES
(3, 1, 1000.00),  -- 1000 kg de riz
(3, 2, 2000.00),  -- 2000 litres d'eau
(3, 3, 200.00),   -- 200 litres d'huile
(3, 4, 500.00),   -- 500 kg de haricots
(3, 8, 300.00),   -- 300 couvertures
(3, 10, 150.00);  -- 150 lots de vêtements

-- Besoin 4: Farafangana - Inondations
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(7, '2026-02-13 16:20:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite) VALUES
(4, 1, 800.00),   -- 800 kg de riz
(4, 2, 1500.00),  -- 1500 litres d'eau
(4, 5, 100.00),   -- 100 kg de sucre
(4, 9, 300.00),   -- 300 m² de bâche
(4, 10, 200.00);  -- 200 lots de vêtements

-- Besoin 5: Ambohidratrimo - Glissement de terrain
INSERT INTO besoin (id_ville, date_saisie) VALUES 
(2, '2026-02-14 09:00:00');

INSERT INTO besoin_article (id_besoin, id_article, quantite) VALUES
(5, 6, 100.00),   -- 100 tôles
(5, 7, 80.00),    -- 80 sacs de ciment
(5, 8, 50.00);    -- 50 couvertures

-- ==========================================
-- 6. DONS (avec leurs articles)
-- ==========================================

-- Don 1: Croix Rouge Malagasy
INSERT INTO don (donateur, date_don) VALUES 
('Croix Rouge Malagasy', '2026-02-11 09:00:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(1, 1, 300.00),   -- 300 kg de riz
(1, 2, 600.00),   -- 600 litres d'eau
(1, 8, 80.00);    -- 80 couvertures

-- Don 2: UNICEF Madagascar
INSERT INTO don (donateur, date_don) VALUES 
('UNICEF Madagascar', '2026-02-12 11:30:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(2, 2, 1000.00),  -- 1000 litres d'eau
(2, 3, 150.00),   -- 150 litres d'huile
(2, 10, 100.00);  -- 100 lots de vêtements

-- Don 3: CARE International
INSERT INTO don (donateur, date_don) VALUES 
('CARE International', '2026-02-13 08:45:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(3, 6, 150.00),   -- 150 tôles
(3, 7, 100.00),   -- 100 sacs de ciment
(3, 9, 400.00);   -- 400 m² de bâche

-- Don 4: Anonyme
INSERT INTO don (donateur, date_don) VALUES 
(NULL, '2026-02-13 14:00:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(4, 1, 500.00),   -- 500 kg de riz
(4, 4, 300.00),   -- 300 kg de haricots
(4, 5, 80.00);    -- 80 kg de sucre

-- Don 5: PAM (Programme Alimentaire Mondial)
INSERT INTO don (donateur, date_don) VALUES 
('PAM', '2026-02-14 10:15:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(5, 1, 800.00),   -- 800 kg de riz
(5, 2, 1200.00),  -- 1200 litres d'eau
(5, 3, 250.00),   -- 250 litres d'huile
(5, 4, 400.00),   -- 400 kg de haricots
(5, 5, 150.00);   -- 150 kg de sucre

-- Don 6: Fondation Orange Madagascar
INSERT INTO don (donateur, date_don) VALUES 
('Fondation Orange Madagascar', '2026-02-15 13:30:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(6, 8, 200.00),   -- 200 couvertures
(6, 10, 150.00);  -- 150 lots de vêtements

-- Don 7: Entreprise ABC (don financier)
INSERT INTO don (donateur, date_don) VALUES 
('Entreprise ABC', '2026-02-15 16:00:00');

INSERT INTO don_article (id_don, id_article, quantite) VALUES
(7, 11, 5000000.00); -- 5 000 000 Ariary

-- ==========================================
-- 7. DISTRIBUTIONS (certains dons déjà distribués)
-- ==========================================

-- Distribution du Don 1 vers Besoin 1 (Antananarivo)
INSERT INTO distribution (id_don_article, id_besoin_article, quantite_attribuee, date_distribution) VALUES
-- Don 1, Article 1 (riz) -> Besoin 1, Article 1 (riz)
(1, 1, 200.00, '2026-02-12 10:00:00'),
-- Don 1, Article 2 (eau) -> Besoin 1, Article 2 (eau)
(2, 2, 500.00, '2026-02-12 10:05:00'),
-- Don 1, Article 3 (couvertures) -> Besoin 1, Article 3 (couvertures)
(3, 3, 80.00, '2026-02-12 10:10:00');

-- Distribution du Don 3 vers Besoin 2 (Antsirabe - séisme)
INSERT INTO distribution (id_don_article, id_besoin_article, quantite_attribuee, date_distribution) VALUES
-- Don 3, Article 1 (tôles) -> Besoin 2, Article 1 (tôles)
(7, 4, 100.00, '2026-02-14 09:00:00'),
-- Don 3, Article 2 (ciment) -> Besoin 2, Article 2 (ciment)
(8, 5, 50.00, '2026-02-14 09:05:00'),
-- Don 3, Article 3 (bâche) -> Besoin 2, Article 3 (bâche)
(9, 6, 300.00, '2026-02-14 09:10:00');

-- Distribution partielle du Don 5 vers Besoin 3 (Antsiranana - cyclone)
INSERT INTO distribution (id_don_article, id_besoin_article, quantite_attribuee, date_distribution) VALUES
-- Don 5, Article 1 (riz) -> Besoin 3, Article 1 (riz)
(14, 7, 600.00, '2026-02-15 11:00:00'),
-- Don 5, Article 2 (eau) -> Besoin 3, Article 2 (eau)
(15, 8, 1000.00, '2026-02-15 11:05:00');
