<?php 

namespace Edalicio\DependencyInjection\Core\Interfaces;

interface IRequest {
    public function getMethod():string ;
    public function getPath():string ;
    public function getParam(string $name):mixed ;
    public function getParamAll():array;
}