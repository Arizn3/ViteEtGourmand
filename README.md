# Vite & Gourmand
## Présentation

Vite & Gourmand est une application web de restauration et de livraison permettant aux utilisateurs de consulter des menus, et une fois inscrit de passer des commandes et gérer leurs livraisons en ligne.
L’application intègre différents rôles du simple utilisateur au rôle employé et administrateur qui assure la gestion des menus, des commandes, des livraisons et du suivi global de l’activité.

## Fonctionnalités principales

- Inscprition, authentification et gestion automatique des accès par rôles via Symfony
- CRUD des menus par les rôles employé et administrateur
- Système de commandes disponible uniquement en Gironde
- Gestion du statut des commandes et des avis utilisateurs par les rôles employé et administrateur
- Tableau de bord administrateur avec statistiques et création de comptes employés

## Fonctionnalités secondaires

- Système d’envoi automatique d’e-mails lors d’une inscription ou d’un changement de statut d’une commande, etc.
- Réinitialisation sécurisée du mot de passe
- Prise de contact rapide avec l’administrateur via un formulaire de contact
- Système d’avis utilisateurs limité et contrôlé

## Technologies utilisées
### Backend
- PHP 8.3-fpm-alpine
- Symfony 7.4.7 (LTS)

### Frontend
- HTML
- CSS
- JavaScript
- Twig

### Base de données
- MySQL 8.0
- MongoDB 7.0 (La BDD MongoDB est utiliser exclusivement pour les statistiques disponible dans l'application)

### Outils & services

- Docker 4.73.0 (conteneurisation de l’application)
- Nginx 1.26-alpine (serveur web)
- OpenStreetMap API (sélection et validation des villes de livraison en Gironde)
- Chart.js (affichage des statistiques administrateur)

## Méthodes de traitement des données

- Soft Delete via `deleted_at`
- Traçabilité des données via `created_at`

Mécanismes mis en place afin d’assurer une meilleure traçabilité des données et de respecter les bonnes pratiques de gestion et de conservation des informations.

## Guide d'installation rapide
### Prérequis
> [!IMPORTANT]
> Avant de lancer le projet, assurez-vous d’avoir installé les outils suivants :

- Docker
- Docker Compose
- Git


Le projet utilise une architecture conteneurisée avec Docker afin de simplifier l’installation et le lancement des différents services comme les bases de données.<br>
Aucune installation locale de PHP, MySQL ou Symfony n’est nécessaire. Tous les services sont exécutés via Docker.

### Installation

Une base de données MySQL préconfigurée est automatiquement importée lors du premier démarrage des conteneurs.<br>
Celle-ci contient un compte administrateur de démonstration permettant un accès immédiat à l’application.<br>
Ce compte Administrateur est configurable par la suite, les identifiants sont disponibles dans la documentation.

### Installation Demo *(Par défaut)*

> [!TIP]
> L’installation Demo est utilisée par défaut afin de faciliter les tests et la démonstration du projet grâce à des données factices intégrées.

Après avoir cloné le dépôt, lancer la construction des conteneurs :

```bash
docker compose up --build -d
```

Récupérer les données démo MongoDB pour le graphique administrateur :<br>

```Bash
docker cp ./database/mongodb-dump app-mongodb-1:/mongodb-dump
```
Puis :
```bash
docker exec -i app-mongodb-1 mongorestore \
-u symfony \
-p change_me_mongo \
--authenticationDatabase admin \
--db vitegourmand \
/dump/vitegourmand
```

Une fois les conteneurs démarrés, l’application sera accessible à l’adresse suivante :

```txt
http://localhost:8080
```

### Installation Clean

Pour démarrer l’application avec une base de données vide, modifier la ligne 50 et 51 du fichier `docker-compose.yml` :

```yaml
# ./database/vitegourmandDBTest.sql:/docker-entrypoint-initdb.d/init.sql
./database/vitegourmandDBClean.sql:/docker-entrypoint-initdb.d/init.sql
```

Si les conteneurs n'ont pas encore été démarrés :

```bash
docker compose up --build -d
```

Sinon, supprimer les volumes Docker existants avant de redémarrer :

```bash
docker compose down -v
docker compose up -d
```

Plus de détail dans le manuel d'utilisation, notamment pour les identifiants.

## Variables d'environnement

Se référer au fichier doc-technique.pdf dans le dossier documentation.

## Documentation

Retrouvez l’ensemble de la documentation du projet dans le dossier `documentation` situé à la racine du dépôt.

- La charte graphique de l’application
- Le manuel d’utilisation avec les identifiants de démonstration (`Demo` et `Clear`)
- Une documentation technique

## Version

Version 1.0 — 17/05/2026.

## Mise à jour à venir :

- [ ] Amélioration du style globale
- [ ] Ajout d'une fonctionnalité pour la création ou la suppression des allergènes
- [ ] Ajout d'un nouveau graphique pour l'administrateur

## Auteur

Lucas Cappello