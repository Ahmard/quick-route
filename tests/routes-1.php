<?php

namespace QuickRoute\Tests;

use QuickRoute\Route;

if (!function_exists('QuickRoute\Tests\printer')) {
    function printer(): void
    {
        //dummy
    }
}

Route::post('user/save', 'QuickRoute\Tests\printer')->name('creator');
Route::patch('user/patch', 'QuickRoute\Tests\printer');
Route::delete('user', 'QuickRoute\Tests\printer');