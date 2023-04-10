<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use drhino\Container\Container;
use drhino\Container\Exception\ContainerException;
use drhino\Container\Exception\ContainerNotFoundException;

// Used to test that we receive a ContainerException
// rather than the Exception thrown from this class
// The Exception is passed to the ContainerException
final class ThrowableObject
{
    /**
     * Throws an Exception when the Object is constructed
     * @throws Exception
     */
    public function __construct()
    {
        throw new Exception;
    }
}

// Used to test that we can construct a class with an argument
final class ObjectWithArguments
{
    private $argument;

    /**
     * Sets the argument in this class
     * @param boolean $argument true
     * @throws Exception when the $argument is false
     */
    public function __construct(bool $argument)
    {
        if (true !== $argument) {
            throw new Exception('Expects $argument=true in test case');
        }

        $this->argument = $argument;
    }

    /**
     * Returns the argument of the class
     * @return boolean true
     */
    public function getArgument(): bool
    {
        return $this->argument;
    }
}

/**
 * Test case
 */
final class ContainerTest extends TestCase
{
    // Ensures the container can be created
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Container::class,
            new Container
        );
    }

    // Returns the container upon successful registration
    public function testContainerAdd(): void
    {
        $container = new Container;

        $this->assertInstanceOf(
            Container::class,
            $container->add(StdClass::class)
        );
    }

    // Returns true without any exception
    public function testContainerHas(): void
    {
        $container = new Container;
        $container->add(StdClass::class);

        $this->assertEquals(
            true,
            $container->has(StdClass::class)
        );
    }

    // Returns false without any exception
    public function testContainerHasNot(): void
    {
        $container = new Container;

        $this->assertEquals(
            false,
            $container->has(StdClass::class)
        );
    }

    // Adds a constructed object to the container
    public function testContainerGetObject(): void
    {
        $container = new Container;
        $container->add(StdClass::class, new StdClass);

        $this->assertInstanceOf(
            StdClass::class,
            $container->get(StdClass::class)
        );
    }

    // Constructs the class-string when it is requested
    public function testContainerCreateObject(): void
    {
        $container = new Container;
        $container->add(StdClass::class);

        $this->assertInstanceOf(
            StdClass::class,
            $container->get(StdClass::class)
        );
    }

    // Constructs the class-string with arguments when it is requested
    public function testContainerCreateObjectWithArguments(): void
    {
        $container = new Container;
        $container->add(ObjectWithArguments::class, [ 'argument' => true ]);

        $constructed = $container->get(ObjectWithArguments::class);

        $this->assertEquals(
            true,
            $constructed->getArgument()
        );
    }

    // Fails because of a missing argument
    public function testContainerCreateObjectWithArgumentsException(): void
    {
        $container = new Container;
        $container->add(ObjectWithArguments::class);

        $this->expectException(ContainerException::class);
        $container->get(ObjectWithArguments::class);
    }

    // Fails because the $id has previously been assigned
    public function testContainerAddImmutableException(): void
    {
        $container = new Container;
        $container->add(StdClass::class);

        $this->expectException(ContainerException::class);
        $container->add(StdClass::class, new StdClass);
    }

    // Throws a ContainerException with the previous Exception
    public function testContainerException(): void
    {
        $container = new Container;
        $container->add(ThrowableObject::class);

        $this->expectException(ContainerException::class);
        $container->get(ThrowableObject::class);
    }

    // Fails because the $id has not been assigned
    public function testContainerNotFoundException(): void
    {
        $container = new Container;

        $this->expectException(ContainerNotFoundException::class);
        $container->get(StdClass::class);
    }
}
