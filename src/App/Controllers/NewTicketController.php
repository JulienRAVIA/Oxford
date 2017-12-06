<?php

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;
use App\Utils\Form;

class NewTicketController {

    /**
     * On récupère le singleton de la base de données
     */
    public function __construct() {
        $this->_db = Database::getInstance();
    }

    /**
     * Page de création du ticket
     */
    public function index() {
        View::make('newTicket.twig', array('subjects' => $this->_db->getDefaultSubjects()));
    }

    /**
     * Création du ticket
     */
    public function create() {
        $field = array('email', 'codeacces', 'selectprob', 'prob');
        if (Form::isNotEmpty($_POST, $field)) {
            //execute le code
            $id = $this->_db->getUserId($_POST['email'], $_POST['codeacces']);
            //On determine le sujet
            
            if (empty($_POST['sujet_autre'])){
                $sujet = $_POST['selectprob'];
            }
            else{
                $numSub = $this->_db->addSubject($_POST['sujet_autre']);
                $sujet = $numSub;
            }
            $ticket = $this->_db->addTicket($sujet, $id, $_POST['prob']);
        } else {
            throw new \Exception('Le formulaire n\'est pas rempli correctement');
        }
        View::redirect('/ticket/'.$ticket);
    }
}
