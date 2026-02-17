-- ==========================================
-- MISE À JOUR DES DONNÉES EXISTANTES AVEC ORDRE
-- ==========================================
-- Ce fichier ajoute des ordres de priorité aux besoins existants

USE bngrc;

-- Mettre à jour les besoins existants avec des ordres logiques
-- Plus la priorité est basse (1, 2, 3...), plus c'est urgent

-- Si vous voulez mettre à jour des besoins existants dans votre base:

-- Option 1: Attribution automatique basée sur la date (plus ancien = plus prioritaire)
SET @ordre_counter = 0;
UPDATE besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
SET ba.ordre = (@ordre_counter := @ordre_counter + 1)
ORDER BY b.date_saisie ASC, ba.id ASC;

-- Option 2: Attribution manuelle par besoin (exemples)
-- Besoin 1 - Articles prioritaires
UPDATE besoin_article SET ordre = 1 WHERE id_besoin = 1 AND id_article = 1;
UPDATE besoin_article SET ordre = 2 WHERE id_besoin = 1 AND id_article = 2;
UPDATE besoin_article SET ordre = 3 WHERE id_besoin = 1 AND id_article = 8;

-- Besoin 2 - Articles de construction
UPDATE besoin_article SET ordre = 4 WHERE id_besoin = 2 AND id_article = 6;
UPDATE besoin_article SET ordre = 5 WHERE id_besoin = 2 AND id_article = 7;
UPDATE besoin_article SET ordre = 6 WHERE id_besoin = 2 AND id_article = 9;

-- Besoin 3 - Urgence cyclone
UPDATE besoin_article SET ordre = 7 WHERE id_besoin = 3 AND id_article = 1;
UPDATE besoin_article SET ordre = 8 WHERE id_besoin = 3 AND id_article = 2;
UPDATE besoin_article SET ordre = 9 WHERE id_besoin = 3 AND id_article = 3;
UPDATE besoin_article SET ordre = 10 WHERE id_besoin = 3 AND id_article = 4;
UPDATE besoin_article SET ordre = 11 WHERE id_besoin = 3 AND id_article = 8;
UPDATE besoin_article SET ordre = 12 WHERE id_besoin = 3 AND id_article = 10;

-- Besoin 4 - Inondations
UPDATE besoin_article SET ordre = 13 WHERE id_besoin = 4 AND id_article = 1;
UPDATE besoin_article SET ordre = 14 WHERE id_besoin = 4 AND id_article = 2;
UPDATE besoin_article SET ordre = 15 WHERE id_besoin = 4 AND id_article = 5;
UPDATE besoin_article SET ordre = 16 WHERE id_besoin = 4 AND id_article = 9;
UPDATE besoin_article SET ordre = 17 WHERE id_besoin = 4 AND id_article = 10;

-- Besoin 5 - Glissement de terrain
UPDATE besoin_article SET ordre = 18 WHERE id_besoin = 5 AND id_article = 6;
UPDATE besoin_article SET ordre = 19 WHERE id_besoin = 5 AND id_article = 7;
UPDATE besoin_article SET ordre = 20 WHERE id_besoin = 5 AND id_article = 8;

-- Vérification
SELECT 'Vérification des ordres mis à jour:' AS info;
SELECT 
    ba.id,
    ba.ordre,
    v.nom AS ville,
    a.nom AS article,
    ba.quantite,
    b.date_saisie
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN article a ON ba.id_article = a.id
WHERE ba.ordre IS NOT NULL
ORDER BY ba.ordre ASC;
