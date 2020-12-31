<?php

namespace ZnLib\Rpc\Domain\Encoders;

use ZnLib\Rpc\Domain\Interfaces\Encoders\ResponseEncoderInterface;

class ResponseEncoder implements ResponseEncoderInterface
{

    public function encode($data)
    {
        $response = [];
        if (isset($data['jsonrpc'])) {
            $response['jsonrpc'] = $data['jsonrpc'];
        }
        if (isset($data['result'])) {
            $response['result']['body'] = $data['result'];
        }
        if (isset($data['meta'])) {
            $response['result']['meta'] = $data['meta'];
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
        if (isset($data['result']['body'])) {
            $response['result'] = $data['result']['body'];
        }
        if (isset($data['result']['meta'])) {
            $response['meta'] = $data['result']['meta'];
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
