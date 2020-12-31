<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class ResponseFormatter
{

    public function forgeErrorResponse(int $code, string $message = null, $data = null): RpcResponseEntity
    {
        $responseArray = [
            'error' => [
                'code' => $code,
                'message' => $message,
                'data' => $data,
            ],
        ];
        $responseEntity = new RpcResponseEntity;
        EntityHelper::setAttributes($responseEntity, $responseArray);
        return $responseEntity;
    }
}
