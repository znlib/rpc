<?php

namespace ZnLib\Rpc\Symfony4\HttpKernel;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\App\Helpers\EnvHelper;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcBatchModeEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Helpers\ErrorHelper;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Interfaces\Services\ProcedureServiceInterface;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Libs\RpcJsonResponse;
use ZnLib\Web\Symfony4\HttpKernel\BaseHttpKernel;

class RpcKernel extends BaseHttpKernel
{

    protected $procedureService;
    protected $logger;
    protected $responseFormatter;
    protected $rpcJsonResponse;

    public function __construct(
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        RpcJsonResponse $rpcJsonResponse,
        EventDispatcherInterface $dispatcher,
        ProcedureServiceInterface $procedureService,
        RequestStack $requestStack = null
    )
    {
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->rpcJsonResponse = $rpcJsonResponse;
        $this->setEventDispatcher($dispatcher);
        $this->procedureService = $procedureService;
        $this->requestStack = $requestStack;
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        $jsonData = $request->getContent();
        $responseData = $this->handleJsonData($jsonData);
        $response = $this->createResponse($responseData);
        $response = $this->filterResponse($response, $request, $type);
        return $response;
    }

    protected function jsonErrorCodeToMessage(int $jsonErrorCode): string
    {
        $errorDescription = ErrorHelper::descriptionFromJsonErrorCode($jsonErrorCode);
        $message = "Invalid request. Parse JSON error! {$errorDescription}";
        return $message;
    }

    protected function createErrorResponseByMessage(string $message): RpcResponseCollection
    {
        $responseEntity = $this->responseFormatter->forgeErrorResponse(RpcErrorCodeEnum::SERVER_ERROR_INVALID_REQUEST, $message);
        $responseCollection = new RpcResponseCollection();
        $responseCollection->add($responseEntity);
        return $responseCollection;
    }

    protected function handleJsonData(string $jsonData)
    {
        $requestData = json_decode($jsonData, true);
        $jsonErrorCode = json_last_error();
        if ($jsonErrorCode) {
            $message = $this->jsonErrorCodeToMessage($jsonErrorCode);
            $responseCollection = $this->createErrorResponseByMessage($message);
            $batchMode = RpcBatchModeEnum::SINGLE;
        } elseif (empty($requestData)) {
//            $message = $this->jsonErrorCodeToMessage($jsonErrorCode);
            $message = "Invalid request. Empty request!";
            $responseCollection = $this->createErrorResponseByMessage($message);
            $batchMode = RpcBatchModeEnum::SINGLE;
        } else {
            //$this->logger->info('request', $requestData ?: []);
//            $controllerEvent = new ControllerEvent($controllerInstance, $actionName, $request);
//            $this->getEventDispatcher()->dispatch($controllerEvent, ControllerEventEnum::BEFORE_ACTION);

            $isBatchRequest = RequestHelper::isBatchRequest($requestData);
            $batchMode = $isBatchRequest ? RpcBatchModeEnum::BATCH : RpcBatchModeEnum::SINGLE;
            $requestCollection = RequestHelper::createRequestCollection($requestData);
            $responseCollection = $this->handleData($requestCollection);
        }
//        $response = $this->rpcJsonResponse->send($responseCollection, $batchMode);

        $responseData = $this->rpcJsonResponse->encode($responseCollection, $batchMode);
        return $responseData;
    }

    private function createResponse(array $responseData): JsonResponse
    {
        $response = new JsonResponse();
        if (EnvHelper::isDebug()) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }
        $response->setData($responseData);
        return $response;
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
