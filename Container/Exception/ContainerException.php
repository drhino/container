<?php declare(strict_types=1);

namespace drhino\Container\Exception;

use Psr\Container\ContainerExceptionInterface;

use Exception;

/**
 * Represents a generic exception thrown in Container.
 */
class ContainerException extends Exception implements ContainerExceptionInterface
{
}
