<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Domain\Exceptions\UnprocessibleEntityException;

class RpcResponseCollection extends BaseRpcCollection
{

    public function add(RpcResponseEntity $responseEntity)
    {
        if($responseEntity->getId() == null) {
            throw new UnprocessibleEntityException('Empty request ID');
        }
        return $this->collection->add($responseEntity);
    }
}
