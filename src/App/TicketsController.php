<?php 

namespace App;

/**
 * summary
 */
class TicketsController
{
	/**
     * On récupère le singleton de la base de données
     */
    public function __construct()
    {
        $this->_db = Database::getInstance();
    }

	public function index()
	{
		$tickets = $this->_db->getTickets();
		View::make('tickets.twig', array('tickets' => $tickets));
	}

	public function showTicket($request) {
		$ticket = $this->_db->getTicket($request['id']);
		$replies = $this->_db->getTicketReplies($request['id']);
		View::make('ticket.twig', array('ticket' => $ticket, 'replies' => $replies));
	}
}
