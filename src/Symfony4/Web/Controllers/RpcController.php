<?php

namespace ZnLib\Rpc\Symfony4\Web\Controllers;

use ZnLib\Rpc\Symfony4\HttpKernel\RpcFramework;
use ZnLib\Rpc\Domain\Helpers\ErrorHelper;
use ZnLib\Rpc\Domain\Services\ProcedureService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcBatchModeEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Libs\RpcJsonResponse;

/**
 * Class RpcController
 * @package ZnLib\Rpc\Symfony4\Web\Controllers
 * @deprecated
 * @see RpcFramework
 */
class RpcController
{

    protected $procedureService;
    protected $logger;
    protected $responseFormatter;
    protected $rpcJsonResponse;

    public function __construct(
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        RpcJsonResponse $rpcJsonResponse,
        ProcedureService $procedureService
    )
    {
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->rpcJsonResponse = $rpcJsonResponse;
        $this->procedureService = $procedureService;
    }

    public function callProcedure(Request $request): Response
    {
        $requestRawData = $request->getContent();
        $requestData = json_decode($requestRawData, true);
        $jsonErrorCode = json_last_error();
        if ($jsonErrorCode || empty($requestData)) {
            if ($jsonErrorCode) {
                $errorDescription = ErrorHelper::descriptionFromJsonErrorCode($jsonErrorCode);
                $message = "Invalid request. Parse JSON error! {$errorDescription}";
            } else {
                $message = "Invalid request. Empty request!";
            }
            //$this->logger->warning('request', $requestData ?: []);
            $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SERVER_ERROR_INVALID_REQUEST, $message);
            $responseCollection = new RpcResponseCollection();
            $responseCollection->add($responseEntity);
            $batchMode = RpcBatchModeEnum::SINGLE;
        } else {
            //$this->logger->info('request', $requestData ?: []);
            $isBatchRequest = RequestHelper::isBatchRequest($requestData);
            $batchMode = $isBatchRequest ? RpcBatchModeEnum::BATCH : RpcBatchModeEnum::SINGLE;
            $requestCollection = RequestHelper::createRequestCollection($requestData);
            $responseCollection = $this->handleData($requestCollection);
        }
        return $this->rpcJsonResponse->send($responseCollection, $batchMode);
    }

    protected function handleData(RpcRequestCollection $requestCollection): RpcResponseCollection
    {
        $responseCollection = new RpcResponseCollection();
        foreach ($requestCollection->getCollection() as $requestEntity) {
            /** @var RpcRequestEntity $requestEntity */
            $requestEntity->addMeta(HttpHeaderEnum::IP, $_SERVER['REMOTE_ADDR']);
            $responseEntity = $this->procedureService->callOneProcedure($requestEntity);
            $responseCollection->add($responseEntity);
        }
        return $responseCollection;
    }
}
