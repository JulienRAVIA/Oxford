<?php 

namespace App\Controllers;
use App\View as View;
use App\Database as Database;
use App\Utils\Session;

/**
 * Controlleur des événements
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
 */
class EventsController
{
    /**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
        Session::check('type', 'rssi', '/connexion');
    }

    /**
     * Affichage de tous les événements, classés par date (descendant)
     * @return twigView Vue des événements
     */
    public function index() {
        $events = $this->_db->getEvents();
        View::make('events.twig', array('events' => $events, 
                                        'filtered' => 'Tous les événements',
                                        'error_message' => 'Il n\'y a aucun événement enregistré dans la base de données'));
    }

    /**
     * Filtrage des événements par une date, on affiche les événements entre 00h et 23h59
     * @param  timestamp $request Timestamp de la date à filtrer
     * @return twigView Vue des événements
     */
    public function filterByDate($request) {
        $date = date('m/d/Y', $request['date']);
        $date = explode('/', $date);
        $dateb = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        $datee = mktime(23, 59, 59, $date[0], $date[1], $date[2]);

    	$events = $this->_db->getEventsByDate($dateb, $datee);
        View::make('events.twig', array('events' => $events, 
                                        'filtered' => 'Evénements du '.date('d/m/Y', $request['date']), 
                                        'error_message' => 'Il n\'y a aucun événements pour ce jour-ci'));
    }

    /**
     * Filtrage des événements pour un utilisateur
     * @param  id $request Identifiant de l'utilisateur à filtrer
     * @return twigView Vue des événements
     */
    public function filterByUser($request)
    {
        $user = $this->_db->getUser($request['user'], 'L\'utilisateur dont vous souhaitez filtrer les événements n\'existe pas');
        $events = $this->_db->getEventsByUser($request['user']);
        View::make('events.twig', array('events' => $events, 
                                        'filtered' => 'Evénements de l\'utilisateur', 
                                        'error_message' => 'Il n\'y a pas d\'événements pour cet utilisateur'));
    }

    /**
     * Filtrage des événements d'une catégorie
     * @param  string $request Type de catégorie à filtrer
     * @return twigView Vue des événements
     */
    public function filterByCategory($request)
    {
        $events = $this->_db->getEventsByCategory($request['category']);
        View::make('events.twig', array('events' => $events, 
                                        'filtered' => 'Evénements de type '.ucfirst($request['category']), 
                                        'error_message' => 'Il n\'y a aucun événement de ce type'));
    }
}
