<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use drhino\Container\Container;
use drhino\Container\ContainerInjector;

final class ContainerInjectorObject extends ContainerInjector
{
}

final class ContainerInjectorTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(
            ContainerInjector::class,
            new ContainerInjector(new Container)
        );
    }

    public function testContainerInjector(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->assertInstanceOf(
            ContainerInjector::class,
            $container->get('ContainerInjectorObject')
        );
    }
}
