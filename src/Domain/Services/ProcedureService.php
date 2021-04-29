<?php

namespace ZnLib\Rpc\Domain\Services;

use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Interfaces\Repositories\ProcedureConfigRepositoryInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ControllerServiceInterface;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;

class ProcedureService implements ProcedureServiceInterface
{

    private $procedureConfigRepository;
    private $meta = [];
//    private $logger;
    private $controllerService;
    private $authService;

    public function __construct(
        ProcedureConfigRepositoryInterface $procedureConfigRepository,
        AuthServiceInterface $authService,
//        LoggerInterface $logger,
        ControllerServiceInterface $controllerService
    )
    {
        $this->procedureConfigRepository = $procedureConfigRepository;
//        $this->logger = $logger;
        $this->authService = $authService;
        $this->controllerService = $controllerService;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function run(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        $authorization = $requestEntity->getMetaItem(HttpHeaderEnum::AUTHORIZATION);
        if($authorization) {
            $identity = $this->authService->authenticationByToken($authorization);
            $this->authService->setIdentity($identity);
        }
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
        $responseEntity = $this->controllerService->runProcedure($handlerEntity, $requestEntity);
//        $this->logger->info('request', EntityHelper::toArray($requestEntity));
//        $this->logger->info('response', EntityHelper::toArray($responseEntity));
        return $responseEntity;
        // https://www.jsonrpc.org/specification#error_object
        // http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
    }
}
