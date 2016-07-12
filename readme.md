### Que es un ACL
Una lista de control de acceso o ACL (del inglés, access control list) es un concepto de seguridad informática usado para fomentar la separación de privilegios. Es una forma de determinar los permisos de acceso apropiados a un determinado objeto, dependiendo de ciertos aspectos del proceso que hace el pedido.

Las listas de control de acceso permiten gestionar detalladamente los permisos de una aplicación de forma sencilla y escalable.

Las listas de control de acceso, o ACL, manejan principalmente dos cosas: las entidades que solicitan el control de algo y las entidades que se quiere controlar. En la jerga de ACL, las entidades que quieren controlar algo, que la mayoría de las veces son los usuarios, son los ARO (en inglés access request objects), y las entidades del sistema que se quiere controlar, que normalmente son acciones o datos, son los ACO (en inglés access control objects). A los ARO se les llama ‘objetos’ porque quien realiza la petición no siempre es una persona; los ACO son cualquier cosa que desees controlar: desde la acción de un controlador o un servicio Web, hasta el diario en línea íntimo de tu abuela.

## Paquete

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

Tambien se puede importar solo el archivo de configuracion con:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"
```

O solo el de migraciones: 

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
```

Este paquete asume que la tabla de usuarios es llamada "users". Si no es el caso, se debe editar manualmente en la migracion que se importe con el comando anterior.

Se pueden cambiar los nombre de las tablas que vienen por defecto, a tráves del archivo laravel-permission.php que se publica dentro del config de la aplicacion.

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authorization Models
    |--------------------------------------------------------------------------
    */

    'models' => [

        /*
        |--------------------------------------------------------------------------
        | Permission Model
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | Eloquent model should be used to retrieve your permissions. Of course, it
        | is often just the "Permission" model but you may use whatever you like.
        |
        | The model you want to use as a Permission model needs to implement the
        | `Spatie\Permission\Contracts\Permission` contract.
        |
        */

        'permission' => Galpa\Permission\Models\Permission::class,

        /*
        |--------------------------------------------------------------------------
        | Role Model
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | Eloquent model should be used to retrieve your roles. Of course, it
        | is often just the "Role" model but you may use whatever you like.
        |
        | The model you want to use as a Role model needs to implement the
        | `Spatie\Permission\Contracts\Role` contract.
        |
        */

        'role' => Galpa\Permission\Models\Role::class,

        /*
        |--------------------------------------------------------------------------
        | Groups Permission Model
        |--------------------------------------------------------------------------        
        |
        */

        'group' => Galpa\Permission\Models\PermissionGroup::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Tables
    |--------------------------------------------------------------------------
    */

    'table_names' => [

        /*
        |--------------------------------------------------------------------------
        | Users Table
        |--------------------------------------------------------------------------
        |
        | The table that your application uses for users. This table's model will
        | be using the "HasRoles" and "HasPermissions" traits.
        |
        */
        'users' => 'users',


        /*
        |--------------------------------------------------------------------------
        | Roles Table
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | table should be used to retrieve your roles. We have chosen a basic
        | default value but you may easily change it to any table you like.
        |
        */

        'roles' => 'roles',

        /*
        |--------------------------------------------------------------------------
        | Permissions Table
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | table should be used to retrieve your permissions. We have chosen a basic
        | default value but you may easily change it to any table you like.
        |
        */

        'permissions' => 'permissions',

        /*
        |--------------------------------------------------------------------------
        | Group Permissions Table
        |--------------------------------------------------------------------------
        |
        */

        'permission_groups' => 'permission_groups',

        /*
        |--------------------------------------------------------------------------
        | User Permissions Table
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | table should be used to retrieve your users permissions. We have chosen a
        | basic default value but you may easily change it to any table you like.
        |
        */

        'user_has_permissions' => 'user_has_permissions',

        /*
        |--------------------------------------------------------------------------
        | User Roles Table
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | table should be used to retrieve your users roles. We have chosen a
        | basic default value but you may easily change it to any table you like.
        |
        */

        'user_has_roles' => 'user_has_roles',

        /*
        |--------------------------------------------------------------------------
        | Role Permissions Table
        |--------------------------------------------------------------------------
        |
        | When using the "HasRoles" trait from this package, we need to know which
        | table should be used to retrieve your roles permissions. We have chosen a
        | basic default value but you may easily change it to any table you like.
        |
        */

        'role_has_permissions' => 'role_has_permissions',

    ],

    /*
     * The path to redirect for login.
     */
    'login_url' => 'auth/login',

    /*
     * Active
     */
    'active' => true,

    /*
     * Default Actions for New Permissions Groups
     */
    'actions' => [
        'create','update','read','delete','excel','pdf','print','refresh'
    ]

];
```

Luego hay que importar las migraciones 

```bash
php artisan migrate
```

Eso creara las tablas dentro de la base de Datos para poder utilizar el paquete, sin necesidad de modificar nada.

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

Entre otros...

### Roles

En caso de que se necesiten roles para trabajar, tenemos lo siguiente:

Asigar in rol a un Usuario
```php
$user->assignRole('writer');
```

Quitar Rol al Usuario
```php
$user->removeRole('writer');
```

Determinar si un Usuario tiene un rol
```php
$user->hasRole('writer');
```

Entre otros...

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


###Usando Directivas de Blade 

El paquete nos brinda de algunas etiquetas que podemos utilizar en Blade para ahorrarnos el trabajo en las vistas:

```php
@role('writer')
I'm a writer!
@else
I'm not a writer...
@endrol
```

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
```
```php
@permission('viewAdmin')
	I have permission to viewAdmin
@endpermission
```

## Errores y Seguridad
Si de casualidad descubre algun error funcional, o alguna fuga de seguridad, no dude en comunicarse con sojeda@galpservices.com

## Creditos
Este paquete esta basado en [Spatie/laravel-permission](https://github.com/spatie/laravel-permission)
