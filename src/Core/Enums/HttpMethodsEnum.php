<?php


namespace Edalicio\SimpleRouter\Core\Enums;

enum HttpMethodsEnum: string
{
    case Get = "GET"; 
    case Post = "POST"; 
    case Put = "PUT"; 
    case Patch = "PATCH"; 
    case Delete = "DELETE"; 
    case Head = "HEAD"; 
    case Options = "OPTIONS"; 
}
