<?php 

namespace App;
use App\View as View;
use App\Database as Database;

/**
 * Controlleur des/d'un utilisateur
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
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
        $types = $this->_db->getTypes();
        View::make('users.twig', array('users' => $users, 'types' => $types, 'filtered' => 'Tous les utilisateurs actifs'));
    }

	/**
     * Affichage de tous les utilisateurs (classés par id)
     * @return twigView Vue des utilisateurs
     */
    public function filterByType($request) {
        if($request['type'] == 'deleted') { 
            $filter = 'Utilisateurs supprimés'; 
        } else {
           $types = $this->_db->typeExistWithFilter($request['type']);
           $filter = 'Utilisateurs étant '.$types;
        }
        $users = $this->_db->getUsersByType($request['type']);
        $types = $this->_db->getTypes();
        View::make('users.twig', array('users' => $users, 
                                       'types' => $types,
                                       'search' => $request['type'],
                                       'filtered' => $filter));
    }

    /**
     * Affichage des infos de l'utilisateur passé en paramètre
     * @param  int $request 	Numéro de l'utilisateur
     * @return twigView         Vue twig d'un utilisateur
     */
    public function showUser($request)
    {
       $user = $this->_db->getUser($request['id']);
       $types = $this->_db->getTypes();
       View::make('user.twig', array('user' => $user, 'types' => $types));
    }

    /**
     * Modification des infos de l'utilisateur
     * @param  int $request Identifiant de l'utilisateur à modifier
     * @return twigView     Redirection
     */
    public function updateUser($request)
    {
    	/*
    	Si on ne met à jour que les informations basiques de l'utilisateur
    	 */
    	if(isset($_POST['type']) AND $_POST['type'] == 'infos') {
    		$fields = array('nom', 'prenom', 'email', 'birth', 'sexe');
    		if(Form::isNotEmpty($_POST, $fields)) {
    			$datas['id'] = $request['id'];
    			$datas['nom'] = Form::isString($_POST['nom'], 3);
    			$datas['prenom'] = Form::isString($_POST['prenom'], 3);
    			$datas['birth'] = Form::isDate($_POST['birth']);
    			$datas['email'] = Form::isMail($_POST['email']);
    			$datas['sexe'] = Form::isSex($_POST['sexe']);
    		}
    		if($this->_db->updateUserInfos($datas)) {
    			View::redirect('/user/'.$request['id']);
    		}
    	}

    	/*
    	  Si on ne met à jour que les informations de travail de l'utilisateur
    	*/
    
    	if(isset($_POST['type']) AND $_POST['type'] == 'job') {
    		$fields = array('poste');
    		if($_POST['poste'] == 1) {
    			$fields[] = 'password';
    		}
    		if(Form::isNotEmpty($_POST, $fields)) {
    			$datas['id'] = $request['id'];  
    			$datas['poste'] = Form::isInt($_POST['poste']);
    			$datas['password'] = Form::isPassword($_POST['password']);
    		}
    		if($this->_db->updateUserJob($datas)) {
    			View::redirect('/user/'.$request['id']);
    		}
    	}

    	/*
    	Si on ne met à jour que le code de l'utilisateur
    	 */
    	if(isset($_POST['type']) AND $_POST['type'] == 'code') {
    		$fields = array('code');
    		if(Form::isNotEmpty($_POST, $fields)) {
    			$datas['id'] = $request['id'];  
    			$datas['code'] = $_POST['code'];  
    		}
    		if($this->_db->updateUserCode($datas)) {
    			View::redirect('/user/'.$request['id']);
    		}
    	}
    }

    /**
     * Affichage du formulaire de création d'utilisateurs
     * @return twigView	 Vue de création d'utilisateur
     */
    public function showNewUserForm()
    {
    	$types = $this->_db->getTypes();
    	View::make('new_user.twig', array('types' => $types));
    }

    /**
     * Création d'un utilisateur
     * @return twigView Vue de l'utilisateur créé
     */
    public function createUser()
    {
    	$fields = array('nom', 'prenom', 'email', 'birth', 'sexe', 'code', 'type'); // Champs obligatoires
    	// Nom d'une photo, les photos seront enregistré avec pour le nom le timestamp de création
    	$name = time(); 
    	// Si le champ type est 1 (RSSI), on ajoute aux champs obligatoires le champ password
    	if($_POST['type'] == 1) { $fields[] = 'password'; }
    	// On vérifie que les champs obligatoires soient remplis
    	if(Form::isNotEmpty($_POST, $fields)) { 
    		$datas['nom'] = Form::isString($_POST['nom'], 3);
    		$datas['prenom'] = Form::isString($_POST['prenom'], 3);
    		$datas['birth'] = Form::isDate($_POST['birth']);
    		$datas['email'] = Form::isMail($_POST['email']);
    		$datas['sexe'] = Form::isSex($_POST['sexe']);
    		if($this->_db->typeExist($_POST['type'])) {
    			$datas['type'] = $_POST['type'];
    		}
    		if($_POST['type'] == 1) {
    			$datas['password'] = Form::isPassword($_POST['password']);
    		}
    		$datas['code'] = $_POST['code'];
    		$datas['photo'] = Form::upload('photo', $name, 'photos/');
    	}
    	// on enregistre l'identifiant de la photo enregistrée dans la DB
    	$datas['photo'] = $this->_db->insertPhoto($datas['photo'], $name); 
    	// on enregistre l'identifiant de l'user créé dans la DB pour la redirection
    	$user = $this->_db->insertUser($datas); 
        // Si on à choisi qu'il soit RSSI on l'ajoute aux admins
        if($datas['type'] == 1) {
            $this->_db->addAdmin($user, $datas['password']);
        }
    	// On redirige vers la page d'édition
    	View::redirect('/user/'.$user); 
    }

    /**
     * Suppression d'un utilisateur
     * @param  int $request    ID de l'utilisateur à supprimer
     * @return twigView        Redirection vers la page des utilisateurs
     */
    public function deleteUser($request)
    {
        $this->_db->getUser($request['id'], 'L\'utilisateur que vous souhaitez supprimer n\'existe pas');
        $this->_db->deleteUser($request['id']);
        View::redirect('/user/'.$request['id']);
    }

    /**
     * Révocation (suppression d'accès) d'un utilisateur
     * @param  int $request    ID de l'utilisateur à révoquer
     * @return twigView        Redirection vers la page de l'utilisateur
     */
    public function revokeUser($request)
    {
        $this->_db->getUser($request['id'], 'L\'utilisateur que vous souhaitez révoquer n\'existe pas');
        $this->_db->revokeUser($request['id']);
        View::redirect('/user/'.$request['id']);
    }

    /**
     * Autorisation (réatribution d'accès) d'un utilisateur
     * @param  int $request    ID de l'utilisateur à autoriser
     * @return twigView        Redirection vers la page de l'utilisateur
     */
    public function autorizeUser($request)
    {
        $this->_db->getUser($request['id'], 'L\'utilisateur que vous souhaitez autoriser n\'existe pas');
        $this->_db->autorizeUser($request['id']);
        View::redirect('/user/'.$request['id']);
    }

    /**
     * Autorisation (réatribution d'accès) d'un utilisateur
     * @param  int $request    ID de l'utilisateur à autoriser
     * @return twigView        Redirection vers la page de l'utilisateur
     */
    public function restoreUser($request)
    {
        $this->_db->getUser($request['id'], 'L\'utilisateur que vous souhaitez restaurer n\'existe pas');
        $this->_db->restoreUser($request['id']);
        View::redirect('/user/'.$request['id']);
    }
}
