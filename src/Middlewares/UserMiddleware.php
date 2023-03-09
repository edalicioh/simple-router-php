<?php 

namespace Edalicio\DependencyInjection\Middlewares;
use Edalicio\DependencyInjection\Core\Interfaces\IMiddleware;


class UserMiddleware implements IMiddleware {
    public function handle() {
        if (false) {       
            echo __METHOD__ . PHP_EOL;
            exit();
        }
    }
}