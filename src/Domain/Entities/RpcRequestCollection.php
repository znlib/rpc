<?php

namespace ZnLib\Rpc\Domain\Entities;

class RpcRequestCollection extends BaseRpcCollection
{

    public function add(RpcRequestEntity $requestEntity)
    {
        return $this->collection->add($requestEntity);
    }
}
