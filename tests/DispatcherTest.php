<?php


namespace QuickRoute\Tests;


use FastRoute\Dispatcher\CharCountBased;
use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Router\Collector;
use QuickRoute\Router\Dispatcher;

class DispatcherTest extends TestCase
{
    public function testDispatching(): void
    {
        $testy = function (): void {
            print time();
        };

        Route::restart();
        Route::get('/', $testy);

        $collector = Collector::create()->collect();
        $routeData = $collector->getCollectedRoutes()[0];
        $collector->register();
        $result = Dispatcher::create($collector)->dispatch('get', '/');

        self::assertSame($routeData, $result->getRoute()->getData());
    }

    public function testUnregisteredCollector(): void
    {
        Route::restart();
        Route::get('/', 'hello');

        $collector = Collector::create()->collect();
        $routeData = $collector->getCollectedRoutes()[0];
        $result = Dispatcher::create($collector)->dispatch('get', '/');
        self::assertSame($routeData, $result->getRoute()->getData());
    }

    public function testUsedDispatcher(): void
    {
        Route::restart();
        Route::get('/', 'hello');

        $collector = Collector::create()->collect()->register();
        $routeData = $collector->getCollectedRoutes()[0];

        $result = Dispatcher::create($collector)
            ->setDispatcher(CharCountBased::class)
            ->dispatch('get', '/');

        self::assertSame($routeData, $result->getRoute()->getData());
    }

    public function testTrailingSlash(): void
    {
        Route::restart();
        Route::get('/user/admin/', 'hello');

        $collector = Collector::create()->collect()->register();

        $result = Dispatcher::create($collector)
            ->dispatch('get', '/user/admin');

        self::assertTrue($result->isFound());
    }

    public function testDoubleForwardSlash(): void
    {
        Route::restart();
        Route::get('/user/admin/', 'hello');

        $collector = Collector::create()->collect()->register();

        $result = Dispatcher::create($collector)
            ->dispatch('get', '/user//admin');

        self::assertTrue($result->isFound());
    }

    public function testBadAmpersand(): void
    {
        Route::restart();
        Route::get('/amp', 'hello');

        $collector = Collector::create()->collect()->register();

        $result = Dispatcher::create($collector)
            ->dispatch('get', '/amp&a=3&r=4');

        self::assertTrue($result->isFound());
    }

    public function testCollectorMethods(): void
    {
        Route::restart();
        Route::get('users/profile', 'Controller@index');
        $result = Dispatcher::collectRoutes()
            ->dispatch('get', 'users/profile');

        $result1 = Dispatcher::collectRoutesFile(__DIR__ . '/routes-1.php')
            ->dispatch('post', '/user/save');

        self::assertSame('/users/profile', $result->getRoute()->getPrefix());
        self::assertSame('/user/save', $result1->getRoute()->getPrefix());
    }

    public function testHelpers(): void
    {
        Route::restart();
        Route::get('/', 'Controller@method')->name('home');
        Route::get('/users/{id}/{name}', 'HelloController')
            ->whereAlpha('id')
            ->name('user.info');

        $result = Dispatcher::collectRoutes()->dispatch('get', '/');
        $uri = $result->uri('user.info', [
            'id' => 1,
            'name' => 'ahmard'
        ]);

        $route = $result->route('home');

        self::assertTrue($result->isFound());
        self::assertIsArray($route);
        self::assertSame('Controller@method', $route['handler'] ?? null);
        self::assertSame('/users/1/ahmard', $uri);
    }

}