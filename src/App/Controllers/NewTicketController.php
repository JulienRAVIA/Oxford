<?php

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;
use App\Utils\Form;
use App\Utils\EventLogger;

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
            $id = $this->_db->getUserId($_POST['email'], $_POST['codeacces']);
            // On determine le sujet            
            if (empty($_POST['sujet_autre'])){
                $sujet = $_POST['selectprob'];
            }
            else{
                $numSub = $this->_db->addSubject($_POST['sujet_autre']);
                $sujet = $numSub;
            }
            $datas = $this->_db->getUser($id);
            $ticket = $this->_db->addTicket($sujet, $id, $_POST['prob']);
            $token = $this->_db->getToken($ticket);
            $body = View::get('mails/new_ticket.twig', array('token' => $token, 
                                                             'datas' => $datas, 
                                                             'probleme' => $_POST['prob'], 
                                                             'id' => $ticket));
            \App\Utils\Mailer::send($_POST['email'], 'Ticket #'.$ticket.' : '.$sujet, $body);
            EventLogger::info($id, 'Création du ticket '.$ticket);
        } else {
            throw new \Exception('Le formulaire n\'est pas rempli correctement');
        }
        // View::redirect('/ticket/'.$ticket);
        var_dump($body);
    }
}
