<?php

namespace ZnLib\Rpc\Rpc\Base;

use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Helpers\ResponseHelper;
use ZnLib\Rpc\Rpc\Interfaces\RpcAuthInterface;
use ZnLib\Rpc\Rpc\Serializers\DefaultSerializer;
use ZnLib\Rpc\Rpc\Serializers\SerializerInterface;

abstract class BaseRpcController implements RpcAuthInterface
{

    protected $service;

    public function attributesOnly(): array
    {
        return [];
    }

    public function attributesExclude(): array
    {
        return [];
    }

    public function serializer(): SerializerInterface
    {
        $serializer = new DefaultSerializer();
        $serializer->setAttributesOnly($this->attributesOnly());
        $serializer->setAttributesExclude($this->attributesExclude());
        return $serializer;
    }

    public function auth(): array
    {
        return [
            "*"
        ];
    }

    protected function serializeResult($result): RpcResponseEntity
    {
        $serializer = $this->serializer();
        $result = $serializer->encode($result);
        return ResponseHelper::forgeRpcResponseEntity($result);
    }
}
