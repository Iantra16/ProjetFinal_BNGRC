-- Types de besoins
CREATE TABLE type_besoin (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL  -- 'nature', 'materiaux', 'argent'    
);

-- Articles/Produits
CREATE TABLE article (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix_unitaire DECIMAL(12,2) NOT NULL,
    unite VARCHAR(20) NOT NULL,
    id_type_besoin INT REFERENCES type_besoin(id)
);

-- Régions
CREATE TABLE region (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Villes
CREATE TABLE ville (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_region INT REFERENCES region(id)
);

-- Besoin (par ville, sans article directement)
CREATE TABLE besoin (
    id SERIAL PRIMARY KEY,
    id_ville INT REFERENCES ville(id),
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- NOUVELLE TABLE : Articles dans un besoin
CREATE TABLE besoin_article (
    id SERIAL PRIMARY KEY,
    id_besoin INT REFERENCES besoin(id),
    id_article INT REFERENCES article(id),
    quantite DECIMAL(12,2) NOT NULL
);

-- Don recue
CREATE TABLE don (
    id SERIAL PRIMARY KEY,
    donateur VARCHAR(200),
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Articles dans un don
CREATE TABLE don_article (
    id SERIAL PRIMARY KEY,
    id_don INT REFERENCES don(id),
    id_article INT REFERENCES article(id),
    quantite DECIMAL(12,2) NOT NULL
);

-- lie don_article à besoin_article
CREATE TABLE distribution (
    id SERIAL PRIMARY KEY,
    id_don_article INT REFERENCES don_article(id),
    id_besoin_article INT REFERENCES besoin_article(id),
    quantite_attribuee DECIMAL(12,2) NOT NULL,
    date_distribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);