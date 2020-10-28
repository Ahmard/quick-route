# QuickRoute
An elegant http router built on top of [FastRoute](https://github.com/nikic/FastRoute) to provide more easy of use.

## Information
Due to object-sharing between routes introduced in version 1, some errors cannot be fixed.
<br/>
Here is version 2, which is object-sharing free.

## Installation
```bash
composer require ahmard/quick-route
```

## Usage

Simple example
```php
use QuickRoute\Route;
use QuickRoute\Route\Collector;
use QuickRoute\Route\Dispatcher;

require('vendor/autoload.php');

Route::get('/', function () {
    echo 'Hello world';
});

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($path, '?')) {
    $uri = substr($path, 0, $pos);
}
$path = rawurldecode($path);


$collector = Collector::create()->collect()->register();

$dispatcher = Dispatcher::create($collector)->dispatch($method, $path);

switch (true) {
    case $dispatcher->isFound():
        $routeData = $dispatcher->getRoute();
        $routeData['controller']($dispatcher->getUrlParameters());
        break;
    case $dispatcher->isNotFound():
        echo "Page not found";
        break;
    case $dispatcher->isMethodNotAllowed():
        echo "Request method not allowed";
        break;
}
```

Controller-like example
```php
use QuickRoute\Route;

Route::get('/home', 'MainController@home');
```

Advance usage
```php
use QuickRoute\Route;

Route::prefix('user')->name('user.')
    ->namespace('User')
    ->middleware('UserMiddleware')
    ->group(function (){
        Route::get('profile', 'UserController@profile');
        Route::put('update', 'UserController@update');
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