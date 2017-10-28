<?php 

include_once '../bootstrap.php';

$router = new AltoRouter();
$router->setBasePath('/Oxford/public');

$router->map('GET','/', 'App\\HomeController@index', 'index');
$router->map('GET','/home', 'App\\HomeController@index', 'home');
$router->map('GET','/users', 'App\\UsersController@index', 'users');
$router->map('GET','/user/[i:id]', 'App\\HomeController@showUser', 'user');

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