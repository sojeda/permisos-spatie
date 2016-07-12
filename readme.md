
Este paquete nos permite guardar permisos y roles en Base de Datos. Se basa en la funcionalidad de [Autorizacion de Laravel] (http://laravel.com/docs/5.1/authorization). introducida en la versión 5.1.11.
Una vez instalado, se puede hacer cosas como esta:

```php
//Agregar Permisos a un Usuario
$user->givePermissionTo('edit articles');

// Agregar permisos via Roles
$user->assignRole('writer');
$user2->assignRole('writer');

$role->givePermissionTo('edit articles');
```
Para probar si un usuario tiene un permiso se puede utilizan la función `can`
```php
$user->can('edit articles');
```
La protección de una ruta se puede realizar agregando un middleware a la misma:

```php
Route::get('/top-secret-page', [
   'middleware'=> 'can:viewTopSecretPage',
   'uses' => 'TopSecretController@index',
]);
```
 Y por supuesto, este middleware también se puede aplicar a un grupo de rutas:
```php
Route::group(['prefix' => 'admin', 'middleware' => 'can:viewAdmin'], function() {

   // Todos los controladores de la sección Admin
   ...
   
});
```

Además  el middleware puede usar [route model binding](https://laracasts.com/series/laravel-5-fundamentals/episodes/18):
```php
Route::get('/post/{post}', [
   'middleware'=> 'can:editPost,post',
   'uses' => 'PostController@edit'),
]);
```

## Instalación

Se puede instalar el package via composer:
``` bash
$ composer require galpa/permission
```

Luego, es necesario instalar el Service Provider

```php
// config/app.php
'providers' => [
    ...
    /*
     * Galpa Providers
     */
     Galpa\Permission\PermissionServiceProvider::class,
];
```

Al continuar, el middleware `\Galpa\Permission\Middleware\Authorize::class`- debe registrarse en el Kernell:

```php
//app/Http/Kernel.php

protected $routeMiddleware = [
  ...
  'can' => \Galpa\Permission\Middleware\Authorize::class,
];
```

EL nombre del Middleware `can` solo es una sugerencia. Puedes poner el nombre que quieras.

El middleware `authorize` incluye todas las funcionalidad del `auth`-middleware. Asi que pudieras reemplazar `App\Http\Middleware\Authenticate` por `Galpa\Permission\Middleware\Authorize`:

```php
//app/Http/Kernel.php

protected $routeMiddleware = [
    'auth' => 'Galpa\Permission\Middleware\Authorize',
    ...
];
```

Para publicar los archivos de configuración utiliza:
```bash
php artisan vendor:publish --provider="Galpa\Permission\PermissionServiceProvider"
```

## Uso

### Chequear Autorización
Cuando se utiliza el middleware sin ningún parámetro, solo se permitirá a los usuarios registrados para utilizar la ruta.
Si usted planea usar el middleware como esto te recomiendo que sustituya el `middleware auth` estándar con el proporcionado por este paquete.
```php
//only logged in users will be able to see this

Route::get('/top-secret-page', ['middleware'=> 'auth','uses' => 'TopSecretController@index']);
```

### Chequear Autorización
El middleware acepta el nombre de un permiso que ha definido como el primer parámetro:

```php
//only users with the viewTopSecretPage-ability be able to see this

Route::get('/top-secret-page', [
   'middleware'=> 'can:viewTopSecretPage',
   'uses' => 'TopSecretController@index',
]);
```

### Usando form Model Binding

```php
//inside the boot method of AuthServiceProvider

$gate->define('update-post', function ($user, $post) {
    return $user->id === $post->user_id;
});
```

El middleware acepta el nombre de un modelo como el segundo parámetro.
```php
Route::get('/post/{post}', [
   'middleware'=> 'can:editPost,post',
   'uses' => 'PostController@edit'),
]);
```





