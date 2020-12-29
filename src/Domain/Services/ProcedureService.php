<?php

namespace ZnLib\Rpc\Domain\Services;

use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Exceptions\ForbiddenException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Exceptions\UnauthorizedException;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
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

        try {

            $result = $this->controllerService->runProcedure($handlerEntity, $requestEntity);
            $responseEntity = $this->responseFormatter->forgeResultResponse($result);

        } catch (NotFoundException $e) {
            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::NOT_FOUND);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (MethodNotFoundException $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::METHOD_NOT_FOUND);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (UnprocessibleEntityException $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::INVALID_PARAMS);
            $error['data'] = ValidationHelper::collectionToArray($e->getErrorCollection());
            $error['message'] = 'Parameter validation error';
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (UnauthorizedException $e) {
            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::UNAUTHORIZED);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (InvalidArgumentException $e) {
//            var_dump($e);
            //throw new Exception('qwe', 123);
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::INVALID_PARAMS);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (ForbiddenException $e) {
            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::FORBIDDEN);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (Exception $e) {
            $error = $this->responseFormatter->createErrorByException($e, RpcErrorCodeEnum::JSON_RPC_ERROR);
            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        }

        return $responseEntity;
        // https://www.jsonrpc.org/specification#error_object
        // http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
    }
}
