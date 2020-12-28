<?php

namespace ZnLib\Rpc\Rpc\Base;

use ZnLib\Rpc\Rpc\Interfaces\RpcAuthInterface;

abstract class BaseRpcController implements RpcAuthInterface
{

    protected $service;

    public function auth(): array
    {
        return [
            "*"
        ];
    }
}
