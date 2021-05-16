<?php

namespace QuickRoute\Tests;

use QuickRoute\Route;

Route::post('admin/save', 'QuickRoute\Tests\printer')->name('creator');
Route::patch('admin/patch', 'QuickRoute\Tests\printer');
Route::delete('admin', 'QuickRoute\Tests\printer');