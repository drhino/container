<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use drhino\Container\Container;
use drhino\Container\Exception\ContainerException;
use drhino\Container\Exception\ContainerNotFoundException;

/**
 * Used to test that we receive a ContainerException
 * rather than the Exception thrown from this class
 * The Exception is passed to the ContainerException
 * @psalm-api
 */
final class ThrowableObject
{
    /**
     * Throws an Exception when the Object is constructed
     * @throws Exception
     * @psalm-api
     */
    public function __construct()
    {
        throw new Exception;
    }
}

/**
 * Used to test that we can construct a class with an argument
 * @psalm-api
 */
final class ObjectWithArguments
{
    /**
     * Sets the argument in this class
     * @param boolean $argument true
     * @throws Exception when the $argument is false
     * @psalm-api
     */
    public function __construct(bool $argument)
    {
        if (true !== $argument) {
            throw new Exception('Expects $argument=true in test case');
        }
    }
}

/**
 * Test case
 * @psalm-api
 */
final class ContainerTest extends TestCase
{
    // psalm.dev/074
    protected $backupStaticAttributes = false;
    protected $runTestInSeparateProcess = false;

    // Ensures the container can be created
    // @psalm-api
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Container::class,
            new Container
        );
    }

    // Returns the container upon successful registration
    // @psalm-api
    public function testContainerAdd(): void
    {
        $container = new Container;

        $this->assertInstanceOf(
            Container::class,
            $container->add(stdClass::class)
        );
    }

    // Returns true without any exception
    // @psalm-api
    public function testContainerHas(): void
    {
        $container = new Container;
        $container->add(stdClass::class);

        $this->assertEquals(
            true,
            $container->has(stdClass::class)
        );
    }

    // Returns false without any exception
    // @psalm-api
    public function testContainerHasNot(): void
    {
        $container = new Container;

        $this->assertEquals(
            false,
            $container->has(stdClass::class)
        );
    }

    // Adds a constructed object to the container
    // @psalm-api
    public function testContainerGetObject(): void
    {
        $container = new Container;
        $container->add(stdClass::class, new stdClass);

        $this->assertInstanceOf(
            stdClass::class,
            $container->get(stdClass::class)
        );
    }

    // Constructs the class-string when it is requested
    // @psalm-api
    public function testContainerCreateObject(): void
    {
        $container = new Container;
        $container->add(stdClass::class);

        $this->assertInstanceOf(
            stdClass::class,
            $container->get(stdClass::class)
        );
    }

    // Constructs the class-string with arguments when it is requested
    // @psalm-api
    public function testContainerCreateObjectWithArguments(): void
    {
        $container = new Container;
        $container->add(ObjectWithArguments::class, [ 'argument' => true ]);

        $this->assertInstanceOf(
            ObjectWithArguments::class,
            $container->get(ObjectWithArguments::class)
        );
    }

    // Fails because of a missing argument
    // @psalm-api
    public function testContainerCreateObjectWithArgumentsException(): void
    {
        $container = new Container;
        $container->add(ObjectWithArguments::class);

        $this->expectException(ContainerException::class);
        $container->get(ObjectWithArguments::class);
    }

    // Fails because the $id has previously been assigned
    // @psalm-api
    public function testContainerAddImmutableException(): void
    {
        $container = new Container;
        $container->add(stdClass::class);

        $this->expectException(ContainerException::class);
        $container->add(stdClass::class, new stdClass);
    }

    // Throws a ContainerException with the previous Exception
    // @psalm-api
    public function testContainerException(): void
    {
        $container = new Container;
        $container->add(ThrowableObject::class);

        $this->expectException(ContainerException::class);
        $container->get(ThrowableObject::class);
    }

    // Fails because the $id has not been assigned
    // @psalm-api
    public function testContainerNotFoundException(): void
    {
        $container = new Container;

        $this->expectException(ContainerNotFoundException::class);
        $container->get(stdClass::class);
    }
}
