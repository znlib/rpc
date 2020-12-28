<?php

namespace ZnLib\Rpc\Domain\Interfaces\Services;

use ZnLib\Rpc\Domain\Entities\HandlerEntity;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

interface ControllerServiceInterface
{

    public function runProcedure(HandlerEntity $handlerEntity, RpcRequestEntity $requestEntity): RpcResponseEntity;
}
