# EcoRide

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

EcoRide offre une plateforme intuitive pour gérer les trajets en covoiturage avec des voitures électriques.  
L’interface utilisateur repose sur des templates PHP générant du HTML/CSS/JavaScript, et le back-end en PHP-FPM expose une API REST.

Localement, tout tourne dans des conteneurs Docker (PHP-Apache, MySQL, MongoDB).  
En dev/prod, on utilise MySQL géré et un cluster MongoDB Atlas.

---

## Prérequis

- **Docker** (version 20+)  
- **Docker Compose** (version 1.27+)  

---

## Installation

1. **Cloner le dépôt**  
   ```bash
   git clone https://github.com/Emma-big/ecoride-web.git
   cd ecoride-web

2. **Préparer les variables d’environnement**

   cp .env.example .env

3. **Construire et démarrer les conteneurs**

   docker-compose build --no-cache
   docker-compose up -d

4. **Vérifier que tout est “Up”**

   docker-compose ps

## Usage en local

Application :
Ouvrez http://localhost:8080 dans votre navigateur.

MySQL :

Hôte : 127.0.0.1

Port : celui défini dans DB_PORT (3306 par défaut)

Utilisateur : celui défini dans DB_USER (root par défaut)

Mot de passe : vide (si MYSQL_ALLOW_EMPTY_PASSWORD=yes)

MongoDB :

Hôte : 127.0.0.1

Port : 27017

Arrêter et nettoyer : docker-compose down --volumes

## Documentation

Tous les guides et chartes sont disponibles au format PDF :

* [Charte graphique](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/charte_graphique.pdf)
* [Manuel d’utilisation](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/manuel_utilisation.pdf)
* [Documentation gestion de projet](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/gestion_projet.pdf)
* [Documentation technique](https://ecoride-web-2fb86cbe3fd4.herokuapp.com/assets/documents/documentation_technique.pdf)

## Stratégie de branches Git

Nous utilisons un workflow Gitflow simplifié :

* main : branche stable en production.
* develop** : intégration des fonctionnalités validées.
* feature/** : branches de fonctionnalités créées depuis `develop`.

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
