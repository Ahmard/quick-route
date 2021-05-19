<?php


use QuickRoute\Route;
use QuickRoute\Router\Collector;

require 'vendor/autoload.php';

Route::get('/', ['index']);

Route::match(['get', 'post'], 'login', ['showLoginForm'])->name('login.');
Route::match(['get', 'post'], 'register', ['showRegisterForm'])->name('register.');

$collector = Collector::create()->collect()->register();
dd($collector->getCollectedRoutes());