<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\ValidationHelper;

class RpcResponseCollection extends BaseRpcCollection
{

    public function add(RpcResponseEntity $responseEntity)
    {
//        ValidationHelper::validateEntity($responseEntity);
        return $this->collection->add($responseEntity);
    }
}
