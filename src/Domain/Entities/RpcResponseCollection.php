<?php

namespace ZnLib\Rpc\Domain\Entities;

use ZnCore\Base\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Base\Validation\Helpers\ValidationHelper;

class RpcResponseCollection extends BaseRpcCollection
{

    public function add(RpcResponseEntity $responseEntity)
    {
//        ValidationHelper::validateEntity($responseEntity);
        return $this->collection->add($responseEntity);
    }
}
