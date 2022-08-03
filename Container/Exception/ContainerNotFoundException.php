<?php declare(strict_types=1);

namespace drhino\Container\Exception;

use drhino\Container\Exception\ContainerException;

use Psr\Container\NotFoundExceptionInterface;

/**
 * No entry was found in the container.
 */
class ContainerNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
