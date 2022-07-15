<?php

namespace ZnLib\Rpc\Domain\Interfaces\Repositories;

use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnCore\Contract\Common\Exceptions\NotFoundException;
use ZnDomain\Repository\Interfaces\CrudRepositoryInterface;

interface MethodRepositoryInterface extends CrudRepositoryInterface
{

    /**
     * @param string $method
     * @param int $version
     * @return MethodEntity
     */
    public function findOneByMethodName(string $method, int $version): MethodEntity;
}
