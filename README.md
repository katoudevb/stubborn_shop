## stubborn_shop
# Installation :
1. git clone https://github.com/katoudevb/stubborn_shop
2. composer install && npm install && npm run build

# Configurer .env (DB, MAILER, STRIPE keys)
4. php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load

5. symfony serve:start

# Exécution des tests :
php bin/phpunit