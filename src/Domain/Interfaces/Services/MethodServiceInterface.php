<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnCore\Domain\Service\Interfaces\CrudServiceInterface;
use ZnLib\Rpc\Domain\Entities\MethodEntity;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;

interface MethodServiceInterface extends CrudServiceInterface
{

    /**
     * @param string $method
     * @param int $version
     * @return MethodEntity
     */
    public function oneByMethodName(string $method, int $version): MethodEntity;
}
