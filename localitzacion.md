# Laravel 5 Multilingual Models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/themsaid/laravel-multilingual.svg?style=flat-square)](https://packagist.org/packages/themsaid/laravel-multilingual)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/themsaid/laravel-multilingual.svg?style=flat-square)](https://packagist.org/packages/themsaid/laravel-multilingual)


Este paquete de Laravel ofrece atributos traducibles para Modelos de Eloquent sin la necesidad de separar las tablas de la Base de Datos para los valorers de traducción o crear varios cambios dentro de las tablas de los modelos. 

Simplemente llamando a `$country->name` se puede obtener un valor basado en la localizacion o idioma actual de la aplicación.

Tambien se puede llamar a `$country->nameTranslations->en` para obtener el valor de una configuracion regional espesifica.

Por ultimo se pueden comprobar todas las traducciones de un determinado atributo tan fácil como llamar a `$country->nameTranslations->toArray()`.

## Beneficios

* Facilidad de Uso.
* Rapidez en la Busqueda de Datos (es sumamente más rapido buscar dentro de un registro, que buscar en multiples registros).
* Mejora la integridad de la Base de Datos
* Evita la necesidad de hacer querys complejos solo para obtener un dato de un idioma (Resuelvo problema N +1)

## Instalacion

Puede comenzar instalando el paquete a traves de Composer ejecutando el siguiente comando en la terminal:

```
composer require themsaid/laravel-multilingual
```

Cuando este listo agregue el service provider del paquete dentro del archivo `config/app.php`

```
Themsaid\Multilingual\MultilingualServiceProvider::class
```

Finalmente ejecute el comando publish para importar el archivo de congiruacion.

```
php artisan vendor:publish
```

Eso es todo, puede ahora a comenzar las utilidades.

# Uso

Lo primero que necesita para asegurarse de que los atributos sean traducibles es tener un tipo de campo de texto o MySQL JSON, si está construyendo la base de datos desde un archivo de migración que puede hacer esto:

```php
<?php

Schema::create('countries', function (Blueprint $table)
{
	$table->increments('id');
	$table->json('name');
});
```

Ahora que tiene la base de datos para guardar una cadena JSON, es necesario preparar los modelos:

```php
<?php

class Country extends Model
{
    use Themsaid\Multilingual\Translatable;

    protected $table = 'countries';
    public $translatable = ['name'];
    public $casts = ['name' => 'array'];
}
```

- Agregar el trait `Translatable` para la clase del modelo.
- Agregar el atributo `punlic $translatable` con una matriz con los nombre de los campos que tienen traducción.
- Recordar que debes colocar los atributos traducibles como array en la propiedad `$casts` del modelo.

Ahora, nuestro modelo tiene `name` como un campo traducible, por lo que en la creacion del nuevo modelo se puede espesificar e campo name de la siguiente manera.

```php
<?php

Country::create([
	'name' => [
		'en' => "Spain",
		'sp' => 'España'
	]
]);
```

Se va a convertir automáticamente a una cadena JSON y lo guarda en el campo name de la base de datos, se puede recuperar posteriormente el nombre de la siguiente manera:

```
$country->name
```

Esto devolvera el nombre del país en la base de datos segun la configuracion de localizacion que este definida, en caso de que este no tenga ningun valor se usara el `fallback_locale` que se haya colocado en el fichero de configuración.

En caso de que no se puede encontrar se devolverá una cadena vacía.

También es posible que desee devolver el valor para una configuración regional específica, puede hacerlo utilizando la siguiente sintaxis:

```
$country->nameTranslations->en
```

Esto devolverá el nombre en Inglés del país (country).

Para devolver una matriz de todos los idiomas disponibles se puede utilizar:

```
$country->nameTranslations->toArray()
```

# Validaciones

Puede utilizar las nuevas funciones de validación de matriz de Laravel 5.2 para validar la presencia de los lugares específicos:

```php
<?php

$validator = Validator::make(
    ['name' => ['en'=>'One', 'sp'=>'Uno']],
    ['name.en' => 'required']
);
```

Sin embargo se incluye una regla de validación en este paquete que se ocupa de todas las validaciones que requieran:

```php
<?php

$validator = Validator::make(
    ['name' => ['en'=>'One', 'sp'=>'Uno']],
    ['name' => 'translatable_required']
);
```

La regla de `translatable_required` se asegurará de que todos los valores de los locales disponibles se establezcan.

Se pueden definir los tipos de traducciones disponibles asi como el fallback locale desde el archivo de configuracion del paquete.

Ahora solo se tiene que añadir el mensaje traducido de nuestra nueva regla de validacion, agregando esto al archivo de traduccion `validation.php`:

```
'translatable_required' => 'The :attribute translations must be provided.',
```

# Queries
Si estás usando MySQL 5.7 o superior, se recomienda que utilice el tipo de datos JSON para las traducciones de en la base de datos, esto le permitirá consultar estas columnas como esta:

```php
Company::whereRaw('name->"$.en" = \'Monsters Inc.\'')->orderByRaw('specs->"$.founded_at"')->get();
```

Sin embargo, en laravel 5.2.23 y superiores se puede utilizar la sintaxis con fluidez:

```php
Company::where('name->en', 'Monsters Inc.')->orderBy('specs->founded_at')->get();

```
