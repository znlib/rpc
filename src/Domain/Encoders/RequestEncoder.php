<?php

namespace ZnLib\Rpc\Domain\Encoders;

use ZnLib\Rpc\Domain\Interfaces\Encoders\RequestEncoderInterface;

class RequestEncoder implements RequestEncoderInterface
{

    public function encode($data)
    {
        $request = [];
        if (isset($data['jsonrpc'])) {
            $request['jsonrpc'] = $data['jsonrpc'];
        }
        if (isset($data['method'])) {
            $request['method'] = $data['method'];
        }
        if(isset($data['params'])) {
            $request['params']['body'] = $data['params'];
        }
        if(isset($data['meta'])) {
            $request['params']['meta'] = $data['meta'];
        }
        if (isset($data['id'])) {
            $request['id'] = $data['id'];
        }
        return $request;
    }

    public function decode($data)
    {
        $request = [];
        if (isset($data['jsonrpc'])) {
            $request['jsonrpc'] = $data['jsonrpc'];
        }
        if (isset($data['method'])) {
            $request['method'] = $data['method'];
        }
        if(isset($data['params']['body'])) {
            $request['params'] = $data['params']['body'];
        }
        if(isset($data['params']['meta'])) {
            $request['meta'] = $data['params']['meta'];
        }
        if (isset($data['id'])) {
            $request['id'] = $data['id'];
        }
        return $request;
    }
}
