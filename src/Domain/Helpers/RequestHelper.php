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

    public static function createRequestCollection(array $requestData): RpcRequestCollection
    {
        $requestCollection = new RpcRequestCollection();
        if (self::isBatchRequest($requestData)) {
            foreach ($requestData as $item) {
                $requestEntity = self::forgeRequestEntity($item);
                $requestCollection->add($requestEntity);
            }
        } else {
            $requestEntity = self::forgeRequestEntity($requestData);
            $requestCollection->add($requestEntity);
        }
        return $requestCollection;
    }

    public static function isBatchRequest(array $requestData): bool {
        return ArrayHelper::isIndexed($requestData);
    }

    private static function forgeRequestEntity(array $requestItem): RpcRequestEntity
    {
        $requestEntity = new RpcRequestEntity();
        EntityHelper::setAttributes($requestEntity, $requestItem);
        return $requestEntity;
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
