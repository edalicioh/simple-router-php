<?php

declare(strict_types = 1);

namespace Edalicio\DependencyInjection\Core;

use Edalicio\DependencyInjection\Core\Attribute\Route;
use Edalicio\DependencyInjection\Core\Attribute\Middleware;
use Edalicio\DependencyInjection\Core\Attribute\Controller;

class Router
{

  public function __construct(
    private string $requestMethod,
    private string $requestUri,
    public array $routes = [],
    private array $middlewares = [],
    private ?object $request = null,
  )
  {
  }

  private function addRoute(string $url, string $method, string $controller, string $action, array $actionParameters,array $middlewares)
  {
    $this->routes[$controller . "::" . $action] = [
      'uri' => $url,
      'method' => $method,
      'controller' => $controller,
      'action' => $action,
      'actionParameters' => $actionParameters,
      'middlewares' => $middlewares,
    ];
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
        $route = $this->routes[$controller . "::" . $action];
        $route['params'] = $params;
        return $route;

      }
    }

    return false;
  }

  private function setMiddleware(\ReflectionMethod | \ReflectionClass $reflection)
  {
    $middlewares = [];
    $middlewareAttributes = $reflection->getAttributes(Middleware::class);
    foreach ($middlewareAttributes as $attribute) {
      $arguments = $attribute->getArguments()[0];
      if(is_array($arguments )) {
        foreach($arguments as $argument) {
          $middleware = $argument;
          $middlewares[] = new $middleware();
        }
      }
      if(is_string($arguments)){
        $middleware = $arguments;
        $middlewares[] = new $middleware();
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
        $methodAttributes = $method->getAttributes(Route::class);

        $middlewares = [...$this->setMiddleware($reflection),  ...$this->setMiddleware($method)];  

        foreach ($methodAttributes as $attribute) {

          $arguments = $attribute->getArguments();
          $uri = $this->setUriPath(arguments: $arguments, prefix: $prefix);
          $http_method = $this->setHttpMethod(arguments: $arguments);
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

      if ($a->getType() && get_class($request) == $a->getType()->getName()) {
        return $request;
      }

      if (isset($params[$a->getName()])) {
        return $params[$a->getName()];
      }

    }, $actionParameters);
  }

  public function run(array $controllers)
  {
    $this->setRoute($controllers);
    $route = $this->findRoute();

    if ($route) {
      [
        'controller' => $controller_name,
        'action' => $action_name,
        'actionParameters' => $actionParameters,
        'params' => $params,
        'middlewares' => $middlewares,

      ] = $route;

      foreach ($middlewares as $middleware) {
          $middleware->handle();
      }

      $args = $this->setArgs(actionParameters: $actionParameters, params: $params);

      $controller = new $controller_name();

      return call_user_func_array([$controller, $action_name], $args);

    } else {
      http_response_code(404);
      echo "Página não encontrada";
    }
  }
}