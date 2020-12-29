<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use Exception;
use Illuminate\Container\Container;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Libs\RpcJsonResponse;

class RpcController
{

    private $procedureService;
    private $logger;
    private $responseFormatter;
    private $rpcJsonResponse;

    public function __construct(
        ProcedureServiceInterface $procedureService,
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        RpcJsonResponse $rpcJsonResponse
    )
    {
        $this->procedureService = $procedureService;
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->rpcJsonResponse = $rpcJsonResponse;
    }

    public function callProcedure(Request $request): Response
    {
        $rawData = $request->getContent();
        $data = json_decode($rawData, true);
        try {
            if (empty($data)) {
                $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::INVALID_REQUEST, "Empty response");
                return $this->rpcJsonResponse->sendEntity($responseEntity);
//                throw new Exception("Empty response", RpcErrorCodeEnum::INVALID_REQUEST);
            }
            $requestCollection = $this->createRequestCollection($data);
            $responseCollection = $this->handleData($requestCollection);
            return $this->rpcJsonResponse->sendBatch($responseCollection);
        } catch (Exception $exception) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($exception->getCode(), $exception->getMessage());
            return $this->rpcJsonResponse->sendEntity($responseEntity);
        }
    }

    private function createRequestCollection(array $data): RpcRequestCollection
    {
        $requestCollection = new RpcRequestCollection();
        if (ArrayHelper::isIndexed($data)) {
            foreach ($data as $item) {
                $requestEntity = new RpcRequestEntity();
                EntityHelper::setAttributes($requestEntity, $item);
                $this->procedureService->validateRequest($requestEntity);
                $requestCollection->add($requestEntity);
            }
        } else {
            $requestEntity = new RpcRequestEntity();
            EntityHelper::setAttributes($requestEntity, $data);
            $this->procedureService->validateRequest($requestEntity);
            $requestCollection->add($requestEntity);
        }
        return $requestCollection;
    }

    private function handleData(RpcRequestCollection $requestCollection): RpcResponseCollection
    {
        $responseCollection = new RpcResponseCollection();
        foreach ($requestCollection->getCollection() as $requestEntity) {
            /** @var RpcRequestEntity $requestEntity */
            try {
                $responseEntity = $this->callOneProcedure($requestEntity);
            } catch (Exception $exception) {
                $responseEntity = $this->responseFormatter->forgeErrorResponse($exception->getCode(), $exception->getMessage());
            }
            $responseCollection->add($responseEntity);
        }
        return $responseCollection;
    }

    private function callOneProcedure(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        // айпи отсекать один раз для всех запросов
        $ip = $_SERVER['REMOTE_ADDR'];
        $requestEntity->addMeta('ip', $ip);
        $responseEntity = $this->procedureService->run($requestEntity);
        $responseEntity->setId($requestEntity->getId());
        return $responseEntity;
    }

    private function responseEntityToArray(RpcResponseEntity $responseEntity): array
    {
        $responseEntity->setJsonrpc(RpcVersionEnum::V2_0);
        $array = EntityHelper::toArray($responseEntity);
        return $array;
    }
}
