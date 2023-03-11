<?php 

namespace Edalicio\SimpleRouter\Controllers;

use Edalicio\SimpleRouter\Core\Attribute\Controller;
use Edalicio\SimpleRouter\Core\Attribute\Middleware;
use Edalicio\SimpleRouter\Core\Attribute\Route;
use Edalicio\SimpleRouter\Middlewares\AuthMiddleware;

#[Controller(name: 'UserController', prefix: '/user')]
class UserController {
    #[Route(uri: '/', method: 'GET')]
    #[Middleware(AuthMiddleware::class)]
    public function __invoke()
    {
        dd(__METHOD__);
    }
}