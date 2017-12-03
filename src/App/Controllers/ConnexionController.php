<?php 

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;
use App\Utils\ErrorLogger;
use App\Utils\EventLogger;

/**
 * Controlleur de la connexion
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
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
     */
	public function index()
	{
		if(Session::isConnected()) {
			View::redirect('/');
		} else {
			View::make('connexion.twig');
		}
	}

	/**
	 * Connexion à partir des identifiants rentrés
	 */
	public function connect()
	{
		$password = hash('sha256', $_POST['password']);
		$results = $this->_db->getAdmin($_POST['login'], $password);

		if(count($results)) {
			$infos = $results[0];
			EventLogger::info($infos['id'], 'Connexion à l\'administration');
			Session::connect($infos['id'], $infos['nom'], $infos['prenom'], $infos['type']);
			EventLogger::success($infos['id'], 'Connexion à l\'administration avec succès');
			View::redirect('/');
		} else {
			ErrorLogger::add('Les identifiants saisis sont incorrects');
			EventLogger::error(1, 'Tentative de connexion à l\'administration avec l\'adresse '.$_POST['login']);
			View::make('connexion.twig', array('erreurs' => ErrorLogger::get()));
		}
	}

	/**
	 * Déconnexion de l'utilisateur
	 */
	public function logout() {
		Session::destroy();
		View::redirect('/');
	}
}