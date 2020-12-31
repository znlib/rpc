<?php

namespace ZnLib\Rpc\Domain\Encoders;

use ZnLib\Rpc\Domain\Interfaces\Encoders\ResponseEncoderInterface;

class ResponseEncoder implements ResponseEncoderInterface
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
        $response = [];
        if (isset($data['jsonrpc'])) {
            $response['jsonrpc'] = $data['jsonrpc'];
        }
        if (isset($data['result'])) {
            $response['result'][$this->bodyName] = $data['result'];
        }
        if (isset($data['meta'])) {
            $response['result'][$this->metaName] = $data['meta'];
        }
        if (isset($data['error'])) {
            $response['error'] = $data['error'];
        }
        if (isset($data['id'])) {
            $response['id'] = $data['id'];
        }
        return $response;
    }

    public function decode($data)
    {
        $response = [];
        if (isset($data['jsonrpc'])) {
            $response['jsonrpc'] = $data['jsonrpc'];
        }
        if (isset($data['result'][$this->bodyName])) {
            $response['result'] = $data['result'][$this->bodyName];
        }
        if (isset($data['result'][$this->metaName])) {
            $response['meta'] = $data['result'][$this->metaName];
        }
        if (isset($data['error'])) {
            $response['error'] = $data['error'];
        }
        if (isset($data['id'])) {
            $response['id'] = $data['id'];
        }
        return $response;
    }
}
