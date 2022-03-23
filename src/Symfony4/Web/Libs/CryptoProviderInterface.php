<?php

namespace ZnLib\Rpc\Symfony4\Web\Libs;

use ZnCrypt\Base\Domain\Exceptions\CertificateExpiredException;
use ZnCrypt\Base\Domain\Exceptions\FailCertificateSignatureException;
use ZnCrypt\Base\Domain\Exceptions\FailSignatureException;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;

/**
 * Интерфейс криптопровайдера, для контроль ЭПЦ.
 *
 * Выполняет контроль ЭЦП запросов и ответов.
 */
interface CryptoProviderInterface
{

    /**
     * Подпись RPC-запроса
     * @param RpcRequestEntity $requestEntity
     */
    public function signRequest(RpcRequestEntity $requestEntity): void;

    /**
     * Верификация подписи RPC-запроса
     * @param RpcRequestEntity $requestEntity
     * @throws FailCertificateSignatureException
     * @throws CertificateExpiredException
     * @throws FailSignatureException
     */
    public function verifyRequest(RpcRequestEntity $requestEntity): void;

    /**
     * Подпись RPC-ответа
     * @param RpcResponseEntity $responseEntity
     */
    public function signResponse(RpcResponseEntity $responseEntity): void;

    /**
     * Верификация подписи RPC-ответа
     * @param RpcResponseEntity $responseEntity
     * @throws FailCertificateSignatureException
     * @throws CertificateExpiredException
     * @throws FailSignatureException
     */
    public function verifyResponse(RpcResponseEntity $responseEntity): void;

}
