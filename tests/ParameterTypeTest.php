<?php

namespace QuickRoute\Tests;

use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Router\Collector;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class ParameterTypeTest extends TestCase
{
    public function testBasic(): void
    {
        Route::restart();
        Route::get('/users/{id}', 'MainController@index')->whereNumber('id');

        $expectedPrefix = Collector::create()
            ->collect()
            ->getCollectedRoutes()[0]['prefix'];

        assertTrue(true);
        assertSame('/users/{id:[0-9]}', $expectedPrefix);
    }

    public function testAdvance(): void
    {
        Route::restart();
        Route::prefix('users')->group(function () {
            Route::get('/', 'UserController@list');
            Route::prefix('{id}')
                ->whereNumber('id')
                ->group(function () {
                    Route::get('/', 'UserController@profile');
                    Route::get('posts/{pid}-{pTitle}', 'PostController')
                        ->whereNumber('pid')
                        ->whereAlphaNumeric('pTitle');
                });
        });

        $routes = Collector::create()
            ->collect()
            ->getCollectedRoutes();

        assertSame('/users', $routes[0]['prefix']);
        assertSame('/users/{id:[0-9]}', $routes[1]['prefix']);
        assertSame('/users/{id:[0-9]}/posts/{pid:[0-9]}-{pTitle:[a-zA-Z0-9]}', $routes[2]['prefix']);
    }
}
