<?php

namespace ZnLib\Rpc\Domain\Encoders;

use ZnCore\Base\Interfaces\EncoderInterface;

class RequestEncoder implements EncoderInterface
{

    public function encode($data)
    {
        $params = [];
        if(isset($data['params'])) {
            $params['body'] = $data['params'];
        }
        if(isset($data['meta'])) {
            $params['meta'] = $data['meta'];
            unset($data['meta']);
        }
        if(!empty($params)) {
            $data['params'] = $params;
        }
        return $data;
    }

    public function decode($request)
    {
        if(isset($request['params']['meta'])) {
            $request['meta'] = $request['params']['meta'];
            unset($request['params']['meta']);
        }
        if(isset($request['params']['body'])) {
            $request['params'] = $request['params']['body'];
        }
        return $request;
    }
}
