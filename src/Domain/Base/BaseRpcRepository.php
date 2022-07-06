<?php

namespace ZnLib\Rpc\Domain\Base;

use ZnCore\Base\Env\Helpers\EnvHelper;
use ZnCore\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Validation\Helpers\ErrorCollectionHelper;
use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Domain\Traits\DispatchEventTrait;
use ZnCore\Domain\Domain\Traits\ForgeQueryTrait;
use ZnCore\Entity\Exceptions\NotFoundException;
use ZnCore\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Repository\Base\BaseRepository;
use ZnCore\Domain\Repository\Traits\RepositoryMapperTrait;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcErrorCodeEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Facades\RpcClientFacade;
use ZnLib\Rpc\Domain\Forms\BaseRpcAuthForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthGuestForm;
use ZnLib\Rpc\Domain\Libs\RpcAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcProvider;

abstract class BaseRpcRepository extends BaseRepository implements GetEntityClassInterface
{

    use RepositoryMapperTrait;
    use DispatchEventTrait;
    use ForgeQueryTrait;

    private $cache = [];

    abstract public function baseUrl(): string;

    public function getRpcProvider(): RpcProvider
    {
        $baseUrl = $this->baseUrl();
        $rpcProvider =
            (new RpcClientFacade(EnvHelper::getAppEnv()))
                ->createRpcProvider($baseUrl);
        $authProvider = new RpcAuthProvider($rpcProvider);
        $rpcProvider->setAuthProvider($authProvider);
        return $rpcProvider;
    }

    protected function createRequest(string $methodName = null): RpcRequestEntity
    {
        $requestEntity = new RpcRequestEntity();
        $requestEntity->setJsonrpc(RpcVersionEnum::V2_0);
        $requestEntity->setMetaItem(HttpHeaderEnum::VERSION, 1);
        $methodName = $this->prepareMethodName($methodName);
        if ($methodName) {
            $requestEntity->setMethod($methodName);
        }
        return $requestEntity;
    }

    protected function prepareMethodName(string $methodName = null): string
    {
        $result = '';
        if ($this->methodPrefix()) {
            $result .= $this->methodPrefix();
        }
        if ($methodName) {
            $result .= $methodName;
        }
        return $result;
    }

    public function authBy(): BaseRpcAuthForm
    {
        return new RpcAuthGuestForm();
    }

    private function getRequestHash(RpcRequestEntity $requestEntity): string
    {
        $requestArray = EntityHelper::toArray($requestEntity);
        unset($requestArray['id']);
        unset($requestArray['jsonrpc']);
        $requestHashScope = json_encode($requestArray);
        $requestHash = hash('sha1', $requestHashScope);
        return $requestHash;
    }

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity, BaseRpcAuthForm $authForm = null): RpcResponseEntity
    {
        $requestHash = $this->getRequestHash($requestEntity);
        $responseEntity = $this->cache[$requestHash] ?? null;
        if (!$responseEntity || EnvHelper::isTest()) {
            $responseEntity = $this->sendRequest($requestEntity, $authForm);
            $this->cache[$requestHash] = $responseEntity;
        }
        if ($responseEntity->isError()) {
            $this->handleError($responseEntity);
        }
        return $responseEntity;
    }

    protected function sendRequest(RpcRequestEntity $requestEntity, BaseRpcAuthForm $authForm = null): RpcResponseEntity
    {
        $provider = $this->getRpcProvider();
        $authForm = $authForm ?: $this->authBy();
        /*if (!$authForm instanceof RpcAuthGuestForm) {
            $provider->authByForm($authForm);
        }*/
        $responseEntity = $provider->sendRequestByEntity($requestEntity, $authForm);
        return $responseEntity;
    }

    protected function handleError(RpcResponseEntity $rpcResponseEntity)
    {
        $errorCode = $rpcResponseEntity->getError()['code'];
        if ($errorCode == RpcErrorCodeEnum::SERVER_ERROR_INVALID_PARAMS) {
            $errors = $rpcResponseEntity->getError()['data'];
            $errorCollection = ErrorCollectionHelper::itemArrayToCollection($errors);
            throw new UnprocessibleEntityException($errorCollection);
        }

        if ($errorCode == 404) {
            throw new NotFoundException();
        }
    }
}
