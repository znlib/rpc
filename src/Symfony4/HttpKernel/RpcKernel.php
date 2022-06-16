<?php

namespace ZnLib\Rpc\Symfony4\HttpKernel;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Libs\App\Helpers\EnvHelper;
use ZnCore\Base\Libs\Container\Helpers\ContainerHelper;
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
use ZnLib\Rpc\Symfony4\Libs\RpcRequestHandler;
use ZnLib\Web\Symfony4\HttpKernel\BaseHttpKernel;

class RpcKernel extends BaseHttpKernel
{

//    protected $procedureService;
//    protected $logger;
    protected $responseFormatter;
//    protected $rpcJsonResponse;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        ControllerResolverInterface $resolver,

//        LoggerInterface $logger,
        ResponseFormatter $responseFormatter,
//        RpcJsonResponse $rpcJsonResponse,
//        ProcedureServiceInterface $procedureService,

        RequestStack $requestStack = null,
        ArgumentResolverInterface $argumentResolver = null
    )
    {
        parent::__construct($dispatcher, $resolver, $requestStack, $argumentResolver);
//        $this->logger = $logger;
        $this->responseFormatter = $responseFormatter;
//        $this->rpcJsonResponse = $rpcJsonResponse;
//        $this->procedureService = $procedureService;
    }

    protected function handleRaw(Request $request, int $type = self::MAIN_REQUEST): Response
    {
        $jsonData = $request->getContent();

        $handler = ContainerHelper::getContainer()->get(RpcRequestHandler::class);
//        $handler = new RpcRequestHandler();
        $responseData = $handler->handleJsonData($jsonData);
//        dump($responseData);
//        $responseData = $this->handleJsonData($jsonData);

        $response = $this->createResponse($responseData);
        $response = $this->filterResponse($response, $request, $type);
        return $response;
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
}
