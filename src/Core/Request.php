<?php

namespace Edalicio\SimpleRouter\Core;

use Edalicio\SimpleRouter\Core\Interfaces\IRequest;

class Request implements IRequest
{
  private string $method;
  private string $path;
  private array $params;

  public function __construct()
  {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $this->setParams();
  }

  private function setParams()
  {
    $array = (array) json_decode(file_get_contents('php://input'));
    $this->params = array_merge($_GET, $_POST, $array);
  }

  public function getMethod(): string
  {
    return $this->method;
  }

  public function getPath(): string
  {
    return $this->path;
  }

  public function getParam($name): mixed
  {
    return isset($this->params[$name]) ? $this->params[$name] : null;
  }
  public function getParamAll(): array
  {
    return $this->params;
  }
}