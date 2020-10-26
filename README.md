# QuickRoute
An elegant http router built on top of [FastRoute](https://github.com/nikic/FastRoute) to provide more easy of use.


## Installation
```bash
composer require ahmard/quick-route
```

## Usage

Simple example
```php
use QuickRoute\Route;
use QuickRoute\Route\Collector;

Route::get('/', function (){
    echo 'Hello world';
});

$collector = Collector::create()
    ->collect()
    ->register();

$routes = $collector->getCollectedRoutes();
```

Controller-like example
```php
use QuickRoute\Route;

Route::get('/home', 'MainController@home');
```

Advance usage
```php
use QuickRoute\Route;
use QuickRoute\RouteInterface;

Route::prefix('user')->name('user.')
    ->namespace('User')
    ->middleware('UserMiddleware')
    ->group(function (RouteInterface $route){
        $route->get('profile', 'UserController@profile');
        $route->put('update', 'UserController@update');
    });
```

More advance usage
```php
use QuickRoute\Route;
use QuickRoute\RouteInterface;

Route::prefix('notes')->name('notes.')
    ->prepend('api')
    ->append('{token}')
    ->namespace('User')
    ->group(function (RouteInterface $route){
        $route->post('add', 'NotesController@add')->name('add');
        $route->put('{noteId}', 'NotesController@update')->name('update');
    });
    
```

Routes as configuration
```php
//routes.php
use QuickRoute\Route;

Route::get('/', 'MainController@index');
Route::get('/help', 'MainController@help');


//server.php
use QuickRoute\Route\Collector;

$collector = Collector::create()
    ->collectFile('routes.php')
    ->register();

$routes = $collector->getCollectedRoutes();
```

## Licence
**QuickRoute** is _MIT_ licenced.