<?php

namespace ZnLib\Rpc\Domain\Libs;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class RpcJsonResponse
{

    public function sendBatch(RpcResponseCollection $responseCollection): JsonResponse
    {
        $collecion = $responseCollection->getCollection();
        if($collecion->count() == 1) {
            return $this->sendEntity($collecion->first());
        }
        $items = [];
        foreach ($collecion as $responseEntity) {
            $item = EntityHelper::toArray($responseEntity);
            $items[] = $item;
        }
        return $this->send($items);
    }

    public function sendEntity(RpcResponseEntity $responseEntity): JsonResponse
    {
        $array = EntityHelper::toArray($responseEntity);
        return $this->send($array);
    }

    public function send(array $array): JsonResponse
    {
        $response = new JsonResponse();
        if (EnvHelper::isDebug()) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }
        $response->setData($array);
        return $response;
    }
}
