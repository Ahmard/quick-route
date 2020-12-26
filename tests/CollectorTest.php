<?php

namespace QuickRoute\Tests;

use PHPUnit\Framework\TestCase;
use QuickRoute\Route;

class CollectorTest extends TestCase
{
    protected function setUp(): void
    {
        Route::restart();
    }

    public function testCollection(): void
    {
        Route::get('hello/world', fn() => print "Hello world");
        Route::get('hello/planet', fn() => print "Hello planet");
        $collector = Route\Collector::create()
            ->collect();

        $fileCollector = Route\Collector::create()
            ->collectFile(__DIR__ . '/routes.php');

        $this->assertCount(2, $collector->getCollectedRoutes());
        $this->assertCount(3, $fileCollector->getCollectedRoutes());
    }

    public function testDispatcher(): void
    {
        $collector = Route\Collector::create()
            ->collectFile(__DIR__ . '/routes.php');

        $collectedRoutes = $collector->getCollectedRoutes();

        $collector->register();

        $dispatchResult = Route\Dispatcher::create($collector)
            ->dispatch('POST', 'user/save');
        self::assertTrue($dispatchResult->isFound());
        self::assertEquals($collectedRoutes[0], $dispatchResult->getRoute()->getData());
        self::assertEquals('creator', $dispatchResult->getRoute()->getName());

        $dispatchResult2 = Route\Dispatcher::create($collector)
            ->dispatch('GET', 'user/list');
        self::assertTrue($dispatchResult2->isNotFound());

        $dispatchResult3 = Route\Dispatcher::create($collector)
            ->dispatch('GET', 'user');
        self::assertTrue($dispatchResult3->isMethodNotAllowed());
    }
}
