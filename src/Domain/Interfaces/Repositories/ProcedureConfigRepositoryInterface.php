<?php

namespace ZnLib\Rpc\Domain\Interfaces\Repositories;

use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Base\Develop\Helpers\DeprecateHelper;
use ZnLib\Rpc\Domain\Entities\HandlerEntity;

DeprecateHelper::hardThrow();

interface ProcedureConfigRepositoryInterface
{

    /**
     * @param string $method
     * @return HandlerEntity
     * @throws NotFoundException
     */
    public function oneByMethodName(string $method): HandlerEntity;
}
