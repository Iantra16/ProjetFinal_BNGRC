USE bngrc;

CREATE OR REPLACE VIEW v_reste_dons_disponibles AS
SELECT
    da.id AS id_don_article,
    a.id AS id_article,
    d.date_don,
    a.nom AS article,
    (da.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_don_article = da.id
    ), 0)) AS stock_restant,
    a.unite
FROM don_article da
JOIN article a ON da.id_article = a.id
JOIN don d ON da.id_don = d.id
HAVING stock_restant > 0
ORDER BY d.date_don ASC;

CREATE OR REPLACE VIEW v_reste_total_dons_disponibles AS
SELECT
    a.nom AS article,
    SUM(da.quantite) AS total_initial,
    COALESCE(SUM((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_don_article = da.id
    )), 0) AS total_distribue,
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
    ba.ordre,
    v.nom AS ville,
    a.id AS id_article,
    b.date_saisie AS date_besoin,
    a.nom AS article,
    tb.libelle AS categorie,
    (ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) AS reste_a_combler,
    a.unite,
    a.prix_unitaire
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN article a ON ba.id_article = a.id
JOIN type_besoin tb ON a.id_type_besoin = tb.id
HAVING reste_a_combler > 0
ORDER BY
    CASE WHEN ba.ordre IS NULL THEN 9999 ELSE ba.ordre END ASC,
    b.date_saisie ASC;

CREATE OR REPLACE VIEW v_historique_distributions_villes AS
SELECT
    dist.id AS distribution_id,
    ba.ordre,
    dist.quantite_attribuee,
    dist.date_distribution,
    v.id AS ville_id,
    v.nom AS ville_nom,
    a.nom AS article_nom,
    tb.libelle AS categorie,
    a.prix_unitaire,
    a.unite,
    don.donateur,
    (dist.quantite_attribuee * a.prix_unitaire) AS valeur_totale
FROM distribution dist
JOIN besoin_article ba ON dist.id_besoin_article = ba.id
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN don_article da ON dist.id_don_article = da.id
JOIN article a ON da.id_article = a.id
JOIN type_besoin tb ON a.id_type_besoin = tb.id
JOIN don ON da.id_don = don.id
ORDER BY dist.date_distribution DESC;

CREATE OR REPLACE VIEW v_besoins_par_priorite AS
SELECT
    ba.id AS id_besoin_article,
    ba.ordre,
    v.nom AS ville,
    r.nom AS region,
    b.date_saisie AS date_besoin,
    a.nom AS article,
    tb.libelle AS categorie,
    ba.quantite AS quantite_demandee,
    COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0) AS quantite_distribuee,
    (ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) AS reste_a_combler,
    a.unite,
    a.prix_unitaire,
    (ba.quantite * a.prix_unitaire) AS valeur_totale_besoin,
    ((ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) * a.prix_unitaire) AS valeur_reste
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN region r ON v.id_region = r.id
JOIN article a ON ba.id_article = a.id
JOIN type_besoin tb ON a.id_type_besoin = tb.id
ORDER BY
    CASE WHEN ba.ordre IS NULL THEN 9999 ELSE ba.ordre END ASC,
    b.date_saisie ASC;

CREATE OR REPLACE VIEW v_stats_par_priorite AS
SELECT
    ba.ordre,
    COUNT(DISTINCT ba.id) AS nombre_besoins,
    COUNT(DISTINCT v.id) AS nombre_villes,
    SUM(ba.quantite) AS quantite_totale_demandee,
    SUM(COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) AS quantite_totale_distribuee,
    SUM(ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) AS reste_total,
    SUM(ba.quantite * a.prix_unitaire) AS valeur_totale,
    SUM((ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) * a.prix_unitaire) AS valeur_reste
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN article a ON ba.id_article = a.id
GROUP BY ba.ordre
ORDER BY CASE WHEN ba.ordre IS NULL THEN 9999 ELSE ba.ordre END ASC;