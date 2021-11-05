<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnSandbox\Sandbox\Rpc\Domain\Entities\MethodEntity;
use ZnCore\Domain\Interfaces\Service\CrudServiceInterface;
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
