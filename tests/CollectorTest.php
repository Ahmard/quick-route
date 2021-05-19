<?php

namespace QuickRoute\Tests;

use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Router\Collector;
use QuickRoute\Router\Dispatcher;
use QuickRoute\Router\RouteData;

class CollectorTest extends TestCase
{
    public function testCollection(): void
    {
        Route::get('hello/world', fn() => print "Hello world");
        Route::get('hello/planet', fn() => print "Hello planet");
        $collector = Collector::create()
            ->collect();

        $fileCollector = Collector::create()
            ->collectFile(__DIR__ . '/routes-1.php');

        $this->assertCount(2, $collector->getCollectedRoutes());
        $this->assertCount(3, $fileCollector->getCollectedRoutes());
    }

    public function testDispatcher(): void
    {
        $collector = Collector::create()
            ->collectFile(__DIR__ . '/routes-1.php');

        $collectedRoutes = $collector->getCollectedRoutes();

        $collector->register();

        $dispatchResult = Dispatcher::create($collector)
            ->dispatch('POST', 'user/save');
        self::assertTrue($dispatchResult->isFound());
        self::assertEquals($collectedRoutes[0], $dispatchResult->getRoute()->getData());
        self::assertEquals('creator', $dispatchResult->getRoute()->getName());

        $dispatchResult2 = Dispatcher::create($collector)
            ->dispatch('GET', 'user/list');
        self::assertTrue($dispatchResult2->isNotFound());

        $dispatchResult3 = Dispatcher::create($collector)
            ->dispatch('GET', 'user');
        self::assertTrue($dispatchResult3->isMethodNotAllowed());
    }

    public function testIsRegisterMethod(): void
    {
        $collector = Collector::create()
            ->collectFile(__DIR__ . '/routes-1.php');

        self::assertFalse($collector->isRegistered());

        $collector->register();

        self::assertTrue($collector->isRegistered());
    }

    public function testMultipleRouteFileCollection(): void
    {
        $collector = Collector::create()
            ->collectFile(__DIR__ . '/routes-1.php')
            ->collectFile(__DIR__ . '/routes-2.php');

        $dispatchResult1 = Dispatcher::create($collector)
            ->dispatch('POST', 'user/save');

        $dispatchResult2 = Dispatcher::create($collector)
            ->dispatch('POST', 'admin/save');

        self::assertTrue($dispatchResult1->isFound());
        self::assertTrue($dispatchResult2->isFound());
    }

    public function testHelpers(): void
    {
        Route::restart();
        Route::get('/', 'Controller@method')->name('home');
        Route::get('/users/{id}/{name}', 'HelloController')
            ->whereAlpha('id')
            ->name('user.info');

        $result = Dispatcher::collectRoutes()->dispatch('get', '/');
        $uri = $result->getCollector()->uri('user.info', [
            'id' => 1,
            'name' => 'ahmard'
        ]);

        $route = $result->getCollector()->route('home');

        self::assertTrue($result->isFound());
        self::assertInstanceOf(RouteData::class, $route);
        /**@phpstan-ignore-next-line**/
        self::assertSame('Controller@method', $route->getHandler() ?? null);
        self::assertSame('/users/1/ahmard', $uri);
    }

    protected function setUp(): void
    {
        Route::restart();
    }
}
