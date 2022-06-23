<?php

namespace ZnLib\Rpc\Domain\Libs;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use ZnCore\Base\Env\Helpers\EnvHelper;
use ZnCore\Base\Arr\Helpers\ArrayHelper;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
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

        if (EnvHelper::isDebug()) {
            if (empty($data)) {
                $data = [];
            }
            if ($e instanceof \Throwable) {
                try {
                    $attributes = EntityHelper::toArray($e);
                    $data = ArrayHelper::merge($attributes, $data);
                } catch (NoSuchPropertyException $e) {}
                if ($e->getPrevious() instanceof \Throwable) {
                    try {
                        $data['previous'] = EntityHelper::toArray($e->getPrevious());
                    } catch (NoSuchPropertyException $e) {}
                }
            }
        }

        $error['data'] = $data;
        $responseArray = [
            'error' => $error,
        ];

        $responseEntity = new RpcResponseEntity;
        EntityHelper::setAttributes($responseEntity, $responseArray);
        return $responseEntity;
    }
}
