<?php

namespace App\Controllers;
use \App\View;
use \App\Utils\Session;

/**
 * Controlleur de la configuration
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class ConfigController
{
    /**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = \App\Database::getInstance(); 
        Session::check('type', 'rssi', '/connexion');
    }

    /**
     * Affichage de la page de configuration
     */
    public function index()
    {
    	View::make('config.twig', array('types' => $this->_db->getTypes(), 
    									'admins' => $this->_db->getAdmins(),
    									'errors' => \App\Utils\ErrorLogger::get(),
    									'subjects' => $this->_db->getDefaultSubjects()));
    }

    /**
     * Suppression d'un sujet par défaut à partir de son identifiant
     * @param  int $request Tableau comportant l'identifiant du sujet à supprimer
     */
    public function deleteSubject($request)
    {
    	if(!$this->_db->deleteSubject($request['id'])) {
            \App\Utils\ErrorLogger::add('Impossible de supprimer le sujet');
        } else {
            \App\Utils\EventLogger::admin(Session::get('id'), 'Suppression du sujet ~'.$request['id']);
            View::redirect('/config');
        }
    }

    /**
     * Suppression d'un type par défaut à partir de son identifiant
     * @param  int $request Tableau comportant l'identifiant du type à supprimer
     */
    public function deleteType($request)
    {
        if ($request['id'] != 1) {
            $this->_db->deleteType($request['id']);
            \App\Utils\EventLogger::admin(Session::get('id'), 'Suppression du type d\'employés |'.$request['id']);
            View::redirect('/config');
        } else {
            View::redirect('/config');
        }
    }

    /**
     * Suppression d'un accès administrateur à partir de son identifiant
     * @param  int $request Tableau comportant l'identifiant de l'accès à supprimer
     */
    public function deleteAdmin($request)
    {
        if ($request['id'] != Session::get('id')) {
            \App\Utils\EventLogger::admin(Session::get('id'), 'Suppression de l\'accès du membre @'.$request['id']);
            $this->_db->deleteAccess($request['id']);
            $email = $this->_db->getUser($request['id']);
            $body = View::get('mails/delete_admin.twig', array('datas' => $email));
            Mailer::send($email['email'], 'Suppression de votre accès administrateur', $body);
        }
        View::redirect('/config');
    }

    /**
     * Ajout d'un type d'employé
     */
    public function addType()
    {
        $fields = array('type', 'icon', 'filter');
        if(\App\Utils\Form::isNotEmpty($_POST, $fields)) {
            $type = $this->_db->addType($_POST);
            \App\Utils\EventLogger::admin(Session::get('id'), 'Ajout du type d\'employés |'.$type);
        }
        View::redirect('/config');
    }

    /**
     * Ajout d'un sujet par défaut
     */
    public function addSubject()
    {
        $fields = array('sujet');
        if(\App\Utils\Form::isNotEmpty($_POST, $fields)) {
            $event = $this->_db->addDefaultSubject($_POST['sujet']);
    		\App\Utils\EventLogger::admin(Session::get('id'), 'Ajout du sujet par défaut ~'.$event);
    	}
    	View::redirect('/config');
    }
}
