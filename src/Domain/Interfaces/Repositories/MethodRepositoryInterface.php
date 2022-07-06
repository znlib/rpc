<?php

namespace ZnLib\Rpc\Domain\Interfaces\Repositories;

use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnCore\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;

interface MethodRepositoryInterface extends CrudRepositoryInterface
{

    /**
     * @param string $method
     * @param int $version
     * @return MethodEntity
     */
    public function findOneByMethodName(string $method, int $version): MethodEntity;
}
