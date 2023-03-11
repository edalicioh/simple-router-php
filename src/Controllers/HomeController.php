<?php 

namespace Edalicio\DependencyInjection\Controllers;

use Edalicio\DependencyInjection\Core\Attribute\Controller;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Delete;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Get;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Post;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Put;
use Edalicio\DependencyInjection\Core\Attribute\Middleware;
use Edalicio\DependencyInjection\Middlewares\AuthMiddleware;

#[Controller('HomeController','/home')]
#[Middleware(AuthMiddleware::class)]
class HomeController {
    #[Get('/')]
    public function index(array $get ) {
      echo 'index';
    }

    public function show(int $id ) {
        dd($id);
    }

    #[Post('/')]
    public function store(array $data) {
        dd($data);
    }

    #[Get('/:id/edit')]
    public function edit(int $id) {
        dd($id);
    }
    #[Put('/:id')]
    public function update(int $id ,array $data) {
        dd($id);
    }
    #[Delete('/')]
    public function delete( ) {
        dd(__METHOD__);
    }
}