<?php

use QuickRoute\Route;
use QuickRoute\Router\Dispatcher;

require 'vendor/autoload.php';

use QuickRoute\Router\Collector;

Route::get('/users', 'Controller@method')->name('users.index');

$collector = Collector::create()->collect();
var_dump($collector->uri('users.index'));  // => /users
$collector->route('users.index'); // => Array of route data