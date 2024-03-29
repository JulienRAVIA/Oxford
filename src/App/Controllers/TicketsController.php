<?php 

namespace App\Controllers;

use App\Database;
use App\View;
use App\Utils\Session;
use App\Utils\Cookie;
use App\Utils\EventLogger;

/**
 * Controlleur des tickets
 * Auteur : Julien RAVIA <julien.ravia@gmail.com>
 * Utilisation ou modification interdite sauf autorisation
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
			if (Session::get('user_tickets')) {
				// sinon on recupère les tickets de l'utilisateur correspondant à notre cookie
				$id = Session::get('user_tickets');
			} else {
				$id = 0;
			}
			$this->filterByUser(array('id' => $id), 'Mes tickets');
		}
	}

	/**
	 * Affichage d'un ticket avec ses réponses et ses informations
	 * @param  array $request Tableau comportant l'identifiant du ticket
	 */
	public function showTicket($request) {
		$ticket = $this->_db->getTicket($request['id']);
		$replies = $this->_db->getTicketReplies($request['id']);
		if(Session::get('type') != 'rssi') {
			Session::check('user_tickets', $ticket['user'], '/tickets');
		}
		View::make('ticket.twig', array('ticket' => $ticket, 'replies' => $replies));
	}

	/**
	 * Génération d'un identifiant de session pour accéder au ticket de l'extérieur
	 * @param  array $request Tableau comportant l'identifiant du ticket
	 */
	public function accessTicket($request) {
		$ticket = $this->_db->getTicket($request['id']);
		$replies = $this->_db->getTicketReplies($request['id']);
		if($request['token'] == $ticket['token']) {
			Session::set('user_tickets', $ticket['user']);
		}
		View::redirect('/ticket/'.$request['id']);
	}

	/**
	 * Ajout d'une réponse au ticket
	 * @param  array $request Tableau comportant l'id du ticket
	 */
	public function newReply($request) {
		if(Session::get('type') == 'rssi') {
			$ticket = $this->_db->getTicket($request['id']);
			$datas = $this->_db->getUser($ticket['user']);
			$user = Session::get('id');
			EventLogger::admin(Session::get('id'), 'Nouvelle réponse au ticket #'.$request['id']);
			$body = View::get('mails/reply_message.twig', array('datas' => $datas, 
															    'token' => $ticket['token'], 
															    'id' => $request['id']));
			\App\Utils\Mailer::send($datas['email'], 'Nouvelle réponse à votre ticket #'.$request['id'], $body);
		} else {
			$user = Session::get('user_tickets');
			EventLogger::info($user, 'Nouvelle réponse au ticket #'.$request['id']. ' par l\'utilisateur @'.$user);
		}
		$array = array('user' => $user, 
					   'value' => \App\Utils\Form::isString($_POST['reply_message'], 3), 
					   'date' => time(), 
					   'ticket' => $request['id']);
		$id = $this->_db->addTicketReply($array);
		$this->_db->updateTicketStatus($request['id'], $this->determineStatut(Session::get('type')), time());
		View::redirect('/ticket/'.$request['id'].'#replie_'.$id);
	}

	/**
	 * Fonction pour déterminer le statut à partir du type d'employé
	 * @param  string $type Type de l'employé
	 * @return int       Statut par rapport au type
	 */
	public function determineStatut($type) {
		if($type == 'rssi') {
			return 3;
		} else {
			return 2;
		}
	}

	/**
	 * Changement du statut du ticket
	 * @param  array $request Tableau comportant l'id du ticket
	 */
	public function changeTicketStatus($request) {
		if ($request['action'] == 'cloturer') {
			if (Session::get('type')) {
				EventLogger::admin(Session::get('id'), 'Cloture du ticket #'.$request['id']);
			} else {
				EventLogger::info(Session::get('user_tickets'), 'Cloture du ticket #'.$request['id']);
			}
			$statut = 4;
		} elseif($request['action'] == 'ouvrir') {
			if (Session::get('type')) {
				EventLogger::admin(Session::get('id'), 'Ouverture du ticket #'.$request['id']);
			} else {
				EventLogger::info(Session::get('user_tickets'), 'Ouverture du ticket #'.$request['id']);
			}
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
		$user = $this->_db->getUserBis($request['id']);
		if (empty($filtered)) {
        	$filtered = 'Tickets de l\'utilisateur '.$user['nom'].' '.$user['prenom'];
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
			EventLogger::admin(Session::get('id'), 'Suppression du ticket #'.$request['id']);
			// on redirige l'utilisateur vers la page des tickets
			View::redirect('/tickets');
		} else {
			EventLogger::error(Session::get('user_tickets'), 'Tentative de suppression du ticket par @'.$request['id']);
			throw new \Exception('Vous n\avez pas les droits nécessaires pour effectuer cette action');
		}
	}
}
