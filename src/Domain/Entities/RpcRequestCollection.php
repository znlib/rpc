<?php

namespace ZnLib\Rpc\Domain\Entities;

use Illuminate\Support\Collection;

class RpcRequestCollection
{

    private $requestArray = [];

    public function add(RpcRequestEntity $requestEntity)
    {
        return $this->requestArray[] = $requestEntity;
    }
}
