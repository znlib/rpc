<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Enums\Http\HttpStatusCodeEnum;
use ZnCore\Base\Exceptions\ForbiddenException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Exceptions\UnauthorizedException;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Exceptions\MethodNotFoundException;
use ZnLib\Rpc\Domain\Exceptions\ParamNotFoundException;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
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
        if (empty($data)) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::INVALID_REQUEST, "Empty response");
            $responseCollection = new RpcResponseCollection();
            $responseCollection->add($responseEntity);
//            return $this->rpcJsonResponse->sendEntity($responseEntity);
        } else {
            $requestCollection = RequestHelper::createRequestCollection($data);
            $responseCollection = $this->handleData($requestCollection);
        }
        return $this->rpcJsonResponse->sendBatch($responseCollection);
    }

    private function handleData(RpcRequestCollection $requestCollection): RpcResponseCollection
    {
        $responseCollection = new RpcResponseCollection();
        foreach ($requestCollection->getCollection() as $requestEntity) {
            /** @var RpcRequestEntity $requestEntity */
            $responseEntity = $this->callOneProcedure($requestEntity);
            $responseCollection->add($responseEntity);
        }
        return $responseCollection;
    }

    private function callOneProcedure(RpcRequestEntity $requestEntity): RpcResponseEntity
    {
        // айпи отсекать один раз для всех запросов
        $ip = $_SERVER['REMOTE_ADDR'];
        $requestEntity->addMeta('ip', $ip);
        try {
            $responseEntity = $this->procedureService->run($requestEntity);
        } catch (NotFoundException $e) {
//            $error = $this->responseFormatter->createErrorByException($e, HttpStatusCodeEnum::NOT_FOUND);
//            $responseEntity = $this->responseFormatter->forgeErrorResponseByError($error, $requestEntity->getId());
        } catch (MethodNotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($e->getCode(), $e->getMessage());
        } catch (UnprocessibleEntityException $e) {
            $errorData = ValidationHelper::collectionToArray($e->getErrorCollection());
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::INVALID_PARAMS, 'Parameter validation error', $errorData);
        } catch (UnauthorizedException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::UNAUTHORIZED, $e->getMessage());
        } catch (ParamNotFoundException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($e->getCode(), $e->getMessage());
        } catch (ForbiddenException $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse(HttpStatusCodeEnum::FORBIDDEN, $e->getMessage());
        } catch (Exception $e) {
            $responseEntity = $this->responseFormatter->forgeErrorResponse($e->getCode(), $e->getMessage());
        }

        $responseEntity->setId($requestEntity->getId());
        return $responseEntity;
    }
}
