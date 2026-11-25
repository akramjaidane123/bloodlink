# BloodLink – Blood Donation Platform

> **Relie les donneurs. Sauve des vies.**

## Table des matières

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Contributors](#contributors)
- [Getting Started](#getting-started)
- [Sécurité](#sécurité)
- [Contribution](#contribution)
- [License](#license)

## Overview

Projet réalisé dans le cadre du **Projet Technologies Web (PW) – 2ème année Ingénieur** à **Esprit School of Engineering** (année universitaire 2026–2027).

BloodLink est une plateforme web nationale de mise en relation entre **donneurs de sang** et **centres de collecte autorisés** en Tunisie : les centres publient leurs besoins et leurs campagnes, les donneurs consultent, réservent un don et reçoivent des notifications.

## Features

| Espace | Fonctionnalités |
|---|---|
| **Visiteur / Donneur** (Front Office) | besoins en sang filtrables par groupe et gouvernorat (sans rechargement grâce à `fetch`), campagnes, centres (lien carte), actualités, commentaires, réservation d'un don, espace personnel (réservations, notifications) |
| **Centre** (Back Office) | publier les besoins, créer des campagnes, gérer les réservations **de son centre** |
| **Administrateur** (Back Office) | valider les centres, gérer gouvernorats, sponsors et contrats, articles et catégories, modération des commentaires, alertes, comptes |

Back Office : tableau de bord (chiffres clés et graphiques), tables avec **recherche, tri et pagination**, CRUD complet de chaque entité, rôles et droits (admin / centre).

## Tech Stack

- **HTML5** sémantique, **CSS3** (Flexbox et Grid), **JavaScript ES6** (modules, DOM, événements, `fetch` / `async-await`)
- **PHP 8.1+** orienté objet, architecture **MVC** du cours
- **MySQL / MariaDB** via **PDO** (requêtes préparées, jointures `INNER JOIN` / `LEFT JOIN`)

## Architecture

Architecture **MVC** exactement comme dans le cours *PHP – PDO – CRUD* (chapitre 6) : `config.php` + `Models/` + `Controllers/` + `Views/`, une table = un modèle = un contrôleur.

```
bloodlink/
├── config.php                 # classe config : connexion PDO (Singleton)          [Chap. 6, § 4.2]
├── init.php                   # amorçage : session sécurisée, en-têtes, chargement du noyau
├── helpers.php                # e(), url(), csrf_field(), flash(), badge()...
├── index.php                  # redirige vers Views/front/accueil.php
├── Models/                    # entités : propriétés privées + getters/setters (aucun SQL, aucun HTML)
│   └── Centre.php, Need.php, Campaign.php, Article.php, Reservation.php, User.php ... (12 classes)
├── Controllers/               # classes XxxC : CRUD avec PDO (add / liste / get / update / delete / valider)
│   └── CentreC.php, NeedC.php, ... AuthC.php (sessions, rôles, CSRF), DashboardC.php (statistiques)
├── Views/
│   ├── front/                 # Front Office : accueil, besoins, campagnes, centres, actualités,
│   │   │                      #   article, réserver, mon_compte, connexion, inscription...
│   │   └── partials/          # head, navbar, footer, scripts, cartes
│   └── back/                  # Back Office
│       ├── tableau_de_bord.php
│       ├── partials/          # head, sidebar, header, footer, scripts, debut, fin
│       └── centres/ besoins/ campagnes/ ...   # par entité : liste_x.php, ajouter_x.php,
│                                              #   modifier_x.php, supprimer_x.php, _form_x.php
├── assets/
│   ├── css/                   # style.css (front), admin.css (back)
│   ├── js/                    # app.js + modules/ (menu, validation, tableau, besoins, confirmation)
│   └── img/
├── database/bloodlink.sql     # schéma + données de démonstration
├── docs/                      # architecture, conception, sécurité
└── .github/                   # modèles d'issue et de pull request
```

Le cycle d'une requête suit le cours : la **Vue** reçoit la requête (clic, formulaire) → appelle le **Contrôleur** (`CentreC`) → qui exécute le CRUD via **PDO** sur le **Modèle** (`Centre`) → la Vue affiche le HTML. Le Contrôleur ne fait aucun affichage, la Vue n'appelle jamais PDO.

Détails et correspondance avec les chapitres du cours : [docs/architecture.md](docs/architecture.md) · conception : [docs/conception.md](docs/conception.md).

## Contributors

**Équipe Nexora – classe 2A11**

Mohamed Kassas · Mohamed Ben Zekri · Akrem Jaidane · Aziz Riahi · Aymen Naffeti · Wael Bali

| Module | Responsable | Dossiers (Back Office) |
|---|---|---|
| 1. Campagnes et sponsors | Mohamed Kassas | `Views/back/campagnes` |
| 2. Centres et gouvernorats | Mohamed Ben Zekri | `Views/back/centres`, `gouvernorats`, `besoins` |
| 3. Sponsors et contrats | Akrem Jaidane | `Views/back/sponsors`, `contrats` |
| 4. Articles et commentaires | Aziz Riahi | `Views/back/articles`, `commentaires` |
| 5. Centres et réservations | Aymen Naffeti | `Views/back/reservations` |
| 6. Notifications et actualités | Wael Bali | `Views/back/notifications`, `categories` |

Tuteurs : Imen El Hakim · Chedy Bouslema

## Getting Started

### Prérequis
- PHP 8.1 ou plus (extensions `pdo_mysql` et `mbstring`, activées par défaut dans XAMPP / WAMP) et MySQL / MariaDB.

### Installation

1. Cloner le dépôt :
   ```bash
   git clone https://github.com/akramjaidane123/bloodlink.git
   cd bloodlink
   ```
2. Démarrer MySQL et importer `database/bloodlink.sql` (phpMyAdmin > Importer). Si une ancienne base `bloodlink` existe, la supprimer d'abord.
3. *(optionnel)* si votre MySQL n'est pas `root` sans mot de passe : copier `config.local.example.php` en `config.local.php` et adapter (fichier ignoré par Git).
4. Lancer le site :
   - **XAMPP / WAMP** : placer le dossier `bloodlink` dans `htdocs` / `www`, puis ouvrir http://localhost/bloodlink/
   - **Serveur intégré PHP** : `php -S localhost:8000` puis ouvrir http://localhost:8000/

> Le JavaScript utilise des modules ES6 : le site doit être ouvert via `http://` (jamais en double-cliquant sur un fichier `file://`).

### Comptes de démonstration (à changer avant toute mise en ligne)

| Rôle | E-mail | Mot de passe | Arrivée après connexion |
|---|---|---|---|
| Administrateur | `admin@bloodlink.tn` | `Admin@1234` | `Views/back/tableau_de_bord.php` |
| Centre (Tunis) | `centre@bloodlink.tn` | `Centre@1234` | `Views/back/tableau_de_bord.php` |
| Donneur | `donneur@bloodlink.tn` | `Donneur@1234` | `Views/front/mon_compte.php` |

## Sécurité

Requêtes préparées PDO, mots de passe hachés (`password_hash`), jetons CSRF, sessions sécurisées (expiration 30 min), rôles vérifiés côté serveur (403), échappement des sorties (`e()`), en-têtes HTTP et CSP. Détails et tests : [docs/securite.md](docs/securite.md).

## Contribution

Conventions de branches, commits et pull requests : [CONTRIBUTING.md](CONTRIBUTING.md).

## License

Distribué sous licence [MIT](LICENSE).
