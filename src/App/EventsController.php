<?php 

namespace App;
use App\View as View;
use App\Database as Database;

/**
 * Controlleur des événements
 */
class EventsController
{
    /**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

    /**
     * Affichage de tous les événements, classés par date (descendant)
     * @return twigView Vue des événements
     */
    public function index() {
        $events = $this->_db->getEvents();
        View::make('events.twig', array('events' => $events));
    }

    /**
     * Filtrage des événements par une date, on affiche les événements entre 00h et 23h59
     * @param  timestamp $request Timestamp de la date à filtrer
     * @return twigView Vue des événements
     */
    public function filterByDate($request) {
    	$events = $this->_db->getEventsByDate($request['date']);
        View::make('events.twig', array('events' => $events, 'filtered' => 'Evénements du '.date('d/m/Y', $request['date'])));
    }
}
