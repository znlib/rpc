<?php

namespace ZnLib\Rpc\Domain\Helpers;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;

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
            throw new InvalidRequestException('Empty version');
        }
        if ($requestEntity->getMethod() == null) {
            throw new InvalidRequestException('Empty method');
        }
        /*if ($requestEntity->getParams() === null) {
            throw new InvalidRequestException('Empty params');
        }*/
        if ($requestEntity->getJsonrpc() != RpcVersionEnum::V2_0) {
            throw new InvalidRequestException('Unsupported RPC version');
        }
    }
}
