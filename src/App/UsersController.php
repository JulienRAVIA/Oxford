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
        View::make('users.twig', array('users' => $users));
    }

    /**
     * Affichage des infos de l'utilisateur passé en paramètre
     * @return twigView Vue d'un utilisateur
     */
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
     * @param  [type] $request [description]
     * @return [type]          [description]
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

}
