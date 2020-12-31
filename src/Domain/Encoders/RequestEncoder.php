<?php

namespace ZnLib\Rpc\Domain\Encoders;

use ZnLib\Rpc\Domain\Interfaces\Encoders\RequestEncoderInterface;

class RequestEncoder implements RequestEncoderInterface
{

    private $bodyName;
    private $metaName;

    public function __construct(string $bodyName = 'body', string $metaName = 'meta')
    {
        $this->bodyName = $bodyName;
        $this->metaName = $metaName;
    }

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
            $request['params'][$this->bodyName] = $data['params'];
        }
        if(isset($data['meta'])) {
            $request['params'][$this->metaName] = $data['meta'];
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
        if(isset($data['params'][$this->bodyName])) {
            $request['params'] = $data['params'][$this->bodyName];
        }
        if(isset($data['params'][$this->metaName])) {
            $request['meta'] = $data['params'][$this->metaName];
        }
        if (isset($data['id'])) {
            $request['id'] = $data['id'];
        }
        return $request;
    }
}
