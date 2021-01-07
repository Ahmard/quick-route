<?php

namespace QuickRoute\Tests;

use PHPUnit\Framework\TestCase;
use QuickRoute\Route;

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

        $this->assertEquals('/', Route\Getter::getDelimiter());

        $routeData = Route\Getter::create()
            ->prefixDelimiter('.')
            ->get(Route::getRoutes());

        $this->assertEquals('.', Route\Getter::getDelimiter());
        $this->assertEquals('.planets.earth', $routeData[0]['prefix']);
    }
}
