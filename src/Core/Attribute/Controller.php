<?php
namespace Edalicio\SimpleRouter\Core\Attribute;
use Attribute;

// definir um atributo de controlador que armazena o nome da classe do controlador
#[Attribute(Attribute::TARGET_CLASS)]
class Controller {
    public function __construct(public string $name, public string $prefix = null) {}
}