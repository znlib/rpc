<?php

namespace ZnLib\Rpc\Domain\Libs;

use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseErrorEntity;
use Exception;
use Psr\Log\LoggerInterface;
use ZnCore\Domain\Helpers\EntityHelper;

class ResponseFormatter
{

    private $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function forgeResultResponse($result, string $id = null): RpcResponseEntity
    {
//        $result = [
//            'result' => $result,
//            'id' => $id,
//        ];

        $result->setId($id);

        /** @var RpcResponseEntity $responseEntity */
//        $responseEntity = EntityHelper::createEntity(RpcResponseResultEntity::class, $result);
        $this->logger->info('response', EntityHelper::toArray($result));
        return $result;
    }

    public function createErrorByException(Exception $e, int $code): array
    {
        $error = [
            'code' => $code,
            'message' => $e->getMessage(),
            'data' => null,
        ];
        if ($_ENV['APP_DEBUG'] == 1) {
            $error['data'] = [];
            $error['data']['file'] = $e->getFile();
            $error['data']['line'] = $e->getLine();
            $error['data']['exception'] = get_class($e);
            $error['data']['previous'] = EntityHelper::toArray($e->getPrevious(), true);
        }
        return $error;
    }

    public function forgeErrorResponse(int $code, string $message = null, string $id = null): RpcResponseEntity
    {
        $error = [
            'code' => $code,
            'message' => $message,
            'data' => null,
            'id' => $id,
        ];
        return $this->forgeErrorResponseByError($error, $id);
    }

    public function forgeErrorResponseByError(array $error, int $id = null): RpcResponseEntity
    {
        $result = [
            'error' => $error,
            'id' => $id,
        ];
        return $this->createRpcResponseFromArray($result, $error);
    }

    public function createRpcResponseFromArray(array $result, array $error): RpcResponseEntity
    {
        /** @var RpcResponseEntity $responseEntity */
        $responseEntity = EntityHelper::createEntity(RpcResponseErrorEntity::class, $result);
        $this->logger->error($error['message'], $result);
        return $responseEntity;
    }
}
