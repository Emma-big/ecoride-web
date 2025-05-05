# EcoRide

[![Build Status](https://github.com/Emma-big/ecoride-web/actions/workflows/ci.yml/badge.svg)](https://github.com/Emma-big/ecoride-web/actions/workflows/ci.yml)  
[![Coverage Status](https://codecov.io/gh/Emma-big/ecoride-web/branch/main/graph/badge.svg)](https://codecov.io/gh/Emma-big/ecoride-web)




## EcoRide

EcoRide est une application collaborative de covoiturage écologique via des voitures électriques permettant aux utilisateurs de rechercher, réserver et proposer des trajets facilement.

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

EcoRide offre une plateforme intuitive pour gérer les trajets en covoiturage avec des voitures électriques. Le front-end est développé en JavaScript/React, et le back-end en PHP-FPM avec une API REST et MySQL.

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

* L'application est ensuite accessible sur [http://ecoride.local](http://ecoride.local) (pensez à configurer votre fichier hosts ou DNS localement).

## Documentation

Tous les guides et chartes sont disponibles au format PDF via les liens suivants hébergés en production :

* [CHARTE GRAPHIQUE](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/charte_graphique.pdf)
* [MANUEL D’UTILISATION](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/manuel_utilisation.pdf)
* [DOCUMENTATION GESTION DE PROJET](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/gestion_projet.pdf)
* [DOCUMENTATION TECHNIQUE](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/documentation_technique.pdf)

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

1. Analyse statique du code (PHP_CodeSniffer, PHPStan).  
2. Exécution des tests unitaires PHPUnit (`composer test`) et génération de la couverture.  
3. Publication de la couverture sur Codecov.  
4. Déploiement automatique sur Heroku via la Heroku CLI intégrée au workflow.

Voir le [fichier de workflow](.github/workflows/ci.yml) pour tous les détails.

## Déploiement

En production, tant le front-end que le back-end sont déployés sur des dynos Heroku. Cette configuration assure un hébergement entièrement managé, la mise à l’échelle automatique des instances et la gestion transparente des certificats SSL.
