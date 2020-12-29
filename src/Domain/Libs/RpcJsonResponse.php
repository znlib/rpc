<?php

namespace ZnLib\Rpc\Domain\Libs;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcBatchModeEnum;

class RpcJsonResponse
{

    public function send(RpcResponseCollection $responseCollection, int $batchMode = RpcBatchModeEnum::AUTO): JsonResponse
    {
        $responseData = $this->collectionToArray($responseCollection);
        $isAutoSingle = $batchMode == RpcBatchModeEnum::AUTO && count($responseData) == 1;
        $isSingle = $batchMode == RpcBatchModeEnum::SINGLE;
        if($isAutoSingle || $isSingle) {
            $responseData = $responseData[0];
        }
        return $this->sendJson($responseData);
    }

    private function collectionToArray(RpcResponseCollection $responseCollection): array
    {
        $collecion = $responseCollection->getCollection();
        $responseData = [];
        foreach ($collecion as $responseEntity) {
            $responseData[] = EntityHelper::toArray($responseEntity);
        }
        return $responseData;
    }

    private function sendJson(array $responseData): JsonResponse
    {
        $response = new JsonResponse();
        if (EnvHelper::isDebug()) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }
        $response->setData($responseData);
        return $response;
    }
}
