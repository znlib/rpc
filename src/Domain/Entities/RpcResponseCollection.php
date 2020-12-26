<?php

namespace ZnLib\Rpc\Domain\Entities;

class RpcResponseCollection extends BaseRpcCollection
{

    public function add(RpcResponseEntity $requestEntity)
    {
        return $this->collection->add($requestEntity);
    }
}
