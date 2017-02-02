<?php
use routes\Router;

spl_autoload_register(function($class){
    include str_replace('\\', '/', $class) . '.php';
});

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
$uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);

$router = new Router($method, $uri);
$router->route();
