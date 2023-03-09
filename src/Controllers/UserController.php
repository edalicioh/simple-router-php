<?php 

namespace Edalicio\DependencyInjection\Controllers;

use Edalicio\DependencyInjection\Core\Attribute\Controller;
use Edalicio\DependencyInjection\Core\Attribute\Middleware;
use Edalicio\DependencyInjection\Core\Attribute\Route;
use Edalicio\DependencyInjection\Middlewares\AuthMiddleware;

#[Controller(name: 'UserController', prefix: '/user')]
class UserController {
    #[Route(uri: '/', method: 'GET')]
    #[Middleware(AuthMiddleware::class)]
    public function __invoke()
    {
        dd(__METHOD__);
    }
}