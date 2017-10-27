<?php 

include_once '../bootstrap.php';

$router = new AltoRouter();
$router->setBasePath('/Oxford/public');

$router->map('GET','/', 'App\\HomeController@index', 'home');
$router->map('GET','/test', 'App\\HomeController@test', 'test');

$match = $router->match();

if (!$match) {
    echo "404 not found";
} else {
    list($controller, $action) = explode('@', $match['target']);
    $controller = new $controller;
    if (is_callable(array($controller, $action))) {
        call_user_func_array(array($controller, $action), array($match['params']));
    } else {
        echo 'Error: can not call ' . get_class($controller) . '@' . $action;
        // here your routes are wrong.
        // Throw an exception in debug, send a 500 error in production
    }
}

?>