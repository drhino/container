# PSR-11: Container

[v1.0.1](https://github.com/drhino/container/tree/v1.0.1)
|
[v1.1.0](https://github.com/drhino/container/tree/v1.1.0)
|
[v2.0.0](https://github.com/drhino/container/tree/v2.0.0)

<br />

Install with Composer:
```bash
composer require drhino/container@2
```

<br />

## Example use:

```php
use drhino\Container\Container;

$container = new Container;

$container
  ->add(
    RouterInterface::class,
    Router::class
  )
;

// Use an interface as a named adapter for your dependencies.
// This allows you to swap `Database::class` without having to update your code.
// Additionally, class constants can be dynamically defined by using enum.

$container
  ->add(
    DatabaseInterface::class,
    Database::class
  )
    ->enum('constant', 'immutable')
    ->enum('dsn', 'mysql:host=localhost')
;

// Constructs the class-string.
$container->get(RouterInterface::class);
```

<br />

> Review all available methods in: [Container](https://github.com/drhino/container/blob/main/Container/Container.php)

> Considering the example above, the following would be your injected dependencies:

<br />

```php
use drhino\Container\ContainerInjector;

interface RouterInterface {}
interface DatabaseInterface {}

class Database extends ContainerInjector implements DatabaseInterface
{
    public function connect() {
        echo 'connecting to: ' . $this->enum->dsn . PHP_EOL;
    }
}

class Router extends ContainerInjector implements RouterInterface
{
    // Optional, executes after __construct() -- which does not have access to
    //  $this->container and $this->enum
    public function __invoke()
    {
        $db = $this->container->get(DatabaseInterface::class);
        $db->connect();
    }
}
```

<br />
