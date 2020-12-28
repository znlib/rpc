<?php

namespace ZnLib\Rpc\Domain\Interfaces\Repositories;

use ZnLib\Rpc\Domain\Entities\HandlerEntity;

interface ProcedureConfigRepositoryInterface
{

    public function oneByMethodName(string $method): HandlerEntity;
}
