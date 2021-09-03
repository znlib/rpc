<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class ResponseFormatter
{

    public function forgeErrorResponse(int $code, string $message = null, $data = null, \Throwable $e = null): RpcResponseEntity
    {
        $error = [
            'code' => $code,
            'message' => $message,
            'data' => null,
        ];

        if(EnvHelper::isDebug()) {
            if(empty($data)) {
                $data = [];
            }
            if($e instanceof \Throwable) {
                $data['previous'] = $e->getPrevious();
            }
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
