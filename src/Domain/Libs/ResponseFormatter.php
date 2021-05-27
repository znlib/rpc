<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class ResponseFormatter
{

    public function forgeErrorResponse(int $code, string $message = null, $data = null): RpcResponseEntity
    {
        $error = [
            'code' => $code,
            'message' => $message,
            'data' => null,
        ];

        if(EnvHelper::isDebug()) {
            $error['data'] = $data;
        }

        $responseArray = [
            'error' => $error,
        ];

        $responseEntity = new RpcResponseEntity;
        EntityHelper::setAttributes($responseEntity, $responseArray);
        return $responseEntity;
    }
}
