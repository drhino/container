<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use drhino\Container\Container;
use drhino\Container\ContainerInjector;
use drhino\Container\ContainerEnum;
use drhino\Container\Exception\ContainerException;

final class ContainerInjectorObject extends ContainerInjector
{
    public function returnContainer() {
        return $this->container;
    }

    public function returnInvalid() {
        return $this->somethingUnused;
    }

    public function returnEnum() {
        return $this->enum;
    }

    public function setContainer() {
        $this->container = 'fails';
    }

    public function accessPrivate() {
        return $this->__container;
    }
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

    public function testContainerInjectorAccessContainer(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->assertInstanceOf(
            Container::class,
            $container->get('ContainerInjectorObject')->returnContainer()
        );
    }

    public function testContainerInjectorException(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->expectException(ContainerException::class);

        $container->get('ContainerInjectorObject')->returnInvalid();
    }

    public function testContainerInjectorAccessEnum(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->assertInstanceOf(
            ContainerEnum::class,
            $container->get('ContainerInjectorObject')->returnEnum()
        );
    }

    public function testContainerInjectorUnknownEnum(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->expectException(ContainerException::class);

        $container->get('ContainerInjectorObject')->returnEnum()->somethingUnused;
    }

    public function testContainerInjectorSetContainer(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->expectException(ContainerException::class);

        $container->get('ContainerInjectorObject')->setContainer();
    }

    public function testContainerInjectorAccessPrivate(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $this->expectException(ContainerException::class);

        $container->get('ContainerInjectorObject')->accessPrivate();
    }

    public function testContainerInjectorSetImmutableEnum(): void
    {
        $container = new Container;
        $container->set('ContainerInjectorObject', ContainerInjectorObject::class);

        $container->get('ContainerInjectorObject')->returnEnum()->testProperty = 'works';

        $this->expectException(ContainerException::class);

        $container->get('ContainerInjectorObject')->returnEnum()->testProperty = 'fails';
    }

    public function testContainerInjectorSetImmutableEnum2(): void
    {
        $container = new Container;

        $enum = new ContainerEnum;
        $enum->testProperty = 'value';

        $std = new ContainerInjectorObject;
        $std->container = $container;
        $std->enum = $enum;

        $this->expectException(ContainerException::class);

        $std->enum->testProperty = 'fails';
    }

    public function testContainerInjectorGetEnum(): void
    {
        $container = new Container;

        $enum = new ContainerEnum;
        $enum->testProperty = 'value';

        $std = new ContainerInjectorObject;
        $std->container = $container;
        $std->enum = $enum;

        $this->assertEquals(
            'value',
            $std->enum->testProperty
        );
    }

    public function testContainerInjectorDesignPattern(): void
    {
        $container = new Container;

        $container
            ->add('ContainerInjectorObject', ContainerInjectorObject::class)
                ->__set('testProperty1', 'value 1')
                ->__set('testProperty2', 'value 2')
        ;

        $this->assertEquals(
            'value 1',
            $container->get('ContainerInjectorObject')->enum->testProperty1
        );
        $this->assertEquals(
            'value 2',
            $container->get('ContainerInjectorObject')->enum->testProperty2
        );

        $this->assertInstanceOf(
            Container::class,
            $container->get('ContainerInjectorObject')->container
        );

        $this->assertInstanceOf(
            ContainerEnum::class,
            $container->get('ContainerInjectorObject')->enum
        );
    }
}
