<?php declare(strict_types=1);

namespace drhino\Container;

use drhino\Container\ContainerInjector;
use drhino\Container\Exception\ContainerException;
use drhino\Container\Exception\ContainerNotFoundException;

use Psr\Container\ContainerInterface;

use Throwable;

/**
 * PSR-11: Container.
 *
 * A $resource of type String is constructed when requested.
 * A $resource of type String which extends from ContainerInjector,
 *  is constructed with **this** container as the first and only argument.
 *
 * A constructed Object always returns that same Object unless (re)set/removed.
 */
class Container implements ContainerInterface
{
    private array $containers = [];
    private array $immutable = [];

    /**
     * Adds or Replaces the representation of a resource.
     *
     * @param string $id
     * @param class-string|object|array $resource
     * @param bool $immutable TRUE to throw Exception on replacement.
     *
     * @throws ContainerException
     *
     * @return void
     */
    public function set(String $id, $resource, Bool $immutable = null): void
    {
        if (in_array($id, $this->immutable)) {
            throw new ContainerException("Immutable Entry: `$id`");
        }

        if (is_string($resource) && !class_exists($resource)) {
            throw new ContainerException("Class Does Not Exist: `$resource`");
        }

        $this->containers[$id] = $resource;

        if ($immutable) {
            array_push($this->immutable, $id);
        }
    }

    /**
     * Sets an immutable array.
     *
     * The $resource can be unset in ->readAndDelete($id).
     * However, the $id can only be assigned once.
     *
     * @param string $id
     * @param array $resource
     *
     * @throws ContainerException when a value has previously been assigned.
     *
     * @return void
     */
    public function env(String $id, Array $resource): void
    {
        $this->set($id, $resource, true);
    }

    /**
     * Returns the resource by its identifier and destructs the entry.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     */
    public function readAndDelete(String $id)
    {
        /** @var class-string|object|array */
        $resource = $this->get($id);

        unset($this->containers[$id]);

        return $resource;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * A String is constructed as an Object when first requested.
     * A different datatype is returned as-is.
     *
     * When the Classname extends from ContainerInjector, the container is passed along.
     *
     * Regenerate a Classname by overwriting the Classname as a String in ->set($id, $class)
     * On a $container->get($id) the Object is again constructed.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerNotFoundException No entry was found for **this** identifier.
     * @throws ContainerException         Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(String $id)
    {
        if (!$this->has($id)) {
            # NotFoundExceptionInterface
            throw new ContainerNotFoundException("Container Not Found: `$id`");
        }

        /** @var class-string|object|array */
        $resource = $this->containers[$id];

        if (is_string($resource)/* && class_exists($resource)*/) {
            try {
                if (get_parent_class($resource) === ContainerInjector::class) {
                    $resource = new $resource($this);
                } else {
                    $resource = new $resource;
                }

                $this->containers[$id] = $resource;
            }
            catch (Throwable $e) {
                # ContainerExceptionInterface
                throw new ContainerException("Error Retreiving Entry: `$id`", 0, $e);
            }
        }

        return $resource;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(String $id): bool
    {
        return isset($this->containers[$id]);
    }
}
