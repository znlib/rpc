<?php

namespace ZnLib\Rpc\Domain\Interfaces\Repositories;

use ZnCore\Base\Exceptions\NotFoundException;
use ZnLib\Rpc\Domain\Entities\HandlerEntity;

interface ProcedureConfigRepositoryInterface
{

    /**
     * @param string $method
     * @return HandlerEntity
     * @throws NotFoundException
     */
    public function oneByMethodName(string $method): HandlerEntity;
}
