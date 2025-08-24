#!/bin/bash

# Exécute les tests avant de lancer le serveur
echo "=== Lancement des tests ==="
php bin/console app:run-tests

# Démarre le serveur Symfony
echo "=== Démarrage du serveur Symfony ==="
symfony server:start
