<?php 

namespace App\Utils;
use App\Database;

/**
 * Classe utilitaire pour l'ajout rapide d'événements
 */
class EventLogger
{
    static function error($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 1, $value);
    }

    static function success($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 4, $value);
    }

    static function admin($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 3, $value);
    }

    static function info($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 2, $value);
    }
}
