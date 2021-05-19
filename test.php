<?php

use QuickRoute\Route;
use QuickRoute\Router\Dispatcher;

require 'vendor/autoload.php';

Route::prefix('school')->name('school')
    ->group(function (){
        Route::matchAny(['get', 'post'], ['/customer/login', '/admin/login'],'MainController@index')->name('login');
    });

dd(\QuickRoute\Router\Collector::create()->collect()->getCollectedRoutes());