<?php 

namespace App;

/**
 * Classe View (Facade) pour le rendu des vues et la redirection
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class View
{
	private static $twig = null;

	/**
	 * Fonction de rendu du film
	 * @param  string $view  Vue à afficher
	 * @param  array $datas  Paramètres à passer à la vue
	 * @return twigView      Vue à afficher
	 */
    static function make($view, $datas = null) {
    	if(self::$twig == null) {
    		self::$twig = new Twig();
    	}
    	echo self::$twig->render($view, $datas);
    }

    /**
     * Fonction de redirection vers la route choisie
     * @param  string $route Route ou rediriger l'utilisateur
     */
    static function redirect($route) {
    	$url = 'http://localhost/Oxford/public';
    	$url = $url.$route;
    	header('Location: '.$url);
    } 
}

?>