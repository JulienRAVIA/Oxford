<?php 

namespace App;
use App\Twig as Twig;

class View
{
	private static $twig = null;

    static function make($view, $datas = null) {
    	if(self::$twig == null) {
    		self::$twig = new Twig();
    	}
    	echo self::$twig->render($view, $datas);
    }
}

?>