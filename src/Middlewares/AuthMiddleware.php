<?php 

namespace Edalicio\SimpleRouter\Middlewares;
use Edalicio\SimpleRouter\Core\Interfaces\IMiddleware;


class AuthMiddleware implements IMiddleware {
    public function handle() {
        if ($_SESSION['user'] != 1) {       
            http_response_code(403);
            echo "Forbidden";
            exit();
        }
    }
}