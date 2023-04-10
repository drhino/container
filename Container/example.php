<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

class Dependency
{
    public string $var = '';

    public function getVar(): string
    {
        return $this->var;
    }
}

class Init
{
    /** @psalm-api */
    public function __construct(Dependency $dependency, String $value)
    {
        $dependency->var = $value;
    }
}

// ---

$container = new drhino\Container\Container;

$container
    ->add(Init::class, [
        // The arguments of the constructor
        'dependency' => $container->ref(Dependency::class),
        'value'      => 'Hello world',
    ])
    ->add(Dependency::class)
;
/*
    The following are exactly the same:

    ->add(Dependency::class)
    ->add($id = Dependency::class, $resource = Dependency::class, $arguments = [])
    ->add($id = Dependency::class, $arguments = [])
*/

// ---

// Assigns $value to Dependency::$var
$container->get(Init::class);

/** @var Dependency */
$dependency = $container->get(Dependency::class);

// Prints 'Hello world'
echo $dependency->getVar();
