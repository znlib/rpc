<?php

namespace ZnLib\Rpc\Rpc\Base;

use Illuminate\Support\Collection;
use ZnCore\Base\Encoders\AggregateEncoder;
use ZnLib\Rpc\Rpc\Interfaces\RpcAuthInterface;
use ZnLib\Rpc\Rpc\Serializers\DefaultSerializer;

abstract class BaseRpcController implements RpcAuthInterface
{

    protected $service;

    public function serializers(): array
    {
        return [
            new DefaultSerializer(),
        ];
    }

    public function auth(): array
    {
        return [
            "*"
        ];
    }

    protected function serializeResult($result) {

        $serializers = $this->serializers();
        if($serializers) {
            $encoders = new AggregateEncoder(new Collection($serializers));
            $result = $encoders->encode($result);
        }
        return $result;
    }
}
