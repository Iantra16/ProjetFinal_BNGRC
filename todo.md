# Documentation du Projet BNGRC - Suivi des Collectes et Distributions de Dons

## 📋 Vue d'ensemble du Projet
Application web de gestion des besoins, des dons et des distributions pour les collectes humanitaires. 

**Stack Technologique :** PHP (Framework Flight) + MySQL + HTML/CSS/JavaScript

---

## 🎯 Fonctionnalités Implémentées

### 1. **Dashboard (Tableau de Bord)**
**Objectif :** Affichage d'un résumé global du système avec statistiques et listes.

#### 📁 Fichiers Impliqués :
- **Controller :** `app/controllers/DashboardController.php`
  - Méthode : `dashboard()`
- **Model :** 
  - `app/models/VilleModel.php` → `getAll()`
  - `app/models/BesoinModel.php` → `getAllBesoins()`
  - `app/models/DonModel.php` → `getAllDons()`
- **View :** `app/views/dashboard.php`

#### 🔧 Fonctionnalités :
- Afficher le total des villes
- Afficher la liste de toutes les villes avec région
- Afficher tous les besoins avec articles et calcul de valeur totale
- Afficher tous les dons avec articles
- Afficher les distributions complètes
- Gestion des transactions et jointures

---

### 2. **Gestion des Villes**
**Objectif :** CRUD complet pour les villes (Create, Read, Update assignation régions).

#### 📁 Fichiers Impliqués :
- **Controller :** `app/controllers/VilleController.php`
  - Méthode `add()` : Afficher formulaire + Traiter l'ajout
  - Méthode `list()` : Afficher la liste des villes
  - Méthode `besoins($id)` : Afficher les besoins d'une ville
  
- **Model :** `app/models/VilleModel.php`
  - `insert($nom, $id_region)` - Ajouter une ville
  - `getAll()` - Récupérer toutes les villes avec région
  - `getById($idVille)` - Récupérer une ville par ID
  - `getVilleById($idVille)` - Alias de getById
  - `getRegions()` - Récupérer toutes les régions disponibles
  - `count()` - Compter le nombre de villes
  - `updateVille($id)` - Mettre à jour une ville (non utilisé actuellement)
  - `deleteVille($id)` - Supprimer une ville (non utilisé actuellement)

- **Views :**
  - `app/views/ville/ajouter_ville.php` - Formulaire d'ajout de ville
  - `app/views/ville/villes.php` - Liste des villes
  - `app/views/ville/besoin_ville.php` - Besoins d'une ville spécifique

#### 🔧 Fonctionnalités :
- Ajouter une nouvelle ville avec région
- Consulter la liste de toutes les villes
- Voir les besoins associés à une ville
- Jointure avec la table `region`

---

### 3. **Gestion des Articles**
**Objectif :** Gestion des articles qui peuvent être utilisés dans les besoins ou les dons.

#### 📁 Fichiers Impliqués :
- **Controller :** `app/controllers/ArticleController.php`
  - Méthode `add()` : Afficher formulaire + Traiter l'ajout/POST
  
- **Model :** `app/models/ArticleModel.php`
  - `insert($nom, $prixU, $unite, $idTypeBesoin)` - Ajouter un article
  - `getAll()` - Récupérer tous les articles
  - `getArticleById($idArticle)` - Récupérer un article par ID
  - `getAllTypeBesoin()` - Récupérer tous les types de besoin

- **View :** `app/views/articles/form.php` - Formulaire d'ajout d'article

#### 🔧 Fonctionnalités :
- Créer un nouvel article avec :
  - Nom
  - Prix unitaire
  - Unité de mesure (kg, L, pièce, etc.)
  - Type de besoin associé
- Validation complète des champs
- Gestion des erreurs et messages de succès
- Récupération des types de besoin (nutrition, santé, hygiène, etc.)

---

### 4. **Gestion des Besoins**
**Objectif :** Enregistrement et gestion des besoins par ville avec articles associés.

