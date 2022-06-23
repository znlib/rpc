<?php

namespace ZnLib\Rpc\Domain\Entities;

use Illuminate\Support\Collection;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;

class BaseRpcCollection
{

    /** @var Collection | EntityIdInterface[] */
    protected $collection;

    public function __construct()
    {
        $this->collection = new Collection();
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    public function getById(int $id): RpcResponseEntity
    {
        foreach ($this->collection as $entity) {
            if ($entity->getId() == $id) {
                return $entity;
            }
        }
        throw new NotFoundException('RPC entity not found!');
    }
}
