# EcoRide - Plateforme de Covoiturage Écologique

Bienvenue sur le dépôt GitHub du projet **EcoRide**, une plateforme de covoiturage conçue pour encourager les déplacements écologiques et économiques.

---

## Table des matières

1. [Objectif du projet](#objectif-du-projet)
2. [Fonctionnalités principales](#fonctionnalités-principales)
3. [Technologies utilisées](#technologies-utilisées)
4. [Prérequis](#prérequis)
5. [Installation en local](#installation-en-local)
6. [Configuration](#configuration)
7. [Usage & Routes principales](#usage--routes-principales)
8. [Comptes de test](#comptes-de-test)
9. [Bonnes pratiques Git](#bonnes-pratiques-git)
10. [Sécurité](#sécurité)
11. [Équipe projet](#équipe-projet)
12. [Déploiement](#déploiement)
13. [Licence](#licence)

---

## Objectif du projet

EcoRide est une startup française dont l’objectif est de réduire l’impact environnemental des déplacements en voiture en favorisant le covoiturage, avec une attention particulière pour les trajets effectués avec des véhicules électriques.

---

## Fonctionnalités principales

- Page d’accueil avec recherche d’itinéraires
- Gestion des comptes (utilisateurs, chauffeurs, passagers)
- Vue des covoiturages disponibles et détail de chaque trajet
- Participation à un covoiturage et gestion des crédits
- Espace personnel pour chauffeurs et passagers
- Saisie et historique des voyages
- Interface dédiée pour employés et administrateurs
- Filtres écologiques (véhicule électrique), prix, durée, note, etc.

---

## Technologies utilisées

### Frontend
- HTML5 / CSS3 (Bootstrap 5)  
- JavaScript (vanilla)

### Backend
- PHP 8.3 (PDO)  
- Gestion d’erreurs centralisée (HTTP 403/404/405/500)

### Bases de données
- MySQL (relationnelle)  
- MongoDB (non relationnelle) pour les réclamations

### Tests & CI
- PHPUnit (tests unitaires, couverture)  
- (À venir) GitHub Actions pour CI/CD

### Versioning & Déploiement
- Versionné avec Git / GitHub  
- Hébergement possible sur Fly.io, Heroku, ou serveur Apache (XAMPP)

---

## Prérequis

- PHP ≥ 8.3 avec extensions `pdo_mysql`, `mbstring`  
- MySQL  
- MongoDB  
- Composer  
- Git  

---

## Installation en local

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/TON-COMPTE/EcoRide.git
   cd EcoRide
   ```
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Importer la base de données :
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ecoride;"
   mysql -u root -p ecoride < ecoride.sql
   ```
4. Lancer le serveur web :
   - **PHP intégré** :
     ```bash
     php -S ecoride.local:80 -t public public/index.php
     ```
   - **Apache (XAMPP/vHost)** : configure un VirtualHost pointant sur `public/`

Ouvre ensuite [http://ecoride.local](http://ecoride.local) dans ton navigateur.

---

## Configuration

Crée un fichier `.env` à la racine du projet (ou copie `.env.example`) :

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=ecoride
DB_USER=root
DB_PASS=
MONGODB_URI=mongodb://localhost:27017
MONGODB_DB_NAME=avisDB
# Autres variables (mail, clé API, etc.)
```

---

## Usage & Routes principales

| Route                 | Description                              |
|-----------------------|------------------------------------------|
| `/`, `/index`         | Accueil                                  |
| `/login`              | Page de connexion                        |
| `/registerForm`       | Formulaire d’inscription                 |
| `/covoiturageForm`    | Créer un nouveau covoiturage             |
| `/covoiturage`        | Rechercher un covoiturage                |
| `/detail-covoiturage` | Détail d’un covoiturage                  |
| `/delete-covoiturage` | Supprimer un covoiturage                 |
| `/utilisateur`        | Espace personnel                         |
| `/stats`              | Statistiques & graphiques                |
| …                     | (voir `public/index.php` pour la liste)  |

---

## Comptes de test

| Rôle           | Email               | Mot de passe    |
|----------------|---------------------|-----------------|
| Utilisateur    | user@test.com       | Test1234!       |
| Employé        | employe@test.com    | Test1234!       |
| Administrateur | admin@test.com      | Admin1234!      |

---

## Bonnes pratiques Git

- Branche `main` : version stable  
- Branche `develop` : intégration continue  
- Créer une branche feature/… pour chaque nouvelle fonctionnalité  
- Merges : feature → develop → main (après validation)

---

## Sécurité

- Mots de passe hachés avec bcrypt  
- Vérification des rôles et permissions  
- Protection CSRF centralisée  
- Modération des avis et double confirmation pour l’usage des crédits

---

## Équipe projet

- **Développeur principal** : CADILHAC E.  
- **Directeur Technique** : José  
- **Contact** : contact@ecoride.fr

---

## Déploiement

- Application en ligne : https://ecoride.fly.io  
- (ou configure sur Heroku / serveur dédié)

---

## Licence

Ce projet est distribué sous licence **MIT**. Voir le fichier [LICENSE](LICENSE) pour plus d’informations.

