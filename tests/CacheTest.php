<?php

namespace QuickRoute\Tests;

use PHPUnit\Framework\TestCase;
use QuickRoute\Route;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        Route::restart();
    }

    public function testCaching()
    {
        $collector = Route\Collector::create()
            ->collectFile(__DIR__ . '/routes.php')
            ->cache(__DIR__ . '/route-cache.php')
            ->register();

        $cache = Route\Cache::get(__DIR__ . '/route-cache.php');

        self::assertFileExists(__DIR__ . '/route-cache.php');
        self::assertEquals($cache, (require __DIR__ . '/route-cache.php'));
    }
}
