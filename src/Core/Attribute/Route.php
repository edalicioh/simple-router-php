<?php
namespace Edalicio\DependencyInjection\Core\Attribute;
use Attribute;
use Edalicio\DependencyInjection\Core\Enums\HttpMethodsEnum;
use Edalicio\DependencyInjection\Core\Interfaces\IRouter;

// definir um atributo de rota que armazena o padrão de URI e o método HTTP
#[Attribute(Attribute::TARGET_METHOD)]
class Route implements IRouter {
    public function __construct(public string $uri, public string|HttpMethodsEnum $method) {}
}