# Oxford

Interface d'administration du projet Oxford

## Installation

Installer les composants: Executez la commande ``composer install`` dans l'invite de commande à la racine du dossier

Changer les identifiants de base de données: Dans le fichier ``src\App\Database.php`` changer les valeurs des variables de la classe

## Démarrage

Il faut placer le projet dans un serveur local

## Tests unitaires

Lancer la commande suivante : ``phpunit --bootstrap src/Class.php tests/ClassTest`` pour lancer un test sur une classe en particulier (sans détails)

Lancer la commande suivante : ``phpunit --bootstrap src/Class.php --testdox tests/ClassTest`` pour lancer un test sur une classe en particulier (avec détails)

## Documentation

Lancer la mise à jour/regénération de la documentation avec la commande suivante (à la racine du projet) : 
``vendor\bin\apigen generate src --destination docs``

## Composants

* [metro-bootstrap](http://talkslab.github.io/metro-bootstrap/) - Version Metro de Bootstrap pour la charte graphique du projet
* [Twig](https://twig.symfony.com/doc/2.x/) - Moteur de template
* [PHPUnit](https://phpunit.de) - Système de tests unitaires pour PHP
* [date](https://github.com/jenssegers/date) - Librairie pour la gestion des dates
* [AltoRouter](http://altorouter.com) - Système de routeur pour PHP
* [ApiGen](https://github.com/ApiGen/ApiGen) - Génération de la documentation (PHP 7.1)
