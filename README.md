# PSR-11: Container

Install with Composer:
```bash
$ composer install drhino\container
```

<br />

Example:
```php
use drhino\Container\Container;

$container = new Container;

$container->set('StdClass', StdClass:class);

// Constructs the instance when requested
// The same instance is returned once created
$myObject = $container->get('StdClass');
```

<br />

Read and delete:
```php
$container = new Container;

$container->set('myArray', ['config' => 'secret']);

$myArray = $container->readAndDelete('myArray');

// Returns false
$container->has('myArray');

// Throws drhino\Container\Exception\ContainerNotFoundException
$container->get('myArray');
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
$container->set('StdClass', StdClass:class);
$container->set('MyClass', MyClass::class);

$container->get('MyClass')->something();
```
