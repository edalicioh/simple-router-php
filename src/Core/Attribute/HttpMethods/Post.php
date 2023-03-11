<?php

namespace Edalicio\SimpleRouter\Core\Attribute\HttpMethods;

use Attribute;
use Edalicio\SimpleRouter\Core\Attribute\Route;
use Edalicio\SimpleRouter\Core\Enums\HttpMethodsEnum;


#[Attribute(Attribute::TARGET_METHOD)]
class Post extends Route
{
    public function __construct(public string $uri)
    {
        parent::__construct(uri: $uri, method: HttpMethodsEnum::Post );
    }
}