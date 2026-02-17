# Base de données BNGRC - Scripts SQL

## Description
Ce dossier contient tous les scripts SQL pour la gestion de la base de données du projet BNGRC (Bureau National de Gestion des Risques et Catastrophes).

## Ordre d'exécution des scripts

### 1. Installation initiale (Nouvelle base de données)

Pour créer une nouvelle base de données complète avec toutes les fonctionnalités :

```bash
# 1. Créer la structure de base
mysql -u root -p < 20260216-01.sql

# 2. Insérer les données de référence (régions, types)
mysql -u root -p < 20260216-02.sql

# 3. Créer les vues de base
mysql -u root -p < 20260216-03.sql

# 4. Insérer les données de test initiales
mysql -u root -p < 20260216-04.sql

# 5. Ajouter les fonctionnalités V2 (achats, config)
mysql -u root -p < 20260216-05-v2.sql

# 6. Ajouter la colonne ordre (priorité)
mysql -u root -p < 20260217-add-ordre-column.sql
```

### 2. Mise à jour d'une base existante

Si vous avez déjà une base de données et voulez ajouter la fonctionnalité de priorité :

```bash
# Ajouter la colonne ordre et mettre à jour les vues
mysql -u root -p bngrc < 20260217-add-ordre-column.sql

# Mettre à jour les données existantes avec des ordres
mysql -u root -p bngrc < 20260217-update-existing-data-ordre.sql
```

### 3. Données de test pour l'examen

Pour utiliser le jeu de données de l'examen final (basé sur jeu_donnees_cyclone_S3.xlsx) :

```bash
# Remplacer toutes les données par celles de l'examen
mysql -u root -p bngrc < 20260217-data-with-ordre.sql
```

⚠️ **Attention** : Ce script supprime toutes les données existantes (distributions, dons, besoins, villes) avant d'insérer les nouvelles.

## Description des fichiers

### Fichiers de structure

- **20260216-01.sql** : Création de la base de données et des tables principales
  - `type_besoin`, `article`, `region`, `ville`
  - `besoin`, `besoin_article`
  - `don`, `don_article`
  - `distribution`

- **20260216-02.sql** : Données de référence
  - 23 régions de Madagascar
  - 3 types de besoins (Nature, Materiaux, Argent)

- **20260216-03.sql** : Vues de base
  - `v_reste_dons_disponibles` : Dons non encore distribués
  - `v_reste_total_dons_disponibles` : Stock total au dépôt
  - `v_reste_besoin` : Besoins non satisfaits
  - `v_historique_distributions_villes` : Historique des distributions

- **20260216-05-v2.sql** : Fonctionnalités avancées
  - Table `config` : Configuration système
  - Table `achat` : Achats avec dons en argent
  - Vues : `v_dons_argent_disponibles`, `v_achats_disponibles`, `v_recapitulatif`

### Fichiers de données

- **20260216-04.sql** : Données de test complètes
  - 11 articles variés
  - 8 villes de test
  - 5 besoins avec leurs articles
  - 7 dons de différentes organisations
  - Distributions d'exemple

- **test_data.sql** : Tests pour l'algorithme de distribution proportionnelle
  - 5 cas de test avec différents scénarios

### Nouveaux fichiers (2026-02-17)

- **20260217-add-ordre-column.sql** : ✨ Ajout de la colonne `ordre` (priorité)
  - Ajoute `ordre` à `besoin_article`
  - Met à jour les vues existantes
  - Crée de nouvelles vues : `v_besoins_par_priorite`, `v_stats_par_priorite`

- **20260217-update-existing-data-ordre.sql** : Mise à jour des données existantes
  - Option 1 : Attribution automatique par date
  - Option 2 : Attribution manuelle par besoin

- **20260217-data-with-ordre.sql** : ✨ Jeu de données pour l'examen
  - Basé sur jeu_donnees_cyclone_S3.xlsx
  - Contient 27 besoins avec ordres de priorité (1-26)
  - 6 villes : Toamasina, Nosy Be, Mananjary, Farafangana, Morondava, Toliara
  - 9 types d'articles

## Nouvelles fonctionnalités : Ordre de priorité

### Colonne `ordre`

La colonne `ordre` dans `besoin_article` représente la priorité du besoin :
- **Valeur basse (1, 2, 3...)** = Haute priorité (urgent)
- **Valeur élevée** = Basse priorité
- **NULL** = Pas de priorité définie (traité en dernier)

### Nouvelles vues

#### v_besoins_par_priorite
Liste tous les besoins (satisfaits ou non) triés par ordre de priorité.
```sql
SELECT * FROM v_besoins_par_priorite;
```

#### v_stats_par_priorite
Statistiques agrégées par niveau de priorité.
```sql
SELECT * FROM v_stats_par_priorite;
```

#### v_reste_besoin (mise à jour)
Maintenant inclut la colonne `ordre` et tri par priorité.
```sql
SELECT * FROM v_reste_besoin;
```

## Utilisation avec PHP

Dans votre code PHP, vous pouvez maintenant :

```php
// Récupérer les besoins par ordre de priorité
$sql = "SELECT * FROM v_besoins_par_priorite WHERE reste_a_combler > 0";

// Insérer un besoin avec priorité
$sql = "INSERT INTO besoin_article (id_besoin, id_article, quantite, ordre) 
        VALUES (?, ?, ?, ?)";

// Distribuer en priorité
$sql = "SELECT * FROM v_reste_besoin ORDER BY ordre ASC LIMIT 1";
```

## Notes importantes

1. **Ordre d'exécution** : Respectez toujours l'ordre indiqué pour éviter les erreurs de clés étrangères
2. **Backup** : Faites toujours une sauvegarde avant d'exécuter des scripts de mise à jour
3. **Test** : Testez sur une base de développement avant de passer en production
4. **Régions** : Vérifiez les IDs de régions dans `20260217-data-with-ordre.sql` (certains IDs peuvent ne pas exister)

## Connexion à la base

```bash
# Se connecter à MySQL
mysql -u root -p

# Utiliser la base BNGRC
USE bngrc;

# Lister les tables
SHOW TABLES;

# Voir la structure d'une table
DESCRIBE besoin_article;
```

## Sauvegarde et restauration

```bash
# Sauvegarder la base
mysqldump -u root -p bngrc > backup_bngrc_$(date +%Y%m%d).sql

# Restaurer depuis une sauvegarde
mysql -u root -p bngrc < backup_bngrc_20260217.sql
```

## Questions fréquentes

**Q: Comment réinitialiser complètement la base ?**
```bash
mysql -u root -p -e "DROP DATABASE IF EXISTS bngrc;"
# Puis réexécuter tous les scripts depuis le début
```

**Q: Comment voir tous les besoins avec leur priorité ?**
```sql
SELECT * FROM v_besoins_par_priorite;
```

**Q: Comment changer la priorité d'un besoin existant ?**
```sql
UPDATE besoin_article SET ordre = 1 WHERE id = 123;
```

## Support

Pour toute question, consultez la documentation du projet ou contactez l'équipe de développement.
