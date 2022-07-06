<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Validation\Helpers\ValidationHelper;

class RpcRequestCollection extends BaseRpcCollection
{

    public function add(RpcRequestEntity $requestEntity)
    {
        //ValidationHelper::validateEntity($requestEntity);
        return $this->collection->add($requestEntity);
    }
}
