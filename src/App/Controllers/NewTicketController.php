<?php

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;

class NewTicketController
{
	/**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }
    
    public function index() {
        View::make('newTicket.twig');
    }
}

