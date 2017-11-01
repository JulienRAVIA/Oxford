<?php 

namespace App;
use App\View as View;
use App\Database as Database;

/**
 * summary
 */
class UsersController
{
    /**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

	/**
     * Affichage de tous les utilisateurs (classés par id)
     * @return twigView Vue des utilisateurs
     */
    public function index() {
    	$users = $this->_db->getUsers();
        View::make('users.twig', array('users' => $users));
    }

    /**
     * Affichage des infos de l'utilisateur passé en paramètre
     * @return twigView Vue d'un utilisateur
     */
    public function showUser($request)
    {
       $user = $this->_db->getUser($request['id']);
       $types = $this->_db->getTypes();
       View::make('user.twig', array('user' => $user, 'types' => $types));
    }

    /**
     * Modification des infos de l'utilisateur
     */
    public function updateUser($request)
    {
    	var_dump($_POST);
    }
}
