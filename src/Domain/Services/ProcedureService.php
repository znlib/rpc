<?php

namespace ZnLib\Rpc\Domain\Services;

use Psr\Log\LoggerInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ControllerServiceInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;

class ProcedureService implements ProcedureServiceInterface
{

    private $procedureConfigRepository;
    private $meta = [];
    private $logger;
    private $responseFormatter;
    private $controllerService;

    public function __construct(
        ProcedureConfigRepositoryInterface $procedureConfigRepository,
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        ControllerServiceInterface $controllerService
    )
    {
        $this->procedureConfigRepository = $procedureConfigRepository;
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->controllerService = $controllerService;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function run(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        RequestHelper::validateRequest($requestEntity);
        if ($requestEntity->getMeta()) {
            $this->meta = $requestEntity->getMeta();
        }
        $method = $requestEntity->getMethod();
        try {
            $handlerEntity = $this->procedureConfigRepository->oneByMethodName($method);
        } catch (NotFoundException $e) {
            throw new MethodNotFoundException('Not found handler');
        }
        $result = $this->controllerService->runProcedure($handlerEntity, $requestEntity);
        return $this->responseFormatter->forgeResultResponse($result);
        // https://www.jsonrpc.org/specification#error_object
        // http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
    }
}
