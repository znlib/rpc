<?php

namespace ZnLib\Rpc\Domain\Helpers;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use Exception;

class RequestHelper
{

    public static function createRequestCollection(array $data): RpcRequestCollection
    {

        $requestCollection = new RpcRequestCollection();
        if (ArrayHelper::isIndexed($data)) {

            foreach ($data as $item) {
                $requestEntity = new RpcRequestEntity();
                EntityHelper::setAttributes($requestEntity, $item);
//                self::validateRequest($requestEntity);
                $requestCollection->add($requestEntity);
            }
        } else {

            $requestEntity = new RpcRequestEntity();

            EntityHelper::setAttributes($requestEntity, $data);
//            self::validateRequest($requestEntity);
            $requestCollection->add($requestEntity);
//            print_r($requestEntity);exit;
        }
        return $requestCollection;
    }

    public static function validateRequest(RpcRequestEntity $requestEntity)
    {
        if ($requestEntity->getJsonrpc() == null) {
            throw new Exception('Empty version', RpcErrorCodeEnum::INVALID_REQUEST);
        }
        if ($requestEntity->getMethod() == null) {
            throw new Exception('Empty method', RpcErrorCodeEnum::INVALID_REQUEST);
        }
        if ($requestEntity->getParams() === null) {
            throw new Exception('Empty params', RpcErrorCodeEnum::INVALID_REQUEST);
        }
        if ($requestEntity->getJsonrpc() != RpcVersionEnum::V2_0) {
            throw new Exception('Unsupported RPC version', RpcErrorCodeEnum::INVALID_REQUEST);
        }
    }
}