#### 📁 Fichiers Impliqués :
- **Controller :** `app/controllers/BesoinController.php`
  - Méthode `ajouterForm($villeId = null)` : Afficher formulaire d'ajout
  - Méthode `ajouterSubmit()` : Traiter l'ajout du besoin (transaction)
  - Méthode `ajouterArticleAjax()` : Créer un article via AJAX
  
- **Model :** `app/models/BesoinModel.php`
  - `createBesoin($idVille)` - Créer un nouveau besoin
  - `addArticleToBesoin($besoinId, $idArticle, $quantite)` - Ajouter article au besoin
  - `getAllBesoins()` - Récupérer tous les besoins avec détails
  - `getBesoinById($besoinId)` - Récupérer un besoin spécifique
  - `getBesoinsByVille($villeId)` - Récupérer les besoins d'une ville
  - `getArticlesForBesoin($besoinId)` - Récupérer les articles d'un besoin
  - `getAllTypeBesoin()` - Récupérer les types de besoin
  - `getAllArticle()` - Récupérer tous les articles avec type
  - `createArticle($nom, $idTypeBesoin, $prixUnitaire, $unite)` - Créer article

- **View :** `app/views/besoin/ajouter_besoin.php` - Formulaire de besoin avec articles

#### 🔧 Fonctionnalités :
- Formulaire d'ajout de besoin pour une ville
- Pré-remplissage de la ville si accès via la fiche ville
- Sélection dynamique des articles
- Création d'articles à la volée (AJAX)
- Ajout multiple d'articles avec quantités
- Transactions & Rollback en cas d'erreur
- Gestion des messages de succès/erreur
- Jointures avec ville et région
- Calcul de valeur totale

---

### 5. **Gestion des Dons**
**Objectif :** Enregistrement et gestion des dons avec articles associés.

#### 📁 Fichiers Impliqués :
- **Controller :** `app/controllers/DonController.php`
  - Méthode `list()` : Afficher la liste des dons
  - Méthode `addForm()` : Afficher formulaire d'ajout
  - Méthode `add()` : Traiter l'ajout du don (GET/POST)
  - Méthode `distributions()` : Afficher les distributions (global ou filtrées par ville)
  
- **Model :** `app/models/DonModel.php`
  - `createDon($donateur = null)` - Créer un nouveau don
  - `createDonWithArticles($donateur, $articlesForDon)` - Créer don + articles
  - `getAllDons()` - Récupérer tous les dons
  - `getDonById($donId)` - Récupérer un don spécifique
  - `getDonsByDonateur($donateur)` - Récupérer dons par donateur
  - `updateDon($donId, $donateur)` - Mettre à jour infos don
  - `deleteDon($donId)` - Supprimer un don
  - `getArticlesForDon($donId)` - Récupérer articles du don
  - `addArticleToDon($donId, $idArticle, $quantite)` - Ajouter article au don

- **Views :**
  - `app/views/don/ajouter_don.php` - Formulaire d'ajout de don
  - `app/views/don/dons.php` - Liste des dons
  - `app/views/distribution/distributions.php` - Liste des distributions

#### 🔧 Fonctionnalités :
- Créer un don avec :
  - Nom du donateur
  - Articles avec quantités
  - Date automatique
- Organisation des articles par catégories/types
- Validation des données
- Gestion des erreurs et messages de succès
- Transactions & Rollback en cas d'erreur
- Affichage dynamique des articles par catégorie

---

### 6. **Gestion des Distributions**
**Objectif :** Affichage des distributions de dons aux villes (matching besoin/don).

#### 📁 Fichiers Impliqués :
- **Controller :** `app/controllers/DonController.php`
  - Méthode `distributions()` : Afficher distributions (global ou filtrées)
  
- **Model :** `app/models/DonModel.php`
  - Méthodes de jointure complètes pour les distributions
  - Jointures entre `distribution`, `besoin_article`, `besoin`, `ville`, `don_article`, `article`, `don`

- **View :** `app/views/distribution/distributions.php` - Affichage des distributions

