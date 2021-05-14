# QuickRoute ChangeLog

## v3.4 to v3.5
### New Methods
- RouteInterface::whereNumber(string $param): RouteInterface
- RouteInterface::whereAlpha(string $param): RouteInterface
- RouteInterface::whereAlphanumeric(string $param): RouteInterface
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
* Changes
- Collector::collect is now static method
```php
use QuickRoute\Router\Collector;

Collector::create()->collect();
//Can now be written as
Collector::collect();
```
- Collector::collectFile is now static method
```php
use QuickRoute\Router\Collector;

Collector::create()->collectFile('routes.php');
//Can now be written as
Collector::collectFile('routes.php');
```