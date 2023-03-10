<?php

require_once __DIR__ ."/vendor/autoload.php";

use Edalicio\DependencyInjection\Controllers\HomeController;
use Edalicio\DependencyInjection\Controllers\UserController;
use Edalicio\DependencyInjection\Core\Enums\HttpMethodsEnum;
use Edalicio\DependencyInjection\Core\Request;
use Edalicio\DependencyInjection\Core\Router;
use Edalicio\DependencyInjection\Middlewares\AuthMiddleware;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function dd($v)
{

    echo "<pre>";
    echo '<b>' . debug_backtrace()[0]['file'] . '</b>:';
    echo '<b>' . debug_backtrace()[0]['line'] . '</b>';
    die(var_dump($v));
}



$controllers = [
    HomeController::class,
    UserController::class,
];

$requestMethod =  $_SERVER['REQUEST_METHOD'];
$requestUri =  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


$router = new Router(requestMethod: $requestMethod, requestUri: $requestUri );


$router->prefix('/home')->register(HttpMethodsEnum::Get, '/:id', [ HomeController::class, 'show' ], [AuthMiddleware::class]);


$router->run($controllers);



