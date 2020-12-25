<?php

namespace ZnLib\Rpc\Rpc\Base;

abstract class BaseRpcController
{

    protected $service;

    public function auth()
    {
        return [
            "*"
        ];
    }
}
