<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Services\ProcedureService;
use Exception;
use Illuminate\Container\Container;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnTool\Dev\Dumper\Domain\Facades\Bot;

class RpcController
{

    private $procedureService;
    private $container;
    private $logger;
    private $responseFormatter;

    public function __construct(
        Container $container,
        ProcedureService $procedureService,
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter
    )
    {
        $this->container = $container;
        $this->procedureService = $procedureService;
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
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
        return $this->sendJsonResponse($array);
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
        return  $responseEntity;
    }

    private function sendJsonResponse(array $array): Response
    {
        $response = new JsonResponse();
        if ($_ENV['APP_DEBUG'] == 1) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }
        $response->setData($array);
        return $response;
    }

    private function responseEntityToArray( RpcResponseEntity $responseEntity): array
    {
        $responseEntity->setJsonrpc(RpcVersionEnum::V2_0);
        $array = EntityHelper::toArray($responseEntity);
        return $array;
    }


}
