-- ==========================================
-- TABLES V2 - Achats et Configuration
-- ==========================================

-- Table de configuration
CREATE TABLE IF NOT EXISTS config (
    cle VARCHAR(50) PRIMARY KEY,
    valeur VARCHAR(100) NOT NULL
);

-- Insérer le frais d'achat par défaut (10%)
INSERT INTO config (cle, valeur) VALUES ('frais_achat_pourcent', '10')
ON DUPLICATE KEY UPDATE valeur = valeur;

-- Table des achats (utiliser don argent pour acheter articles)
CREATE TABLE IF NOT EXISTS achat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_don_article INT NOT NULL,          -- don en argent utilisé
    id_article INT NOT NULL,              -- article acheté
    quantite DECIMAL(12,2) NOT NULL,
    prix_unitaire DECIMAL(12,2) NOT NULL,
    frais_pourcent DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL, -- (quantite * prix) * (1 + frais/100)
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don_article) REFERENCES don_article(id),
    FOREIGN KEY (id_article) REFERENCES article(id)
);

-- Modifier la table distribution pour supporter les achats
ALTER TABLE distribution ADD COLUMN id_achat INT NULL;
ALTER TABLE distribution ADD CONSTRAINT fk_distribution_achat 
    FOREIGN KEY (id_achat) REFERENCES achat(id);

-- ==========================================
-- VUES V2
-- ==========================================

-- Vue: Dons en argent disponibles
CREATE OR REPLACE VIEW v_dons_argent_disponibles AS
SELECT 
    da.id AS id_don_article,
    da.quantite AS montant_initial,
    d.donateur,
    d.date_don,
    (da.quantite - COALESCE((
        SELECT SUM(ac.montant_total) 
        FROM achat ac 
        WHERE ac.id_don_article = da.id
    ), 0)) AS solde_disponible
FROM don_article da
JOIN don d ON da.id_don = d.id
JOIN article a ON da.id_article = a.id
JOIN type_besoin t ON a.id_type_besoin = t.id
WHERE t.libelle = 'Argent'
HAVING solde_disponible > 0
ORDER BY d.date_don ASC;

-- Vue: Achats disponibles pour distribution
CREATE OR REPLACE VIEW v_achats_disponibles AS
SELECT 
    ac.id AS id_achat,
    ac.id_article,
    a.nom AS article,
    ac.quantite AS quantite_achetee,
    COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_achat = ac.id
    ), 0) AS quantite_distribuee,
    (ac.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_achat = ac.id
    ), 0)) AS stock_restant,
    a.unite,
    ac.date_achat
FROM achat ac
JOIN article a ON ac.id_article = a.id
HAVING stock_restant > 0
ORDER BY ac.date_achat ASC;

-- Vue: Récapitulatif général
CREATE OR REPLACE VIEW v_recapitulatif AS
SELECT 
    (SELECT COALESCE(SUM(ba.quantite * a.prix_unitaire), 0)
     FROM besoin_article ba JOIN article a ON ba.id_article = a.id) AS montant_total_besoins,
    
    (SELECT COALESCE(SUM(dist.quantite_attribuee * a.prix_unitaire), 0)
     FROM distribution dist 
     JOIN besoin_article ba ON dist.id_besoin_article = ba.id
     JOIN article a ON ba.id_article = a.id) AS montant_besoins_satisfaits,
    
    (SELECT COALESCE(SUM(ba.quantite * a.prix_unitaire), 0)
     FROM besoin_article ba JOIN article a ON ba.id_article = a.id) -
    (SELECT COALESCE(SUM(dist.quantite_attribuee * a.prix_unitaire), 0)
     FROM distribution dist 
     JOIN besoin_article ba ON dist.id_besoin_article = ba.id
     JOIN article a ON ba.id_article = a.id) AS montant_besoins_restants;
