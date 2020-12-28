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
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
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
            $array = $this->handleData($data);
        } catch (Exception $exception) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($exception->getCode(), $exception->getMessage());
            $array = $this->responseEntityToArray($responseEntity);
        }
        return $this->rpcJsonResponse->send($array);
    }

    private function handleData($data): array
    {
        if (empty($data)) {
            throw new Exception("Empty response", RpcErrorCodeEnum::INVALID_REQUEST);
        }

        if (ArrayHelper::isIndexed($data)) {
            // выполняем батч
            $array = [];
            foreach ($data as $item) {
                $array[] = $this->handleProcedure($item);
            }
        } else {
            // единичный
            $array = $this->handleProcedure($data);
        }
        return $array;
    }

    private function handleProcedure(array $data): array
    {
        /** @var RpcRequestEntity $requestEntity */
        $requestEntity = EntityHelper::createEntity(RpcRequestEntity::class, $data);
        $responseEntity = $this->callOneProcedure($requestEntity);
        return $this->responseEntityToArray($responseEntity);
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
