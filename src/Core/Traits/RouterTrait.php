<?php

namespace Edalicio\SimpleRouter\Core\Traits;

use Edalicio\SimpleRouter\Core\Enums\HttpMethodsEnum;

trait RouterTrait
{
    
    private ?string $prefix = null;
    private array $middlewares = [];
    public function prefix(string $path): self
    {
        $this->prefix = $path;

        if(isset($this->routes[$this->routerName])) {
            $route = $this->routes[$this->routerName];
            $route['uri'] = $this->prefix.$route['uri'];

            $this->setRouteName( $route['uri'],  $route['method']);

            $this->routes[$this->routerName] = $route;
        }
        
        return $this;
    }
    public function middleware(string|array $middleware):self
    {

        if(is_string($middleware)){
            $middleware = [$middleware];
        }
        
        if(isset($this->routes[$this->routerName])) {
            $this->routes[$this->routerName]['middlewares'] = $middleware;
        }

        $this->middlewares = $middleware;
        return $this;

    }

    public function get(string $url, callable|array $action): self
    {
        $this->register(HttpMethodsEnum::Get, $url, $action);
        return $this;
    }
    public function post(string $url, callable|array $action): self
    {
        $this->register(HttpMethodsEnum::Post, $url, $action);
        return $this;
    }
    public function put(string $url, callable|array $action): self
    {
        $this->register(HttpMethodsEnum::Put, $url, $action);
        return $this;
    }
    public function delete(string $url, callable|array $action): self
    {
        $this->register(HttpMethodsEnum::Delete, $url, $action);
        return $this;
    }



}