<?php

namespace ZnLib\Rpc\Domain\Helpers;

use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Code\Helpers\PropertyHelper;
use ZnCore\Env\Enums\EnvEnum;
use ZnLib\Rpc\Domain\Encoders\RequestEncoder;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Exceptions\InvalidRequestException;
use ZnLib\Rpc\Domain\Facades\RpcClientFacade;

class RequestHelper
{

    protected function sendRpcRequest(RpcRequestEntity $requestEntity, string $login = null): RpcResponseEntity
    {
        $rpcClientFacade = new RpcClientFacade(EnvEnum::TEST);
//        $rpcClientFacade->authBy($login, 'Wwwqqq111');
        $response = $rpcClientFacade->send($requestEntity, $login, 'Wwwqqq111');
        return $response;
    }

    public static function createRequestCollection(array $requestData): RpcRequestCollection
    {
        $requestCollection = new RpcRequestCollection();
        if (!self::isBatchRequest($requestData)) {
            $requestData = [$requestData];
        }
        $requestEncoder = new RequestEncoder();
        foreach ($requestData as $item) {
            $item = $requestEncoder->decode($item);
            $requestEntity = self::forgeRequestEntity($item);
            $requestCollection->add($requestEntity);
        }
        return $requestCollection;
    }

    public static function isBatchRequest(array $requestData): bool
    {
        return ArrayHelper::isIndexed($requestData);
    }

    private static function forgeRequestEntity(array $requestItem): RpcRequestEntity
    {
        $requestEntity = new RpcRequestEntity();
        PropertyHelper::setAttributes($requestEntity, $requestItem);
        return $requestEntity;
    }

    public static function validateRequest(RpcRequestEntity $requestEntity)
    {
        if ($requestEntity->getJsonrpc() == null) {
            throw new InvalidRequestException('Empty RPC version');
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
