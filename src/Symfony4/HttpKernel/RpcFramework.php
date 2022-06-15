<?php

namespace ZnLib\Rpc\Symfony4\HttpKernel;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\Event\Traits\EventDispatcherTrait;
use ZnLib\Rpc\Domain\Entities\RpcRequestCollection;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseCollection;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcBatchModeEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Helpers\ErrorHelper;
use ZnLib\Rpc\Domain\Helpers\RequestHelper;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Domain\Libs\RpcJsonResponse;
use ZnLib\Rpc\Domain\Services\ProcedureService2;

class RpcFramework implements HttpKernelInterface, TerminableInterface
{

    use EventDispatcherTrait;

    protected $procedureService;
    protected $logger;
    protected $responseFormatter;
    protected $rpcJsonResponse;

    public function __construct(
        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
        RpcJsonResponse $rpcJsonResponse,
        EventDispatcherInterface $dispatcher,
        ProcedureService2 $procedureService
    )
    {
        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
        $this->rpcJsonResponse = $rpcJsonResponse;
        $this->setEventDispatcher($dispatcher);
        $this->procedureService = $procedureService;
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
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
//            $controllerEvent = new ControllerEvent($controllerInstance, $actionName, $request);
//            $this->getEventDispatcher()->dispatch($controllerEvent, ControllerEventEnum::BEFORE_ACTION);

            $isBatchRequest = RequestHelper::isBatchRequest($requestData);
            $batchMode = $isBatchRequest ? RpcBatchModeEnum::BATCH : RpcBatchModeEnum::SINGLE;
            $requestCollection = RequestHelper::createRequestCollection($requestData);
            $responseCollection = $this->handleData($requestCollection);
        }
        return $this->rpcJsonResponse->send($responseCollection, $batchMode);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $this->getEventDispatcher()->dispatch(new TerminateEvent($this, $request, $response), KernelEvents::TERMINATE);
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
