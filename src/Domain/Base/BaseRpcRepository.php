<?php

namespace ZnLib\Rpc\Domain\Base;

use ZnCore\Base\Env\Helpers\EnvHelper;
use ZnCore\Base\EventDispatcher\Traits\EventDispatcherTrait;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\EntityManager\Traits\EntityManagerAwareTrait;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Repository\Base\BaseRepository;
use ZnCore\Domain\Repository\Traits\MapperTrait;
use ZnCore\Domain\Repository\Traits\RepositoryDispatchEventTrait;
use ZnLib\Rpc\Domain\Entities\RpcRequestEntity;
use ZnLib\Rpc\Domain\Entities\RpcResponseEntity;
use ZnLib\Rpc\Domain\Enums\HttpHeaderEnum;
use ZnLib\Rpc\Domain\Enums\RpcVersionEnum;
use ZnLib\Rpc\Domain\Facades\RpcClientFacade;
use ZnLib\Rpc\Domain\Forms\BaseRpcAuthForm;
use ZnLib\Rpc\Domain\Forms\RpcAuthGuestForm;
use ZnLib\Rpc\Domain\Libs\RpcAuthProvider;
use ZnLib\Rpc\Domain\Libs\RpcProvider;

abstract class BaseRpcRepository extends BaseRepository implements GetEntityClassInterface
{

    use EventDispatcherTrait;
    use EntityManagerAwareTrait;
    use MapperTrait;
    use RepositoryDispatchEventTrait;

    private $entityClassName;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    public function getEntityClass(): string
    {
        return $this->entityClassName;
    }

    abstract public function baseUrl(): string;

    protected function forgeQuery(Query $query = null): Query
    {
        $query = Query::forge($query);
        $this->dispatchQueryEvent($query, EventEnum::BEFORE_FORGE_QUERY);
        return $query;
    }

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

    protected function sendRequestByEntity(RpcRequestEntity $requestEntity, BaseRpcAuthForm $authForm = null): RpcResponseEntity
    {
        $provider = $this->getRpcProvider();
        $authForm = $authForm ?: $this->authBy();
        if (!$authForm instanceof RpcAuthGuestForm) {
            $provider->authByForm($authForm);
        }
        $responseEntity = $provider->sendRequestByEntity($requestEntity);
        return $responseEntity;
    }
}
