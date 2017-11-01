<?php 

namespace App;

/**
 * Utilitaire pour les tableaux
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class ArrayUtils
{

	/**
	 * Fonction de copie d'un tableau pour éviter 
	 * la recopie des variables dans les requêtes		
	 * @param  array $array         Tableau à copier et filtrer
	 * @param  array $keysToExclude Clés de tableau à supprimer du tableau
	 * @return array               	Tableau sans les clés que l'on à choisi d'exclure
	 */
    static function copyAndExclude(array $array, array $keysToExclude) {
    	foreach ($keysToExclude as $exclude) {
    		unset($array[$exclude]);
    	}
    	foreach ($array as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }
}
