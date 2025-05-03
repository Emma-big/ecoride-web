## EcoRide

EcoRide est une application collaborative de covoiturage écologique via des voitures électriques plus particulièrement permettant aux utilisateurs de rechercher, réserver et proposer des trajets facilement.

---

## Table des matières

1. [Description du projet](#description-du-projet)
2. [Prérequis](#prérequis)
3. [Installation](#installation)
4. [Usage en local](#usage-en-local)
5. [Documentation](#documentation)
6. [Stratégie de branches Git](#stratégie-de-branches-git)
7. [Docker & Environnement](#docker--environnement)
8. [CI/CD](#cicd)
9. [Déploiement](#déploiement)

---

## Description du projet

EcoRide offre une plateforme intuitive pour gérer les trajets en covoiturage avec des voitures électriques à privilégier. Le front-end est développé en JavaScript/React, et le back-end en PHP-FPM avec une API REST et MySQL.

## Prérequis

* PHP >= 8.2
* Composer
* Node.js & npm
* Docker & Docker Compose (pour le développement)
* Git

## Installation

1. Clonez le dépôt :

   ```bash
   git clone https://github.com/Emma-big/ecoride-web.git
   cd EcoRide
   ```
2. Installez les dépendances PHP :

   ```bash
   composer install
   ```
3. Installez les dépendances JavaScript :

   ```bash
   npm install
   ```
4. Préparez vos variables d'environnement :

   ```bash
   cp .env.example .env
   
   ```

## Usage en local

Lancez l'application avec Docker Compose :

```bash
docker-compose up --build -d
```

* L'application est ensuite accessible sur l'URL http://ecoride.local (pensez à configurer votre fichier hosts ou DNS localement).


## Documentation

Tous les guides et chartes sont disponibles au format PDF dans le dossier `DOCUMENTS PDF/` :

* CHARTE GRAPHIQUE
* MANUEL D’UTILISATION
* DOCUMENTATION GESTION DE PROJET
* DOCUMENTATION TECHNIQUE

## Stratégie de branches Git

Nous utilisons un workflow Gitflow simplifié :

* **main** : branche principale, version stable déployée en production.
* **develop** : branche de développement, rassemble les fonctionnalités validées.
* **feature/**\* : branches de fonctionnalités issues de `develop` pour chaque nouvelle tâche.

**Cycle de vie** :

1. Créez `feature/xxx` depuis `develop`.
2. Travaillez et committez vos modifications.
3. Ouvrez une PR `feature/xxx` → `develop`, faites passer les CI/tests, puis mergez.
4. Une fois toutes les fonctionnalités validées, mergez `develop` → `main` pour la production.

## Docker & Environnement

Le projet intègre un `Dockerfile` multi-stage et un `docker-compose.yml` :

* **Dockerfile** : image PHP-FPM optimisée pour l’API.
* **docker-compose.yml** : orchestre l’API, la base MySQL et Traefik en reverse-proxy.

## CI/CD

Le pipeline CI/CD est entièrement géré par GitHub Actions :

1. Analyse statique du code avec PHP\_CodeSniffer (PSR-12) et PHPStan
2. Exécution des tests unitaires PHPUnit avec génération du rapport de couverture
3. Construction d’une image Docker multi-stage
4. Publication des artefacts (`coverage.xml`) et de l’image sur GitHub Container Registry
5. Déploiement automatique sur Heroku via la Heroku CLI intégrée au workflow

Des badges de statut et de couverture sont ajoutés en tête du README.

## Déploiement

En production, tant le front-end que le back-end sont déployés sur des dynos Heroku. Cette configuration assure un hébergement entièrement managé, la mise à l’échelle automatique des instances et la gestion transparente des certificats SSL.
