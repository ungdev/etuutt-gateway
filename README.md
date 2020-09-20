# Etuutt-gateway

[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

Reçoit toutes les requêtes et les authentifie, puis les passe aux autres applications (etuutt-api, etuutt-files, ...)

## Mettre en route le projet

### Dépendances

* Les extensions gmp et openssl doivent être activées
    * Sous Ubuntu : `sudo apt-get install php7.4-gmp php7.4-openssl`
    * Sous Windows : décommentez les lignes `;ext=gmp` et `;ext=openssl`
    
* Dans le dossier du projet : `composer install`

### Base de donnée

Il faut créer un utilisateur et une base de donnée correspondant à notre projet.

```
sudo mysql
create database etuutt-gateway;
CREATE USER 'etuutt-gateway'@'127.0.0.1' IDENTIFIED BY 'TheDbPassword';
GRANT ALL PRIVILEGES ON etuutt-gateway.* TO 'etuutt-gateway'@'127.0.0.1';
FLUSH PRIVILEGES;
```

### Configuration

* Copiez le `.env` en `.env.local`.
    * `ETUUTT_FRONT_BASE_URL` : Indiquez l'URL du front (avec un `/` à la fin)
    * `UNG_LDAP_HOST` : Indiquez le nom de domaine du serveur LDAP de l'UNG (utile seulement si vous avez l'accès VPN)
    * `JWT_PRIVATE_KEY` : Indiquez le contenu obtenu par la commande `php bin/console key:generate:rsa 4096` en l'encadrant de guillemets simples `'`
    * `DATABASE_URL` : vous pouvez indiquer les identifiants d'accès à la base de donnée ainsi que son nom.
    
* Lancer les migrations : `php bin/console doctrine:migrations:migrate`
    
* Lancer le serveur : `symfony serve`