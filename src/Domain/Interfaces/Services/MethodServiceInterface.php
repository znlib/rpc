<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnDomain\Service\Interfaces\CrudServiceInterface;
use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;

interface MethodServiceInterface extends CrudServiceInterface
{

    /**
     * @param string $method
     * @param int $version
     * @return MethodEntity
     */
    public function findOneByMethodName(string $method, string $version): MethodEntity;
}
