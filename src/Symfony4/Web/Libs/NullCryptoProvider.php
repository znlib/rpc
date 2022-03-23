<?php

namespace ZnLib\Rpc\Symfony4\Web\Libs;

use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

/**
 * Стратегия криптопровайдера, которая не выпоняет проверок ЭЦП.
 *
 * Используется при отключении контроля ЭЦП запросов и ответов.
 */
class NullCryptoProvider implements CryptoProviderInterface
{

    public function signRequest(RpcRequestEntity $requestEntity): void
    {

    }

    public function verifyRequest(RpcRequestEntity $requestEntity): void
    {

    }

    public function signResponse(RpcResponseEntity $responseEntity): void
    {

    }

    public function verifyResponse(RpcResponseEntity $responseEntity): void
    {

    }
}
