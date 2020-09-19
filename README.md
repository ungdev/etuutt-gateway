# Etuutt-gateway

[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

Reçoit toutes les requêtes et les authentifie, puis les passe aux autres applications (etuutt-api, etuutt-files, ...)

## Mettre en route le projet

### Dépendances

* Les extensions gmp et openssl doivent être activées
    * Sous Ubuntu : `sudo apt-get install php7.4-gmp php7.4-openssl`
    * Sous Windows : décommentez les lignes `;ext=gmp` et `;ext=openssl`
    
* Dans le dossier du projet : `composer install`

### Configuration

* Copiez le `.env` en `.env.local`.
    * `ETUUTT_FRONT_BASE_URL` : Indiquez l'URL du front (avec un `/` à la fin)
    * `UNG_LDAP_HOST` : Indiquez le nom de domaine du serveur LDAP de l'UNG (utile seulement si vous avez l'accès VPN)
    * `JWT_PRIVATE_KEY` : Indiquez le contenu obtenu par la commande `php bin/console key:generate:rsa 4096` en l'encadrant de guillemets simples `'`
    * `DATABASE_URL` : vous pouvez indiquer les identifiants d'accès à la base de donnée ainsi que son nom. Pour du dev en local, indiquez des identifiants root (plus pratique pour drop et recréer la db à la volée)
    
* Initier
    * Créer la DB si elle n'existe pas déjà : `php bin/console doctrine:database:create`
    * Lancer les migrations : `php bin/console doctrine:migrations:migrate`
    
* Lancer le serveur : `symfony serve`