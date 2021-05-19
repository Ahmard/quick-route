<?php

use QuickRoute\Route;
use QuickRoute\Router\Dispatcher;

require 'vendor/autoload.php';

Route::resource('users', 'UserController', 'userId')->whereAlpha('userId');

dump(\QuickRoute\Router\Collector::create()->collect()->getCollectedRoutes());