<?php 

namespace App\Utils;
use App\Database;

/**
 * Classe utilitaire pour l'ajout rapide d'événements
 */
class EventLogger
{   
    /**
     * Ajout d'un événement de type 'erreur'
     * @param  int $user  Identifiant de l'utilisateur à attribuer à l'évenement
     * @param  string $value Message de l'évenement
     */
    static function error($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 1, $value);
    }

    /**
     * Ajout d'un événement de type 'succès'
     * @param  int $user  Identifiant de l'utilisateur à attribuer à l'évenement
     * @param  string $value Message de l'évenement
     */
    static function success($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 4, $value);
    }

    /**
     * Ajout d'un événement de type 'admin'
     * @param  int $user  Identifiant de l'utilisateur à attribuer à l'évenement
     * @param  string $value Message de l'évenement
     */
    static function admin($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 3, $value);
    }

    /**
     * Ajout d'un événement de type 'info'
     * @param  int $user  Identifiant de l'utilisateur à attribuer à l'évenement
     * @param  string $value Message de l'évenement
     */
    static function info($user, $value) {
    	$req = Database::getInstance()->newEvent($user, 2, $value);
    }
}
