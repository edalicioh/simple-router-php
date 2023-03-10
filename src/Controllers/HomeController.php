<?php 

namespace Edalicio\DependencyInjection\Controllers;

use Edalicio\DependencyInjection\Core\Attribute\Controller;
use Edalicio\DependencyInjection\Core\Attribute\Middleware;
use Edalicio\DependencyInjection\Core\Attribute\Route;
use Edalicio\DependencyInjection\Core\Enums\HttpMethodsEnum;
use Edalicio\DependencyInjection\Core\Request;
use Edalicio\DependencyInjection\Middlewares\AuthMiddleware;
use Edalicio\DependencyInjection\Middlewares\UserMiddleware;

#[Controller('HomeController')]
class HomeController {
    #[Route('/', HttpMethodsEnum::Get)]    
    #[Middleware([AuthMiddleware::class])]
    public function index( ) {
      echo 'index';
    }
    #[Route('/:id', 'GET')]
    public function show($id ) {
        dd($id);
    }
    #[Route('/', 'POST')]
    public function store() {
        dd("sda");
    }

    #[Route('/:id/edit', 'POST')]
    public function edit(int $id) {
        dd($id);
    }
    #[Route('/', 'PUT')]
    public function update( ) {
        dd(__METHOD__);
    }
    #[Route('/', 'DELETE')]
    public function delete( ) {
        dd(__METHOD__);
    }
}