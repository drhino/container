# PSR-11: Container

<br />

Install with Composer:
```bash
composer require drhino/container:^3.0.0
```

<br />

## Example use:

```php
class Dependency
{
    public string $var = '';
}

class Init
{
    public function __construct(Dependency $dependency, String $value)
    {
        $dependency->var = $value;
    }
}
```

```php
$container = new drhino\Container\Container;

$container
    ->add(Init::class, [
        // The arguments of the constructor
        'dependency' => $container->ref(Dependency::class),
        'value'      => 'Hello world',
    ])
    ->add(Dependency::class)
;
```

<br />

> Use $container->ref() to reference an identifier before it has been added into the container.

<br />

```php
// Executes __construct()
$init = $container->get(Init::class);

// Prints 'Hello world'
echo $container->get(Dependency::class)->var;
```

<br />

## Signature:

The following are exactly the same:

```php
$container->add(Dependency::class);
```
```php
$container->add($id = Dependency::class, $resource = Dependency::class, $arguments = []);
```
```php
$container->add($id = Dependency::class, $arguments = []);
```

<br />
