CREATE OR REPLACE VIEW v_besoins_en_attente AS
SELECT 
    b.id AS id_besoin,
    v.nom AS ville,
    b.date_saisie,
    a.nom AS article,
    ba.quantite AS quantite_demandee,
    -- Si NULL (jamais distribué), on met 0
    COALESCE(SUM(dist.quantite_attribuee), 0) AS quantite_recue,
    -- Calcul du reste
    (ba.quantite - COALESCE(SUM(dist.quantite_attribuee), 0)) AS reste_a_livrer,
    a.unite
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN article a ON ba.id_article = a.id
-- LEFT JOIN est obligatoire pour voir ceux qui n'ont aucune distribution
LEFT JOIN distribution dist ON dist.id_besoin_article = ba.id
GROUP BY ba.id, b.id, v.nom, b.date_saisie, a.nom, ba.quantite, a.unite
-- C'est ici qu'on filtre : on garde seulement si le reste est supérieur à 0
HAVING (ba.quantite - COALESCE(SUM(dist.quantite_attribuee), 0)) > 0
ORDER BY b.date_saisie ASC;



CREATE OR REPLACE VIEW v_dons_distribues_par_ville AS
SELECT 
    v.nom AS ville,
    r.nom AS region,
    d.donateur,
    a.nom AS article,
    dist.quantite_attribuee AS quantite_donnee,
    a.unite,
    (dist.quantite_attribuee * a.prix_unitaire) AS valeur_estimee_monetaire,
    dist.date_distribution,
    d.id AS id_don,
    b.id AS id_besoin
FROM distribution dist
-- On remonte vers le donateur
JOIN don_article da ON dist.id_don_article = da.id
JOIN don d ON da.id_don = d.id
-- On remonte vers la ville qui a exprimé le besoin
JOIN b_art_alias (SELECT ba.id, ba.id_besoin, ba.id_article FROM besoin_article ba) ba 
    ON dist.id_besoin_article = ba.id
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN region r ON v.id_region = r.id
-- On récupère les détails de l'article
JOIN article a ON da.id_article = a.id
ORDER BY dist.date_distribution DESC;



SELECT 
     v.nom AS ville,
     r.nom AS region,
     a.nom AS article,
     ba.quantite AS quantite_demandee,
     -- Somme des quantités distribuées à cette ville pour cet article précis
     COALESCE((
         SELECT SUM(dist.quantite_attribuee)
         FROM distribution dist
         WHERE dist.id_besoin_article = ba.id
     ), 0) AS quantite_recue,
     -- Différence pour savoir ce qu'il manque
     (ba.quantite - COALESCE((
         SELECT SUM(dist.quantite_attribuee)
         FROM distribution dist
         WHERE dist.id_besoin_article = ba.id
     ), 0)) AS reste_a_combler,
     a.unite,
     a.prix_unitaire,
     -- Valeur monétaire du besoin total pour cette ville
     (ba.quantite * a.prix_unitaire) AS valeur_totale_besoin
 FROM besoin_article ba
 JOIN besoin b ON ba.id_besoin = b.id
 JOIN ville v ON b.id_ville = v.id
 JOIN region r ON v.id_region = r.id
 JOIN article a ON ba.id_article = a.id
 ORDER BY v.nom ASC, a.nom ASC;


CREATE OR REPLACE VIEW v_suivi_besoins_restants AS
SELECT 
    v.nom AS ville,
    r.nom AS region,
    a.nom AS article,
    -- On calcule le reste en soustrayant les distributions du besoin initial
    (ba.quantite - COALESCE((
        SELECT SUM(dist.quantite_attribuee)
        FROM distribution dist
        WHERE dist.id_besoin_article = ba.id
    ), 0)) AS reste_a_combler,
    a.unite,
    -- Valeur monétaire basée sur le prix unitaire de l'article
    (ba.quantite * a.prix_unitaire) AS valeur_totale_besoin
FROM besoin_article ba
JOIN besoin b ON ba.id_besoin = b.id
JOIN ville v ON b.id_ville = v.id
JOIN region r ON v.id_region = r.id
JOIN article a ON ba.id_article = a.id
ORDER BY v.nom ASC, a.nom ASC;