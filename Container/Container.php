<?php declare(strict_types=1);

namespace drhino\Container;

use drhino\Container\ContainerEnum;
use drhino\Container\ContainerInjector;
use drhino\Container\Exception\ContainerException;
use drhino\Container\Exception\ContainerNotFoundException;

use Psr\Container\ContainerInterface;

use Throwable;

/**
 * PSR-11: Container.
 *
 * A $resource of type (class-)String is constructed when requested.
 * A $resource which extends from ContainerInjector,
 *  inherits **this** as ->container and adds a new ContainerEnum to ->enum.
 *
 * A constructed Object always returns that same Object unless (re)set/removed.
 * An immutable resource can be destructed but only assigned once.
 */
class Container implements ContainerInterface
{
    // All resources except unset (see ->readAndDelete())
    private array $containers = []; // [ $id => $resource, ... ]
    // A resource of type class-string,
    //  is replaced with the object once requested in ->get()

    // List of immutable identifiers
    // Keeps the identifier even when unset in ->readAndDelete()
    private array $immutable = [];  // [ $id, ... ]

    // Resources added by using the method `->add()`
    // Holds a reference to the ContainerEnum of that class-string|object
    //  until that class-string|object is requested in ->get()
    private array $enums = [];      // [ $id => ContainerEnum, ... ]

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
            throw new ContainerException(
                "Immutable Entry (resource previously assigned): `$id`");
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
     * Sets an immutable resource which extends from ContainerInjector.
     * A class-string is replaced with the object once requested in ->get().
     *
     * The $resource can be unset in ->readAndDelete($id).
     * However, the $id can only be assigned once.
     *
     * @param string $id
     * @param class-string|object|array $resource, uses $id if undefined.
     *
     * @throws ContainerException when the resource is an array.
     * @throws ContainerException when a value has previously been assigned.
     * @throws ContainerException when the class is invalid.
     *
     * @return ContainerEnum
     */
    public function add(String $id, $resource = null): ContainerEnum
    {
        if (is_array($resource))
            throw new ContainerException(
                'Use ->env($id, $resource) to set an immutable array');

        /** @var class-string|object */
        $resource = $resource ?? $id;

        if (get_parent_class($resource) !== ContainerInjector::class) {

            $resource = is_string($resource) ? $resource : get_class($resource);

            throw new ContainerException(
                "Must Extend From ContainerInjector: `$resource`");
        }

        $this->set($id, $resource, true);

        $this->enums[$id] = new ContainerEnum;

        return $this->enums[$id];
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
     * A (class-)String is constructed as an Object when first requested.
     * A different datatype is returned as-is.
     *
     * A class extending from ContainerInjector, inherits $this and new ContainerEnum.
     *
     * Regenerate an object by reassigning the resource in ->set($id, $resource)
     * On a $container->get($id) the class-string is again constructed.
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
                $resource = new $resource;

                if (get_parent_class($resource) === ContainerInjector::class) {
                    $resource->container = $this;
                    $resource->enum = $this->enums[$id] ?? new ContainerEnum;
                    unset($this->enums[$id]);
                    $resource();
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
