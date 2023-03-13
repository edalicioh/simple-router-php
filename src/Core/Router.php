<?php

declare(strict_types=1);

namespace Edalicio\SimpleRouter\Core;

use Edalicio\SimpleRouter\Core\Attribute\Route;
use Edalicio\SimpleRouter\Core\Attribute\Middleware;
use Edalicio\SimpleRouter\Core\Attribute\Controller;
use Edalicio\SimpleRouter\Core\Enums\HttpMethodsEnum;

class Router
{

  use Traits\RouterTrait, Traits\RouterPageTrait;

  private ?string $routerName = null;
  public array $routes = [];

  public function __construct(
    private string $requestMethod,
    private string $requestUri,  
    private ?object $request = null,
  )
  {
  }

  private function addRoute(string $url, string|HttpMethodsEnum $method, ?string $controller, string|callable $action, array $actionParameters, array $middlewares)
  {

    if (!is_string($method)) {
      $method = $method->value;
    }

    $this->setRouteName($url, $method);

    $this->routes[$this->routerName] = [
      'uri' => $url,
      'method' => $method,
      'controller' => $controller,
      'action' => $action,
      'actionParameters' => $actionParameters,
      'middlewares' => $middlewares,
    ];

  }

  public function setRouteName( string $url, string $method ): void
  {
      $this->routerName = $url . "::" . $method;
  }

  public function register(string|HttpMethodsEnum $method, string $url, callable|array $action, array $middlewares = []) : self
  {

    if( is_array($action)){
      [$controller, $action] = $action;
      $actionParameters = (new \ReflectionMethod( $controller , $action))->getParameters();
    }

    if(is_callable($action)){
      $controller = '';
      $actionParameters = (new \ReflectionFunction($action))->getParameters();
    }

    if( $this->prefix ){
      $url = $this->prefix.$url;
      $this->prefix = null;
    }

    if(!empty($this->middlewares) ){
      $middlewares = [...$this->middlewares , ...$middlewares];
      $this->middlewares = [];
    }

    $this->addRoute(
      url: $url,
      method: $method,
      controller: $controller,
      action: $action,
      actionParameters: $actionParameters ?? [],
      middlewares: $middlewares
    );

    return $this;
  }
 

  public function findRoute(): array|bool
  {
    foreach ($this->routes as $key => $route) {
      list('uri' => $uri_pattern, 'method' => $method, 'controller' => $controller, 'action' => $action) = $route;

      $uri_regex = preg_replace('/:[a-zA-Z0-9_-]+/', '([a-zA-Z0-9_-]+)', $uri_pattern);
      
      // verifique se a URI atual corresponde à rota
      if ($this->requestMethod == $method && preg_match("#^$uri_regex$#", $this->requestUri, $matches)) {

        preg_match_all("~:([a-zA-Z0-9_-]+)~is", $uri_pattern, $urikeys);

        $params = [];
        for ($i = 1; $i < count($matches); $i++) {
          $params[$urikeys[1][$i - 1]] = $matches[$i];
        }
        $this->setRouteName($uri_pattern, $method);
        $route = $this->routes[$this->routerName];
        $route['params'] = $params;
        return $route;

      }
    }

    return false;
  }

  private function setMiddleware(\ReflectionMethod|\ReflectionClass $reflection)
  {
    $middlewares = [];
    $middlewareAttributes = $reflection->getAttributes(Middleware::class);
    foreach ($middlewareAttributes as $attribute) {
      $arguments = $attribute->getArguments()[0];
      if (is_array($arguments)) {
        foreach ($arguments as $argument) {
          $middlewares[] =$argument;
        }
      }
      if (is_string($arguments)) {
        $middlewares[] = $arguments;
      }


    }


    return $middlewares;
  }



  private function setPrefix(array $controllerArguments): string|null
  {
    if (!empty($controllerArguments['prefix'])) {
      return $controllerArguments['prefix'];
    }

    if (!empty($controllerArguments[1])) {
      return $controllerArguments[1];
    }

    return null;
  }

  private function setUriPath(array $arguments, string|null $prefix)
  {

    $attrUri = !empty($arguments['uri']) ? $arguments['uri'] : $arguments[0];
    if ($prefix) {
      return $prefix . $attrUri;
    }
    return $attrUri;
  }
  private function setHttpMethod(array $arguments)
  {
    return !empty($arguments['method']) ? $arguments['method'] : $arguments[1];
  }

  public function setRoute(array $controllers)
  {

    foreach ($controllers as $controller) {

      $reflection = new \ReflectionClass($controller);
      $controllerAttributes = $reflection->getAttributes(Controller::class);
      $controllerArguments = $controllerAttributes[0]->getArguments();

      $prefix = $this->setPrefix($controllerArguments);

      foreach ($reflection->getMethods() as $method) {
        $methodAttributes = $method->getAttributes(Route::class,2);
        
        $middlewares = [...$this->setMiddleware($reflection), ...$this->setMiddleware($method)];

        foreach ($methodAttributes as $attribute) {

          $arguments = $attribute->newInstance();
          $uri = $prefix . $arguments->uri;
          $http_method = $arguments->method;
          $action = $method->getName();
          $actionParameters = $method->getParameters();

          $this->addRoute(
          url: $uri,
          method: $http_method,
          controller: $controller,
          action: $action,
          actionParameters: $actionParameters,
          middlewares: $middlewares
          );
        }
      }
    }
  }

  private function setArgs(array $actionParameters, array $params): array
  {
    $request = $this->request;
    return array_map(function (\ReflectionParameter $a) use ($request, $params) {

      if (!empty($request) && $a->getType() && get_class($request) == $a->getType()->getName()) {
        return $request;
      }

      if($a->getName() == 'post') {
        $params['post'] = $_POST;
      }

      if($a->getName() == 'get') {
        $params['get'] = $_GET;
      }

      if($a->getName() == 'json') {
        $params['json'] =  (array) json_decode(file_get_contents('php://input'));
      }

      if($a->getName() == 'data') {
        $array = (array) json_decode(file_get_contents('php://input'));        
        $params['data'] =  array_merge($_GET, $_POST, $array);
      }

      if($a->getName() == 'args'){
        $params = ['args' => $params];
      }


      if (isset($params[$a->getName()])) {
        return $params[$a->getName()];
      }

      

    }, $actionParameters);
  }

  public function run(?array $controllers = null)
  {
    if( $controllers) {

      $this->setRoute($controllers);
    }


    $route = $this->findRoute();
    if ($route) {
      [
        'controller' => $controller_name,
        'action' => $action,
        'actionParameters' => $actionParameters,
        'params' => $params,
        'middlewares' => $middlewares,

      ] = $route;

      foreach ($middlewares as $middleware) {
        (new $middleware)->handle();
      }

      $args = $this->setArgs(actionParameters: $actionParameters, params: $params);


      if(is_callable($action)) {
        return call_user_func_array($action, $args);
      }

      $controller = new $controller_name();

      return call_user_func_array([$controller, $action], $args);

    } else {
      http_response_code(404);
      echo "Página não encontrada";
    }
  }

  
}