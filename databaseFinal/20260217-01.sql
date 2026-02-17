CREATE DATABASE bngrc;
USE bngrc;

-- Types de besoins
CREATE TABLE type_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- Articles/Produits
CREATE TABLE article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix_unitaire DECIMAL(12,2) NOT NULL,
    unite VARCHAR(20) NOT NULL,
    id_type_besoin INT,
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id)
);

-- Régions
CREATE TABLE region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

-- Villes
CREATE TABLE ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_region INT,
    FOREIGN KEY (id_region) REFERENCES region(id)
);

-- Besoin
CREATE TABLE besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ville INT,
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES ville(id)
);

-- Articles dans un besoin
CREATE TABLE besoin_article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_besoin INT,
    id_article INT,
    quantite DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_besoin) REFERENCES besoin(id),
    FOREIGN KEY (id_article) REFERENCES article(id)
);

-- Don recue
CREATE TABLE don (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donateur VARCHAR(200),
    date_don TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Articles dans un don
CREATE TABLE don_article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_don INT,
    id_article INT,
    quantite DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_don) REFERENCES don(id),
    FOREIGN KEY (id_article) REFERENCES article(id)
);

-- Distribution
CREATE TABLE distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_don_article INT,
    id_besoin_article INT,
    quantite_attribuee DECIMAL(12,2) NOT NULL,
    date_distribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don_article) REFERENCES don_article(id),
    FOREIGN KEY (id_besoin_article) REFERENCES besoin_article(id)
);

-- Modification de structure (20260217-add-ordre-column.sql)
ALTER TABLE besoin_article
ADD COLUMN ordre INT DEFAULT NULL AFTER quantite;