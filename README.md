# PSR-11: Container

Install with Composer:
```bash
composer require drhino/container
```

<br />

Example:
```php
use drhino\Container\Container;

$container = new Container;

$container->set('StdClass', StdClass::class);

// Constructs the instance when requested
// The same instance is returned once created
$myObject = $container->get('StdClass');
```

<br />

Immutable:
```php
$container->set('StdClass', StdClass::class, $immutable = true);

// Throws drhino\Container\Exception\ContainerException
$container->set('StdClass', StdClass::class);
```

<br />

Immutable + Truncate:
```php
// Equivalent to: $container->set('myArray', ['config' => 'secret'], true);
$container->env('myArray', ['config' => 'secret']);

$myArray = $container->readAndDelete('myArray');

// Returns false
$container->has('myArray');

// Throws drhino\Container\Exception\ContainerNotFoundException
$container->get('myArray');

// Throws drhino\Container\Exception\ContainerException
$container->env('myArray', ['config' => 'secret']);
```

<br />

Service locator:
```php
use drhino\Container\Container;
use drhino\Container\ContainerInjector;

class MyClass extends ContainerInjector
{
    public function something(): void
    {
        $std = $this->container->get('StdClass');
    }
}

$container = new Container;
$container->set('StdClass', StdClass::class);
$container->set('MyClass', MyClass::class);

$container->get('MyClass')->something();
```
