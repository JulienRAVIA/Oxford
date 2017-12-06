<?php 

include_once '../bootstrap.php';

$router = new AltoRouter();
// si pas de Virtual HOST
// $router->setBasePath('/Oxford/public');
$router->setBasePath('');

// Page d'accueil
$router->addRoutes(array(
    // affichage de la page d'accueil ou de la page de connexion si non connecté
    array('GET','/', 'App\\Controllers\\HomeController@index', 'index'), 
    // affichage de la page de connexion
    array('GET','/home', 'App\\Controllers\\HomeController@index', 'home') 
));

// Page d'accueil
$router->addRoutes(array(
    // affichage de la page d'accueil ou de la page de connexion si non connecté
    array('GET','/connexion', 'App\\Controllers\\ConnexionController@index', 'connexion'), 
    // connexion
    array('POST','/connexion', 'App\\Controllers\\ConnexionController@connect', 'connexion.post'), 
    // deconnexion
    array('GET', '/logout', 'App\\Controllers\\ConnexionController@logout', 'logout')
));

// Gestion des événements
$router->addRoutes(array(
    // page des derniers événements
   array('GET','/events/', 'App\\Controllers\\EventsController@index', 'events'),
   array('GET','/events', 'App\\Controllers\\EventsController@index', 'events.list'),
    // filtrage par type de catégorie, on accepte uniquement les types info ou erreur
   array('GET','/events/category/[erreur|info|admin|succes:category]', 'App\\Controllers\\EventsController@filterByCategory', 'events.list.category'),
    // filtrage par date (timestamp)
   array('GET','/events/date/[i:date]', 'App\\Controllers\\EventsController@filterByDate', 'events.list.date'),
    // filtrage par utilisateur
   array('GET','/events/user/[i:user]', 'App\\Controllers\\EventsController@filterByUser', 'events.list.user')
));

// Gestion des utilisateurs
$router->addRoutes(array(
    // liste des utilisateurs
    array('GET','/users', 'App\\Controllers\\UsersController@index', 'users'),
    array('GET','/users/', 'App\\Controllers\\UsersController@index', 'users.list'),
    // ajout d'un utilisateur
    array('GET','/users/new', 'App\\Controllers\\UsersController@showNewUserForm', 'users.add.show'),
    // création d'un utilisateur
    array('POST','/users/new', 'App\\Controllers\\UsersController@createUser', 'users.add.create'),
    //filtrage des utilisateurs par catégorie
    array('GET','/users/[a:type]', 'App\\Controllers\\UsersController@filterByType', 'users.filter')
));

// Gestion d'un utilisateur
$router->addRoutes(array(
    // page d'édition d'un utilisateur
    array('GET','/user/[i:id]', 'App\\Controllers\\UsersController@showUser', 'user.show'),
    // édition d'un utilisateur
    array('POST','/user/[i:id]', 'App\\Controllers\\UsersController@updateUser', 'user.update'),
    // suppression d'un utilisateur
    array('GET','/user/[i:id]/delete', 'App\\Controllers\\UsersController@deleteUser', 'user.delete'),
    // restauration d'un utilisateur
    array('GET','/user/[i:id]/restore', 'App\\Controllers\\UsersController@restoreUser', 'user.restore'),
    // révocation d'un utilisateur
    array('GET','/user/[i:id]/revoke', 'App\\Controllers\\UsersController@revokeUser', 'user.revoke'),
    // autorisation d'un utilisateur
    array('GET','/user/[i:id]/autorize', 'App\\Controllers\\UsersController@autorizeUser', 'user.autorize')
));

// Gestion des tickets
$router->addRoutes(array(
    //affichage des tickets
    array('GET','/tickets', 'App\\Controllers\\TicketsController@index', 'tickets'),
    // affichage des tickets d'un utilisateur
    array('GET','/tickets/user/[i:id]', 'App\\Controllers\\TicketsController@filterByUser', 'ticket.filter.user'),
    // affichage des tickets par date
    array('GET','/tickets/date/[i:date]', 'App\\Controllers\\TicketsController@filterByDate', 'ticket.filter.date'),
    // affichage des tickets par statut
    array('GET','/tickets/statut/[replied|newreply|closed|opens:statut]', 'App\\Controllers\\TicketsController@filterByStatut', 'tickets.filter.statut')
));

// Gestion d'un ticket
$router->addRoutes(array(
    // affichage d'un ticket
    array('GET','/ticket/[i:id]', 'App\\Controllers\\TicketsController@showTicket', 'ticket.show'),
    // accès à un ticket par un utilisateur
    array('GET','/ticket/[i:id]/token_[a:token]', 'App\\Controllers\\TicketsController@accessTicket', 'ticket.show.token'),
    // cloture ou ouverture d'un ticket
    array('GET','/ticket/[i:id]/[cloturer|ouvrir:action]', 'App\\Controllers\\TicketsController@changeTicketStatus', 'ticket.change'),
    // ahout d'une réponse au ticket
    array('POST','/ticket/[i:id]', 'App\\Controllers\\TicketsController@newReply', 'ticket.reply'),
    // ajout d'une réponse au ticket
    array('GET','/ticket/[i:id]/delete', 'App\\Controllers\\TicketsController@deleteTicket', 'ticket.delete')
));

// Configuration
$router->addRoutes(array(
    // Page d'accueil
    array('GET','/config', 'App\\Controllers\\ConfigController@index', 'config'),
    array('GET','/config/delete/subject/[i:id]', 'App\\Controllers\\ConfigController@deleteSubject', 'config.delete.subject'),
    array('GET','/config/delete/type/[i:id]', 'App\\Controllers\\ConfigController@deleteType', 'config.delete.type'),
    array('GET','/config/delete/admin/[i:id]', 'App\\Controllers\\ConfigController@deleteAdmin', 'config.delete.admin'),
    array('POST','/config/addtype', 'App\\Controllers\\ConfigController@addType', 'config.add.type'),
    array('POST','/config/addsubject', 'App\\Controllers\\ConfigController@addSubject', 'config.add.subject')
));

// Ajout d'un ticket
$router->addRoutes(array(
    // affichage d'un ticket
    array('GET','/tickets/new', 'App\\Controllers\\NewTicketController@index', 'ticket.new'),
    //envoie du ticket
    array('POST','/tickets/new', 'App\\Controllers\\NewTicketController@create', 'ticket.new.envoie')
));

$match = $router->match();

if (!$match) {
    App\View::make('404.twig');
} else {
    list($controller, $action) = explode('@', $match['target']);
    $controller = new $controller;
    if (is_callable(array($controller, $action))) {
        try {
            call_user_func_array(array($controller, $action), array($match['params']));
        } catch (Exception $e) {
            App\View::make('error.twig', array('error' => $e->getMessage()));
        }
    } else {
        echo 'Error: can not call ' . get_class($controller) . '@' . $action;
        // here your routes are wrong.
        // Throw an exception in debug, send a 500 error in production
    }
}
