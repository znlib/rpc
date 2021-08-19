<?php

namespace ZnLib\Rpc\Rpc\Serializers;

use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

interface SerializerInterface
{

    public function encode($data): RpcResponseEntity;
}
