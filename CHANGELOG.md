# QuickRoute ChangeLog

## v3.6 to v3.7
### New Methods
- RouteInterface::matchAny(array $methods, array $paths, $handler): RouteInterface - Generate multiple routes using multiple method and path but single handle
- Collector::route(string $routeName): ?array - This is to find route by its name
- Collector::uri(string $routeName): ?string - Generate route uri using route's name
- RouteData::getRegExp() - This will return regular expression defined within the route's prefix/pth 

#### RouteInterface::matchAny()
```php
use QuickRoute\Route;

Route::matchAny(
    ['get', 'post'], 
    ['/customer/login', '/admin/login'],
    'MainController@index'
);

//Which is equivalent to:
Route::get('/customer/login', 'MainController@index');
Route::post('/customer/login', 'MainController@index');
Route::get('/admin/login', 'MainController@index');
Route::post('/admin/login', 'MainController@index');
```
#### Finding route & generating route uri 

```php
use QuickRoute\Route;
use QuickRoute\Router\Collector;

Route::get('/users', 'Controller@method')->name('users.index');

$collector = Collector::create()->collect();
echo $collector->uri('users.index');  // => /users
$collector->route('users.index'); // => Array of route data
```


## v3.5 to v3.6
### New Methods
- RouteInterface::where(string|array $param, ?string $regExp = null): RouteInterface
- RouteInterface::matchAny(array $methods, array $paths, $handler): RouteInterface

```php
use QuickRoute\Route;

Route::get('/users/{id}', 'a')->where('id', '[0-9]+');
Route::get('/users/{user}/posts/{post}', 'Ctrl@method')->where([
    'user' => '[a-zA-Z]+',
    'post' => '[0-9]+'
]);
```

### Bug fixes
- Regular expression bug related to whereNumber(), whereAlpha(), whereAlphanumeric() methods has been fixed

### Changes
- RouteInterface::resource() gets one additional parameter
<br/> You can now provide id parameter name

```php
use QuickRoute\Route;

Route::resource('/users', 'UserController', 'userId', true);
// /users/{userId:[0-9]+}
```

## v3.4 to v3.5
### New Methods
- RouteInterface::whereNumber(string $param): RouteInterface
- RouteInterface::whereAlpha(string $param): RouteInterface
- RouteInterface::whereAlphanumeric(string $param): RouteInterface
- Dispatcher::collectRoutes(array $routesInfo = []): Dispatcher
- Dispatcher::collectRoutesFile(string $filePath, array $routesInfo = []): Dispatcher

```php
use QuickRoute\Route;
use QuickRoute\Router\Dispatcher;

Route::get('users/{id}', 'Controller@index')->whereNumber('id');
Route::get('users/{name}', 'Controller@profile')->whereAlpha('name');
Route::get('users/{username}', 'Controller@profile')->whereAlphaNumeric('username');

//Collect routes
Dispatcher::collectRoutes()->dispatch('get', '/');
//Collect routes in file
Dispatcher::collectRoutesFile('routes.php')->dispatch('get', '/');
```