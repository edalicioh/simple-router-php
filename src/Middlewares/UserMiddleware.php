<?php 

namespace Edalicio\SimpleRouter\Middlewares;
use Edalicio\SimpleRouter\Core\Interfaces\IMiddleware;


class UserMiddleware implements IMiddleware {
    public function handle() {
        if (false) {       
            echo __METHOD__ . PHP_EOL;
            exit();
        }
    }
}