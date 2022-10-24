# BilemoOC
***

A list of technologies used within the project:
* PHP(https://www.php.net) Version 8.1.10
* Symfony(https://symfony.com) Version 6.1
* Composer(https://getcomposer.org) Version 2.4.2
* Doctrine-bundle (https://symfony.com/doc/2.7/doctrine.html) Version 2.7
* lexik/jwt-authentication-bundle (https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html) Version 2.16
* nelmio/api-doc-bundle (https://symfony.com/bundles/NelmioApiDocBundle/current/index.html) Version 4.10


## installation guide

```shell
$ git clone https://github.com/ghislainvava/SnowTricks_OC.git
$ composer install
$ create an `.env.local` (from `.env`) file and write the necessary information to the database
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ php bin/console doctrine:fixtures:load
$ symfony server:start
```
	
