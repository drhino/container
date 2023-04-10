<?php declare(strict_types=1);

namespace drhino\Container;

class ContainerReference
{
    private $id;

    public function __construct(String $id)
    {
        $this->id = $id;
    }

    public function getIdentifier(): string
    {
        return $this->id;
    }
}
