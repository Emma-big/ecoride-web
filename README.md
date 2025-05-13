# EcoRide

\\

EcoRide est une application collaborative de covoiturage écologique via des voitures électriques permettant aux utilisateurs de rechercher, réserver et proposer des trajets facilement. Le front-end est construit en PHP (templates HTML/CSS/JavaScript), et le back-end en PHP-FPM avec une API REST. Les données principales sont stockées dans une base SQL et une base noSQL (MongoDB).

---

## Table des matières

1. [Description du projet](#description-du-projet)
2. [Prérequis](#prérequis)
3. [Installation](#installation)
4. [Usage en local](#usage-en-local)
5. [Documentation](#documentation)
6. [Stratégie de branches Git](#stratégie-de-branches-git)
7. [CI/CD](#cicd)
8. [Déploiement](#déploiement)

---

## Description du projet

EcoRide offre une plateforme intuitive pour gérer les trajets en covoiturage avec des voitures électriques. L’interface utilisateur repose sur des templates PHP générant du HTML/CSS/JavaScript, et le back-end en PHP-FPM expose une API REST.
L'environnement local est réalisé avec une instance MongoDB locale et une base MySQL locale (accessible via phpMyAdmin).
L'environnement de développement et production est réalisé avec une base MySQL gérée et cluster MongoDB Atlas.

## Prérequis

* **PHP >= 8.2** avec l’extension MongoDB et PDO MySQL
* **Composer** pour gérer les dépendances PHP
* **Git** pour cloner le dépôt
* **Environnement local** :
  * **MongoDB** (instance locale)
  * **phpMyAdmin** (pour accéder à la base SQL locale)

* **Environnement de développement/production** :
  * **MySQL** (base SQL gérée)
  * **MongoDB Atlas** (cluster configuré)

## Installation

1. Clonez le dépôt :

   ```bash
   git clone https://github.com/Emma-big/ecoride-web.git
   cd ecoride-web
   ```
2. Installez les dépendances PHP :

   ```bash
   composer install
   ```
3. Préparez vos variables d'environnement :

   ```bash
   cp .env.example .env
   ```

   Puis éditez `.env` :

   ```dotenv
   # MongoDB Atlas (prod / dev)
   MONGODB_URI="mongodb+srv://<user>:<pass>@cluster0.mongodb.net/ecoride?retryWrites=true&w=majority"
   MONGODB_DB_NAME="ecoride"

   # SQL local (phpMyAdmin)
   DB_HOST="127.0.0.1"
   DB_PORT="3306"
   DB_NAME="ecoride"
   DB_USER="root"
   DB_PASS="root"
   ```

## Usage en local

1. **Démarrer MongoDB** (instance locale sur le port 27017)
2. **Accéder à phpMyAdmin** : ouvrez `http://localhost:8080` et connectez-vous avec `root/root` sur la base `ecoride`.
3. **Lancer l’API PHP** :

   ```bash
   php -S localhost:8000 -t public
   ```
4. **Ouvrir l’application** : accédez à `http://localhost:8000` dans votre navigateur.
5. **Exécuter les tests unitaires** (sur la base SQL locale) :

   ```bash
   composer test
   ```

## Documentation

Tous les guides et chartes sont disponibles au format PDF :

* [Charte graphique](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/charte_graphique.pdf)
* [Manuel d’utilisation](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/manuel_utilisation.pdf)
* [Documentation gestion de projet](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/gestion_projet.pdf)
* [Documentation technique](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/documentation_technique.pdf)

## Stratégie de branches Git

Nous utilisons un workflow Gitflow simplifié :

* **main** : branche stable en production.
* **develop** : intégration des fonctionnalités validées.
* **feature/**\*\* : branches de fonctionnalités créées depuis `develop`.

**Cycle de vie** :

1. Créez `feature/xxx` depuis `develop`.
2. Développez et committez.
3. Ouvrez une PR `feature/xxx` → `develop`, passez la CI/tests puis mergez.
4. Mergez `develop` → `main` pour la production.

## CI/CD

Le pipeline GitHub Actions comprend :

1. Analyse statique (PHP\_CodeSniffer, PHPStan).
2. Tests unitaires PHPUnit et génération de la couverture.
3. Publication de la couverture sur Codecov.
4. Déploiement automatique sur Heroku via la CLI intégrée.

Voir [`.github/workflows/ci.yml`](.github/workflows/ci.yml) pour les détails.

## Déploiement

En développement et production, l’application tourne sur des dynos Heroku connectés à une base MySQL gérée et à MongoDB Atlas pour la persistance des données. Cela assure l’hébergement managé, la mise à l’échelle automatique et la gestion SSL transparente.
