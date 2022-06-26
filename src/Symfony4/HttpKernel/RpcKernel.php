<?php

namespace ZnLib\Rpc\Symfony4\HttpKernel;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ZnCore\Base\Env\Helpers\EnvHelper;
use ZnLib\Rpc\Domain\Libs\ResponseFormatter;
use ZnLib\Rpc\Symfony4\Libs\RpcRequestHandler;
use ZnSf\Web\Domain\Libs\BaseHttpKernel;

class RpcKernel extends BaseHttpKernel
{

    protected $responseFormatter;
    protected $rpcRequestHandler;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        ControllerResolverInterface $resolver,
        RpcRequestHandler $rpcRequestHandler,
        ResponseFormatter $responseFormatter,
        RequestStack $requestStack = null,
        ArgumentResolverInterface $argumentResolver = null
    )
    {
        parent::__construct($dispatcher, $resolver, $requestStack, $argumentResolver);
        $this->responseFormatter = $responseFormatter;
        $this->rpcRequestHandler = $rpcRequestHandler;
    }

    protected function handleRaw(Request $request, int $type = self::MAIN_REQUEST): Response
    {
        $jsonData = $request->getContent();
        $responseData = $this->rpcRequestHandler->handleJsonData($jsonData);
        $response = $this->createJsonResponse($responseData);
        $response = $this->filterResponse($response, $request, $type);
        return $response;
    }

    private function createJsonResponse(array $responseData): JsonResponse
    {
        $response = new JsonResponse();
        if (EnvHelper::isDebug()) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }
        $response->setData($responseData);
        return $response;
    }
}
