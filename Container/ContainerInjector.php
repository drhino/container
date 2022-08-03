<?php declare(strict_types=1);

namespace drhino\Container;

use Psr\Container\ContainerInterface;

/**
 * Classes that extend from this class inherit the Container.
 */
class ContainerInjector
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
