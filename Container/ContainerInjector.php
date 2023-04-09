<?php declare(strict_types=1);

namespace drhino\Container;

use drhino\Container\ContainerEnum;
use drhino\Container\Exception\ContainerException;

use InvalidArgumentException;

use Psr\Container\ContainerInterface;

/**
 * Classes that extend from this class inherit the Container.
 */
class ContainerInjector
{
    /** @var ContainerInterface|null */
    private $__container = null;

    /** @var ContainerEnum|null */
    private $__enum = null;

    public function __invoke() {}

    /**
     * Returns a class variable.
     *
     * @param string $private variable without leading underscores.
     *
     * @throws ContainerException
     *
     * @return ContainerEnum|ContainerInterface|null
     */
    public function __get(String $private)
    {
        if ($private === 'enum')
            /** @var ContainerEnum */
            return $this->__enum;

        if ($private === 'container')
            /** @var ContainerInterface */
            return $this->__container;

        throw new ContainerException("Unknown Property: `$private`");
    }

    /**
     * Rejects replacing the class variables.
     *
     * @param string $private variable without leading underscores.
     * @param mixed|ContainerEnum|ContainerInterface $value
     *
     * @throws ContainerException Unknown Property
     * @throws ContainerException Immutable Property
     * @throws InvalidArgumentException
     */
    public function __set(String $private, $value): void
    {
        // Throws "Unknown Property" when "$private"
        //  does not equal either: "enum" or "container".
        if ($this->__get($private))
            // Throws "Immutable Property" when
            //  a value has previously been assigned.
            throw new ContainerException("Immutable Property: `$private`");

        if ($private === 'container' && ! $value instanceOf ContainerInterface)
            throw new InvalidArgumentException("`$private` expects `ContainerInterface`");
        else
        if ($private === 'enum' && ! $value instanceOf ContainerEnum)
            throw new InvalidArgumentException("`$private` expects `ContainerEnum`");

        // Prefixes the named private variable with the leading underscores.
        $private = "__$private";

        // Assigns the immutable $value to the private property.
        $this->$private = $value;
    }
}
