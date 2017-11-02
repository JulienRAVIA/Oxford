<?php 

include_once '../bootstrap.php';

$router = new AltoRouter();
$router->setBasePath('/Oxford/public');

$router->map('GET','/', 'App\\HomeController@index', 'index');
$router->map('GET','/home', 'App\\HomeController@index', 'home');

/** Page d'accueils des différentes sections */
$router->map('GET','/users', 'App\\UsersController@index', 'users');
$router->map('GET','/users/', 'App\\UsersController@index', 'users.list');
$router->map('GET','/events/', 'App\\EventsController@index', 'events');
$router->map('GET','/events', 'App\\EventsController@index', 'events.list');

// filtrage par type de catégorie, on accepte uniquement les types info ou erreur
$router->map('GET','/events/category/[erreur|info|admin:category]', 'App\\EventsController@filterByCategory', 'events.list.category');
// filtrage par date (timestamp)
$router->map('GET','/events/date/[i:date]', 'App\\EventsController@filterByDate', 'events.list.date');
// filtrage par utilisateur
$router->map('GET','/events/user/[i:user]', 'App\\EventsController@filterByUser', 'events.list.user');

// affichage et modification d'un utilisateur
$router->map('GET','/user/[i:id]', 'App\\UsersController@showUser', 'user.show');
$router->map('POST','/user/[i:id]', 'App\\UsersController@updateUser', 'user.update');

// suppression d'un utilisateur
$router->map('GET','/user/[i:id]/delete', 'App\\UsersController@deleteUser', 'user.delete');
// révocation d'un utilisateur
$router->map('GET','/user/[i:id]/revoke', 'App\\UsersController@revokeUser', 'user.revoke');
// autorisation d'un utilisateur
$router->map('GET','/user/[i:id]/autorize', 'App\\UsersController@autorizeUser', 'user.autorize');

// ajout d'un utilisateur
$router->map('GET','/users/new', 'App\\UsersController@showNewUserForm', 'users.add.show');
$router->map('POST','/users/new', 'App\\UsersController@createUser', 'users.add.create');



$match = $router->match();

if (!$match) {
    echo "404 not found";
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

?>