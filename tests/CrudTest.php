<?php

namespace QuickRoute\Tests;

use QuickRoute\Crud;
use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Router\Collector;

class CrudTest extends TestCase
{
    public function testBasic(): void
    {
        Route::restart();
        Crud::create('users', 'UserController')->go();

        $routes = Collector::create()
            ->collect()
            ->getCollectedRoutes();

        self::assertSame('GET', $routes[0]['method']);
        self::assertSame('/users', $routes[0]['prefix']);

        self::assertSame('POST', $routes[1]['method']);
        self::assertSame('/users', $routes[1]['prefix']);

        self::assertSame('DELETE', $routes[2]['method']);
        self::assertSame('/users', $routes[2]['prefix']);

        self::assertSame('GET', $routes[3]['method']);
        self::assertSame('/users/{id}', $routes[3]['prefix']);

        self::assertSame('PUT', $routes[4]['method']);
        self::assertSame('/users/{id}', $routes[4]['prefix']);

        self::assertSame('DELETE', $routes[5]['method']);
        self::assertSame('/users/{id}', $routes[5]['prefix']);
    }

    public function testRouteDisabling(): void
    {
        Route::restart();

        Crud::create('/', 'Controller')
            ->disableIndexRoute()
            ->disableStoreRoute()
            ->disableDestroyAllRoute()
            ->disableShowRoute()
            ->disableUpdateRoute()
            ->disableDestroyRoute()
            ->go();

        self::assertCount(
            0,
            Collector::create()
                ->collect()
                ->getCollectedRoutes()
        );
    }
}
