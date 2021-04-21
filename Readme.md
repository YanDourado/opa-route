# OPA-ROUTE

![Status](https://img.shields.io/static/v1?label=status&message=development&color=critical)

## How to use
```php
<?php

// route.php

use OpaRoute\Router;

$router = new Router();

$router->get('/', function () {
    return 'Hello World!';
})->name('home');

$router->group([
    'namespace' => 'OpaRoute\Namespace\Controller',
    'prefix'    => '/users'
], function ($router) {
    $router->get('', '\UserController@index')->name('users.index');

    $router->get('/{id}', '\UserController@edit')->name('users.edit');

    $router->post('/{id}', '\UserController@save')->name('users.save');

    $router->put('/{id}', '\UserController@update')->name('users.update');

    $router->delete('/{id}', '\UserController@delete')->name('users.delete');
    
});

$router->execute();

// UserController.php

namespace OpaRoute\Namespace\Controller;

class UserController
{
    public function index()
    {
        return 'Lists users';
    }

    public function edit($id)
    {
        return 'Show user form | ID: #' . $id;
    }

    public function save($id)
    {
        return 'Save user | ID: #' . $id;
    }

    public function update($id)
    {
        return 'Update user | ID: #' . $id;
    }

    public function delete($id)
    {
        return 'Delete user | ID: #' . $id;
    }
}

```
