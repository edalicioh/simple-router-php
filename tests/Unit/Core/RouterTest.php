<?php
use Edalicio\SimpleRouter\Core\Attribute\Controller;
use Edalicio\SimpleRouter\Core\Attribute\Middleware;
use Edalicio\SimpleRouter\Core\Attribute\Route;
use Edalicio\SimpleRouter\Core\Router;


class AuthMiddlewareMock {
    public function handle() {
        if (false) {       
            echo "dfsd" ;
            exit();
        }
    }
}


#[Controller('HomeController')]
#[Middleware([AuthMiddlewareMock::class])]
class HomeControllerMock
{

    #[Route('/', 'GET')]
    public function index()
    {
        return __METHOD__;
    }
    #[Route('/:id', 'GET')]
    public function show($id)
    {
        return $id;
    }
    #[Route('/', 'POST')]
    public function store()
    {
        return __METHOD__;
    }

    #[Route('/:id/edit', 'GET')]
    public function edit(int $id)
    {
        return __METHOD__;
    }
    #[Route('/:id', 'PUT')]
    public function update()
    {
        return __METHOD__;
    }
    #[Route('/:id', 'DELETE')]
    public function delete()
    {
        return __METHOD__;
    }
}

it('find route', function ($requestMethod, $requestUri, $expectedRoute) {
    $router = new Router(requestMethod: $requestMethod, requestUri: $requestUri);

    $controllers = [
        HomeControllerMock::class,
    ];

    $router->setRoute($controllers);

    $actualRoute = $router->findRoute();
  
    $this->assertEquals($expectedRoute['method'], $actualRoute['method']);
    $this->assertEquals($expectedRoute['action'], $actualRoute['action']);
    $this->assertEquals($expectedRoute['middlewares'], $actualRoute['middlewares']);

})->with([
        'GET::index' => [
            'GET',
            '/',
            [
                'uri' => '/',
                'method' => "GET",
                'controller' => HomeControllerMock::class,
                'action' => "index",
                'actionParameters' => [],
                'params' => [],
                'middlewares' => [AuthMiddlewareMock::class],
            ]
        ],
        "GET::show" => [
            'GET',
            '/1',
            [
                'uri' => '/:id',
                'method' => "GET",
                'controller' => HomeControllerMock::class,
                'action' => "show",
                'actionParameters' => (new \ReflectionClass( HomeControllerMock::class ))->getMethods()[1]->getParameters(),
                'params' => ['id' => '1'],
                'middlewares' => [AuthMiddlewareMock::class],
            ]
        ],
        'GET::edit' => [
            'GET',
            '/1/edit',
            [
                'uri' => '/:id/edit',
                'method' => "GET",
                'controller' => HomeControllerMock::class,
                'action' => "edit",
                'actionParameters' => (new \ReflectionClass( HomeControllerMock::class ))->getMethods()[1]->getParameters(),
                'params' => ['id' => '1'],
                'middlewares' => [AuthMiddlewareMock::class],
            ]
        ],
        'POST::store'=>[
            'POST',
            '/',
            [
                'uri' => '/',
                'method' => "POST",
                'controller' => HomeControllerMock::class,
                'action' => "store",
                'actionParameters' => [],
                'params' => [],
                'middlewares' => [AuthMiddlewareMock::class],
            ]
        ],
        'PUT::update'=>[
            'PUT',
            '/1',
            [
                'uri' => '/:id',
                'method' => "PUT",
                'controller' => HomeControllerMock::class,
                'action' => "update",
                'actionParameters' => (new \ReflectionClass( HomeControllerMock::class ))->getMethods()[4]->getParameters(),
                'params' => ['id' => '1'],
                'middlewares' => [AuthMiddlewareMock::class],
            ]
        ],
        "DELETE::delete"=>[
            "DELETE",
            '/1',
            [
                'uri' => '/:id',
                'method' => "DELETE",
                'controller' => HomeControllerMock::class,
                'action' => "delete",
                'actionParameters' => (new \ReflectionClass( HomeControllerMock::class ))->getMethods()[5]->getParameters(),
                'params' => ['id' => '1'],
                'middlewares' => [AuthMiddlewareMock::class],
            ]
        ],
    ]);


it("Rum", function ($requestMethod, $requestUri, $expected)
{
    $router = new Router(requestMethod: $requestMethod, requestUri: $requestUri);

    $controllers = [
        HomeControllerMock::class,
    ];

    $actualRoute = $router->run( $controllers );
    
    $this->assertEquals( $expected, $actualRoute);

})->with([
    ['GET' , '/' , "HomeControllerMock::index"],
    ['GET' , '/1' , "1"]
]);

