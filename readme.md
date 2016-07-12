
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

Este paquete asume que la tabla de usuarios es llamada "users". Si no es el caso, se debe editar manualmente en la migracion que se importe con el comando anterior.

Se pueden cambiar los nombre de las tablas que vienen por defecto, a tráves del archivo laravel-permission.php que se publica dentro del config de la aplicacion.

Luego hay que importar las migraciones 

```bash
php artisan migrate
```

## Uso

Primero hay que agregar el trait en el modelo usuario `Galpa\Permission\Traits\HasPermission;`.

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasPermission;
    
    // ...
}
```

HasRoles añade relaciones de eloquent a los modelos, se puede acceder directamente o utilizarse como una consulta base.

```php
$permissions = $user->permissions;
$roles = $user->roles()->pluck('name');
```

### Usando Permisos

Un permiso puede ser dado a un usuario de la siguiente manera:

```php
$user->givePermissionTo('edit articles');
```

Para revocar un permiso: 

```php
$user->revokePermissionTo('edit articles');
```

Para probar si un usuario tiene un permiso: 

```php
$user->hasPermissionTo('edit articles');
```

Los permisos son registrados dentro de la clase `Illuminate\Auth\Access\Gate`. Es decir tambien se puede probar si un usuario tiene un permiso con la funcion por defecto de laravel `can`.

```php
$user->can('edit articles');
```

### Chequear Autorización
Cuando se utiliza el middleware sin ningún parámetro, solo se permitirá a los usuarios registrados para utilizar la ruta.
Si usted planea usar el middleware como esto te recomiendo que sustituya el `middleware auth` estándar con el proporcionado por este paquete.
```php
// Solo los usuariosas registrados pueden ver esto

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


###Usando Directevas de Blade 

```php
@role('writer')
I'm a writer!
@else
I'm not a writer...
@endrol

```php
@hasrole('writer')
I'm a writer!
@else
I'm not a writer...
@endhasrole
```

```php
@hasanyrole(Role::all())
I have one or more of these roles!
@else
I have none of these roles...
@endhasanyrole
```

```php
@hasallroles(Role::all())
I have all of these roles!
@else
I don't have all of these roles...
@endhasallroles


@permission('viewAdmin')
	I have permission to viewAdmin
@endpermission


