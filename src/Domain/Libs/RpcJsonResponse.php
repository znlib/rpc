<?php

namespace ZnLib\Rpc\Domain\Libs;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use ZnCore\Base\Helpers\EnvHelper;

class RpcJsonResponse
{

    public function send(array $array): JsonResponse
    {
        $response = new JsonResponse();
        if (EnvHelper::isDebug()) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }
        $response->setData($array);
        return $response;
    }
}