#### 🔧 Fonctionnalités :
- Afficher toutes les distributions
- Filtrer par ville (paramètre GET `?ville=id`)
- Afficher pour chaque distribution :
  - Nom de la ville
  - Article distribué
  - Quantité attribuée
  - Date de distribution
  - Nom du donateur
  - Valeur totale (quantité × prix unitaire)
- Jointures complexes multi-tables
- Tri par date de distribution

---

## 🗄️ Architecture Base de Données

### Entités Principales :
1. **ville** - Villes bénéficiaires
2. **region** - Régions
3. **type_besoin** - Types de besoins (Nutrition, Santé, Hygiène, etc.)
4. **article** - Articles disponibles
5. **besoin** - Besoins des villes
6. **besoin_article** - Relation besoin-article (articles constituant un besoin)
7. **don** - Dons reçus
8. **don_article** - Relation don-article (articles constituant un don)
9. **distribution** - Distribution des dons pour couvrir les besoins

---

ville,region,type_besoin,article,besoin,besoin_article,don,don_article,distribution

## 📍 Routes Disponibles

### Dashboard
- `GET /` - Page d'accueil (Dashboard)

### Villes
- `GET /villes/` - Liste des villes
- `GET /villes/ajouter` - Formulaire d'ajout de ville
- `POST /villes/ajouter` - Traitement ajout ville
- `GET /villes/@id/besoins` - Besoins d'une ville

### Articles
- `GET /articles/` - Formulaire d'ajout d'article
- `POST /articles/` - Traitement ajout article

### Besoins
- `GET /besoins/` - Formulaire d'ajout de besoin
- `GET /besoins/@villeId` - Formulaire avec ville pré-remplie
- `POST /besoins/` - Traitement ajout besoin
- `POST /besoins/article` - Créer article via AJAX

### Dons
- `GET /dons/` - Liste des dons
- `GET /dons/ajouter` - Formulaire d'ajout de don
- `POST /dons/ajouter` - Traitement ajout don

### Distributions
- `GET /distributions/` - Liste des distributions (toutes)
- `GET /distributions/?ville=id` - Distributions filtrées par ville

---

## 🎨 Vues Partagées

- `app/views/layout/layout.php` - Layout principal
- `app/views/layout/header.php` - En-tête
- `app/views/layout/footer.php` - Pied de page
- `app/views/function.php` - Fonctions utilitaires

---

## 💾 Fichiers de Configuration

- `app/config/bootstrap.php` - Initialisation
- `app/config/config.php` - Configuration BD
- `app/config/config_sample.php` - Exemple config
- `app/config/services.php` - Services
- `public/index.php` - Point d'entrée

---

## 📦 Dépendances

Framework : **Flight PHP** - Framework MVC léger
Autres : **Nette PHP-Generator**, **Tracy** (debugging)

---

## 📊 Schéma Relationnel Simplifié

```
region (1) ─── (N) ville
           
type_besoin (1) ─── (N) article ─── (N) besoin_article ─── (N) besoin ─── (1) ville

article (1) ─── (N) don_article ─── (N) don

besoin_article (1) ─── (N) distribution ─── (N) don_article
```

---

## ✅ État du Projet

- ✅ Gestion des villes complète
- ✅ Gestion des articles complète  
- ✅ Gestion des besoins avec articles multiples
- ✅ Gestion des dons avec articles multiples
- ✅ Affichage des distributions
- ✅ Transactions / Rollback
- ✅ Validation des données
- ✅ Messages de succès/erreur
- ✅ AJAX pour création article à la volée
- ✅ Filtrage et jointures complexes

---

## 📝 Notes de Développement

1. Les articles peuvent être créés à la volée lors de l'ajout de besoin (AJAX)
2. Les transactions sont utilisées pour garantir l'intégrité des données
3. Les jointures multi-tables permettent l'affichage complet des données
4. Les régions sont des données de référence pré-existantes
5. Les types de besoin sont des données de référence pré-existantes
