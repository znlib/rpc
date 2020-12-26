<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Domain\Exceptions\UnprocessibleEntityException;

class RpcRequestCollection extends BaseRpcCollection
{

    public function add(RpcRequestEntity $requestEntity)
    {
        if($requestEntity->getId() == null) {
            throw new UnprocessibleEntityException('Empty request ID');
        }
        return $this->collection->add($requestEntity);
    }
}
