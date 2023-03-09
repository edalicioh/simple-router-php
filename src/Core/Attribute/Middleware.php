<?php 

namespace Edalicio\DependencyInjection\Core\Attribute;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
#[Attribute(Attribute::TARGET_METHOD)]
class Middleware {
    public function __construct(public string|array $name) {}
}