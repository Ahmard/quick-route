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
        function testy(): void
        {
            print time();
        }

        Route::restart();
        Route::get('/', 'testy');

        $collector = Collector::create()->collect();
        $routeData = $collector->getCollectedRoutes()[0];
        $collector->register();
        $result = Dispatcher::create($collector)->dispatch('get', '/');

        self::assertSame($routeData, $result->getRoute()->getData());
    }

    public function testUnregisteredCollector()
    {
        Route::restart();
        Route::get('/', 'hello');

        $collector = Collector::create()->collect();
        $routeData = $collector->getCollectedRoutes()[0];
        $result = Dispatcher::create($collector)->dispatch('get', '/');
        self::assertSame($routeData, $result->getRoute()->getData());
    }

    public function testUsedDispatcher()
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
}