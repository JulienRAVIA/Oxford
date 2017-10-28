<?php 

namespace App;

use \Twig_Loader_Filesystem as Twig_Loader_Filesystem;
use \Twig_Environment as Twig_Environment;

class Twig
{
    private $_twig;
    private $_loader;

    public function __construct()
    {
        $this->_loader = new Twig_Loader_Filesystem('..\src\views');
		$this->_twig = new Twig_Environment($this->_loader, array(
    		'cache' => false,
            'debug' => true
		));
        $this->_twig->addExtension(new \Twig_Extension_Debug());
		return $this->_twig;
    }

    public function render($view, $array = array())
    {
    	if(empty($array)) {
    		echo $this->_twig->render($view);
    	} else {
    		echo $this->_twig->render($view, $array);
    	}
    }
}

?>