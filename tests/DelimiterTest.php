<?php

namespace QuickRoute\Tests;

use PHPUnit\Framework\TestCase;
use QuickRoute\Route;
use QuickRoute\Router\Getter;

class DelimiterTest extends TestCase
{
    protected function setUp(): void
    {
        Route::restart();
    }

    public function testPrefix(): void
    {
        Route::prefix('planets')->group(function (){
            Route::get('earth', fn() => time());
        });

        $this->assertEquals('/', Getter::getDelimiter());

        $routeData = Getter::create()
            ->prefixDelimiter('.')
            ->get(Route::getRoutes());

        $this->assertEquals('.', Getter::getDelimiter());
        $this->assertEquals('.planets.earth', $routeData[0]['prefix']);
    }
}
