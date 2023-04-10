<?php declare(strict_types=1);

namespace drhino\Container;

use drhino\Container\Exception\ContainerException;
use drhino\Container\Exception\ContainerNotFoundException;

use Psr\Container\ContainerInterface;

use Throwable;
use InvalidArgumentException;
use ReflectionClass;

use function is_array;
use function is_string;
use function is_object;
use function class_exists;
use function array_values;

/**
 * PSR-11: Container.
 *
 * A $resource of type (class-)String is constructed when requested.
 * The order of the $arguments must be the same as in the constructor.
 * A constructed Object always returns that same Object.
 * An identifier can only be assigned once (immutable).
 *
 * @see https://www.php-fig.org/psr/psr-11/
 * @see https://www.php-fig.org/psr/psr-11/meta/
 */
class Container implements ContainerInterface
{
    private array $containers = [];

    /**
     * Adds a resource to the container.
     *
     * @param class-string $id
     * @param class-string|object|array $resource or $arguments
     * @param array $arguments
     *
     * @throws ContainerException when $resource is invalid or $id exists.
     *
     * @return Container **this**
     */
    public function add(String $id, $resource = null, Array $arguments = null): Container
    {
        // When $resource is an array, it is used as the $arguments.
        if (!isset($arguments) && is_array($resource)) {
            $arguments = $resource;
            $resource = null;
        }

        // When $id has previously been assigned an error is thrown.
        if ($this->has($id)) {
            throw new ContainerException("Resource previously assigned to: `$id`");
        }

        // When $resource is omitted, $id is used as a class-string.
        $resource = $resource ?? $id;

        if (is_string($resource)) {
            if (!class_exists($resource)) {
                throw new ContainerException("Class does not exist: `$resource`");
            }

            $this->containers[$id] = [ $resource, $arguments ];

        } else if (!is_object($resource)) {
            throw new InvalidArgumentException('Resource must be class-string|object');
        } else {
            $this->containers[$id] = $resource;
        }

        return $this;
    }

    /**
     * Returns the entry for a given identifier.
     * Constructs the resource when it has not been previously constructed.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerNotFoundException No entry was found for $id.
     * @throws ContainerException         Error retrieving the entry.
     *
     * @return object Entry.
     */
    public function get(String $id): object
    {
        if (!$this->has($id)) {
            # NotFoundExceptionInterface
            throw new ContainerNotFoundException("Container not found for: `$id`");
        }

        /** @var object|array */
        $resource = $this->containers[$id];

        if (is_array($resource)) {
            /** @var array|null */
            $arguments = $resource[1];

            /** @var class-string */
            $resource = $resource[0];

            try {
                $resource = new ReflectionClass($resource);
                $resource = $arguments
                    ? $resource->newInstanceArgs(array_values($arguments))
                    : $resource->newInstance();

                $this->containers[$id] = $resource;
            }
            catch (Throwable $e) {
                # ContainerExceptionInterface
                throw new ContainerException("Error retreiving entry for: `$id`", 0, $e);
            }
        }

        return $resource;
    }

    /**
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool True if the container can return an entry for $id, false otherwise.
     */
    public function has(String $id): bool
    {
        return isset($this->containers[$id]);
    }
}
