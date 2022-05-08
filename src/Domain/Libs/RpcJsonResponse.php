<?php

namespace ZnLib\Rpc\Domain\Libs;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use ZnCore\Base\Libs\App\Helpers\EnvHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Enums\RpcBatchModeEnum;
use ZnLib\Rpc\Domain\Interfaces\Encoders\ResponseEncoderInterface;

class RpcJsonResponse
{

    private $responseEncoder;
    private $logger;

    public function __construct(ResponseEncoderInterface $responseEncoder, LoggerInterface $logger)
    {
        $this->responseEncoder = $responseEncoder;
        $this->logger = $logger;
    }

    public function send(RpcResponseCollection $responseCollection, int $batchMode = RpcBatchModeEnum::AUTO): JsonResponse
    {
        $responseData = $this->collectionToArray($responseCollection);
        $this->logger->info('response', $responseData);
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
            $responseItem = EntityHelper::toArray($responseEntity);
            $responseItem = $this->responseEncoder->encode($responseItem);
            $responseData[] = $responseItem;
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
