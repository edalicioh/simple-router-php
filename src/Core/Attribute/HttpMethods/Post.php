<?php

namespace Edalicio\DependencyInjection\Core\Attribute\HttpMethods;

use Attribute;
use Edalicio\DependencyInjection\Core\Attribute\Route;
use Edalicio\DependencyInjection\Core\Enums\HttpMethodsEnum;


#[Attribute(Attribute::TARGET_METHOD)]
class Post extends Route
{
    public function __construct(public string $uri)
    {
        parent::__construct(uri: $uri, method: HttpMethodsEnum::Post );
    }
}