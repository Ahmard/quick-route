<?php
namespace QuickRoute\Tests;

if (! function_exists('QuickRoute\Tests\printer')){
    function printer(){
        //dummy
    }
}

\QuickRoute\Route::post('user/save', 'QuickRoute\Tests\printer')->name('creator');
\QuickRoute\Route::patch('user/patch', 'QuickRoute\Tests\printer');
\QuickRoute\Route::delete('user', 'QuickRoute\Tests\printer');