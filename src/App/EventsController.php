<?php 

namespace App;
use App\View as View;
use App\Database as Database;

/**
 * summary
 */
class EventsController
{
    /**
     * summary
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

    public function index() {
    	$events = $this->_db->getEvents();
        View::make('events.twig', array('events' => $events));
    }
}
