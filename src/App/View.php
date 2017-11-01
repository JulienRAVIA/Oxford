<?php 

namespace App;
use App\Twig as Twig;

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
}

?>