<?php

namespace ZnLib\Rpc\Domain\Libs;

use Exception;
use Psr\Log\LoggerInterface;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

class ResponseFormatter
{

    private $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function forgeErrorResponse(int $code, string $message = null, $data = null): RpcResponseEntity
    {
        $responseArray = [
            'error' => [
                'code' => $code,
                'message' => $message,
                'data' => $data,
            ],
        ];
        return $this->createRpcResponseFromArray($responseArray);
    }

    private function createRpcResponseFromArray(array $responseArray): RpcResponseEntity
    {
        /** @var RpcResponseEntity $responseEntity */
        $responseEntity = EntityHelper::createEntity(RpcResponseEntity::class, $responseArray);
        //$this->logger->error($responseArray['error']['message'], $responseArray);
        return $responseEntity;
    }
}
