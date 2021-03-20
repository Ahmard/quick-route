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

    public function testDoubleForwardSlash()
    {
        Route::restart();
        Route::get('/user/admin/', 'hello');

        $collector = Collector::create()->collect()->register();

        $result = Dispatcher::create($collector)
            ->dispatch('get', '/user//admin');

        self::assertTrue($result->isFound());
    }
}