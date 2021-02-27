<?php


namespace QuickRoute\Tests;


use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Router\Collector;
use QuickRoute\Router\Dispatcher;
use QuickRoute\Router\Getter;

class RouteRegisterTest extends TestCase
{
    public function testPrefix(): void
    {
        $theRoute = $this->createTheRoute();
        $theRoute->prefix('hello');
        $theRoute->name('name');
        $theRoute->middleware('middleware');
        $theRoute->namespace('Name\Space');
        $theRoute->addField('test', 'success');

        $routeData = $theRoute->getData();
        $this->assertEquals('hello', $routeData['prefix']);
        $this->assertEquals('name', $routeData['name']);
        $this->assertEquals('middleware', $routeData['middleware']);
        $this->assertEquals('Name\Space\\', $routeData['namespace']);
    }

    protected function createTheRoute(string $delimiter = '/'): TheRouteFactory
    {
        Getter::create()->prefixDelimiter($delimiter);
        return (new TheRouteFactory());
    }

    public function testAppend(): void
    {
        Route::append('earth')->group(function () {
            Route::get('planets', fn() => print time());
        });

        $routeData = Getter::create()->get(Route::getRoutes())[0];
        $this->assertEquals('/planets/earth', $routeData['prefix']);
    }

    public function testPrepend(): void
    {
        Route::restart();
        Route::prepend('galaxies')->group(function () {
            Route::get('milkyway', fn() => print time());
        });

        $routeData = Getter::create()->get(Route::getRoutes())[0];
        $this->assertEquals('/galaxies/milkyway', $routeData['prefix']);
    }

    public function testGroup(): void
    {
        Route::restart();
        Route::prefix('one')->group(function () {
            Route::get('route', fn() => time());
        });

        Route::prefix('start')
            ->append('end')
            ->group(function () {
                Route::get('middle', fn() => time());
                Route::prefix('inner')->group(function () {
                    Route::get('route', fn() => printer());
                });
            });

        Route::prefix('middle')
            ->prepend('start')
            ->group(function () {
                Route::get('end', fn() => time());
            });

        Route::name('planets.')
            ->group(function () {
                Route::get('earth', fn() => time())->name('earth');
            });

        $routeData = $this->getRouteData();
        $this->assertEquals('/one/route', $routeData[0]['prefix']);
        $this->assertEquals('/start/middle/end', $routeData[1]['prefix']);
        $this->assertEquals('/start/inner/route/end', $routeData[2]['prefix']);
        $this->assertEquals('/start/middle/end', $routeData[3]['prefix']);
        $this->assertEquals('planets.earth', $routeData[4]['name']);
    }

    protected function getRouteData(): array
    {
        return Getter::create()->get(Route::getRoutes());
    }

    public function testRequestMethods(): void
    {
        $theRoute = $this->createTheRoute();
        //GET method
        $theRoute->get('user', fn() => print time());
        $routeData = $theRoute->getData();
        $this->assertEquals('GET', $routeData['method']);
        $this->assertEquals('user', $routeData['prefix']);

        //POST method
        $theRoute->post('create', fn() => print time());
        $routeData = $theRoute->getData();
        $this->assertEquals('POST', $routeData['method']);
        $this->assertEquals('create', $routeData['prefix']);

        //DELETE method
        $theRoute->delete('1', fn() => print time());
        $routeData = $theRoute->getData();
        $this->assertEquals('DELETE', $routeData['method']);
        $this->assertEquals('1', $routeData['prefix']);

        //PUT method
        $theRoute->put('2', fn() => print time());
        $routeData = $theRoute->getData();
        $this->assertEquals('PUT', $routeData['method']);
        $this->assertEquals('2', $routeData['prefix']);

        //PATCH method
        $theRoute->patch('3', fn() => print time());
        $routeData = $theRoute->getData();
        $this->assertEquals('PATCH', $routeData['method']);
        $this->assertEquals('3', $routeData['prefix']);

        //PATCH method
        $theRoute->head('test', fn() => print time());
        $routeData = $theRoute->getData();
        $this->assertEquals('HEAD', $routeData['method']);
        $this->assertEquals('test', $routeData['prefix']);
    }

    public function testMatch(): void
    {
        $fn = fn() => print time();
        Route::restart();
        Route::match(['GET', 'POST'], 'login', $fn)
            ->name('login')
            ->namespace('Auth');

        Route::match(['DELETE', 'GET'], 'user', $fn)
            ->middleware('auth')
            ->addField('test', 'field');

        $collector = Collector::create()->collect();

        $dispatchResult1 = Dispatcher::create($collector)
            ->dispatch('get', '/login');

        $dispatchResult2 = Dispatcher::create($collector)
            ->dispatch('delete', '/user');

        self::assertSame('', $dispatchResult1->getRoute()->getMiddleware());
        self::assertSame('login', $dispatchResult1->getRoute()->getName());
        self::assertSame('Auth\\', $dispatchResult1->getRoute()->getNamespace());
        self::assertSame('auth', $dispatchResult2->getRoute()->getMiddleware());
        self::assertSame([
            'test' => 'field'
        ], $dispatchResult2->getRoute()->getFields());
    }

    public function testAny(): void
    {
        $handler = 'strtoupper';
        Route::any([
            '/',
            '/login',
            '/admin/login'
        ], 'get', $handler);

        Route::create()
            ->prepend('server')
            ->any(
                ['home', 'panel'],
                'post',
                $handler
            );


        $collector = Collector::create()->collect();
        $dispatchResult1 = Dispatcher::create($collector)
            ->dispatch('get', '/');
        $dispatchResult2 = Dispatcher::create($collector)
            ->dispatch('get', '/login');
        $dispatchResult3 = Dispatcher::create($collector)
            ->dispatch('get', '/admin/login');
        $dispatchResult4 = Dispatcher::create($collector)
            ->dispatch('post', '/server/home');
        $dispatchResult5 = Dispatcher::create($collector)
            ->dispatch('post', '/server/panel');

        self::assertSame('/', $dispatchResult1->getRoute()->getPrefix());
        self::assertSame('/login', $dispatchResult2->getRoute()->getPrefix());
        self::assertSame('/admin/login', $dispatchResult3->getRoute()->getPrefix());
        self::assertSame('/admin/login', $dispatchResult3->getRoute()->getPrefix());
        self::assertSame('/server/home', $dispatchResult4->getRoute()->getPrefix());
        self::assertSame('/server/panel', $dispatchResult5->getRoute()->getPrefix());
    }

    protected function setUp(): void
    {
        Route::restart();
    }
}