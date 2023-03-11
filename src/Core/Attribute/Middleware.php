<?php 

namespace Edalicio\SimpleRouter\Core\Attribute;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class Middleware {
    public function __construct(public string|array $name) {}
}