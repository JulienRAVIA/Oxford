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

	public function index($tickets = '')
	{
		$tickets = $this->_db->getTickets();
		View::make('tickets.twig', array('tickets' => $tickets));
	}

	public function showTicket($request) {
		$ticket = $this->_db->getTicket($request['id']);
		$replies = $this->_db->getTicketReplies($request['id']);
		View::make('ticket.twig', array('ticket' => $ticket, 'replies' => $replies));
	}

	public function newReply($request) {
		$array = array('user' => 1, 
					   'value' => Form::isString($_POST['reply_message'], 5), 
					   'date' => time(), 
					   'ticket' => $request['id']);
		$this->_db->addTicketReply($array);
		$this->_db->updateTicketStatus($request['id'], $this->determineStatut('rssi'), time());
		View::redirect('/ticket/'.$request['id']);
	}

	public function determineStatut($type) {
		if($type == 'rssi') {
			return 3;
		} else {
			return 2;
		}
	}

	public function changeTicketStatus($request) {
		if ($request['action'] == 'cloturer') {
			$statut = 4;
		} elseif($request['action'] == 'ouvrir') {
			$statut = 1;
		}
		$this->_db->updateTicketStatus($request['id'], $statut);
		View::redirect('/ticket/'.$request['id']);
	}

	public function filterByStatut($request)
	{
		$tickets = $this->_db->getTicketsByStatut($request['statut']);
		View::make('tickets.twig', array('tickets' => $tickets));
	}

	public function filterByUser($request)
	{
		$tickets = $this->_db->getTicketsByUser($request['id']);
		View::make('tickets.twig', array('tickets' => $tickets));
	}

	public function filterByDate($request)
	{
        $date = date('m/d/Y', $request['date']);
        $date = explode('/', $date);
        $dateb = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        $datee = mktime(23, 59, 59, $date[0], $date[1], $date[2]);

		$tickets = $this->_db->getTicketsByDate($dateb, $datee);
		View::make('tickets.twig', array('tickets' => $tickets));
	}
}
