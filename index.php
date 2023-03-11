<?php

require_once __DIR__ ."/vendor/autoload.php";

use Edalicio\DependencyInjection\Controllers\HomeController;
use Edalicio\DependencyInjection\Controllers\UserController;
use Edalicio\DependencyInjection\Core\Request;
use Edalicio\DependencyInjection\Core\Router;

session_start();

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

$_SESSION['user'] = 1;

$controllers = [
    HomeController::class,
    UserController::class,
];

$request =  new Request();

$requestMethod =  $request->getMethod();
$requestUri =  $request->getPath();


$router = new Router(requestMethod: $requestMethod, requestUri: $requestUri , request: $request);


$router->run($controllers);



