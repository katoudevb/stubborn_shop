# stubborn_shop
# Installation

- Cloner le repository :

git clone <lien-du-repo-github>
cd stubborn-symfony


- Installer les dépendances avec Composer :

composer install


- Configurer l’environnement :
Copier le fichier .env et adapter la connexion MySQL :

cp .env .env.local

# Modifier DATABASE_URL selon votre configuration MySQL
- Créer la base de données et charger les fixtures :

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load


-Lancer le serveur Symfony :

symfony server:start

# Configuration

Base de données : MySQL

Authentification : Symfony Security Bundle, avec rôles ROLE_USER et ROLE_ADMIN.

user :root
mp : (vide)
nom bdd: stubborn_db

Paiement : Stripe (mode développement)

Envoi d’e-mails : Symfony Mailer (pour la confirmation d’inscription)

- Routes principales :

/ : Page d’accueil

/login : Connexion

/register : Inscription

/products : Liste des produits

/product/{id} : Détail d’un produit

/cart : Panier

/admin : Back-office (administrateurs uniquement)

# Fonctionnalités
- Page d’accueil /

Menu dynamique selon l’état de connexion

Non connecté : Accueil / S’inscrire / Se connecter

Connecté : Accueil / Boutique / Panier / Se déconnecter

Présentation de la société

Produits mis en avant (3 sweat-shirts)

- Authentification

Formulaire de connexion /login

Formulaire d’inscription /register avec confirmation par e-mail

Gestion des rôles USER et ADMIN

- Gestion des produits

Liste des produits /products avec filtre par prix

Page produit /product/{id} avec sélection de taille et ajout au panier

Panier /cart avec calcul automatique du total, suppression d’articles et validation de la commande via Stripe

Back-office /admin pour ajouter, modifier, supprimer des produits (admin seulement)

# Architecture

- Symfony 7

## Bundles utilisés :

SecurityBundle pour l’authentification

DoctrineBundle pour ORM

Twig pour les templates

Symfony Mailer pour envoi d’e-mails

Stripe PHP SDK ou bundle compatible pour paiement

## Organisation du code :

Controller/ : contrôleurs

Entity/ : entités Doctrine

Repository/ : gestion des requêtes

Service/ : service Stripe

Form/ : formulaires

templates/ : vues Twig

tests/ : tests unitaires

# Tests

- Tests unitaires pour :

Ajout d’un produit au panier

Validation d’une commande (Stripe en mode développement)

php bin/phpunit
