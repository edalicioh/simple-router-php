<?php
namespace Edalicio\DependencyInjection\Core\Attribute;
use Attribute;

// definir um atributo de rota que armazena o padrão de URI e o método HTTP
#[Attribute(Attribute::TARGET_METHOD)]
class Route {
    public function __construct(public string $uri, public string $method) {}
}