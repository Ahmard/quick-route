<?php


namespace QuickRoute\Tests;


use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Route\Getter;

class RouteRegisterTest extends TestCase
{
    protected function setUp(): void
    {
        Route::restart();
    }

    protected function createTheRoute(string $delimiter = '/'): TheRouteFactory
    {
        Getter::create()->prefixDelimiter($delimiter);
        return (new TheRouteFactory());
    }

    protected function getRouteData(): array
    {
        return Getter::create()->get(Route::getRoutes());
    }

    public function testPrefix(): void
    {
        $theRoute = $this->createTheRoute();
        $theRoute->prefix('hello');
        $theRoute->name('name');
        $theRoute->middleware('middleware');
        $theRoute->namespace('Name\Space');
        $theRoute->addField('test', 'success');

        $routeData = $theRoute->getRouteData();
        $this->assertEquals('hello', $routeData['prefix']);
        $this->assertEquals('name', $routeData['name']);
        $this->assertEquals('middleware', $routeData['middleware']);
        $this->assertEquals('Name\Space\\', $routeData['namespace']);
    }

    public function testAppend(): void
    {
        Route::append('earth')->group(function (){
            Route::get('planets', fn() => print time());
        });

        $routeData = Getter::create()->get(Route::getRoutes())[0];
        $this->assertEquals('/planets/earth', $routeData['prefix']);
    }

    public function testPrepend(): void
    {
        Route::restart();
        Route::prepend('galaxies')->group(function (){
            Route::get('milkyway', fn() => print time());
        });

        $routeData = Getter::create()->get(Route::getRoutes())[0];
        $this->assertEquals('/galaxies/milkyway', $routeData['prefix']);
    }

    public function testGroup(): void
    {
        Route::restart();
        Route::prefix('one')->group(function (){
            Route::get('route', fn() => time());
        });

        Route::prefix('start')->append('end')
            ->group(function (){
                Route::get('middle', fn() => time());
            });

        Route::prefix('middle')->prepend('start')
            ->group(function (){
                Route::get('end', fn() => time());
            });

        Route::name('planets.')
            ->group(function (){
                Route::get('earth', fn() => time())->name('earth');
            });

        $routeData = $this->getRouteData();
        $this->assertEquals('/one/route', $routeData[0]['prefix']);
        $this->assertEquals('/start/middle/end', $routeData[1]['prefix']);
        $this->assertEquals('/start/middle/end', $routeData[2]['prefix']);
        $this->assertEquals('planets.earth', $routeData[3]['name']);
    }

    public function testRequestMethods(): void
    {
        $theRoute = $this->createTheRoute();
        //GET method
        $theRoute->get('user', fn() => print time());
        $routeData = $theRoute->getRouteData();
        $this->assertEquals('GET', $routeData['method']);
        $this->assertEquals('user', $routeData['prefix']);

        //POST method
        $theRoute->post('create', fn() => print time());
        $routeData = $theRoute->getRouteData();
        $this->assertEquals('POST', $routeData['method']);
        $this->assertEquals('create', $routeData['prefix']);

        //DELETE method
        $theRoute->delete('1', fn() => print time());
        $routeData = $theRoute->getRouteData();
        $this->assertEquals('DELETE', $routeData['method']);
        $this->assertEquals('1', $routeData['prefix']);

        //PUT method
        $theRoute->put('2', fn() => print time());
        $routeData = $theRoute->getRouteData();
        $this->assertEquals('PUT', $routeData['method']);
        $this->assertEquals('2', $routeData['prefix']);

        //PATCH method
        $theRoute->patch('3', fn() => print time());
        $routeData = $theRoute->getRouteData();
        $this->assertEquals('PATCH', $routeData['method']);
        $this->assertEquals('3', $routeData['prefix']);

        //PATCH method
        $theRoute->head('test', fn() => print time());
        $routeData = $theRoute->getRouteData();
        $this->assertEquals('HEAD', $routeData['method']);
        $this->assertEquals('test', $routeData['prefix']);
    }
}