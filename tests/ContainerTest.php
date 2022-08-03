<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use drhino\Container\Container;
use drhino\Container\Exception\ContainerException;
use drhino\Container\Exception\ContainerNotFoundException;

#use StdClass;
#use Exception;

final class ThrowableObject
{
    public function __construct()
    {
        throw new Exception;
    }
}

final class ContainerTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Container::class,
            new Container
        );
    }

    public function testContainerSet(): void
    {
        $container = new Container;

        $this->assertEquals(
            null,
            $container->set('StdClass', StdClass::class)
        );
    }

    public function testContainerHas(): void
    {
        $container = new Container;
        $container->set('StdClass', StdClass::class);

        $this->assertEquals(
            true,
            $container->has('StdClass')
        );
    }

    public function testContainerHasNot(): void
    {
        $container = new Container;

        $this->assertEquals(
            false,
            $container->has('StdClass')
        );
    }

    // Adds a constructed object to the container.
    public function testContainerGetObject(): void
    {
        $container = new Container;
        $container->set('StdClass', new StdClass);

        $this->assertInstanceOf(
            StdClass::class,
            $container->get('StdClass')
        );
    }

    // Adds a reference to the class and constructs on first ->get()
    public function testContainerCreateObject(): void
    {
        $container = new Container;
        $container->set('StdClass', StdClass::class);

        $this->assertInstanceOf(
            StdClass::class,
            $container->get('StdClass')
        );
    }

    // Adds a string, which does not resolve to a classname.
    public function testContainerString(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container;
        $container->set('mystring', 'mystring');
    }

    // Adds an array, which returns the exact same array.
    public function testContainerArray(): void
    {
        $container = new Container;
        $container->set('myArray', ['test' => '123']);

        $this->assertEquals(
            ['test' => '123'],
            $container->get('myArray')
        );
    }

    public function testReadAndDelete(): void
    {
        $container = new Container;
        $container->set('myArray', ['test' => '123']);

        $this->assertEquals(
            ['test' => '123'],
            $container->readAndDelete('myArray')
        );

        $this->assertEquals(
            false,
            $container->has('myArray')
        );
    }

    public function testReadAndDeleteException(): void
    {
        $container = new Container;
        $container->set('myArray', ['test' => '123']);

        $container->readAndDelete('myArray');

        $this->expectException(ContainerNotFoundException::class);

        $container->get('myArray');
    }

    public function testContainerNotFoundException(): void
    {
        $this->expectException(ContainerNotFoundException::class);

        $container = new Container;
        $container->get('StdClass');
    }

    public function testContainerException(): void
    {
        $this->expectException(ContainerException::class);

        $container = new Container;
        $container->set('ThrowableObject', ThrowableObject::class);
        $container->get('ThrowableObject');
    }
}
