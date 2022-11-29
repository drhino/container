# PSR-11: Container

<br />

Install with Composer:
```bash
composer require drhino/container
```

<br />

```php
/**
 * Adds or Replaces the representation of a resource.
 * Optionally, set the resource as immutable.
 *
 * @throws ContainerException when replacing an immutable $resource.
 * @throws ContainerException when a class-string does not exist.
 */
$container->set($id, $resource, $immutable);

/** 
 * Returns the $resource.
 *
 * @throws ContainerNotFoundException when the identifier does not exist.
 * @throws ContainerException when constructing a class-string failed.
 */
$container->get($id);

/**
 * Returns a boolean whether the identifier exists.
 */
$container->has($id);

/** 
 * Same as ->get() + destructs the $resource.
 */
$container->readAndDelete($id);
```

<br />

## Basic example:

```php
use drhino\Container\Container;

$container = new Container;

$container->set('database adapter', Your\Namespace\Database::class);

// Constructs the instance when requested
// The same instance is returned once created
$db = $container->get('database adapter');
```

<br />

Or keep the existing namespace as the identifier:

```php
$container->set(StdClass::class, StdClass::class);

$std = $container->get(StdClass::class);
```

<br />

Or construct the object before adding it to the container:

```php
$std = new StdClass($param);

$container->set(StdClass::class, $std);

$std = $container->get(StdClass::class);
```

<br />

Or add some data to the container:

```php
$container->set('config', ['my' => 'secret']);

$config = $container->get('config');
```

<br />

## Immutable:

> A $resource can be set to immutable. \
> Once a $resource has been assigned to the given identifier, that $resource cannot be replaced. \
> However, the $resource can still be unset by calling `->readAndDelete()` (see next example).

```php
$container->set('StdClass', StdClass::class, $immutable = true);

// Throws drhino\Container\Exception\ContainerException
$container->set('StdClass', StdClass::class);
```

<br />

## Immutable + Truncate:

> `->env()` is an alias of `->set()` with the third parameter ($immutable) set to true. \
> `->env()` only accepts an array as the value (second parameter).

```php
$container->env('config', ['my' => 'secret']);

$myArray = $container->readAndDelete('myArray');

// Returns false
$container->has('config');

// Throws drhino\Container\Exception\ContainerNotFoundException
$container->get('config');

// Throws drhino\Container\Exception\ContainerException
$container->env('config', ['my' => 'secret']);
```

<br />

## Service locator:

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
