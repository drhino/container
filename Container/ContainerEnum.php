<?php declare(strict_types=1);

namespace drhino\Container;

use drhino\Container\Exception\ContainerException;

/**
 * Dynamic constants.
 *
 * A $key can be assigned a $value once.
 */
class ContainerEnum
{
    private array $_enum = []; // [ $key => $value, ... ]

    /**
     * Returns the $value of a given $key.
     *
     * @param string $key
     *
     * @throws ContainerException when $key is undefined.
     * 
     * @return mixed $value
     */
    public function __get(String $key)
    {
        if (isset( $this->_enum[$key] ))
            return $this->_enum[$key];

        throw new ContainerException("Unknown Enum: `$key`");
    }

    /**
     * Assigns the value to a given $key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @throws ContainerException when the $key has been previously set.
     *
     * @return ContainerEnum **this**
     */
    public function __set(String $key, $value)//: ContainerEnum
    {
        try {
            $this->__get($key);
        }
        catch (ContainerException $e) {
            $this->_enum[$key] = $value;

            return $this;
        }

        throw new ContainerException("Immutable Enum: `$key`");
    }

    /**
     * alias of __set()
     * @psalm-suppress MissingParamType
     */
    public function enum(String $key, $value): ContainerEnum
    {
        return $this->__set($key, $value);
    }
}
