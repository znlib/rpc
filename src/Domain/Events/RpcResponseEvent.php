<?php

namespace ZnLib\Rpc\Domain\Events;

use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\Base\Develop\Helpers\DeprecateHelper;
use ZnCore\EventDispatcher\Traits\EventSkipHandleTrait;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class RpcResponseEvent extends Event
{

    use EventSkipHandleTrait;

    private $requestEntity;
    private $responseEntity;

    public function __construct(RpcRequestEntity $requestEntity, RpcResponseEntity $responseEntity)
    {
        $this->requestEntity = $requestEntity;
        $this->responseEntity = $responseEntity;
    }

    public function getRequestEntity(): RpcRequestEntity
    {
        return $this->requestEntity;
    }

    public function getResponseEntity(): RpcResponseEntity
    {
        return $this->responseEntity;
    }
}
