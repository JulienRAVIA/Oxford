<?php 

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;

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

    /**
     * Page d'accueil des tickets
     */
	public function index()
	{
		// Si on est connecté en tant que RSSI
		if (Session::isConnected()) {
			// on recupère tous les tickets et on les affiche
			$tickets = $this->_db->getTickets();
			View::make('tickets.twig', array('tickets' => $tickets, 'filtered' => 'Tous les tickets'));
		} else {
			// sinon on recupère les tickets de l'utilisateur correspondant à notre cookie
			Cookie::set('user_tickets', 4);
			$this->filterByUser(array('id' => Cookie::get('user_tickets')), 'Mes tickets');
		}
	}

	public function showTicket($request) {
		$ticket = $this->_db->getTicket($request['id']);
		$replies = $this->_db->getTicketReplies($request['id']);
		if(Session::get('type') != 'rssi') {
			Cookie::check('user_tickets', $ticket['user'], '/tickets');
		}
		View::make('ticket.twig', array('ticket' => $ticket, 'replies' => $replies));
	}

	public function accessTicket($request) {
		$ticket = $this->_db->getTicket($request['id']);
		$replies = $this->_db->getTicketReplies($request['id']);
		if($request['token'] == $ticket['token']) {
			Cookie::set('ticket_'.$ticket['id'], $ticket['token']);
			Cookie::set('ticket_user_'.$ticket['id'], $ticket['user']);
		}
		View::redirect('/ticket/'.$request['id']);
	}

	public function newReply($request) {
		if(Session::get('type') == 'rssi') {
			$user = Session::get('id');
		} else {
			$user = Cookie::get('ticket_user_'.$request['id']);
		}
		$array = array('user' => $user, 
					   'value' => \App\Utils\Form::isString($_POST['reply_message'], 3), 
					   'date' => time(), 
					   'ticket' => $request['id']);
		$id = $this->_db->addTicketReply($array);
		$this->_db->updateTicketStatus($request['id'], $this->determineStatut(Session::get('type')), time());
		View::redirect('/ticket/'.$request['id'].'#replie_'.$id);
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

	/**
	 * Filtrage des tickets par statut (ouvert, fermé, etc)
	 * @param  array $request  Données à trier avec le statut à trier
	 */
	public function filterByStatut($request)
	{
		// on récupère les tickets avec le statut à filtrer
		$tickets = $this->_db->getTicketsByStatut($request['statut']);
		// on affiche les tickets
		View::make('tickets.twig', array('tickets' => $tickets));
	}

	/**
	 * Filtrage des tickets par utilisateur
	 * @param  array $request  Données à trier avec l'identifiant de l'utilisateur
	 * @param  string $filtered Message de filtrage pour la vue
	 */
	public function filterByUser($request, $filtered = '')
	{
		$tickets = $this->_db->getTicketsByUser($request['id']);
		if (empty($filtered)) {
        	$filtered = 'Tickets de l\'utilisateur';
        }
		View::make('tickets.twig', array('tickets' => $tickets, 'filtered' => $filtered));
	}

	/**
	 * Filtrage des tickets par date
	 * @param  array $request  Données à trier avec la date à filtrer
	 * @param  string $filtered Message de filtrage pour la vue
	 */
	public function filterByDate($request, $filtered = '')
	{
		// message à afficher sur la vue
        if (empty($filtered)) {
        	$filtered = 'Tickets du '.date('d/m/Y', $request['date']);
        }
		// on prépare la date pour la requête dans la BDD
        $date = date('m/d/Y', $request['date']);
        $date = explode('/', $date);
        $dateb = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        $datee = mktime(23, 59, 59, $date[0], $date[1], $date[2]);
        // on récupère les tickets entre le d/m/Y 00:00 et 23:59
		$tickets = $this->_db->getTicketsByDate($dateb, $datee);
		// on affiche les tickets
		View::make('tickets.twig', array('tickets' => $tickets, 'filtered' => $filtered));
	}

	/**
	 * Suppression d'un ticket
	 * @param  array $request Données à trier avec le ticket à supprimer
	 */
	public function deleteTicket($request) {
		// Si l'utilisateur est RSSI
		if (Session::get('type') == 'rssi') {
			// on supprime le ticket
			$this->_db->deleteTicket($request['id']);
			// on redirige l'utilisateur vers la page des tickets
			View::redirect('/tickets');
		} else {
			throw new \Exception('Vous n\avez pas les droits nécessaires pour effectuer cette action');
		}
	}
}
