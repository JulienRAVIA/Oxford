<?php 

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;
use App\Utils\ErrorLogger;

/**
 * summary
 */
class ConnexionController {

	/**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

    /**
     * Page d'accueil du formulaire de connexion
     * @return [type] [description]
     */
	public function index()
	{
		if(Session::isConnected()) {
			View::redirect('/');
		} else {
			View::make('connexion.twig');
		}
	}

	public function connect()
	{
		$password = hash('sha256', $_POST['password']);
		$results = $this->_db->getAdmin($_POST['login'], $password);

		if(count($results)) {
			$infos = $results[0];
			Session::connect($infos['id'], $infos['nom'], $infos['prenom'], $infos['type']);
			View::redirect('/');
		} else {
			ErrorLogger::add('Les identifiants saisis sont incorrects');
			View::make('connexion.twig', array('erreurs' => ErrorLogger::get()));
		}
	}

	public function logout() {
		Session::destroy();
		View::redirect('/');
	}
}