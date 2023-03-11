<?php 

namespace Edalicio\SimpleRouter\Core\Interfaces;

interface IRequest {
    public function getMethod():string ;
    public function getPath():string ;
    public function getParam(string $name):mixed ;
    public function getParamAll():array;
}