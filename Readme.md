# OPA-ROUTE

![Status](https://img.shields.io/static/v1?label=status&message=development&color=critical)

## How to use
```php
<?php

use OpaRoute\Router;

$router = new Router();

$router->get('/', function () {
    return 'Hello World!';
});

$router->get('/users/{id}', 'UserController@get');

$router->get('/users/{id}/save', [\Namespace\UserController::class, 'post']);


$router->execute();
```
