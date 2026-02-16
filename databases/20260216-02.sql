
DELETE FROM distribution;
DELETE FROM don_article;
DELETE FROM don;
DELETE FROM besoin_article;
DELETE FROM besoin;
DELETE FROM ville;
DELETE FROM region;
DELETE FROM article;
DELETE FROM type_besoin;

-- Réinitialiser les AUTO_INCREMENT
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