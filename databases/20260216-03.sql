CREATE OR REPLACE VIEW v_reste_dons_disponibles AS
SELECT 
    da.id AS id_don_article,   
    a.id AS id_article,
    d.date_don,
    a.nom AS article,
    -- Calcul direct du reste uniquement
    (da.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_don_article = da.id
    ), 0)) AS stock_restant,
    a.unite
FROM don_article da
JOIN article a ON da.id_article = a.id
JOIN don d ON da.id_don = d.id
-- On filtre pour ne voir que ce qui n'est pas encore épuisé
HAVING stock_restant > 0
ORDER BY d.date_don ASC;
select * from v_reste_dons_disponibles;

CREATE OR REPLACE VIEW v_reste_total_dons_disponibles AS
SELECT 
    a.nom AS article,
    SUM(da.quantite) AS total_initial,
    -- Somme de toutes les distributions pour cet article
    COALESCE(SUM((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_don_article = da.id
    )), 0) AS total_distribue,
    -- Stock total disponible au dépôt
    (SUM(da.quantite) - COALESCE(SUM((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_don_article = da.id
    )), 0)) AS stock_total_restant,
    a.unite
FROM don_article da
JOIN article a ON da.id_article = a.id
GROUP BY a.nom, a.unite
HAVING stock_total_restant > 0
ORDER BY a.nom ASC;


CREATE OR REPLACE VIEW v_reste_besoin AS
SELECT 
    ba.id AS id_besoin_article,
    v.nom AS ville,
    a.id AS id_article,
    b.date_saisie AS date_besoin,
    a.nom AS article,
    -- Calcul du reste à combler (Demande - Somme des distributions reçues)
    (ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) AS reste_a_combler,
    a.unite
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN article a ON ba.id_article = a.id
-- Filtre pour ne garder que les besoins non terminés
HAVING reste_a_combler > 0
ORDER BY b.date_saisie ASC;

CREATE OR REPLACE VIEW v_historique_distributions_villes AS
SELECT 
    dist.id AS distribution_id,
    dist.quantite_attribuee,
    dist.date_distribution,
    v.id AS ville_id,
    v.nom AS ville_nom,
    a.nom AS article_nom,
    a.prix_unitaire,
    a.unite,
    don.donateur,
    -- Calcul de la valeur financière de cette distribution précise
    (dist.quantite_attribuee * a.prix_unitaire) AS valeur_totale
FROM distribution dist
JOIN besoin_article ba ON dist.id_besoin_article = ba.id
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN don_article da ON dist.id_don_article = da.id
JOIN article a ON da.id_article = a.id
JOIN don ON da.id_don = don.id
ORDER BY dist.date_distribution DESC;