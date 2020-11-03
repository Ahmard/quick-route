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

#### Controller-like example
```php
use QuickRoute\Route;

Route::get('/home', 'MainController@home');
```

#### Advance usage
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

#### More Advance Usage
```php
use QuickRoute\Route;

Route::prefix('user')
    ->prepend('api')
    ->append('{token}')
    ->middleware('UserMiddleware')
    ->group(function (){
        Route::get('profile', 'UserController@profile');
        Route::put('update', 'UserController@update');
    });

// => /api/user/{token}
```

#### Routes as configuration
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

#### Caching
Caching will be updated after the file set to be collected is modified.
<br/>Caching only works with **QuickRoute\Route\Collector::collectFile** method at the moment.
```php
use QuickRoute\Route\Collector;

$collector = Collector::create()
    ->collectFile('routes.php')
    ->cache('path/to/save/cache.php', 'path/to/save/caching-definitions.json')
    ->register();

$routes = $collector->getCollectedRoutes();
```

#### Passing Default Data
You can alternatively pass data to be prepended to all routes.
<br/>
If you have caching turned on, you most clear cached routes manually after setting/updating default route data.
```php
use QuickRoute\Route\Collector;

$collector = Collector::create()->collectFile('api-routes.php', [
    'prefix' => 'api',
    'name' => 'api.',
    'namespace' => 'Api\\'
])->register();
```

## Licence
**QuickRoute** is _MIT_ licenced.