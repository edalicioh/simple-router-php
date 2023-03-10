<?php 

namespace Edalicio\DependencyInjection\Controllers;

use Edalicio\DependencyInjection\Core\Attribute\Controller;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Delete;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Get;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Post;
use Edalicio\DependencyInjection\Core\Attribute\HttpMethods\Put;
use Edalicio\DependencyInjection\Core\Attribute\Route;
use Edalicio\DependencyInjection\Core\Enums\HttpMethodsEnum;

#[Controller('HomeController','/home')]
class HomeController {
    #[Get('/')]
    public function index( ) {
      echo 'index';
    }
    public function show(int $id ) {
        dd($id);
    }
    #[Post('/')]
    public function store() {
        dd("sda");
    }

    #[Get('/:id/edit')]
    public function edit(int $id) {
        dd($id);
    }
    #[Put('/')]
    public function update( ) {
        dd(__METHOD__);
    }
    #[Delete('/')]
    public function delete( ) {
        dd(__METHOD__);
    }
}